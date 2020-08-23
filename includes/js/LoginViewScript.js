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
    
    dataType: "json",
    success: function (result) {
      console.log(result.ShortReason);
      switch (result.Status) {
        case "-1":
          swal(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              document.location =
                "../Views/LoginView.php";
            }
          );
          return;
        case "0":
          // success
          swal(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              document.location =
                "../Views/RedirectView.php?path=../Views/HomeView.php&name=Home Page&header=Home";
            }
          );
          break;
      }
    },
  });
}
