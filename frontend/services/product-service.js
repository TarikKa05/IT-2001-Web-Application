let ProductService = {
  getAll: function (callback, error_callback) {
    RestClient.get("products/", callback, error_callback);
  },

  getById: function (id, callback, error_callback) {
    RestClient.get(`product/${id}`, callback, error_callback);
  },

  getByCategoryName: function (categoryName, callback, error_callback) {
    const query = $.param({ category_name: categoryName });
    RestClient.get(`productsbycategoryname/?${query}`, callback, error_callback);
  },

  update: function (id, payload, callback, error_callback) {
    RestClient.put(
      `product/${id}`,
      JSON.stringify(payload),
      callback,
      error_callback,
      { contentType: "application/json", dataType: "json", processData: false }
    );
  },

  delete: function (id, callback, error_callback) {
    RestClient.delete(`product/${id}`, null, callback, error_callback);
  },
};
