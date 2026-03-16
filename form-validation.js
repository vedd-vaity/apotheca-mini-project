const form = document.querySelector("form");

function formvalid(e) {
  e.preventDefault();
  // adds all valid form input fields
  const name = form.querySelector('input[type="text"]');
  const email = form.querySelector('input[type="email"]');
  const password = form.querySelector('input[type="password"]');
  const roles = form.querySelectorAll('input[name="role"]');
  const departments = form.querySelectorAll('input[type="checkbox"]');

  // tests if name is only alphabets and spaces
  if (!/^[A-Za-z ]+$/.test(name.value.trim())) {
    alert("Name must contain only letters and spaces");
    return;
  }
  // tests if email is following an appropriate ppattern
  const emailPattern = /^[^@]+@[^@]+\.hp$/;
  if (!emailPattern.test(email.value.trim())) {
    alert("email is wrong!");
    return;
  }
  // deals with department checking
  let deptPicked = false;
  for (let d of departments) {
    if (d.checked) {
      deptPicked = true;
      break;
    }
  }

  if (!deptPicked) {
    alert("You havent picked a department!");
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
