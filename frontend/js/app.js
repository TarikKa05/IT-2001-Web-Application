document.addEventListener("click", (event) => {
  const question = event.target.closest(".faq-question");
  if (!question) return;

  const item = question.closest(".faq-item");
  const faqWrapper = question.closest(".faq");
  if (!item || !faqWrapper) return;

  faqWrapper.querySelectorAll(".faq-item").forEach((other) => {
    if (other !== item) other.classList.remove("active");
  });

  item.classList.toggle("active");

  faqWrapper.querySelectorAll(".faq-item").forEach((faqItem) => {
    const answer = faqItem.querySelector(".faq-answer");
    if (!answer) return;
    answer.style.display = faqItem.classList.contains("active")
      ? "block"
      : "none";
  });
});

const cartState = {
  products: [],
};

const DEFAULT_STOCK_LIMIT = 10;

function setStockMessage(message = "") {
  const stockMessage = document.getElementById("stockMessage");
  if (!stockMessage) return;
  stockMessage.textContent = message;
}

function renderCartTable() {
  const tbody = document.querySelector("#productTable tbody");
  if (!tbody) return;

  if (!Array.isArray(cartState.products) || cartState.products.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center py-4">Cart is empty.</td>
      </tr>
    `;
    return;
  }

  const rows = cartState.products
    .map((product = {}, index) => {
      const quantity = Number(product.quantity) || 0;
      const price = Number(product.price) || 0;
      const stockValue = product.stock_quantity ?? product.stock;
      const stock = Number.isFinite(stockValue) ? stockValue : 0;
      const totalPrice = quantity * price;
      return `
        <tr id="product-${index}">
          <td>${product.name ?? "Unnamed product"}</td>
          <td>
            <div class="quantity-controls">
              <button
                type="button"
                class="qty-btn decrease"
                data-qty-change="down"
                data-index="${index}"
                aria-label="Decrease quantity"
              >&minus;</button>
              <span id="quantity-display-${index}" class="quantity-value">${quantity}</span>
              <button
                type="button"
                class="qty-btn increase"
                data-qty-change="up"
                data-index="${index}"
                aria-label="Increase quantity"
              >+</button>
            </div>
          </td>
          <td>${stock}</td>
          <td>${price}$</td>
          <td id="total-price-${index}">${totalPrice}$</td>
          <td>
            <button class="btn btn-sm btn-danger btn-sm-custom px-2 py-1" onclick="resetProduct(${index})">Reset</button>
          </td>
        </tr>
      `;
    })
    .join("");

  tbody.innerHTML = rows;
}


function normalizeCartProducts(raw = []) {
  return raw.map((item = {}) => {
    const parsedStock = Number(item.stock_quantity ?? item.stock);
    const stock = Number.isFinite(parsedStock) && parsedStock > 0 ? parsedStock : DEFAULT_STOCK_LIMIT;
    const parsedQuantity = Number(item.quantity);
    const baseQuantity = Number.isFinite(parsedQuantity) && parsedQuantity > 0 ? parsedQuantity : 1;
    return {
      ...item,
      name: item.name || "Product",
      price: Number(item.price) || 0,
      quantity: Math.max(0, Math.min(stock, baseQuantity)),
      stock,
      stock_quantity: stock,
    };
  });
}

async function loadCartProducts() {
  try {
    const handleProducts = (products) => {
      const listData = Array.isArray(products) ? products : products?.data || [];
      if (!listData.length) throw new Error("No products received from API");
      cartState.products = normalizeCartProducts(listData);
      renderCartTable();
    };

    // Prefer ProductService; fall back to RestClient; lastly to direct fetch.
    if (window.ProductService) {
      ProductService.getAll(
        function (products) {
          try {
            handleProducts(products);
          } catch (err) {
            console.error("ProductService data issue:", err);
            showCartLoadError(err);
          }
        },
        function (err) {
          console.error("Failed to load products for cart:", err);
          showCartLoadError(err);
        }
      );
      return;
    }

    if (window.RestClient) {
      RestClient.get(
        "products/",
        function (products) {
          try {
            handleProducts(products);
          } catch (err) {
            console.error("RestClient data issue:", err);
            showCartLoadError(err);
          }
        },
        function (err) {
          console.error("Failed to load products for cart via RestClient:", err);
          showCartLoadError(err);
        }
      );
      return;
    }

    // Last-resort direct fetch
    const res = await fetch(`${Constants.PROJECT_BASE_URL}products/`, { cache: "no-store" });
    if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
    const data = await res.json();
    handleProducts(data);
  } catch (error) {
    console.error("Failed to load cart items:", error);
    showCartLoadError(error);
  }
}

function showCartLoadError(error) {
  const tbody = document.querySelector("#productTable tbody");
  if (tbody) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-danger py-4">
          Unable to load cart data.<br/>
          <small>${error?.message || "Unknown error"}</small>
        </td>
      </tr>
    `;
  }
}


