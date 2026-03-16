const form = document.querySelector("form");

function formvalid(e) {
  e.preventDefault();
  // adds all valid form input fields
  const email = form.querySelector('input[type="email"]');
  const password = form.querySelector('input[type="password"]');
  const roles = form.querySelectorAll('input[name="role"]');

  // tests if email is following an appropriate ppattern
  const emailPattern = /^[^@]+@[^@]+\.hp$/;
  if (!emailPattern.test(email.value.trim())) {
    alert("email is wrong!");
    return;
  }

  // password check
  if (password.value.length < 8) {
    alert("password must be 8 characters or longer!");
    return;
  }

  form.submit();
}

form.addEventListener("submit", formvalid);
