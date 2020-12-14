function registerRequested() {
  var file_data = document.getElementById("pro-image").files[0];
  var formData = new FormData($("#reg-form")[0]);

  formData.append("file", file_data);
  formData.append("requestType", "register");
  console.log(formData);
  
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
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              document.location = "../Views/RegisterView.php";
            }
          );
          return;
        case "0":
          // success
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              let timerInterval;
              Swal.fire({
                title: "Redirecting you to login page!",
                html: "redirecting in <b></b> milliseconds.",
                timer: 1000,
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
                  document.location = "../Views/LoginView.php";
                },
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
