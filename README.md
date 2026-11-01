# IT-2001-Web-Application

## Gym Club – Supplement Shop

Gym Club is a web application for a gym that sells fitness supplements (whey protein, creatine, omega 3). Users can register, log in, browse products, add them to a cart, place orders and view their profile and order history.

## Project Structure

```
backend/
  config/
    config.example.php   # settings template 
    config.php           # (excluded from Git)
  logs/                  # (excluded from Git)
  middleware/
  rest/
    dao/                 
    routes/
    services/
database/
  gym_web_app.sql        # schema + sample data
frontend/
  assets/       # images, video, static JSON data
  css/
  js/           # app.js (page logic), router.js (SPA routing)
  views/        # HTML templates loaded by the router
  index.html
```

## Pages (SPA)

Navigation is hash-based (`#/route`); views are loaded into `index.html` without full page reloads.

- `#/landing` – home page (gallery, FAQ, weather)
- `#/products` – product list
- `#/whey`, `#/creatine`, `#/omega3` – product detail pages
- `#/cart` – shopping cart
- `#/signup` – registration
- `#/signin` – login
- `#/profile` – user profile and order history

## Database (Draft ERD)

![ERD](ERD_Draft.png)

MySQL database `gym_web_app`.

- **users** 
- **products** 
- **categories** 
- **product_categories**
- **orders**
- **order_products** 

## DAO Layer

The DAO layer is in `backend/rest/dao` and uses PDO with prepared statements.

- **BaseDao** – one shared PDO connection and the generic methods `get_all()`, `get_by_id()`, `count_all()`, `add()`, `update()`, `delete()` and transactions. 
- **UserDao** – `get_by_email()`; `get_all()` and `get_by_id()` never return the password hash
- **ProductDao** – `get_by_category()`, `change_stock()`
- **CategoryDao** – `count_products_per_category()`
- **ProductCategoryDao**
- **OrderDao** – `get_by_user()`
- **OrderProductDao** – `get_by_order()`