function registerRequested() {
  var data = $(".login-form").serializeArray();
  $.ajax({
    method: "POST",
    url: "../Controllers/RegisterController.php",
    data: {
      requestType: "register",
      postData: data,
    },
    success: function (result) {
      switch (result) {
        case "-1":
          console.log("Invalid request type.");
          return;
        case "0":
          // success
          swal(
            "Registered!",
            "You will be required to login now!",
            "success"
          ).then((value) => {
            document.location =
              "../Views/RedirectView.php?path=../Views/LoginView.php&name=Login Page&header=Login";
          });
          break;
        case "1":
          // account already exist
          swal(
            "Account exist!",
            "An account with this email id already exists!",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "10":
          // email invalid
          swal(
            "Email is empty!",
            "Please fill in the email box",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "11":
          // invalid email, start with zhost domain
          swal(
            "Invalid Email",
            "Emails should end with @zhost.com domain",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "12":
          // user name invalid or empty
          swal(
            "Username is empty!",
            "Please fill in the username box",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "13":
          // password invalid or empty
          swal(
            "Password is empty!",
            "Please fill in the password box",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "14":
          // password has whitespace charecters
          swal(
            "Invalid Password",
            "Passwords shouldn't contain whitespace or illegal chareceters (ASCII/Unicode)",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "15":
          // re-enter password
          swal(
            "Reenter your password!",
            "Reenter your password for confirmation",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "16":
          // passwords do not match
          swal(
            "Passwords don't match",
            "Entered passwords don't match with each other",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "17":
          // phone number is empty
          swal(
            "Phone number cannot be empty!",
            "Please enter a phone number",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
        case "18":
          // phone number is invalid
          swal(
            "Phone number is invalid!",
            "Please enter a valid phone number with 10 charecters in length",
            "warning"
          ).then((value) => {
            document.location = "../Views/RegisterView.php";
          });
          break;
      }
    },
  });
}