function adjustQuantity(index, delta) {
  if (!Number.isInteger(index) || !cartState.products[index]) return;

  const product = cartState.products[index];
  const currentQuantity = Number(product.quantity) || 0;
  const stock = Number.isFinite(product.stock_quantity ?? product.stock)
    ? (product.stock_quantity ?? product.stock)
    : 0;

  if (delta > 0 && currentQuantity >= stock) {
    alert("Stock is empty for this item.");
    return;
  }

  const nextQuantity = Math.max(0, Math.min(stock, currentQuantity + delta));
  if (nextQuantity === currentQuantity) return;

  product.quantity = nextQuantity;
  setStockMessage("");
  const paymentMessage = document.getElementById("paymentMessage");
  if (paymentMessage) {
    paymentMessage.textContent = "";
    paymentMessage.classList.remove("payment-message--visible");
  }
  renderCartTable();
  updateDisplayedTotalIfPresent();
}


function handleTotalCalculation() {
  if (window.UserService && !UserService.requireAuthOrRedirect("/signup")) {
    return;
  }

  const total = cartState.products.reduce((sum, product = {}) => {
    const quantity = Number(product.quantity) || 0;
    const price = Number(product.price) || 0;
    return sum + quantity * price;
  }, 0);

  const totalAmount = document.getElementById("totalAmount");

  const paymentMessage = document.getElementById("paymentMessage");
  if (paymentMessage) {
    if (total > 0) {
      paymentMessage.textContent = `Payment of $${total} processed successfully! You will receive an email with the details shortly.`;
      paymentMessage.classList.add("payment-message--visible");
    } else {
      paymentMessage.textContent =
        "Add items to your cart before proceeding to payment.";
      paymentMessage.classList.add("payment-message--visible");
    }
  }
}

function updateDisplayedTotalIfPresent() {
  const totalAmount = document.getElementById("totalAmount");
  if (!totalAmount || totalAmount.textContent.trim() === "") return;

  const updatedTotal = cartState.products.reduce((sum, item = {}) => {
    const qty = Number(item.quantity) || 0;
    const itemPrice = Number(item.price) || 0;
    return sum + qty * itemPrice;
  }, 0);

  totalAmount.textContent = `Total Amount: $${updatedTotal}`;
}

function resetProduct(index) {
  if (!cartState.products[index]) return;
  cartState.products[index].quantity = 0;
  setStockMessage("");
  const paymentMessage = document.getElementById("paymentMessage");
  if (paymentMessage) {
    paymentMessage.textContent = "";
    paymentMessage.classList.remove("payment-message--visible");
  }
  renderCartTable();
  updateDisplayedTotalIfPresent();
}


function initCartView() {
  const cartTable = document.getElementById("productTable");
  if (!cartTable || cartTable.dataset.cartInitialized === "true") return;

  cartTable.dataset.cartInitialized = "true";

  loadCartProducts();

  const totalButton = document.getElementById("calculateTotal");
  if (totalButton) {
    totalButton.addEventListener("click", handleTotalCalculation);
  }

  cartTable.addEventListener("click", (event) => {
    const control = event.target.closest("[data-qty-change]");
    if (!control) return;

    event.preventDefault();

    const index = Number.parseInt(control.dataset.index, 10);
    if (!Number.isInteger(index)) return;

    const delta = control.dataset.qtyChange === "up" ? 1 : -1;
    adjustQuantity(index, delta);
  });

  window.resetProduct = resetProduct;
}

