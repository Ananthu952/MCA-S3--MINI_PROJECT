<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>EcoCycle Authentication</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/login.css" />
  <style>
    .toggle-link { cursor: pointer; color: #2e7d32; text-decoration: underline; }
    .error-msg { color: red; font-size: .9rem; margin-top: .25rem; }
  </style>
</head>
<body>
  <!-- Shared Left Panel -->
  <div class="left-panel">
    <div class="logo-container">
      <img src="images/logo.svg" alt="EcoCycle Logo" class="logo" />
    </div>
  </div>

  <div class="split-screen">
    <!-- Left Panel (Illustration) -->
    <div class="left-panel">
      <img src="images/login.png" alt="EcoCycle Illustration" />
    </div>

    <!-- Right Panel (Forms) -->
    <div class="right-panel" style="align-items: flex-start; padding-top: 125px;">
      <!-- LOGIN FORM -->
      <form id="loginForm" class="login-form" style="display: block;">
        <h2 class="login-title"> Login</h2>
        <div class="mb-3">
          <label for="loginEmail" class="form-label">Email or Username</label>
          <input type="text" class="form-control" id="loginEmail" name="email" required placeholder="Enter your email or username" />
        </div>

        <div class="mb-1">
          <label for="loginPassword" class="form-label">Password</label>
          <input type="password" class="form-control" id="loginPassword" name="password" required placeholder="Enter your password" />
        </div>
        <br />

        <div id="loginMessage" class="text-danger mb-2"></div>

        <div class="forgot-password">
          <p><a href="#" id="forgot-link">Forgot Password?</a></p>
        </div>

        <button type="submit" class="btn btn-login">Login</button>

        <div class="form-text text-center mt-3">
          Don’t have an account? <span class="toggle-link" id="showCreateForm">Register here</span>
        </div>
      </form>

      <!-- SIGNUP FORM (actual account creation after Gmail verify) -->
      <form id="signupForm" class="signup-form login-form" style="display: none;">
        <h2 class="signup-title">Create Account</h2>

        <div class="mb-3">
          <label for="signupName" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="signupName" name="name" required placeholder="Enter your full name" />
          <div id="name-error" class="error-msg"></div>
        </div>

        <div class="mb-3">
          <label for="signupEmail" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="signupEmail" name="email" required placeholder="Enter your email" />
          <div id="email-error" class="error-msg"></div>
        </div>

        <div class="mb-3">
          <label for="signupPhone" class="form-label">Phone Number</label>
          <input type="tel" class="form-control" id="signupPhone" name="phone" required placeholder="Enter your phone number" />
          <div id="phone-error" class="error-msg"></div>
        </div>

        <div class="mb-3">
          <label for="signupPassword" class="form-label">Password</label>
          <input type="password" class="form-control" id="signupPassword" name="password" required placeholder="Enter password" />
          <div id="password-error" class="error-msg"></div>
        </div>

        <div class="mb-3">
          <label for="signupConfirmPassword" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="signupConfirmPassword" name="confirm_password" required placeholder="Re-enter password" />
          <div id="confirm-error" class="error-msg"></div>
        </div>

        <div class="mb-3">
          <label for="signupAddress" class="form-label">Address</label>
          <textarea class="form-control" id="signupAddress" name="address" required placeholder="Enter your full address" rows="3"></textarea>
          <div id="address-error" class="error-msg"></div>
        </div>

        <div class="mb-3">
          <label for="signupPincode" class="form-label">Pincode</label>
          <input type="text" class="form-control" id="signupPincode" name="pincode" required placeholder="Enter your pincode" />
          <div id="pincode-error" class="error-msg"></div>
        </div>

        <div id="signup-message" class="text-center mb-3">
          <div class="alert alert-danger" style="display: none;"></div>
        </div>

        <button type="submit" class="btn btn-signup">Sign Up</button>

        <div class="form-text text-center mt-3">
          Already have an account? <span class="toggle-link" id="showLogin">Login here</span>
        </div>
      </form>

      <!-- Forgot Password Form -->
      <form id="forgotForm" class="forgot-form login-form" method="POST" novalidate style="display:none;margin:0;">
        <h2 class="forgot-title">Forgot Password</h2>

        <div class="mb-3">
          <label for="forgot_email" class="form-label">Email</label>
          <input type="email" id="forgot_email" name="email" class="form-control"
            placeholder="Enter your registered email" required autocomplete="email" aria-describedby="forgotEmailHelp">
          <div id="forgotEmailHelp" class="form-text">Enter the email linked to your account.</div>
        </div>

        <div id="forget-message" class="text-center mb-3"></div>
        <button type="submit" class="btn btn-login" id="sendOtpBtn">Send Reset Link</button>

        <!-- OTP Field -->
        <div class="mb-3" id="otp-section" style="display:none;">
          <label for="otp_input" class="form-label">Enter OTP</label>
          <input type="text" id="otp_input" name="otp" class="form-control"
            placeholder="Enter the 6-digit OTP" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code">
          <div id="otpHelp" class="form-text">Check your email for the OTP.</div>
          <div id="otp-error" class="text-danger small mt-1"></div>
        </div>

        <div class="form-text text-center mt-3">
          Remembered your password? <span class="toggle-link back-to-login">Login here</span>
        </div>
      </form>

      <!-- Reset Password Form -->
      <form id="resetPasswordForm" class="login-form" style="display:none; margin:0;">
        <h2 class="login-title">Reset Password</h2>

        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
        </div>

        <div id="reset-error" class="text-danger small mb-3"></div>

        <button type="submit" class="btn btn-login">Reset Password</button>

        <div class="form-text text-center mt-3">
            Remembered your password? <span class="toggle-link back-to-login">Login here</span>
        </div>
      </form>
          <!-- First-Time Collector Password Form -->
    <form id="firstTimeCollectorForm" class="login-form" style="display:none; margin:0;">
      <h2 class="login-title">Set Your Password</h2>

      <div class="mb-3">
        <label for="ftc_new_password" class="form-label">New Password</label>
        <input type="password" id="ftc_new_password" name="new_password" class="form-control" placeholder="Enter new password" required>
      </div>

      <div class="mb-3">
        <label for="ftc_confirm_password" class="form-label">Confirm Password</label>
        <input type="password" id="ftc_confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
      </div>

      <div id="ftc-reset-error" class="text-danger small mb-3"></div>

      <button type="submit" class="btn btn-login">Set Password</button>

      <div class="form-text text-center mt-3">
          Remembered your password? <span class="toggle-link back-to-login">Login here</span>
      </div>
    </form>
      <!-- CREATE ACCOUNT FORM (Step 1: Verify Gmail) -->
      <form id="createacForm" class="login-form" style="display:none;">
        <h2 class="login-title">Verify Gmail</h2>

        <div class="mb-3">
          <label for="create_email" class="form-label">Enter Gmail</label>
          <input type="email" id="create_email" name="email" class="form-control"
                placeholder="Enter your Gmail" required>
          <div id="create-email-error" class="text-danger small mt-1"></div>
        </div>

        <div id="create-message" class="text-center mb-3"></div>

        <button type="submit" class="btn btn-login" id="sendOtpCreateBtn">Send OTP</button>

        <!-- OTP Section -->
        <div class="mb-3" id="create-otp-section" style="display:none;">
          <label for="create_otp_input" class="form-label">Enter OTP</label>
          <input type="text" id="create_otp_input" name="otp" class="form-control"
                placeholder="Enter the 6-digit OTP" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code">
          <div id="create-otpHelp" class="form-text">Check your Gmail for the OTP.</div>
          <div id="create-otp-error" class="text-danger small mt-1"></div>
        </div>

        <div class="form-text text-center mt-3">
          Already have an account? <span class="toggle-link" id="gotoLoginFromCreate">Login here</span>
        </div>
      </form>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");
    const forgotForm = document.getElementById("forgotForm");
    const createacForm = document.getElementById("createacForm");
    const resetFormGlobal = document.getElementById("resetPasswordForm");
    const firstTimeCollectorForm = document.getElementById("firstTimeCollectorForm");

    // Helper safe getter
    function $(id) { return document.getElementById(id); }
    function show(el) { if (!el) return; el.style.display = "block"; }
    function hide(el) { if (!el) return; el.style.display = "none"; }
    function safeAddEvent(el, event, handler) { if (!el) return; el.addEventListener(event, handler); }

    // Helpers to show and clear errors
    function showError(input, message) {
      if (!input) return;
      const errorDiv = input.nextElementSibling;
      if (errorDiv) { errorDiv.textContent = message; errorDiv.style.color = "red"; }
      input.style.border = "1px solid red";
    }
    function clearError(input) {
      if (!input) return;
      const errorDiv = input.nextElementSibling;
      if (errorDiv) { errorDiv.textContent = ""; }
      input.style.border = "";
    }

    // Clear all errors in a form
    function clearAllErrors(form) {
      if (!form) return;
      const inputs = form.querySelectorAll("input, textarea");
      inputs.forEach((input) => clearError(input));
    }

    // Single password validator used everywhere
    function validatePassword(password) {
      const minLength = 6;
      const hasLower = /[a-z]/.test(password);
      const hasUpper = /[A-Z]/.test(password);
      const hasNumber = /\d/.test(password);
      const hasSpecial = /[!@#$%^&*(),.?\":{}|<>]/.test(password);
      return (
        password.length >= minLength &&
        hasLower &&
        hasUpper &&
        hasNumber &&
        hasSpecial
      );
    }

    // Toggle helpers
    function showOnly(formToShow) {
      [loginForm, signupForm, forgotForm, createacForm, resetFormGlobal,firstTimeCollectorForm].forEach(f => {
        if (!f) return;
        f.style.display = (f === formToShow) ? "block" : "none";
      });
      if (formToShow) clearAllErrors(formToShow);
    }

    // Safe listeners for toggles
    safeAddEvent($("showCreateForm"), "click", () => showOnly(createacForm));
    safeAddEvent($("showLogin"), "click", () => showOnly(loginForm));
    safeAddEvent($("showLogin"), "keydown", (e) => { if (e.key === "Enter") showOnly(loginForm); });
    safeAddEvent($("forgot-link"), "click", (e) => {
      if (e) e.preventDefault();
      showOnly(forgotForm);
    });
    safeAddEvent($("gotoLoginFromCreate"), "click", () => showOnly(loginForm));

    // any element with class back-to-login should move to login
    document.querySelectorAll(".back-to-login").forEach(el => {
      el.addEventListener("click", () => showOnly(loginForm));
    });

    // LOGIN FORM
safeAddEvent(loginForm, "submit", function (e) {
  e.preventDefault();
  clearAllErrors(loginForm);

  let valid = true;
  const emailInput = loginForm.querySelector("#loginEmail");
  const passwordInput = loginForm.querySelector("#loginPassword");
  const loginMessage = document.getElementById("loginMessage");

  if (loginMessage) loginMessage.textContent = "";

  if (!emailInput.value.trim()) {
    showError(emailInput, "Email or username is required.");
    valid = false;
  }

  if (!passwordInput.value) {
    showError(passwordInput, "Password is required.");
    valid = false;
  }

  if (!valid) return;

  const formData = new FormData(loginForm);
  fetch("login_handler.php", { method: "POST", body: formData })
    .then((res) => res.text())
    .then((data) => {
      const response = data.trim();

      if (response.startsWith("success")) {
        let role = response.split(":")[1] || "user";

        if (loginMessage) {
          loginMessage.classList.remove("text-danger");
          loginMessage.style.color = "green";
          loginMessage.textContent = "Login successful! Redirecting...";
        }

        setTimeout(() => {
          switch (role) {
            case "admin":
              window.location.href = "admin/admin_home.php";
              break;
            case "collector":
              window.location.href = "collector/collector_home.php?page=dashboard";
              break;
            case "user":
            default:
              window.location.href = "user/homes.php?page=dashboard";
              break;
          }
        }, 700);

      } else if (response === "first-time") {
        // 🔹 Handle first-time collector login
        if (loginMessage) {
          loginMessage.style.color = "orange";
          loginMessage.textContent = "First-time login detected. Please set a new password.";
        }
        showOnly(firstTimeCollectorForm); // show the set_password form

      } else {
        if (loginMessage) {
          loginMessage.style.color = "red";
          loginMessage.textContent = response;
        }
      }
    })
    .catch(() => alert("An error occurred during login."));
});

// FIRST-TIME COLLECTOR PASSWORD FORM
safeAddEvent(firstTimeCollectorForm, "submit", function (e) {
  e.preventDefault();
  clearAllErrors(firstTimeCollectorForm);

  const newPass = $("ftc_new_password").value.trim();
  const confirmPass = $("ftc_confirm_password").value.trim();
  const errorDiv = $("ftc-reset-error");

  if (!validatePassword(newPass)) {
    if (errorDiv) errorDiv.textContent = "Password must be at least 6 chars with upper, lower, number, and special char.";
    return;
  }

  if (newPass !== confirmPass) {
    if (errorDiv) errorDiv.textContent = "Passwords do not match.";
    return;
  }

  const formData = new FormData();
  formData.append("new_password", newPass);
  formData.append("confirm_password", confirmPass);
  formData.append("action", "first_time_set");

  fetch("first_time_collector_handler.php", { method: "POST", body: formData })
    .then(res => res.text())
    .then(data => {
      if (data.trim() === "PASSWORD_UPDATED") {
        alert("Password set successfully! You can now log in.");
        window.location.href = "auth.php"; // back to login
      } else {
        if (errorDiv) errorDiv.textContent = data;
      }
    })
    .catch(() => {
      if (errorDiv) errorDiv.textContent = "Error updating password.";
    });
});

    // Create account step: verify gmail and send OTP
    safeAddEvent(createacForm, "submit", function (e) {
      e.preventDefault();
      const emailInput = $("create_email");
      const errorDiv = $("create-email-error");
      const createMessage = $("create-message");

      if (errorDiv) errorDiv.textContent = "";
      if (createMessage) createMessage.textContent = "";

      const emailVal = emailInput.value.trim();

      if (!/^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(emailVal)) {
        if (errorDiv) errorDiv.textContent = "Please enter a valid Gmail address.";
        return;
      }

      const formData = new FormData();
      formData.append("email", emailVal);
      formData.append("action", "send_otp_create");

      fetch("create_account_handler.php", { method: "POST", body: formData })
        .then((res) => res.text())
        .then((data) => {
          if (data.trim() === "OTP_SENT") {
            show($("create-otp-section"));
            hide($("sendOtpCreateBtn"));
            if (createMessage) createMessage.textContent = "OTP sent to your Gmail.";
          } else {
            if (errorDiv) errorDiv.textContent = data;
          }
        })
        .catch(() => {
          if (errorDiv) errorDiv.textContent = "Error sending OTP.";
        });
    });

    // Verify create-account OTP (auto-verify on 6 digits)
    safeAddEvent($("create_otp_input"), "input", function () {
      const otpError = $("create-otp-error");
      const emailVal = $("create_email") ? $("create_email").value.trim() : "";

      if (!/^\d{0,6}$/.test(this.value)) {
        if (otpError) otpError.textContent = "Only numbers allowed.";
        return;
      } else if (otpError) {
        otpError.textContent = "";
      }

      if (this.value.length === 6) {
        const formData = new FormData();
        formData.append("otp", this.value.trim());
        formData.append("email", emailVal);
        formData.append("action", "verify_otp_create");

        fetch("create_account_handler.php", { method: "POST", body: formData })
          .then((res) => res.text())
          .then((data) => {
            if (data.trim() === "OTP_VALID") {
              // Hide createacForm & show signup form, prefill email
              showOnly(signupForm);
              const signupEmailEl = signupForm.querySelector("#signupEmail");
              if (signupEmailEl) signupEmailEl.value = emailVal;
            } else {
              if (otpError) otpError.textContent = "Invalid OTP.";
            }
          })
          .catch(() => {
            if (otpError) otpError.textContent = "Error verifying OTP.";
          });
      }
    });

    // SIGNUP FORM
    safeAddEvent(signupForm, "submit", function (e) {
      e.preventDefault();
      clearAllErrors(signupForm);

      let valid = true;

      const nameInput = signupForm.querySelector("#signupName");
      const emailInput = signupForm.querySelector("#signupEmail");
      const phoneInput = signupForm.querySelector("#signupPhone");
      const passwordInput = signupForm.querySelector("#signupPassword");
      const confirmInput = signupForm.querySelector("#signupConfirmPassword");
      const addressInput = signupForm.querySelector("#signupAddress");
      const pincodeInput = signupForm.querySelector("#signupPincode");
      const signupMessage = document.getElementById("signup-message");

      if (signupMessage) signupMessage.innerHTML = "";

      if (!nameInput.value.trim()) { showError(nameInput, "Full name is required."); valid = false; }

      const emailVal = emailInput.value.trim();
      if (!emailVal) { showError(emailInput, "Email is required."); valid = false; }
      else if (!/^\S+@\S+\.\S+$/.test(emailVal)) { showError(emailInput, "Invalid email format."); valid = false; }

      if (!/^\d{10}$/.test(phoneInput.value.trim())) { showError(phoneInput, "Phone number must be 10 digits."); valid = false; }

      if (!passwordInput.value) { showError(passwordInput, "Password is required."); valid = false; }
      else if (!validatePassword(passwordInput.value)) {
        showError(passwordInput, "Password must be at least 6 characters and include uppercase, lowercase, number, and special character.");
        valid = false;
      }

      if (confirmInput.value !== passwordInput.value) { showError(confirmInput, "Passwords do not match."); valid = false; }

      if (!addressInput.value.trim()) { showError(addressInput, "Address is required."); valid = false; }

      if (!/^\d{6}$/.test(pincodeInput.value.trim())) { showError(pincodeInput, "Pincode must be 6 digits."); valid = false; }

      if (!valid) return;

      const formData = new FormData(signupForm);
      fetch("signup_handler.php", { method: "POST", body: formData })
        .then((res) => res.text())
        .then((data) => {
          if (signupMessage) signupMessage.innerHTML = '<div class="alert alert-info">' + data + '</div>';
          if (data.toLowerCase().includes("successful") || data.toLowerCase().includes("success")) {
            signupForm.reset();
            setTimeout(() => {
              if (signupMessage) signupMessage.innerHTML = "";
              window.location.href = "user/homes.php?page=scraprequest";
            }, 300);
          }
        })
        .catch(() => alert("An error occurred during signup."));
    });

    // REAL-TIME VALIDATION FOR SIGNUP FIELDS
    const safeQuery = (form, selector) => { if (!form) return null; return form.querySelector(selector); };
    const sName = safeQuery(signupForm, "#signupName");
    const sEmail = safeQuery(signupForm, "#signupEmail");
    const sPhone = safeQuery(signupForm, "#signupPhone");
    const sPassword = safeQuery(signupForm, "#signupPassword");
    const sConfirm = safeQuery(signupForm, "#signupConfirmPassword");
    const sAddress = safeQuery(signupForm, "#signupAddress");
    const sPincode = safeQuery(signupForm, "#signupPincode");

    if (sName) sName.addEventListener("input", function () { this.value.trim() === "" ? showError(this, "Full name is required.") : clearError(this); });
    if (sEmail) sEmail.addEventListener("input", function () {
      const val = this.value.trim();
      if (val === "") showError(this, "Email is required.");
      else if (!/^\S+@\S+\.\S+$/.test(val)) showError(this, "Invalid email format.");
      else clearError(this);
    });
    if (sPhone) sPhone.addEventListener("input", function () { !/^\d{10}$/.test(this.value.trim()) ? showError(this, "Phone number must be 10 digits.") : clearError(this); });
    if (sPassword) sPassword.addEventListener("input", function () { validatePassword(this.value) ? clearError(this) : showError(this, "Password must be at least 6 characters and include uppercase, lowercase, number, and special character."); });
    if (sConfirm) sConfirm.addEventListener("input", function () { const passwordVal = sPassword ? sPassword.value : ""; this.value !== passwordVal ? showError(this, "Passwords do not match.") : clearError(this); });
    if (sAddress) sAddress.addEventListener("input", function () { this.value.trim() === "" ? showError(this, "Address is required.") : clearError(this); });
    if (sPincode) sPincode.addEventListener("input", function () { !/^\d{6}$/.test(this.value.trim()) ? showError(this, "Pincode must be 6 digits.") : clearError(this); });

    // FORGOT PASSWORD FORM
    safeAddEvent(forgotForm, "submit", function (e) {
      e.preventDefault();
      clearAllErrors(forgotForm);

      let valid = true;
      const emailInput = document.querySelector("#forgot_email");
      const forgetMessage = document.getElementById("forget-message");

      if (forgetMessage) forgetMessage.textContent = "";

      const emailVal = emailInput.value.trim();
      if (!emailVal) { showError(emailInput, "Email is required."); valid = false; }
      else if (!/^\S+@\S+\.\S+$/.test(emailVal)) { showError(emailInput, "Invalid email format."); valid = false; }

      if (!valid) return;

      const formData = new FormData();
      formData.append("email", emailVal);
      formData.append("action", "send_otp");

      fetch("forgot_password_manager.php", { method: "POST", body: formData })
        .then((res) => res.text())
        .then((data) => {
          if (data.trim() === "OTP_SENT") {
            show($("otp-section"));
            hide($("sendOtpBtn"));
            if (forgetMessage) forgetMessage.textContent = "OTP sent. Check your email.";
          } else {
            showError(emailInput, data);
          }
        })
        .catch(() => alert("Error sending OTP."));
    });

    // Real-time OTP validation + auto verify for forgot password
    safeAddEvent($("otp_input"), "input", function () {
      const otpError = $("otp-error");
      if (!/^\d{0,6}$/.test(this.value)) {
        if (otpError) otpError.textContent = "Only numbers are allowed.";
        return;
      } else if (otpError) {
        otpError.textContent = "";
      }

      if (this.value.length === 6) {
        const formData = new FormData();
        formData.append("otp", this.value.trim());
        formData.append("email", document.getElementById("forgot_email").value.trim());
        formData.append("action", "verify_otp");

        fetch("forgot_password_manager.php", { method: "POST", body: formData })
          .then((res) => res.text())
          .then((data) => {
            if (data.trim() === "OTP_VALID") {
              // open reset form
              showOnly(resetFormGlobal);
              prepareResetForm();
            } else {
              if (otpError) otpError.textContent = "Invalid OTP.";
            }
          })
          .catch(() => { if (otpError) otpError.textContent = "Error verifying OTP."; });
      }
    });

    // Prepare reset form listeners (removes duplicates by cloning nodes)
    function prepareResetForm() {
      const newPasswordInputOld = $("new_password");
      const confirmPasswordInputOld = $("confirm_password");
      const resetError = $("reset-error");

      if (!newPasswordInputOld || !confirmPasswordInputOld) return;

      // clear values
      newPasswordInputOld.value = "";
      confirmPasswordInputOld.value = "";
      if (resetError) resetError.textContent = "";

      // replace with clones to remove old listeners if any
      const newClone = newPasswordInputOld.cloneNode(true);
      newClone.value = "";
      newPasswordInputOld.parentNode.replaceChild(newClone, newPasswordInputOld);

      const confirmClone = confirmPasswordInputOld.cloneNode(true);
      confirmClone.value = "";
      confirmPasswordInputOld.parentNode.replaceChild(confirmClone, confirmPasswordInputOld);

      const newPassInput = $("new_password");
      const confirmPassInput = $("confirm_password");

      if (!newPassInput || !confirmPassInput) return;

      // Real-time validation
      newPassInput.addEventListener("input", function () {
        if (!validatePassword(this.value)) {
          if (resetError) resetError.textContent = "Password must be at least 6 characters and include uppercase, lowercase, number, and special character.";
        } else if (confirmPassInput.value && this.value !== confirmPassInput.value) {
          if (resetError) resetError.textContent = "Passwords do not match.";
        } else {
          if (resetError) resetError.textContent = "";
        }
      });

      confirmPassInput.addEventListener("input", function () {
        if (this.value !== newPassInput.value) {
          if (resetError) resetError.textContent = "Passwords do not match.";
        } else if (!validatePassword(newPassInput.value)) {
          if (resetError) resetError.textContent = "Password must be at least 6 characters and include uppercase, lowercase, number, and special character.";
        } else {
          if (resetError) resetError.textContent = "";
        }
      });

      // Handle reset form submission
      resetFormGlobal.onsubmit = function (e) {
        e.preventDefault();

        const newPass = newPassInput.value.trim();
        const confirmPass = confirmPassInput.value.trim();

        if (!validatePassword(newPass)) {
          if (resetError) resetError.textContent = "Password must be at least 6 characters and include uppercase, lowercase, number, and special character.";
          return;
        }

        if (newPass !== confirmPass) {
          if (resetError) resetError.textContent = "Passwords do not match.";
          return;
        }

        const formData = new FormData();
        formData.append("email", document.getElementById("forgot_email").value.trim());
        formData.append("new_password", newPass);
        formData.append("action", "reset_password");

        fetch("reset_password_handler.php", { method: "POST", body: formData })
          .then((res) => res.text())
          .then((data) => {
            if (data.trim() === "PASSWORD_RESET") {
              alert("Password reset successfully! You can now login.");
              resetFormGlobal.reset();
              showOnly(loginForm);
            } else {
              if (resetError) resetError.textContent = data;
            }
          })
          .catch(() => { if (resetError) resetError.textContent = "Error resetting password."; });
      };
    }

    // initialize: ensure only login visible
    showOnly(loginForm);
  });
  </script>
</body>
</html>
