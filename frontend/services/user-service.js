var UserService = {
 getCurrentUser: function () {
   const parsed = Utils.parseJwt(localStorage.getItem("user_token"));
   return parsed?.user || null;
 },
 getRole: function () {
   const cached = localStorage.getItem("user_role");
    if (cached) return cached.toLowerCase();
   const user = UserService.getCurrentUser();
    if (user?.role) return user.role.toString().toLowerCase();
    return null;
 },
 isAdmin: function () {
    return (UserService.getRole() || "").toLowerCase() === Constants.ADMIN_ROLE;
 },
 isUser: function () {
    return (UserService.getRole() || "").toLowerCase() === Constants.USER_ROLE;
 },
 bindSignupForm: function () {
   const token = localStorage.getItem("user_token");
   if (token) {
     const defaultHash = UserService.isAdmin() ? "/admin/home" : "/landing";
     window.location.replace(`index.html#${defaultHash}`);
     return;
   }

   const form = document.getElementById("registrationForm");
   if (!form || form.dataset.signupBound === "true") return;
   form.dataset.signupBound = "true";

   const submitBtn = form.querySelector('button[type="submit"]');

   form.addEventListener("submit", function (e) {
     e.preventDefault();
     if (!form.checkValidity()) {
       form.classList.add("was-validated");
       return;
     }

     const formData = new FormData(form);
     const password = formData.get("password") || "";
     const repeatPassword = formData.get("repeatPassword") || "";
     if (password !== repeatPassword) {
       toastr.error("Passwords must match.");
       return;
     }

     const entity = Object.fromEntries(formData.entries());
     delete entity.repeatPassword; // not needed by API
     entity.role = Constants.USER_ROLE; // enforce user role on registration

     if (submitBtn) submitBtn.disabled = true;
    UserService.register(entity, {
      autoLogin: true,
      onComplete: () => {
        if (submitBtn) submitBtn.disabled = false;
      },
    });
  });
},

  bindAdminRegistrationForm: function () {
    const form = document.getElementById("adminRegistrationForm");
    if (!form || form.dataset.adminSignupBound === "true") return;
    form.dataset.adminSignupBound = "true";

   const submitBtn = form.querySelector('button[type="submit"]');

   form.addEventListener("submit", function (e) {
     e.preventDefault();
     if (!form.checkValidity()) {
       form.classList.add("was-validated");
       return;
     }

    const formData = new FormData(form);
    const password = formData.get("password") || "";
    const repeatPassword = formData.get("repeatPassword") || "";
    if (password !== repeatPassword) {
      toastr.error("Passwords must match.");
      return;
    }

    const entity = Object.fromEntries(formData.entries());
    delete entity.repeatPassword;
    const firstName = (entity.first_name || "").trim();
    const lastName = (entity.last_name || "").trim();
    entity.name = [firstName, lastName].filter(Boolean).join(" ").trim();
    delete entity.first_name;
    delete entity.last_name;
    entity.role = Constants.ADMIN_ROLE;

     if (submitBtn) submitBtn.disabled = true;
     UserService.register(entity, {
       autoLogin: false,
       onComplete: () => {
         if (submitBtn) submitBtn.disabled = false;
         window.location.hash = "/admin/home";
         toastr.success("Admin registered successfully.");
       },
     });
   });
 },

 bindSigninForm: function () {
   // If a user is already authenticated, send them to the landing page.
   const token = localStorage.getItem("user_token");
   if (token) {
     const defaultHash = UserService.isAdmin() ? "/admin/home" : "/landing";
     window.location.replace(`index.html#${defaultHash}`);
     return;
   }

   const form = document.getElementById("signinForm");
   if (!form || form.dataset.signinBound === "true") return;
   form.dataset.signinBound = "true";

   const submitBtn = form.querySelector('button[type="submit"]');

   form.addEventListener("submit", function (e) {
     e.preventDefault();
     if (!form.checkValidity()) {
       form.classList.add("was-validated");
       return;
     }

     const entity = Object.fromEntries(new FormData(form).entries());
     if (submitBtn) submitBtn.disabled = true;
     UserService.login(entity, () => {
       if (submitBtn) submitBtn.disabled = false;
     });
   });
 },

 register: function (entity, options = {}) {
   $.ajax({
     url: Constants.PROJECT_BASE_URL + "auth/register",
     type: "POST",
     data: JSON.stringify(entity),
     contentType: "application/json",
     dataType: "json",
     success: function () {
       toastr.success("Registration successful.");
       if (options.autoLogin) {
         UserService.login(
           { email: entity.email, password: entity.password },
           options.onComplete,
         );
       } else {
         window.location.hash = "/signin";
       }
     },
     error: function (xhr) {
       const message =
         xhr.responseJSON?.error ||
         xhr.responseJSON?.message ||
         xhr.responseText ||
         "Registration failed";
       toastr.error(message);
     },
     complete: function () {
       if (!options.autoLogin && options.onComplete) {
         options.onComplete();
       }
      },
    });
  },

  bindAdminUserRegistrationForm: function () {
    const form = document.getElementById("adminUserRegistrationForm");
    if (!form || form.dataset.adminUserSignupBound === "true") return;
    form.dataset.adminUserSignupBound = "true";

    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
      }

      const formData = new FormData(form);
      const password = formData.get("password") || "";
      const repeatPassword = formData.get("repeatPassword") || "";
      if (password !== repeatPassword) {
        toastr.error("Passwords must match.");
        return;
      }

      const entity = Object.fromEntries(formData.entries());
      delete entity.repeatPassword;
      const firstName = (entity.first_name || "").trim();
      const lastName = (entity.last_name || "").trim();
      entity.name = [firstName, lastName].filter(Boolean).join(" ").trim();
      delete entity.first_name;
      delete entity.last_name;
      entity.role = Constants.USER_ROLE;

      if (submitBtn) submitBtn.disabled = true;
      UserService.register(entity, {
        autoLogin: false,
        onComplete: () => {
          if (submitBtn) submitBtn.disabled = false;
          window.location.hash = "/admin/home";
          toastr.success("User registered successfully.");
        },
      });
    });
  },

 login: function (entity, onComplete) {
   $.ajax({
     url: Constants.PROJECT_BASE_URL + "auth/login",
     type: "POST",
     data: JSON.stringify(entity),
     contentType: "application/json",
     dataType: "json",
     success: function (result) {
       localStorage.setItem("user_token", result.data.token);
       const role = (result?.data?.role ||
         UserService.getCurrentUser()?.role ||
         Constants.USER_ROLE).toString().toLowerCase();
        localStorage.setItem("user_role", role);
       document.dispatchEvent(new CustomEvent("auth:changed", { detail: { role } }));
       const targetHash =
         role === Constants.ADMIN_ROLE ? "/admin/home" : "/landing";
       window.location.replace(`index.html#${targetHash}`);
     },
     error: function (xhr) {
       const message =
         xhr.responseJSON?.error ||
         xhr.responseJSON?.message ||
         xhr.responseText ||
         "Login failed";
       toastr.error(message);
     },
     complete: function () {
       if (onComplete) onComplete();
     },
   });
 },

 logout: function () {
   localStorage.removeItem("user_token");
   localStorage.removeItem("user_role");
   document.dispatchEvent(new CustomEvent("auth:changed", { detail: { role: null } }));
   window.location.hash = "/landing";
 },

 logoutWithCheck: function () {
   const token = localStorage.getItem("user_token");
   if (!token) {
     alert("You are not signed in. Please sign in or register first.");
     return;
   }
   UserService.logout();
 },

 isAuthenticated: function () {
   const token = localStorage.getItem("user_token");
   const parsed = Utils.parseJwt(token);
   return Boolean(parsed && parsed.user);
 },

 requireAuthOrRedirect: function (redirectHash = "/signup") {
   if (!UserService.isAuthenticated()) {
     alert("Please sign in or register to continue.");
     window.location.hash = redirectHash;
     return false;
   }
   return true;
 },

 getAll: function (callback, error_callback) {
   RestClient.get("users/", callback, error_callback);
 },

 deleteUser: function (id, callback, error_callback) {
   RestClient.delete(`user/${id}`, null, callback, error_callback);
 },
};
