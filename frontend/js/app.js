// frontend/js/app.js
// Delegated interactions that need to work across SPA route loads.

document.addEventListener('click', (event) => {
  const question = event.target.closest('.faq-question');
  if (!question) return;

  const item = question.closest('.faq-item');
  const faqWrapper = question.closest('.faq');
  if (!item || !faqWrapper) return;

  // Collapse every other FAQ entry.
  faqWrapper.querySelectorAll('.faq-item').forEach((other) => {
    if (other !== item) other.classList.remove('active');
  });

  item.classList.toggle('active');
});

// ---------------- Cart view handling ----------------

const cartState = {
  products: [],
  currentEditIndex: null,
  modalInstance: null,
};

function renderCartTable() {
  const tbody = document.querySelector('#productTable tbody');
  if (!tbody) return;

  if (!Array.isArray(cartState.products) || cartState.products.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-4">Cart is empty.</td>
      </tr>
    `;
    return;
  }

  const rows = cartState.products
    .map((product = {}, index) => {
      const quantity = Number(product.quantity) || 0;
      const price = Number(product.price) || 0;
      const totalPrice = quantity * price;
      return `
        <tr id="product-${index}">
          <td>${product.name ?? 'Unnamed product'}</td>
          <td>
            <div class="d-flex align-items-center">
              <span id="quantity-display-${index}">${quantity}</span>
              <button
                type="button"
                class="btn btn-sm btn-primary ms-2 btn-sm-custom edit-product-btn"
                data-edit-index="${index}"
                data-bs-toggle="modal"
                data-bs-target="#editQuantityModal"
              >Edit</button>
            </div>
          </td>
          <td>${price}$</td>
          <td id="total-price-${index}">${totalPrice}$</td>
          <td>
            <button class="btn btn-sm btn-danger btn-sm-custom" onclick="resetProduct(${index})">Reset</button>
          </td>
        </tr>
      `;
    })
    .join('');

  tbody.innerHTML = rows;
}

async function loadCartProducts() {
  try {
    const res = await fetch('assets/cart.json', { cache: 'no-store' });
    if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);

    const data = await res.json();
    cartState.products = Array.isArray(data) ? data : [];
    renderCartTable();
  } catch (error) {
    console.error('Failed to load cart items:', error);
    const tbody = document.querySelector('#productTable tbody');
    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" class="text-center text-danger py-4">
            Unable to load cart data.<br/>
            <small>${error.message}</small>
          </td>
        </tr>
      `;
    }
  }
}

function handleTotalCalculation() {
  const total = cartState.products.reduce((sum, product = {}) => {
    const quantity = Number(product.quantity) || 0;
    const price = Number(product.price) || 0;
    return sum + quantity * price;
  }, 0);

  const totalAmount = document.getElementById('totalAmount');
  if (totalAmount) {
    totalAmount.textContent = `Total Amount: $${total}`;
  }

  if (window.toastr) {
    window.toastr.success(
      `Total Payment: $${total}. Thank you for your purchase! Soon you will receive an email with more details.`,
      'Purchase Successful',
      {
        timeOut: 5000,
        closeButton: true,
        progressBar: true,
      }
    );
  }
}

function prepareEditModal(index) {
  if (!Number.isInteger(index) || index < 0 || index >= cartState.products.length) {
    console.warn('Invalid product index for edit modal:', index);
    return false;
  }

  cartState.currentEditIndex = index;
  const product = cartState.products[index] ?? {};
  const quantity = Number(product.quantity) || 0;
  const input = document.getElementById('editQuantityInput');
  if (input) {
    input.value = quantity;
    const focusField = () => {
      input.focus();
      input.select();
    };
    if (typeof queueMicrotask === 'function') {
      queueMicrotask(focusField);
    } else {
      setTimeout(focusField, 0);
    }
  }
  const productNameLabel = document.getElementById('editProductName');
  if (productNameLabel) {
    productNameLabel.textContent = product.name ?? '';
  }
  return true;
}

