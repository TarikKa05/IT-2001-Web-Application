// frontend/js/router.js

// 1) Definiši rute: hash -> fajl view-a (relativno na /frontend/)
const routes = {
  '/landing':  'views/landing.html',
  '/products': 'views/products.html',
  '/creatine': 'views/creatine.html',
  '/omega3':   'views/omega3.html',
  '/whey':     'views/whey.html',
  '/cart':     'views/cart.html',
  '/signup':   'views/signup.html',
  '/signin':   'views/signin.html',
  '/login':    'views/login.html'
};

// 2) Učitavanje view-a u #app
async function loadView(route) {
  const path = routes[route] || routes['/landing'];
  try {
    // cache: 'no-cache' da Live Server ne zadrži stari HTML
    const res = await fetch(path, { cache: 'no-cache' });
    if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
    const html = await res.text();
    const app = document.getElementById('app');
    app.innerHTML = html;
    setActiveNav(route);
    window.scrollTo(0, 0);
    document.dispatchEvent(new CustomEvent('view:loaded', {
      detail: { route, path }
    }));
  } catch (err) {
    document.getElementById('app').innerHTML = `
      <div class="alert alert-danger mt-3">
        Unable to load view <code>${path}</code><br>
        <small>${err.message}</small>
      </div>`;
  }
}

// 3) Router handler
function handleRoute() {
  const hash = location.hash.replace('#', '');
  loadView(hash || '/landing');
}

// 4) Aktivna klasa na meniju (lijepo izgleda)
function setActiveNav(route) {
  document.querySelectorAll('.nav-link').forEach(a => {
    a.classList.toggle('active', a.getAttribute('href') === `#${route}`);
  });
}

// 5) Event listeneri
window.addEventListener('hashchange', handleRoute);
window.addEventListener('DOMContentLoaded', handleRoute);
