const AdminService = {
  getUsers(callback, error_callback) {
    RestClient.get("users/", callback, error_callback);
  },

  deleteUser(id, callback, error_callback) {
    RestClient.delete(`user/${id}`, null, callback, error_callback);
  },

  getProducts(callback, error_callback) {
    RestClient.get("products/", callback, error_callback);
  },

  updateProduct(id, payload, callback, error_callback) {
    RestClient.put(
      `product/${id}`,
      JSON.stringify(payload),
      callback,
      error_callback,
      { contentType: "application/json", dataType: "json", processData: false },
    );
  },

  deleteProduct(id, callback, error_callback) {
    RestClient.delete(`product/${id}`, null, callback, error_callback);
  },

  getOrders(callback, error_callback) {
    RestClient.get("orders/", callback, error_callback);
  },
};