function hideEditModal() {
  const modalEl = document.getElementById('editQuantityModal');
  if (modalEl) {
    if (cartState.modalInstance && typeof cartState.modalInstance.hide === 'function') {
      cartState.modalInstance.hide();
    } else {
      modalEl.classList.remove('show');
      modalEl.style.display = 'none';
      cartState.currentEditIndex = null;
      const productNameLabel = document.getElementById('editProductName');
      if (productNameLabel) productNameLabel.textContent = '';
      const input = document.getElementById('editQuantityInput');
      if (input) input.value = '';
      document.body.classList.remove('modal-open');
      document.body.style.removeProperty('padding-right');
      document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
        if (backdrop && backdrop.parentNode) {
          backdrop.parentNode.removeChild(backdrop);
        }
      });
    }
  }
}

function updateDisplayedTotalIfPresent() {
  const totalAmount = document.getElementById('totalAmount');
  if (!totalAmount || totalAmount.textContent.trim() === '') return;

  const updatedTotal = cartState.products.reduce((sum, item = {}) => {
    const qty = Number(item.quantity) || 0;
    const itemPrice = Number(item.price) || 0;
    return sum + qty * itemPrice;
  }, 0);

  totalAmount.textContent = `Total Amount: $${updatedTotal}`;
}

function saveQuantityFromModal() {
  const input = document.getElementById('editQuantityInput');
  const index = cartState.currentEditIndex;
  if (!input || index === null || index === undefined) return;

  const newQuantity = Number.parseInt(input.value, 10);
  if (!Number.isInteger(newQuantity) || newQuantity < 0) {
    alert('Please enter a valid quantity.');
    input.focus();
    return;
  }

  if (cartState.products[index]) {
    cartState.products[index].quantity = newQuantity;
  }

  renderCartTable();
  updateDisplayedTotalIfPresent();
  hideEditModal();
}

function resetProduct(index) {
  if (cartState.products[index]) {
    cartState.products[index].quantity = 0;
    const quantityCell = document.getElementById(`quantity-display-${index}`);
    if (quantityCell) {
      quantityCell.textContent = 0;
    }
    const totalCell = document.getElementById(`total-price-${index}`);
    if (totalCell) {
      totalCell.textContent = '0$';
    }
    updateDisplayedTotalIfPresent();
  }
}

function initCartView() {
  const cartTable = document.getElementById('productTable');
  if (!cartTable || cartTable.dataset.cartInitialized === 'true') return;

  cartTable.dataset.cartInitialized = 'true';

  const hasBootstrapModal = Boolean(window.bootstrap?.Modal);
  document.body.classList.toggle('no-bootstrap', !hasBootstrapModal);

  loadCartProducts();

  const totalButton = document.getElementById('calculateTotal');
  if (totalButton) {
    totalButton.addEventListener('click', handleTotalCalculation);
  }

  const saveButton = document.getElementById('saveQuantity');
  if (saveButton) {
    saveButton.addEventListener('click', saveQuantityFromModal);
  }

  cartTable.addEventListener('click', (event) => {
    const editButton = event.target.closest('.edit-product-btn');
    if (!editButton) return;

    if (window.bootstrap?.Modal) {
      return;
    }

    event.preventDefault();
    const index = Number.parseInt(editButton.dataset.editIndex, 10);
    if (!Number.isInteger(index)) return;
    if (!prepareEditModal(index)) return;

    const modalEl = document.getElementById('editQuantityModal');
    if (!modalEl) return;

    modalEl.classList.add('show');
    modalEl.style.display = 'block';
    document.body.classList.add('modal-open');
    if (!document.querySelector('.modal-backdrop')) {
      const backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade show';
      document.body.appendChild(backdrop);
    }
  });

  const editModalEl = document.getElementById('editQuantityModal');
  if (editModalEl) {
    if (hasBootstrapModal) {
      cartState.modalInstance = window.bootstrap.Modal.getOrCreateInstance(editModalEl);
    }
    editModalEl.addEventListener('show.bs.modal', (event) => {
      const trigger = event.relatedTarget;
      if (!trigger) return;
      const index = Number.parseInt(trigger.getAttribute('data-edit-index'), 10);
      if (!Number.isInteger(index) || !prepareEditModal(index)) {
        event.preventDefault();
      }
    });
    editModalEl.addEventListener('hidden.bs.modal', () => {
      cartState.currentEditIndex = null;
      const productNameLabel = document.getElementById('editProductName');
      if (productNameLabel) productNameLabel.textContent = '';
      const input = document.getElementById('editQuantityInput');
      if (input) input.value = '';
    });

    editModalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach((btn) => {
      btn.addEventListener('click', () => {
        if (!window.bootstrap?.Modal) {
          hideEditModal();
        }
      });
    });
  }

  // Provide global access for inline onclick handlers rendered in the table.
  window.resetProduct = resetProduct;
}

