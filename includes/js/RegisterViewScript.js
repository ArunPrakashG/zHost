function registerRequested() { 
  var file_data = document.getElementById("pro-image").files[0];
  var formData = new FormData($("#reg-form")[0]);
  
  formData.append("file", file_data);
  formData.append("requestType", "register");

  $.ajax({
    method: "POST",
    url: "../Controllers/RegisterController.php",
    data: formData,    
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (result) {      
      switch (result.Status) {
        case "-1":
          swal(
            result.ShortReason,
            result.Reason,
            result.Level
          ).then((value) => {
            document.location =
              "../Views/RegisterView.php";
          });
          return;
        case "0":
          // success
          swal(
            result.ShortReason,
            result.Reason,
            result.Level
          ).then((value) => {
            document.location =
              "../Views/RedirectView.php?path=../Views/LoginView.php&name=Login Page&header=Login";
          });
          break;
      }
    },
    error: function (e) {
      console.log(e);
    },
  });
}
