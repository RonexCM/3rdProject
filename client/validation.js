document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const email = document.getElementById("loginEmail").value;
  const password = document.getElementById("loginPassword").value;
  const error = document.getElementById("loginError");

  // Simple validation
  if (email === "" || password === "") {
    error.textContent = "All fields are required.";
  } else {
    error.textContent = "";
    alert("Login successful!");
    // Perform login (e.g., send data to server)
  }
});

document.getElementById("signupForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const name = document.getElementById("signupName").value;
  const email = document.getElementById("signupEmail").value;
  const password = document.getElementById("signupPassword").value;
  const confirmPassword = document.getElementById(
    "signupConfirmPassword"
  ).value;
  const error = document.getElementById("signupError");

  // Simple validation
  if (
    name === "" ||
    email === "" ||
    password === "" ||
    confirmPassword === ""
  ) {
    error.textContent = "All fields are required.";
  } else if (password !== confirmPassword) {
    error.textContent = "Passwords do not match.";
  } else {
    error.textContent = "";
    alert("Signup successful!");
    // Perform signup (e.g., send data to server)
  }
});
