document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");

  form.addEventListener("submit", function (event) {
    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Remove previous error messages
    const oldError = document.querySelector(".error-message");
    if (oldError) oldError.remove();

    let errorMessage = "";

    if (!username || !password) {
      errorMessage = "Silakan isi semua kolom.";
    } else if (!emailRegex.test(username)) {
      errorMessage = "Format email tidak valid.";
    }

    if (errorMessage) {
      event.preventDefault();

      const errorBox = document.createElement("div");
      errorBox.className = "error-message";
      errorBox.textContent = errorMessage;
      form.prepend(errorBox);
    }
  });
});
