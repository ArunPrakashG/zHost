function logoutRequested() {
  swal({
    title: "Are you sure?",
    text: "You will logged out and redirected to Index page.",
    icon: "warning",
    buttons: {
      cancel: {
        text: "Cancel",
        value: null,
        visible: true,
        className: "",
        closeModal: true,
      },
      confirm: {
        text: "Confirm Logout",
        value: true,
        visible: true,
        className: "",
        closeModal: true,
      },
    },
  }).then((isConfirmed) => {
    if (isConfirmed) {
      $.ajax({
        method: "POST",
        url: "../Controllers/UserController.php",
        data: {
          requestType: "logout",
        },
        success: function (result) {
          switch (result) {
            case "-1":
              console.log("Invalid request type.");
              return;
            case "0":
              // logout done
              swal(
                "You are logged out!",
                "Click OK for redirect to Index page.",
                "success"
              ).then((value) => {
                document.location = "../";
              });
              break;
            case "1":
              // not logged in
              swal(
                "Session expired?!",
                "Couldn't logout as you are not logged in. Press Ok to login.",
                "warning"
              ).then((value) => {
                if (value) {
                  document.location =
                    "../Views/RedirectView.php?path=../Views/LoginView.php&name=Login Page&header=Login";
                  return;
                }

                document.location = "../";
              });
              break;
          }
        },
      });
    }
  });
}
