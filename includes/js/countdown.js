var dateNow = new Date();
var seconds = dateNow.setSeconds(dateNow.getSeconds() + 3.0);
var countDownDate = new Date(seconds);

// Update the count down every 1 second
var x = setInterval(function () {
  var now = new Date().getTime();
  var distance = countDownDate - now;
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
  document.getElementById("countdownElement").innerHTML = "Redirecting to login page in " + seconds + " seconds";
  
  if (distance <= 0) {
    clearInterval(x);
    window.location = "../Views/LoginView.php";
    return;
  }
}, 1000);
