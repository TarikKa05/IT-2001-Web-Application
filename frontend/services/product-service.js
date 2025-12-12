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
};
