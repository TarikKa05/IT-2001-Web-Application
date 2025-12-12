let CategoryService = {
  getAll: function (callback, error_callback) {
    RestClient.get("categories/", callback, error_callback);
  },

  getById: function (id, callback, error_callback) {
    RestClient.get(`category/${id}`, callback, error_callback);
  },
};
