function logoutUser() {
  Swal.fire({
    title: "Are you sure?",
    text: "You will logged out and redirected to Index page.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Confirm Logout",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.value) {
      $.ajax({
        method: "POST",
        url: "../Controllers/UserController.php",
        data: {
          requestType: "logout",
        },
        cache: false,
        dataType: "json",
        success: function (result) {
          switch (result.Status) {
            case "-1":
              Swal.fire(result.ShortReason, result.Reason, result.Level).then(
                (value) => {
                  document.location = "../Views/HomeView.php";
                }
              );
              return;
            case "0":
              // logout done
              Swal.fire(result.ShortReason, result.Reason, result.Level).then(
                (value) => {
                  let timerInterval;
                  Swal.fire({
                    title: "Redirecting you to login page!",
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
                      document.location = "../Views/LoginView.php";
                    },
                  });
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
  });
}

function deleteMail(rowIndex) {
  // mail uuid
  var uuid = getUuidOfSelectedRow(rowIndex);

  if (uuid == null) {
    console.log("uuid can't be null.");
    return;
  }

  Swal.fire({
    title: "Are you sure?",
    text: "The mail will be deleted permentantly from your account.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Confirm",
    cancelButtonText: "Cancel",
  }).then((isConfirmed) => {
    if (isConfirmed.value) {
      // create ajax request here to HomeController.php to delete the mail from db
      $.ajax({
        method: "POST",
        url: "../Controllers/HomeController.php",
        data: {
          requestType: "delete_trash_mail",
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
                  document.getElementById("mailTable").deleteRow(rowIndex);
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
    }
  });
}

function getUuidOfSelectedRow(rowIndex) {
  var selectedRow = document.getElementById("mailTable").rows[rowIndex];
  for (var i = 0, cell; (cell = selectedRow.cells[i]); i++) {
    if (cell.id == "mailUuid") {
      return cell.innerHTML;
    }
  }
}

function trashMail(rowIndex) {
  // mail uuid
  var uuid = getUuidOfSelectedRow(rowIndex);

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
              document.getElementById("mailTable").deleteRow(rowIndex);
              document.location = "../Views/HomeView.php";
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

function getInboxMails() {
  $("#mailTable tbody tr").remove(0);

  $.ajax({
    method: "POST",
    url: "../Controllers/HomeController.php",
    data: {
      requestType: "inbox_view",
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      switch (result.Status) {
        case "-1":
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              //document.location = "../Views/HomeView.php?previousError=true";
              return;
            }
          );
          return;
        case "0":
          // inbox fetch done
          if (result.Emails == null || result.Emails.length <= 0) {
            setTimeout(function () {
              Swal.fire({
                title: "All caught up!",
                text: "No new mails found in current mail folder.",
                timer: 3000,
                timerProgressBar: true,
              });
            }, 100);

            return;
          }

          for (var i = 0; i < result.Emails.length; i++) {
            var mail = result.Emails[i];
            var rowHtml =
              '<td class="table-row-field">' +
              (i + 1) +
              "</td>" +
              '<td class="table-row-field" id="mailUuid">' +
              mail.MailID +
              "</td>" +
              '<td class="table-row-field">' +
              mail.From +
              "</td>" +
              '<td class="table-row-field">' +
              (mail.Subject.length > 18
                ? mail.Subject.substring(0, 18 - 3) + "..."
                : mail.Subject) +
              "</td>" +
              '<td class="table-row-field">' +
              mail.At +
              "</td>" +
              '<td class="table-row-field">' +
              '<button class="deletebttn" id="trash-inbox" onclick="onDeleteAnchorClicked(this);">Trash</button>' +
              "</td>";
            addRow(rowHtml, "mailTable");
          }

          //if(result.Emails.length > 0){
          //document.getElementsByClassName("mailTable")[0].rows[0].setAttribute('class', 'active-row');
          //}

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

function getDraftMails() {
  $("#mailTable tbody tr").remove();

  $.ajax({
    method: "POST",
    url: "../Controllers/HomeController.php",
    data: {
      requestType: "draft_view",
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      switch (result.Status) {
        case "-1":
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              //document.location = "../Views/HomeView.php?previousError=true";
              return;
            }
          );
          return;
        case "0":
          // draft fetch done
          if (result.Emails == null || result.Emails.length <= 0) {
            setTimeout(function () {
              Swal.fire({
                title: "All caught up!",
                text: "No new mails found in current mail folder.",
                timer: 3000,
                timerProgressBar: true,
              });
            }, 100);

            return;
          }

          for (var i = 0; i < result.Emails.length; i++) {
            var mail = result.Emails[i];
            var rowHtml =
              '<td class="table-row-field">' +
              i +
              1 +
              "</td>" +
              '<td class="table-row-field" id="mailUuid">' +
              mail.MailID +
              "</td>" +
              '<td class="table-row-field">' +
              mail.From +
              "</td>" +
              '<td class="table-row-field">' +
              (mail.Subject.length > 18
                ? mail.Subject.substring(0, 18 - 3) + "..."
                : mail.Subject) +
              "</td>" +
              '<td class="table-row-field">' +
              mail.At +
              "</td>" +
              '<td class="table-row-field">' +
              '<button class="deletebttn" id="delete-draft" onclick="onDeleteAnchorClicked(this);">Delete</button>' +
              "</td>";
            addRow(rowHtml, "mailTable");
          }

          //if(result.Emails.length > 0){
          //document.getElementsByClassName("mailTable")[0].rows[0].setAttribute('class', 'active-row');
          //}

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

function getTrashMails() {
  $("#mailTable tbody tr").remove();

  $.ajax({
    method: "POST",
    url: "../Controllers/HomeController.php",
    data: {
      requestType: "trash_view",
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      switch (result.Status) {
        case "-1":
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              //document.location = "../Views/HomeView.php?previousError=true";
              return;
            }
          );
          return;
        case "0":
          // trash fetch done
          if (result.Emails == null || result.Emails.length <= 0) {
            setTimeout(function () {
              Swal.fire({
                title: "All caught up!",
                text: "No new mails found in current mail folder.",
                timer: 3000,
                timerProgressBar: true,
              });
            }, 100);

            return;
          }

          for (var i = 0; i < result.Emails.length; i++) {
            var mail = result.Emails[i];
            var rowHtml =
              '<td class="table-row-field">' +
              i +
              1 +
              "</td>" +
              '<td class="table-row-field" id="mailUuid">' +
              mail.MailID +
              "</td>" +
              '<td class="table-row-field">' +
              mail.From +
              "</td>" +
              '<td class="table-row-field">' +
              (mail.Subject.length > 18
                ? mail.Subject.substring(0, 18 - 3) + "..."
                : mail.Subject) +
              "</td>" +
              '<td class="table-row-field">' +
              mail.At +
              "</td>" +
              '<td class="table-row-field">' +
              '<button class="deletebttn" id="trash-options" onclick="onDeleteAnchorClicked(this);">Option</button>' +
              "</td>";
            addRow(rowHtml, "mailTable");
          }

          //if(result.Emails.length > 0){
          //document.getElementsByClassName("mailTable")[0].rows[0].setAttribute('class', 'active-row');
          //}

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

function getSendMails(){
  $("#mailTable tbody tr").remove();

  $.ajax({
    method: "POST",
    url: "../Controllers/HomeController.php",
    data: {
      requestType: "send_view",
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      switch (result.Status) {
        case "-1":
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              //document.location = "../Views/HomeView.php?previousError=true";
              return;
            }
          );
          return;
        case "0":
          // send fetch done
          if (result.Emails == null || result.Emails.length <= 0) {
            setTimeout(function () {
              Swal.fire({
                title: "All caught up!",
                text: "No new mails found in current mail folder.",
                timer: 3000,
                timerProgressBar: true,
              });
            }, 100);

            return;
          }

          for (var i = 0; i < result.Emails.length; i++) {
            var mail = result.Emails[i];
            var rowHtml =
              '<td class="table-row-field">' +
              i +
              1 +
              "</td>" +
              '<td class="table-row-field" id="mailUuid">' +
              mail.MailID +
              "</td>" +
              '<td class="table-row-field">' +
              mail.From +
              "</td>" +
              '<td class="table-row-field">' +
              (mail.Subject.length > 18
                ? mail.Subject.substring(0, 18 - 3) + "..."
                : mail.Subject) +
              "</td>" +
              '<td class="table-row-field">' +
              mail.At +
              "</td>" +
              '<td class="table-row-field">' +
              '<button class="deletebttn" id="trash-options" disabled onclick="onDeleteAnchorClicked(this);">Option</button>' +
              "</td>";
            addRow(rowHtml, "mailTable");
          }
          
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

function onComposeButtonClicked() {
  Swal.fire({
    title: "<strong>COMPOSE MAIL</strong>",
    html:
      '<input id="mail-to" class="swal2-input" placeholder="To" required>' +
      '<input id="mail-subject" class="swal2-input" placeholder="Subject" required>' +
      '<textarea id="mail-body" class="swal2-textarea" type="textarea" placeholder="Enter your message..." required></textarea>' +
      '<input id="mail-attachment" class="swal2-file" type="file" name="attachment" placeholder="Attachment">' +
      '<input id="mail-option1" class="swal2-radio" type="radio" name="sendoption" value="instant" checked>' +
      '<label for="mail-option1" class="swal2-radio">Instant Send</label>' +
      "&nbsp; &nbsp; &nbsp;" +
      '<input id="mail-option2" class="swal2-radio" type="radio" name="sendoption" value="draft">' +
      '<label for="mail-option2" class="swal2-radio">Save Draft</label>',
    showCancelButton: true,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showCloseButton: false,
    confirmButtonText: "Send",
    confirmButtonAriaLabel: "Send",
    cancelButtonText: "Cancel",
    cancelButtonAriaLabel: "Cancel",
    preConfirm: () => {
      return [
        document.getElementById("mail-to").value,
        document.getElementById("mail-subject").value,
        document.getElementById("mail-body").value,
        document.getElementById("mail-attachment").value,
        document.getElementById("mail-option1").checked ? "instant" : "draft",
      ];
    },
  }).then((formValues) => {
    if (!formValues.isConfirmed) {
      // TODO: Save current data as a draft mail
      // Validate fields, if To and Subject fields are set, save as draft, else return
      return;
    }

    if (isBlank(formValues.value[0])) {
      Swal.fire(
        "Invalid Details!",
        "You must specify a valid 'To' value.",
        "warning"
      );
      return;
    }

    if (isBlank(formValues.value[1])) {
      Swal.fire(
        "Invalid Details!",
        "You must specify a valid 'Subject' value.",
        "warning"
      );
      return;
    }

    if (isBlank(formValues.value[2])) {
      Swal.fire(
        "Invalid Details!",
        "You must specify a valid 'Body' value.",
        "warning"
      );
      return;
    }

    var formData = new FormData();
    var mailObject = {
      To: formValues.value[0],
      IsDraft: formValues.value[4] == "draft" ? 1 : 0,
      IsTrash: 0,
      Subject: formValues.value[1],
      Body: formValues.value[2],
      HasAttachment: isBlank(formValues.value[3]) ? false : true,
    };

    if (!isBlank(formValues.value[3])) {
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
                document.location = "../Views/HomeView.php";
              }
            );
            break;
          case "-1":
            Swal.fire(result.ShortReason, result.Reason, result.Level).then(
              (value) => {
                document.location = "../Views/HomeView.php";
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

function addRow(rowHtml, tableId) {
  var inboxTable = document
    .getElementById(tableId)
    .getElementsByTagName("tbody")[0];
  var newRow = inboxTable.insertRow(inboxTable.rows.length);
  newRow.setAttribute("ondblclick", "onRowDoubleClicked(this);");
  newRow.setAttribute("onclick", "onRowClicked(this);");
  newRow.innerHTML = rowHtml;
}

function allowOnlySingleSelectedRow(tableId) {
  var inboxTableRows = document
    .getElementById(tableId)
    .getElementsByTagName("tbody")[0].rows;

  for (var i = 0; i < inboxTableRows.length; i++) {
    if (inboxTableRows[i].hasAttribute("class")) {
      inboxTableRows[i].removeAttribute("class");
    }
  }
}

function onRowDoubleClicked(row) {
  var index = row.rowIndex;
  displayEmailUi(index);

  // TODO: Display email ui
  // includes: Reply, delete, view attachments
}

function onRowClicked(row) {
  if (row.hasAttribute("class")) {
    row.removeAttribute("class");
    return;
  }

  allowOnlySingleSelectedRow("mailTable");
  row.setAttribute("class", "active-row");
}

function onSettingsButtonClicked() {
  console.log("settings bttn clicked");
}

function displayEmailUi(selectedIndex) {
  var mailUuid = getUuidOfSelectedRow(selectedIndex);

  if (mailUuid == null) {
    console.log("uuid can't be null.");
    return;
  }

  $.ajax({
    method: "POST",
    url: "../Controllers/HomeController.php",
    data: {
      requestType: "get_mail",
      uuid: mailUuid,
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      switch (result.Status) {
        case "-1":
          Swal.fire(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              //document.location = "../Views/HomeView.php?previousError=true";
              return;
            }
          );
          return;
        case "0":
          // draft fetch done
          if (result.Emails == null || result.Emails.length <= 0) {
            setTimeout(function () {
              Swal.fire({
                title: result.ShortReason,
                text: result.Reason,
                icon: result.Level,
                timer: 3000,
                timerProgressBar: true,
              });
            }, 100);

            return;
          }

          console.log(result.Emails);
          console.log(result.Emails[0].IsDraft);
          console.log(result.Emails[0].IsTrash);
          var attachmentHtml =
            result.Emails[0].AttachmentPath != ""
              ? "<b>Attachment:</b></br>" +
                "<img src=" +
                result.Emails[0].AttachmentPath +
                " style='width:150px;'>"
              : "";

          var draftedCheckBoxHtml =
            result.Emails[0].IsDraft == 0
              ? '<input type="checkbox" disabled="disabled"> Is Drafted <br/>'
              : '<input type="checkbox" checked="true" disabled="disabled"> Is Drafted <br/>';
          var trashedCheckBoxHtml =
            result.Emails[0].IsTrash == 0
              ? '<input type="checkbox" disabled="disabled"> Is Trashed <br/>'
              : '<input type="checkbox" checked="true" disabled="disabled"> Is Trashed <br/>';

          Swal.fire({
            title: "From: <u>" + result.Emails[0].From + "</u>",
            html:
              '<div style="text-align: left;" >' +
              "<b>" +
              result.Emails[0].Subject +
              " @ " +
              result.Emails[0].At +
              "<br/>" +
              "<p>" +
              result.Emails[0].Body +
              "</p>" +
              draftedCheckBoxHtml +
              trashedCheckBoxHtml +
              "<br/>" +
              attachmentHtml +
              "</div>",
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: "Quick Reply",
            cancelButtonText: "Delete",
          }).then((result) => {
            if (result.value) {
              // handle quick reply
            }

            // handle delete mail
          });

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

function trashOptions(index) {
  Swal.fire({
    text: "You can either Delete mail permanently or restore it.",
    icon: "info",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Delete",
    cancelButtonText: "Restore",
    showCloseButton: true,
  }).then((result) => {
    if (!result.isDismissed) {
      if (result.isConfirmed) {
        // delete true
        deleteMail(index);
        return;
      }
    }

    if (result.isDismissed && result.dismiss == "cancel") {
      // restore true
      restoreMail(index);
    }
  });
}

function restoreMail(index) {
  // mail uuid
  var uuid = getUuidOfSelectedRow(index);

  if (uuid == null) {
    console.log("uuid can't be null.");
    return;
  }

  $.ajax({
    method: "POST",
    url: "../Controllers/HomeController.php",
    data: {
      requestType: "restore_trash_mail",
      emailUuid: uuid,
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      switch (result.Status) {
        case "0":
          Swal.fire(result.ShortReason, result.Reason, result.Level);
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
}

function isBlank(str) {
  return !str || /^\s*$/.test(str);
}

// click event for each delete option click on row
function onDeleteAnchorClicked(anchor) {
  var index = anchor.parentNode.parentNode.rowIndex;
  switch (anchor.id) {
    case "trash-options":
      trashOptions(index);
      break;
    case "delete-draft":
      deleteMail(index);
      break;
    case "trash-inbox":
      trashMail(index);
      break;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  // load inbox emails by default as its the first selection
  getInboxMails();  
});
