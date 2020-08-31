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
      switch (result.Status) {
        case "-1":
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              document.location = "../Views/LoginView.php";
            }
          );
          return;
        case "0":
          // success
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              let timerInterval;
              Swal.fire({
                title: "Redirecting you to home page!",
                html: "redirecting in <b></b> milliseconds.",
                timer: 3000,
                timerProgressBar: true,
                allowOutsideClick: false,
                onBeforeOpen: () => {
                  Swal.showLoading();
                  timerInterval = setInterval(() => {
                    const content = Swal.getContent();
                    if (content) {
                      const b = content.querySelector("b");
                      if (b) {
                        b.textContent = Swal.getTimerLeft();
                      }
                    }
                  }, 100);
                },
                onClose: () => {
                  clearInterval(timerInterval);
                  document.location = "../Views/HomeView.php";
                },
              });
            }
          );
          break;
      }
    },
  });
}

function onForgotPasswordClicked() {
  Swal.fire({
    title: "Email ID",
    text: "Enter email address registered with your account",
    input: "email",
    inputAttributes: {
      autocapitalize: "off",
    },
    showCancelButton: true,
    confirmButtonText: "Continue",
  }).then(async (emailResult) => {
    if (emailResult.isDismissed) {
      return;
    }

    $.ajax({
      method: "POST",
      url: "../Controllers/UserController.php",
      data: {
        requestType: "recovery_security_data",
        email: emailResult.value,
      },

      dataType: "json",
      success: function (reqResult) {
        switch (reqResult.Status) {
          case "-1":
            Swal.fire(
              reqResult.ShortReason,
              reqResult.Reason,
              reqResult.Level
            ).then((value) => {
              document.location = "../Views/LoginView.php";
            });
            return;
          case "0":
            // success
            if (reqResult.SecData == null) {
              return;
            }

            Swal.fire({
              title: "Select a recovery method",
              input: "select",
              inputOptions: {
                secQues: "Security Question",
                pNumber: "Phone Number",
              },
              inputPlaceholder: "Select a method",
              showCancelButton: true,
            }).then((recoveryMethodResult) => {
              switch (recoveryMethodResult.value) {
                case "secQues":
                  Swal.fire({
                    title: "Security Question",
                    text:
                      reqResult.SecData.SecurityQuestion +
                      " [Enter answer below]",
                    input: "text",
                    inputAttributes: {
                      autocapitalize: "off",
                    },
                    showCancelButton: true,
                    confirmButtonText: "Continue",
                  }).then((secResult) => {
                    if (secResult.isDismissed) {
                      return;
                    }

                    if (
                      secResult.value.toLowerCase().replace(" ", "") ==
                      reqResult.SecData.SecurityAnswer.toLowerCase().replace(
                        " ",
                        ""
                      )
                    ) {
                      Swal.mixin({
                        confirmButtonText: "Continue",
                        showCancelButton: false,
                        allowOutsideClick: false,
                        progressSteps: ["1", "2"],
                      })
                        .queue([
                          {
                            title: "New Password",
                            text:
                              "Enter your new password (will be used for future logins)",
                            input: "password",
                          },
                          {
                            title: "Confirm Password",
                            text: "Re enter your password to confirm",
                            input: "password",
                          },
                        ])
                        .then((result) => {
                          if (result.value) {
                            if (result.value[0] != result.value[1]) {
                              Swal.fire(
                                "Password's do not match.",
                                "Entered passwords do not match.",
                                "error"
                              );
                              return;
                            }

                            $.ajax({
                              method: "POST",
                              url: "../Controllers/UserController.php",
                              data: {
                                requestType: "recovery_set_password",
                                email: emailResult.value,
                                new_pass: result.value[0],
                              },

                              dataType: "json",
                              success: function (result) {
                                switch (result.Status) {
                                  case "-1":
                                    Swal.fire(
                                      result.ShortReason,
                                      result.Reason,
                                      result.Level
                                    ).then((value) => {
                                      document.location =
                                        "../Views/LoginView.php";
                                    });
                                    return;
                                  case "0":
                                    // success
                                    Swal.fire(
                                      result.ShortReason,
                                      result.Reason,
                                      result.Level
                                    ).then((value) => {
                                      let timerInterval;
                                      Swal.fire({
                                        title: "Redirecting you to login page!",
                                        html:
                                          "redirecting in <b></b> milliseconds.",
                                        timer: 3000,
                                        timerProgressBar: true,
                                        allowOutsideClick: false,
                                        onBeforeOpen: () => {
                                          Swal.showLoading();
                                          timerInterval = setInterval(() => {
                                            const content = Swal.getContent();
                                            if (content) {
                                              const b = content.querySelector(
                                                "b"
                                              );
                                              if (b) {
                                                b.textContent = Swal.getTimerLeft();
                                              }
                                            }
                                          }, 100);
                                        },
                                        onClose: () => {
                                          clearInterval(timerInterval);
                                          document.location =
                                            "../Views/LoginView.php";
                                        },
                                      });
                                    });
                                    break;
                                }
                              },
                            });
                          }
                        });
                    }
                  });
                  break;
                case "pNumber":
                  // TODO

                  break;
                default:
                  return;
              }
            });
            break;
        }
      },
    });
  });
}
