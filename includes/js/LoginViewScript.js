function loginRequested() {
  // since we are directly calling js() as form action, $_POST[] array wont get populated.
  // so manually fetch all field values and serialize them to json array
  // parse the data on server side and assign to individual $_POST[] indexes
  var data = $(".login-form").serializeArray();
  $.ajax({
    method: "POST",
    url: "../Controllers/UserController.php",
    data: {
      requestType: "login",
      postData: data,
    },
    success: function (result) {
      console.log(result);
      switch (result) {
        case "-1":
          console.log("Invalid request type.");
          return;
        case "0":
          // success
          swal(
            "Welcome to zHost!",
            "You will be redirected to your inbox.",
            "success"
          ).then((value) => {
            document.location =
              "../Views/RedirectView.php?path=../Views/HomeView.php&name=Home Page&header=Home";
          });
          break;
        case "2":
          // account doesnt exist
          swal(
            "Account doesn't exist!",
            "Consider registering yourself.",
            "warning"
          ).then((value) => {
            document.location = "../Views/LoginView.php";
          });
          break;
        case "3":
          // email and pass doesnt match
          swal(
            "Email/Password missmatch.",
            "Entered email and password doesn't match.",
            "warning"
          ).then((value) => {
            document.location = "../Views/LoginView.php";
          });
          break;
        case "10":
          // email invalid
          swal(
            "Entered email is empty/invalid.",
            "All email ids should have @zhost.com appended at end.",
            "warning"
          ).then((value) => {
            document.location = "../Views/LoginView.php";
          });
          break;
        case "11":
          // password invalid
          swal(
            "Entered password is empty/invalid.",
            "Passwords should not contain whitespaces or empty charecters (ASCII included)",
            "warning"
          ).then((value) => {
            document.location = "../Views/LoginView.php";
          });
          break;
      }
    },
  });
}
