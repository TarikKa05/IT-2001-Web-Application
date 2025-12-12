let OrderService = {
  createOrder: function (orderData, callback, error_callback) {
    RestClient.post(
      "order",
      JSON.stringify(orderData),
      callback,
      error_callback,
      { contentType: "application/json", dataType: "json", processData: false }
    );
  },

  getOrder: function (id, callback, error_callback) {
    RestClient.get(`order/${id}`, callback, error_callback);
  },
};
