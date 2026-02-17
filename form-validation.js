const form = document.querySelector("form");

function formvalid(e) {
  e.preventDefault();

  const name = form.querySelector('input[type="text"]');
  const email = form.querySelector('input[type="email"]');
  const password = form.querySelector('input[type="password"]');
  const roles = form.querySelectorAll('input[name="role"]');
  const departments = form.querySelectorAll('input[type="checkbox"]');

  const emailPattern = /^[^@]+@[^@]+\.gov$/;

  if (!/^[A-Za-z ]+$/.test(name.value.trim())) {
    alert("Name must contain only letters and spaces");
    return;
  }

  if (!emailPattern.test(email.value.trim())) {
    alert("email is wrong!");
    return;
  }

  let rolePicked = false;

  for (let r of roles) {
    if (r.checked) {
      rolePicked = true;
      break;
    }
  }
  if (!rolePicked) {
    alert("you havent picked a role!");
    return;
  }

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

  if (password.value.length < 8) {
    alert("password must be 8 characters or longer!");
    return;
  }

  form.submit();
}

form.addEventListener("submit", formvalid);
