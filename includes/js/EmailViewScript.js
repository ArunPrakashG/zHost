document.addEventListener("DOMContentLoaded", function () {});

function onNoSelectedMail() {
  Swal.fire("INVALID!", "No mails selected!", "error").then((value) => {
    window.history.back();
  });
}

function quickReply(sendTo) {
  console.log(sendTo);
  Swal.fire({
    title: "<strong>Reply</strong>",
    html:
      '<input id="mail-subject-quick" class="swal2-input" placeholder="Subject" required>' +
      '<textarea id="mail-body-quick" class="swal2-textarea" type="textarea" placeholder="Enter your message..." required></textarea>' +
      '<input id="mail-attachment-quick" class="swal2-file" type="file" name="attachment" placeholder="Attachment">',
    showCancelButton: true,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showCloseButton: false,
    confirmButtonText: "Reply",
    confirmButtonAriaLabel: "Reply",
    cancelButtonText: "Cancel",
    cancelButtonAriaLabel: "Cancel",
    preConfirm: () => {
      return [
        document.getElementById("mail-subject-quick").value,
        document.getElementById("mail-body-quick").value,
        document.getElementById("mail-attachment-quick").value,
      ];
    },
  }).then((formValues) => {
    if (!formValues.value) {
      return;
    }

    if (isBlank(formValues.value[0])) {
      Swal.fire(
        "Invalid Details!",
        "You must specify a valid 'Subject' value.",
        "warning"
      );
      return;
    }

    if (isBlank(formValues.value[1])) {
      Swal.fire(
        "Invalid Details!",
        "You must specify a valid 'Body' value.",
        "warning"
      );
      return;
    }

    var formData = new FormData();
    var mailObject = {
      To: sendTo,
      IsDraft: 0,
      IsTrash: 0,
      Subject: formValues.value[0],
      Body: formValues.value[1],
      HasAttachment: isBlank(formValues.value[2]) ? false : true,
    };

    if (!isBlank(formValues.value[2])) {
      formData.append("file", $("#mail-attachment")[0].files[0]);
    }

    formData.append("requestType", "compose");
    formData.append("mailObject", JSON.stringify(mailObject));

    $.ajax({
      url: "../Controllers/HomeController.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (result) {
        result = JSON.parse(result);
        switch (result.Status) {
          case "0":
            Swal.fire(result.ShortReason, result.Reason, result.Level).then(
              (value) => {
                document.location = "../Views/InboxView.php";
              }
            );
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
  });
}

function quickEdit(emailObj) {
  Swal.fire({
    title: "<strong>Edit</strong>",
    html:
      '<input id="qmail-subject" class="swal2-input" placeholder="Subject" value="' +
      emailObj.Subject +
      '" required>' +
      '<textarea id="qmail-body" class="swal2-textarea" type="textarea" placeholder="Enter your message..." required>' +
      emailObj.Body +
      "</textarea>",
    showCancelButton: true,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showCloseButton: false,
    confirmButtonText: "Update",
    confirmButtonAriaLabel: "Update",
    cancelButtonText: "Cancel",
    cancelButtonAriaLabel: "Cancel",
    preConfirm: () => {
      return [
        document.getElementById("qmail-subject").value,
        document.getElementById("qmail-body").value,
      ];
    },
  }).then((result) => {
    if (!result.isConfirmed) {
      return;
    }

    var sub = result.value[0];
    var body = result.value[1];
    var uuid = emailObj.MailID;

    $.ajax({
      method: "POST",
      url: "../Controllers/HomeController.php",
      data: {
        requestType: "update_mail",
        uuid: uuid,
        nSubject: sub,
        nBody: body,
      },
      cache: false,
      dataType: "json",
      success: function (result) {
        switch (result.Status) {
          case "0":
            Swal.fire(result.ShortReason, result.Reason, result.Level).then(
              (result) => {
                window.history.back();
              }
            );
            break;
          case "-1":
            Swal.fire(result.ShortReason, result.Reason, result.Level);
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
  });
}

function trashMail(uuid) {
    // mail uuid  
    if (uuid == null) {
      console.log("uuid can't be null.");
      return;
    }
  
    // create ajax request here to HomeController.php to delete the mail from db
    $.ajax({
      method: "POST",
      url: "../Controllers/HomeController.php",
      data: {
        requestType: "trash_mail",
        emailUuid: uuid,
      },
      cache: false,
      dataType: "json",
      success: function (result) {
        switch (result.Status) {
          case "0":
            Swal.fire(result.ShortReason, result.Reason, result.Level).then(
              (value) => {
                // if success, delete from ui
                window.history.back();
              }
            );
            break;
          case "-1":
            Swal.fire(
              result.ShortReason,
              result.Reason,
              result.Level
            ).then((value) => {});
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