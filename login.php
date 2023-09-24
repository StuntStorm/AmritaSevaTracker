<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amrita Seva Tracker</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>
</head>

<body>

  <div class="container">
    <div class="form-box" style="border-radius: 10px;  width: 90%;
    max-width: 450px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px 50px 30px;
    text-align: center;
    transition: max-height 0.5s; /* Add transition for smooth resizing */">
      <h1 id="title">Login</h1>
      <form id="myForm" name="register">
        <div class="input-field">
          <i class="fa-solid fa-envelope"></i>
          <input id="emailInput" type="email" placeholder="Email" name="email">
        </div>

        <span id="emailWarning" style="color: red;"></span>
        <div class="input-field">
          <i class="fa-solid fa-lock"></i>
          <input type="password" placeholder="Password" name="password" id="password">
        </div>



        <input type="submit" id="login" value="Login" name="login" style="
    background-color: #0000FF;
    color: #FFFFFF;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 200px; /* Adjust as needed */
    height: 50px; /* Adjust as needed */
    border-radius: 25px; /* Adjust as needed */
" onmouseover="this.style.backgroundColor='#000099'" onmouseout="this.style.backgroundColor='#0000FF'">
        <br><br>
        <h2 id="title">Check Junk Mail for the OTP</h2><br><br>
        <p id="quote" style="font-style: italic;"></p>


      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var emailInput = document.getElementById('emailInput');
      var passwordInput = document.getElementById('password');
      var loginButton = document.getElementById('login');
      var emailWarning = document.getElementById('emailWarning');
      loginButton.disabled = true;

      function updateLoginButtonState() {

        if (!isValidEmail(emailInput.value) || passwordInput.value === '') {
          loginButton.disabled = true;
        } else {
          loginButton.disabled = false;
        }
      }

      emailInput.addEventListener('input', function() {
        if (!isValidEmail(emailInput.value)) {
          emailWarning.innerHTML = 'Invalid email address';
        } else {
          emailWarning.innerHTML = '';
        }
        updateLoginButtonState();
      });

      passwordInput.addEventListener('input', updateLoginButtonState);
    });

    function isValidEmail(email) {
      // A basic regex for validating an email format
      var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+amrita\.edu$/;
      return regex.test(email);
    }

    //-------------------------------------

    document.getElementById('myForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var email = document.getElementById('emailInput').value;
    var password = document.getElementById('password').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'check.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            var userType = this.responseText;
            
            switch (userType) {
                case 'main_coordinator':
                    window.location.href = 'main_coordinator.php';
                    break;
                case 'seva_coordinator':
                    window.location.href = 'seva_coordinator.php';
                    break;
                case 'faculty':
                    window.location.href = 'faculty.php';
                    break;
                  case 'student':
                    window.location.href = 'student.php';
                    break;
                default:
                    alert('Email or password is incorrect');
            }
        } else {
            alert('Email or password is incorrect');
        }
    };
    xhr.send('email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password));
});



    //------------------------------------
    fetch('https://api.quotable.io/random')
      .then(response => response.json())
      .then(data => {
        var quote = data.content;
        document.getElementById('quote').innerText = quote;
      })
      .catch(error => console.error(error));
  </script>
</body>

</html>