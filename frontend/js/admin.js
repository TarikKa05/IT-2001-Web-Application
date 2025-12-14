const AdminApp = (function () {
  const state = {
    users: [],
    products: [],
    orders: [],
  };

  function ensureAdminAccess() {
    if (window.UserService && UserService.isAdmin()) return true;
    toastr.warning("Admin access only.");
    window.location.hash = "/landing";
    return false;
  }

  function setTableLoading(tableId, message = "Loading...") {
    const tbody = document.querySelector(`#${tableId} tbody`);
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="10" class="text-center py-3">${message}</td></tr>`;
    }
  }

  function renderUsers(users = []) {
    state.users = Array.isArray(users) ? users : [];
    const tbody = document.querySelector("#adminUsersTable tbody");
    if (!tbody) return;

    if (!state.users.length) {
      tbody.innerHTML =
        '<tr><td colspan="6" class="text-center py-3">No users found.</td></tr>';
      return;
    }

    tbody.innerHTML = state.users
      .map((user) => {
        const fullName = (user.name || "").trim();
        const [firstName, ...rest] = fullName.split(/\s+/);
        const lastName = rest.join(" ");
        const role = (user.role || "-").toString().toUpperCase();
        return `
          <tr>
            <td>${user.id ?? "-"}</td>
            <td>${firstName || "-"}</td>
            <td>${lastName || "-"}</td>
            <td>${user.email ?? "-"}</td>
            <td>${user.username ?? "-"}</td>
            <td>${role}</td>
            <td class="text-end">
              <button class="btn btn-sm admin-btn-danger" data-admin-action="delete-user" data-user-id="${user.id}">
                Delete
              </button>
            </td>
          </tr>
        `;
      })
      .join("");
  }

  function renderProducts(products = []) {
    state.products = Array.isArray(products) ? products : [];
    const tbody = document.querySelector("#adminProductsTable tbody");
    if (!tbody) return;

    if (!state.products.length) {
      tbody.innerHTML =
        '<tr><td colspan="8" class="text-center py-3">No products found.</td></tr>';
      return;
    }

    tbody.innerHTML = state.products
      .map((product) => {
        const price = Number(product.price);
        const formattedPrice = Number.isFinite(price) ? price.toFixed(2) : "-";
        const stock = product.stock_quantity ?? product.stock ?? "-";
        const categories =
          product.category_names || product.category_name || "Unassigned";
        const availability =
          product.is_available === 1 ||
          product.is_available === true ||
          product.is_available === "1"
            ? "Available"
            : "Unavailable";
        return `
          <tr>
            <td>${product.id ?? "-"}</td>
            <td>${product.name ?? "-"}</td>
            <td>${product.description ?? "-"}</td>
            <td>${formattedPrice}$</td>
            <td>${stock}</td>
            <td>${categories}</td>
            <td>${availability}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-primary" data-admin-action="edit-product" data-product-id="${product.id}">
                Edit
              </button>
              <button class="btn btn-sm btn-danger ms-2" data-admin-action="delete-product" data-product-id="${product.id}">
                Delete
              </button>
            </td>
          </tr>
        `;
      })
      .join("");
  }

  function renderOrders(orders = []) {
    state.orders = Array.isArray(orders) ? orders : [];
    const tbody = document.querySelector("#adminOrdersTable tbody");
    if (!tbody) return;

    if (!state.orders.length) {
      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center py-3">No orders found.</td></tr>';
      return;
    }

    tbody.innerHTML = state.orders
      .map((order) => {
        const total = Number(order.total_amount);
        const formattedTotal = Number.isFinite(total) ? total.toFixed(2) : "-";
        const placed =
          order.order_date || order.created_at || order.date || "-";
        return `
          <tr>
            <td>${order.id ?? "-"}</td>
            <td>${order.user_id ?? "-"}</td>
            <td>${formattedTotal}$</td>
            <td>${placed}</td>
          </tr>
        `;
      })
      .join("");
  }

  function loadUsers() {
    if (!ensureAdminAccess()) return;
    setTableLoading("adminUsersTable");
    AdminService.getUsers(
      (users) => renderUsers(users),
      () => setTableLoading("adminUsersTable", "Unable to load users."),
    );
  }

  function loadProducts() {
    if (!ensureAdminAccess()) return;
    setTableLoading("adminProductsTable");
    AdminService.getProducts(
      (products) => renderProducts(products),
      () => setTableLoading("adminProductsTable", "Unable to load products."),
    );
  }

  function loadOrders() {
    if (!ensureAdminAccess()) return;
    setTableLoading("adminOrdersTable");
    AdminService.getOrders(
      (orders) => renderOrders(orders),
      () => setTableLoading("adminOrdersTable", "Unable to load orders."),
    );
  }

  function handleUserDelete(userId) {
    if (!userId || !confirm("Delete this user?")) return;
    AdminService.deleteUser(
      userId,
      () => {
        toastr.success("User deleted.");
        loadUsers();
      },
      (err) => {
        const msg =
          err?.responseJSON?.error ||
          err?.responseJSON?.message ||
          err?.responseText ||
          "Failed to delete user.";
        toastr.error(msg);
      },
    );
  }

  function showProductEditor(productId) {
    const product =
      state.products.find((p) => Number(p.id) === Number(productId)) || null;
    const form = document.getElementById("productEditForm");
    const editor = document.getElementById("productEditorCard");
    if (!form || !editor || !product) return;

    form.dataset.productId = product.id;
    form.productName.value = product.name || "";
    form.productDescription.value = product.description || "";
    form.productPrice.value = product.price ?? "";
    form.productStock.value = product.stock_quantity ?? product.stock ?? "";
    form.productAvailable.checked =
      product.is_available === 1 ||
      product.is_available === true ||
      product.is_available === "1";

    editor.classList.remove("d-none");
    editor.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  function resetProductEditor() {
    const form = document.getElementById("productEditForm");
    const editor = document.getElementById("productEditorCard");
    if (form) {
      form.reset();
      delete form.dataset.productId;
    }
    if (editor) {
      editor.classList.add("d-none");
    }
  }

  function handleProductDelete(productId) {
    if (!productId || !confirm("Delete this product?")) return;
    AdminService.deleteProduct(
      productId,
      () => {
        toastr.success("Product deleted.");
        loadProducts();
        resetProductEditor();
      },
      (err) => {
        const msg =
          err?.responseJSON?.error ||
          err?.responseJSON?.message ||
          err?.responseText ||
          "Failed to delete product.";
        toastr.error(msg);
      },
    );
  }

  function handleProductFormSubmit(event) {
    if (!event.target || event.target.id !== "productEditForm") return;
    event.preventDefault();

    const form = event.target;
    const productId = form.dataset.productId;
    if (!productId) {
      toastr.error("Select a product to edit.");
      return;
    }

    const payload = {
      name: form.productName.value.trim(),
      description: form.productDescription.value.trim(),
      price: parseFloat(form.productPrice.value),
      stock_quantity: parseInt(form.productStock.value, 10),
      is_available: form.productAvailable.checked ? 1 : 0,
    };

    if (!payload.name || !payload.description) {
      toastr.error("Name and description are required.");
      return;
    }

    if (!Number.isFinite(payload.price) || payload.price < 0) {
      toastr.error("Price must be a non-negative number.");
      return;
    }

    if (!Number.isInteger(payload.stock_quantity) || payload.stock_quantity < 0) {
      toastr.error("Stock must be a non-negative integer.");
      return;
    }

    AdminService.updateProduct(
      productId,
      payload,
      () => {
        toastr.success("Product updated.");
        loadProducts();
        resetProductEditor();
      },
      (err) => {
        const msg =
          err?.responseJSON?.error ||
          err?.responseJSON?.message ||
          err?.responseText ||
          "Failed to update product.";
        toastr.error(msg);
      },
    );
  }

  function bindAdminEvents() {
    if (document.body.dataset.adminBound === "true") return;
    document.body.dataset.adminBound = "true";

    document.addEventListener("click", (event) => {
      const action = event.target.closest("[data-admin-action]");
      if (!action) return;

      const { adminAction } = action.dataset;
      if (adminAction === "delete-user") {
        handleUserDelete(action.dataset.userId);
      } else if (adminAction === "edit-product") {
        showProductEditor(action.dataset.productId);
      } else if (adminAction === "delete-product") {
        handleProductDelete(action.dataset.productId);
      }
    });

    document.addEventListener("submit", handleProductFormSubmit);
  }

  document.addEventListener("view:loaded", (event) => {
    const route = event.detail?.route;
    if (route === "/admin/home") {
      if (!ensureAdminAccess()) return;
      bindAdminEvents();
      loadUsers();
    }

    if (route === "/admin/products") {
      if (!ensureAdminAccess()) return;
      bindAdminEvents();
      resetProductEditor();
      loadProducts();
    }

    if (route === "/admin/orders") {
      if (!ensureAdminAccess()) return;
      bindAdminEvents();
      loadOrders();
    }

    if (route === "/admin/register") {
      if (!ensureAdminAccess()) return;
      bindAdminEvents();
      if (window.UserService && typeof UserService.bindAdminRegistrationForm === "function") {
        UserService.bindAdminRegistrationForm();
      }
    }

    if (route === "/admin/register-user") {
      if (!ensureAdminAccess()) return;
      bindAdminEvents();
      if (window.UserService && typeof UserService.bindAdminUserRegistrationForm === "function") {
        UserService.bindAdminUserRegistrationForm();
      }
    }
  });

  return {
    loadUsers,
    loadProducts,
    loadOrders,
  };
})();