function buildOrderPayloadFromCart() {
  const token = localStorage.getItem("user_token");
  const parsed = Utils.parseJwt(token);
  const userId = parsed?.user?.id;
  const total = cartState.products.reduce((sum, product = {}) => {
    const quantity = Number(product.quantity) || 0;
    const price = Number(product.price) || 0;
    return sum + quantity * price;
  }, 0);
  return {
    user_id: userId,
    total_amount: total,
  };
}

function initSigninView() {
  if (window.UserService && typeof UserService.bindSigninForm === "function") {
    UserService.bindSigninForm();
  }
}

function initSignupView() {
  if (window.UserService && typeof UserService.bindSignupForm === "function") {
    UserService.bindSignupForm();
  }
}

function initLandingView() {
  const gallery = document.querySelector(".gallery");
  const modal = document.getElementById("imageModal");
  const modalImage = document.getElementById("modalImage");
  const closeModalBtn = document.getElementById("closeModal");

  if (
    gallery &&
    modal &&
    modalImage &&
    closeModalBtn &&
    gallery.dataset.galleryInitialized !== "true"
  ) {
    const openModal = (src = "") => {
      modal.style.display = "flex";
      modalImage.src = src;
    };

    const hideModal = () => {
      modal.style.display = "none";
      modalImage.src = "";
    };

    gallery.querySelectorAll("img").forEach((image) => {
      const fullSrc = image.dataset.full || image.src;
      image.addEventListener("click", () => openModal(fullSrc));
    });

    closeModalBtn.addEventListener("click", hideModal);
    modal.addEventListener("click", (event) => {
      if (event.target === modal) {
        hideModal();
      }
    });

    gallery.dataset.galleryInitialized = "true";
  }

  const faqWrapper = document.querySelector(".faq");
  if (faqWrapper && faqWrapper.dataset.faqInitialized !== "true") {
    faqWrapper.querySelectorAll(".faq-answer").forEach((answer) => {
      answer.style.display = "none";
    });
    faqWrapper.dataset.faqInitialized = "true";
  }
}

document.addEventListener("view:loaded", (event) => {
  const route = event.detail?.route;

  if (route === "/cart") {
    initCartView();
  }

  if (route === "/signin") {
    initSigninView();
  }

  if (route === "/landing") {
    initLandingView();
  }

  if (route === "/signup") {
    initSignupView();
  }

  if (route === "/products") {
    initProductsView();
  }
});

function handleLandingPosition(position) {
  const output = document.getElementById("location");
  if (!output) return;
  output.innerText = `Latitude: ${position.coords.latitude}, Longitude: ${position.coords.longitude}`;
}

function handleLandingGeolocationError(error) {
  const output = document.getElementById("location");
  if (!output) return;
  switch (error.code) {
    case error.PERMISSION_DENIED:
      output.innerText = "User denied the request for Geolocation.";
      break;
    case error.POSITION_UNAVAILABLE:
      output.innerText = "Location information is unavailable.";
      break;
    case error.TIMEOUT:
      output.innerText = "The request to get user location timed out.";
      break;
    default:
      output.innerText = "An unknown error occurred.";
  }
}

function updateLandingWeatherDisplay(data) {
  const weatherDiv = document.getElementById("weather");
  if (!weatherDiv) return;
  weatherDiv.innerHTML = `
      <div class="weather-card">
        <h3>Current Weather:</h3>
        <p><b>Temperature:</b> ${data.main?.temp ?? "-"}&deg;C</p>
        <p><b>Weather:</b> ${data.weather?.[0]?.description ?? "-"}</p>
        <p><b>Humidity:</b> ${data.main?.humidity ?? "-"}%</p>
        <p><b>Wind Speed:</b> ${data.wind?.speed ?? "-"} m/s</p>
      </div>
    `;
}

window.getLocation = function () {
  const output = document.getElementById("location");
  if (!output) return;

  if (!navigator.geolocation) {
    output.innerText = "Geolocation is not supported by this browser.";
    return;
  }

  navigator.geolocation.getCurrentPosition(
    handleLandingPosition,
    handleLandingGeolocationError,
  );
};

