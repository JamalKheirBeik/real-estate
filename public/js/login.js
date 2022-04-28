const formsContainer = document.querySelector(".forms-container"),
  pwShowHide = document.querySelectorAll(".showHidePw"),
  pwFields = document.querySelectorAll(".password"),
  signup = document.querySelector(".signup-link"),
  login = document.querySelector(".login-link");

pwShowHide.forEach((eyeIcon) => {
  eyeIcon.addEventListener("click", () => {
    pwFields.forEach((pwField) => {
      if (pwField.type === "password") {
        pwField.type = "text";

        pwShowHide.forEach((icon) => {
          icon.classList.replace("uil-eye-slash", "uil-eye");
        });
      } else {
        pwField.type = "password";

        pwShowHide.forEach((icon) => {
          icon.classList.replace("uil-eye", "uil-eye-slash");
        });
      }
    });
  });
});

signup.addEventListener("click", (e) => {
  e.preventDefault();
  formsContainer.classList.add("active");
});

login.addEventListener("click", (e) => {
  e.preventDefault();
  formsContainer.classList.remove("active");
});
