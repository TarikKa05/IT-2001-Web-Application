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
      const stock = Number.isFinite(product.stock) ? product.stock : 0;
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
            <button class="btn btn-sm btn-danger btn-sm-custom" onclick="resetProduct(${index})">Reset</button>
          </td>
        </tr>
      `;
    })
    .join("");

  tbody.innerHTML = rows;
}


async function loadCartProducts() {
  try {
    const res = await fetch("assets/cart.json", { cache: "no-store" });
    if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);

    const data = await res.json();
    cartState.products = Array.isArray(data)
      ? data.map((item = {}) => {
          const parsedStock = Number(item.stock);
          const stock = Number.isFinite(parsedStock) && parsedStock > 0 ? parsedStock : DEFAULT_STOCK_LIMIT;
          const parsedQuantity = Number(item.quantity);
          return {
            ...item,
            price: Number(item.price) || 0,
            quantity: Number.isFinite(parsedQuantity) ? Math.max(0, Math.min(stock, parsedQuantity)) : 0,
            stock,
          };
        })
      : [];
    renderCartTable();
  } catch (error) {
    console.error("Failed to load cart items:", error);
    const tbody = document.querySelector("#productTable tbody");
    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center text-danger py-4">
            Unable to load cart data.<br/>
            <small>${error.message}</small>
          </td>
        </tr>
      `;
    }
  }
}


function adjustQuantity(index, delta) {
  if (!Number.isInteger(index) || !cartState.products[index]) return;

  const product = cartState.products[index];
  const currentQuantity = Number(product.quantity) || 0;
  const stock = Number.isFinite(product.stock) ? product.stock : 0;

  if (delta > 0 && currentQuantity >= stock) {
    const stockText = stock === 0 ? "This item is currently out of stock." : `Only ${stock} item${stock === 1 ? "" : "s"} available in stock.`;
    setStockMessage(stockText);
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

function initSigninView() {
  const form = document.getElementById("signinForm");
  if (!form || form.dataset.signinInitialized === "true") return;

  form.dataset.signinInitialized = "true";

  const emailInput = form.querySelector("#email");
  const passwordInput = form.querySelector("#password");
  const repeatPasswordInput = form.querySelector("#repeatPassword");
  const allInputs = [emailInput, passwordInput, repeatPasswordInput].filter(
    Boolean,
  );

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    allInputs.forEach((input) => input.classList.remove("is-invalid"));
    const messages = [];

    const emailValue = emailInput?.value.trim() ?? "";
    const passwordValue = passwordInput?.value.trim() ?? "";
    const repeatValue = repeatPasswordInput?.value.trim() ?? "";

    if (!emailValue) {
      messages.push("Please enter your email address.");
      emailInput?.classList.add("is-invalid");
    }

    if (!passwordValue) {
      messages.push("Please enter your password.");
      passwordInput?.classList.add("is-invalid");
    }

    if (repeatPasswordInput) {
      if (!repeatValue) {
        messages.push("Please repeat your password.");
        repeatPasswordInput.classList.add("is-invalid");
      } else if (repeatValue !== passwordValue) {
        messages.push("Passwords must match.");
        repeatPasswordInput.classList.add("is-invalid");
      }
    }

    if (messages.length > 0) {
      alert(messages.join("\n"));
      return;
    }

    window.location.hash = "/landing";
  });
}

function initSignupView() {
  const form = document.getElementById("registrationForm");
  if (!form || form.dataset.signupInitialized === "true") return;

  form.dataset.signupInitialized = "true";

  const fields = {
    name: form.querySelector("#name"),
    username: form.querySelector("#username"),
    email: form.querySelector("#email"),
    password: form.querySelector("#password"),
    repeatPassword: form.querySelector("#repeatPassword"),
    phoneNumber: form.querySelector("#phoneNumber"),
    birthDate: form.querySelector("#birthDate"),
  };

  const activityRadios = Array.from(
    form.querySelectorAll('input[name="activityLevel"]'),
  );
  const allValidatedInputs = [
    ...Object.values(fields).filter(Boolean),
    ...activityRadios,
  ];

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    allValidatedInputs.forEach((input) => input.classList.remove("is-invalid"));
    const messages = [];

    if (!fields.name?.value.trim()) {
      messages.push("Please enter your name.");
      fields.name?.classList.add("is-invalid");
    }

    if (!fields.username?.value.trim()) {
      messages.push("Please enter a username.");
      fields.username?.classList.add("is-invalid");
    }

    const emailValue = fields.email?.value.trim() ?? "";
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!emailPattern.test(emailValue)) {
      messages.push("Please enter a valid email address.");
      fields.email?.classList.add("is-invalid");
    }

    const passwordValue = fields.password?.value.trim() ?? "";
    if (passwordValue.length < 7 || passwordValue.length > 15) {
      messages.push("Password must be between 7 and 15 characters.");
      fields.password?.classList.add("is-invalid");
    }

    const repeatValue = fields.repeatPassword?.value.trim() ?? "";
    if (passwordValue !== repeatValue) {
      messages.push("Passwords must match.");
      fields.repeatPassword?.classList.add("is-invalid");
    }

    const phoneValue = fields.phoneNumber?.value.trim() ?? "";
    const phonePattern = /^[0-9]+$/;
    if (!phonePattern.test(phoneValue)) {
      messages.push("Please enter a valid phone number.");
      fields.phoneNumber?.classList.add("is-invalid");
    }

    if (!fields.birthDate?.value.trim()) {
      messages.push("Please enter a valid date of birth.");
      fields.birthDate?.classList.add("is-invalid");
    }

    if (messages.length > 0) {
      alert(messages.join("\n"));
      return;
    }

    try {
      const existing = JSON.parse(
        localStorage.getItem("registrationData") || "[]",
      );
      const registrationEntry = {
        name: fields.name?.value.trim() ?? "",
        username: fields.username?.value.trim() ?? "",
        email: emailValue,
        password: passwordValue,
        phoneNumber: phoneValue,
        birthDate: fields.birthDate?.value.trim() ?? "",
      };
      existing.push(registrationEntry);
      localStorage.setItem("registrationData", JSON.stringify(existing));
    } catch (error) {
      console.error("Failed to cache registration info:", error);
    }

    alert("Registration successful!");
    window.location.hash = "/landing";
  });
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
