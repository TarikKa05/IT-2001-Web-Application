let RestClient = {
   get: function (url, callback, error_callback) {
     RestClient.request(url, "GET", null, callback, error_callback);
   },
   request: function (url, method, data, callback, error_callback, options = {}) {
     const ajaxOptions = {
       url: Constants.PROJECT_BASE_URL + url,
       type: method,
       beforeSend: function (xhr) {
         xhr.setRequestHeader(
           "Authentication",
           localStorage.getItem("user_token")
         );
       },
       data: data,
       success: function (response) {
         if (callback) callback(response);
       },
       error: function (jqXHR) {
         if (error_callback) {
           error_callback(jqXHR);
         } else {
           const msg =
             jqXHR?.responseJSON?.error ||
             jqXHR?.responseJSON?.message ||
             jqXHR?.responseText ||
             "Request failed";
           toastr.error(msg);
         }
       },
     };

     if (options.contentType) ajaxOptions.contentType = options.contentType;
     if (options.processData !== undefined) ajaxOptions.processData = options.processData;
     if (options.dataType) ajaxOptions.dataType = options.dataType;

     $.ajax(ajaxOptions);
   },
   post: function (url, data, callback, error_callback, options = {}) {
     RestClient.request(url, "POST", data, callback, error_callback, options);
   },
   delete: function (url, data, callback, error_callback, options = {}) {
     RestClient.request(url, "DELETE", data, callback, error_callback, options);
   },
   patch: function (url, data, callback, error_callback, options = {}) {
     RestClient.request(url, "PATCH", data, callback, error_callback, options);
   },
   put: function (url, data, callback, error_callback, options = {}) {
     RestClient.request(url, "PUT", data, callback, error_callback, options);
   },
 };