window.fetchWeatherData = async function (latitude, longitude) {
  const weatherDiv = document.getElementById("weather");
  if (!weatherDiv) return;

  const apiKey = "05b820be27153c766bb293e18b4f87c6";
  const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&units=metric&appid=${apiKey}`;

  try {
    const response = await fetch(apiUrl);
    if (!response.ok) {
      throw new Error("Failed to fetch weather data");
    }
    const data = await response.json();
    updateLandingWeatherDisplay(data);
  } catch (error) {
    weatherDiv.innerHTML = `<p class="text-danger">Error fetching weather data: ${error.message}</p>`;
  }
};

document.addEventListener("DOMContentLoaded", () => {
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn && !logoutBtn.dataset.bound) {
    logoutBtn.dataset.bound = "true";
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (window.UserService) {
        UserService.logoutWithCheck();
      }
    });
  }
});

document.addEventListener("click", (event) => {
  const target = event.target.closest("#calculateTotal");
  if (!target) return;

  if (window.UserService && !UserService.requireAuthOrRedirect("/signup")) {
    event.preventDefault();
    return;
  }

  const payload = buildOrderPayloadFromCart();
  if (payload.total_amount <= 0) {
    alert("Add items to your cart before proceeding to payment.");
    return;
  }

  if (window.OrderService) {
    OrderService.createOrder(
      payload,
      function () {
        alert("Order placed successfully!");
        handleTotalCalculation();
      },
      function (err) {
        const msg =
          err?.responseJSON?.error ||
          err?.responseJSON?.message ||
          err?.responseText ||
          "Failed to place order.";
        toastr.error(msg);
      }
    );
  }
});

function renderProductsList(products = []) {
  const list = document.getElementById("dynamicProductCards");
  if (!list) return;

  const items = Array.isArray(products) ? products : [];

  const chunks = [];
  for (let i = 0; i < items.length; i += 3) {
    chunks.push(items.slice(i, i + 3));
  }

  list.innerHTML = chunks
    .map(
      (chunk) => `
        ${chunk
          .map((product) => {
            const name = product.name || "Product";
            const category =
              (product.category_names
                ? product.category_names.split(",").map((c) => c.trim()).filter(Boolean)[0]
                : null) ||
              product.category_name ||
              "Category";
            const price = product.price !== undefined ? `${product.price}$` : "";
            const description = product.description || "";
            return `
              <div class="col-12 col-md-4">
                <div class="card h-100 text-center text-black">
                  <div class="card-body">
                    <h3 class="card-title"><b>${name}</b></h3>
                    <p class="mt-2">${description}</p>
                    <p class="text-muted mb-1">Category: ${category}</p>
                    <p class="fw-bold">${price}</p>
                    <a href="#/cart" class="productsButtons">Buy</a>
                  </div>
                </div>
              </div>
            `;
          })
          .join("")}
      `
    )
    .join("");
}

function renderProductsError(message) {
  const list = document.getElementById("dynamicProductCards");
  if (!list) return;
  list.innerHTML = `
    <div class="col-12">
      <div class="alert alert-danger text-center">
        Unable to load products.<br/>
        <small>${message || "Unknown error"}</small>
      </div>
    </div>`;
}

function initProductsView() {
  const fetchAndRender = (products) => {
    const listData = Array.isArray(products) ? products : products?.data || [];
    if (listData && listData.length) {
      renderProductsList(listData);
    } else {
      renderProductsError("No products received from API.");
    }
  };

  if (window.ProductService) {
    ProductService.getAll(
      function (products) {
        fetchAndRender(products);
      },
      function () {
        // try next fallback
        if (window.RestClient) {
          RestClient.get(
            "products/",
            function (data) {
              fetchAndRender(data);
            },
            function (err) {
              renderProductsError(err?.responseText || "Failed to load products.");
            }
          );
        } else {
          // last-resort direct fetch
          fetch(`${Constants.PROJECT_BASE_URL}products/`, { cache: "no-store" })
            .then((res) => {
              if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
              return res.json();
            })
            .then(fetchAndRender)
            .catch((err) => renderProductsError(err.message));
        }
      }
    );
    return;
  }

  if (window.RestClient) {
    RestClient.get(
      "products/",
      function (products) {
        fetchAndRender(products);
      },
      function () {
        renderProductsError("Failed to load products.");
      }
    );
    return;
  }

  // last-resort direct fetch
  fetch(`${Constants.PROJECT_BASE_URL}products/`, { cache: "no-store" })
    .then((res) => {
      if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
      return res.json();
    })
    .then(fetchAndRender)
    .catch((err) => renderProductsError(err.message));
}
