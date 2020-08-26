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
        cache: false,
        dataType: "json",
        success: function (result) {
          console.log(result);
          switch (result.Status) {
            case "-1":
              swal(result.ShortReason, result.Reason, result.Level).then(
                (value) => {
                  document.location =
                    "../Views/RedirectView.php?path=../Views/HomeView.php&name=Home Page&header=Home";
                }
              );
              return;
            case "0":
              // logout done
              swal(result.ShortReason, result.Reason, result.Level).then(
                (value) => {
                  document.location =
                    "../Views/RedirectView.php?path=../Views/LoginView.php&name=Login Page&header=Login";
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
  });
}

function inboxRequested() {
  $.ajax({
    method: "POST",
    url: "../Controllers/HomeController.php",
    data: {
      requestType: "inbox",
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      console.log(result);
      switch (result.Status) {
        case "-1":
          swal(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              
            }
          );
          return;
        case "0":
          // logout done
          swal(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
             
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
