// Error messages are shown when user clicks outside the email input.
// Error is not displayed for checkbox,
// if the click is performed on the checkbox itself (since it would be checked then).

const form = document.querySelector("form");
const terms = document.getElementById("terms");
const emailError = document.querySelector(".email-error");
const termsError = document.querySelector(".terms-error");

const emailInput = document.querySelector("input[name=email]");
emailInput.onfocus = emailInput.addEventListener("blur", () => {
  validateEmail();
  validateTerms();
});

terms.parentElement.addEventListener("click", validateTerms);
// for pressing back button
window.onload = validateEmail();
window.onload = validateTerms();

function validateEmail() {
  const emailValue = document.getElementById("email").value;
  if (emailValue === "") {
    emailError.innerText = "Email address is required";
  } else if (!isEmail(emailValue)) {
    emailError.innerText = "Please provide a valid e-mail address";
  } else if (isColombianEmail(emailValue)) {
    emailError.innerText =
      "We are not accepting subscriptions from Colombia emails";
  } else {
    emailError.innerText = "";
  }
}

function validateTerms() {
  if (!terms.checked) {
    termsError.innerText = "You must accept the terms and conditions";
  } else {
    termsError.innerText = "";
  }
}

// regex
function isEmail(email) {
  return /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
    email
  );
}

function isColombianEmail(email) {
  return /\.(co)$/i.test(email);
}
