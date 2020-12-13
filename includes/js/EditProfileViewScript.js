function editRequested() {
  var file_data = document.getElementsByName("profilePicture")[0].files[0];
  var formData = new FormData($("#editForm")[0]);

  formData.append("file", file_data);
  formData.append("requestType", "update_user");

  $.ajax({
    method: "POST",
    url: "../Controllers/EditProfileController.php",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (result) {
      console.log(result);
      switch (result.Status) {
        case "-1":
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              document.location = "../Views/EditProfileView.php";
            }
          );
          return;
        case "0":
          // success
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              Swal.fire(
                "Session Expired!",
                "You will be required to relogin again.",
                "warning"
              ).then((value) => {
                clearSession();
              });
            }
          );
          break;
      }
    },
    error: function (e) {
      console.log(e);
    },
  });
}

function clearSession() {
  $.ajax({
    url: "../Controllers/EditProfileController.php",
    type: "POST",
    data: {
      requestType: "clear_user_session",
    },
    dataType: "json",
    success: function (result) {
      switch (result.Status) {
        case "0":
          window.location = "../Views/LoginView.php";
          break;
        case "-1":
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              document.location = "../Views/InboxView.php";
            }
          );
          break;
      }
    },
    error: function (e) {
      Swal.fire(
        "Request Exception!",
        "Exception occured during AJAX Request. Check console for more info.",
        "error"
      );
      console.log(e);
    },
  });
}

function cancelForm() {
  window.location = "../Views/ProfileView.php";
}
