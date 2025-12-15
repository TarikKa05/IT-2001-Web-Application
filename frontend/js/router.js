const routes = {
  "/landing": "views/landing.html",
  "/products": "views/products.html",
  "/creatine": "views/creatine.html",
  "/omega3": "views/omega3.html",
  "/whey": "views/whey.html",
  "/cart": "views/cart.html",
  "/signup": "views/signup.html",
  "/signin": "views/signin.html",
  "/admin/home": "views/admin-home.html",
  "/admin/products": "views/admin-products.html",
  "/admin/orders": "views/admin-orders.html",
  "/admin/register": "views/admin-register.html",
  "/admin/register-user": "views/admin-register-user.html",
};

function getDefaultRoute() {
  if (window.UserService && typeof UserService.isAdmin === "function" && UserService.isAdmin()) {
    return "/admin/home";
  }
  return "/landing";
}

async function loadView(route) {
  const path = routes[route] || routes[getDefaultRoute()];
  try {
    const response = await fetch(path, { cache: "no-cache" });
    if (!response.ok) {
      throw new Error(`${response.status} ${response.statusText}`);
    }

    const html = await response.text();
    const app = document.getElementById("app");
    app.innerHTML = html;
    setActiveNav(route);
    window.scrollTo(0, 0);

    document.dispatchEvent(
      new CustomEvent("view:loaded", {
        detail: { route, path },
      }),
    );
  } catch (error) {
    document.getElementById("app").innerHTML = `
      <div class="alert alert-danger mt-3">
        Unable to load view <code>${path}</code><br>
        <small>${error.message}</small>
      </div>`;
  }
}

function handleRoute() {
  const hash = window.location.hash.replace("#", "") || getDefaultRoute();
  const isAdminRoute = hash.startsWith("/admin");
  if (isAdminRoute && !(window.UserService && UserService.isAdmin())) {
    toastr.warning("Admin access only.");
    const fallback = getDefaultRoute();
    window.location.hash = fallback;
    return;
  }

  const routeToLoad = routes[hash] ? hash : getDefaultRoute();
  loadView(routeToLoad);
}

function setActiveNav(route) {
  document.querySelectorAll(".nav-link").forEach((anchor) => {
    anchor.classList.toggle(
      "active",
      anchor.getAttribute("href") === `#${route}`,
    );
  });
}

window.addEventListener("hashchange", handleRoute);
window.addEventListener("DOMContentLoaded", handleRoute);
