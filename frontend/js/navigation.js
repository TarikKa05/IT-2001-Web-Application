const Navigation = (function () {
  const USER_LINKS = [
    { label: "Home", hash: "/landing" },
    { label: "Products", hash: "/products" },
    { label: "Cart", hash: "/cart" },
  ];

  const ADMIN_LINKS = [
    { label: "Home", hash: "/admin/home" },
    { label: "Products", hash: "/admin/products" },
    { label: "Orders", hash: "/admin/orders" },
  ];

  function renderNav() {
    const nav = document.getElementById("primaryNav");
    if (!nav) return;

    const isAdmin = window.UserService && UserService.isAdmin();
    const links = isAdmin ? ADMIN_LINKS : USER_LINKS;

    nav.innerHTML = links
      .map(
        (link) => `
        <li class="nav-item">
          <a class="nav-link" href="#${link.hash}">${link.label}</a>
        </li>
      `,
      )
      .join("");

    syncAuthButtons();
    if (typeof setActiveNav === "function") {
      const currentRoute = window.location.hash.replace("#", "");
      setActiveNav(currentRoute || links[0]?.hash || "/landing");
    }
  }

  function syncAuthButtons() {
    const isAuth = window.UserService && UserService.isAuthenticated();
    const signinBtn = document.getElementById("signinBtn");
    const signupBtn = document.getElementById("signupBtn");
    const logoutBtn = document.getElementById("logoutBtn");

    if (isAuth) {
      signinBtn?.classList.add("d-none");
      signupBtn?.classList.add("d-none");
      logoutBtn?.classList.remove("d-none");
    } else {
      signinBtn?.classList.remove("d-none");
      signupBtn?.classList.remove("d-none");
      logoutBtn?.classList.add("d-none");
    }
  }

  document.addEventListener("DOMContentLoaded", renderNav);
  window.addEventListener("hashchange", renderNav);
  document.addEventListener("auth:changed", renderNav);

  return { renderNav };
})();