function initSigninView() {
  const form = document.getElementById('signinForm');
  if (!form || form.dataset.signinInitialized === 'true') return;

  form.dataset.signinInitialized = 'true';

  const emailInput = form.querySelector('#email');
  const passwordInput = form.querySelector('#password');
  const repeatPasswordInput = form.querySelector('#repeatPassword');
  const allInputs = [emailInput, passwordInput, repeatPasswordInput].filter(Boolean);

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    allInputs.forEach((input) => input.classList.remove('is-invalid'));
    const messages = [];

    const emailValue = emailInput?.value.trim() ?? '';
    const passwordValue = passwordInput?.value.trim() ?? '';
    const repeatValue = repeatPasswordInput?.value.trim() ?? '';

    if (!emailValue) {
      messages.push('Please enter your email address.');
      emailInput?.classList.add('is-invalid');
    } else {
      const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!emailPattern.test(emailValue)) {
        messages.push('Please enter a valid email address.');
        emailInput?.classList.add('is-invalid');
      }
    }

    if (!passwordValue) {
      messages.push('Please enter your password.');
      passwordInput?.classList.add('is-invalid');
    } else if (passwordValue.length < 7 || passwordValue.length > 15) {
      messages.push('Password must be between 7 and 15 characters.');
      passwordInput?.classList.add('is-invalid');
    }

    if (repeatPasswordInput) {
      if (!repeatValue) {
        messages.push('Please repeat your password.');
        repeatPasswordInput.classList.add('is-invalid');
      } else if (repeatValue !== passwordValue) {
        messages.push('Passwords must match.');
        repeatPasswordInput.classList.add('is-invalid');
      }
    }

    if (messages.length > 0) {
      alert(messages.join('\n'));
      return;
    }

    const registeredUsers = JSON.parse(localStorage.getItem('registrationData') || '[]');
    const matchingUser = registeredUsers.find(
      (user = {}) =>
        typeof user.email === 'string' &&
        user.email.toLowerCase() === emailValue.toLowerCase()
    );

    if (!matchingUser) {
      alert('No account found with that email. Please sign up first.');
      return;
    }

    if (!matchingUser.password) {
      alert('Your account does not have a password stored. Please complete the sign-up form again.');
      return;
    }

    if (matchingUser.password !== passwordValue) {
      alert('Incorrect password. Please try again.');
      passwordInput?.classList.add('is-invalid');
      return;
    }

    localStorage.setItem(
      'currentUser',
      JSON.stringify({
        email: matchingUser.email,
        username: matchingUser.username ?? '',
        name: matchingUser.name ?? '',
      })
    );

    alert('Signed in successfully!');
    window.location.hash = '/products';
  });
}

document.addEventListener('view:loaded', (event) => {
  const route = event.detail?.route;

  if (route === '/cart') {
    initCartView();
  } else {
    if (cartState.modalInstance && typeof cartState.modalInstance.dispose === 'function') {
      cartState.modalInstance.dispose();
    }
    cartState.currentEditIndex = null;
    cartState.modalInstance = null;
  }

  if (route === '/signin') {
    initSigninView();
  }
});




window.getLocation = function () {
  const out = document.getElementById('location');
  if (!navigator.geolocation) {
    out.textContent = 'Geolocation is not supported by your browser.';
    return;
  }
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = pos.coords.latitude.toFixed(4);
      const lon = pos.coords.longitude.toFixed(4);
      out.textContent = `Latitude: ${lat}, Longitude: ${lon}`;
      
    },
    (err) => {
      out.textContent = 'Location denied or unavailable.';
      console.error(err);
    }
  );
};


window.fetchWeatherData = async function (lat, lon) {
  try {
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;
    const res = await fetch(url);
    const data = await res.json();
    const w = data.current_weather;
    const box = document.getElementById('weather');
    box.innerHTML = `
      <div class="alert alert-info">
        Current weather: ${w.weathercode ?? '-'}<br>
        Temperature: ${w.temperature}°C<br>
        Wind: ${w.windspeed} km/h
      </div>`;
  } catch (e) {
    console.error(e);
    alert('Failed to fetch weather.');
  }
};
