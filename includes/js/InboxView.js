var currentActiveIndex = 0;

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
                  document.location = "../Views/InboxView.php";
                }
              );
              return;
            case "0":
              // logout done
              Swal.fire(result.ShortReason, result.Reason, result.Level).then(
                (value) => {
                  let timerInterval;
                  Swal.fire({
                    title: "Please wait...",
                    html:
                      "Redirecting you to login page in <b></b> milliseconds.",
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
              window.location.reload();
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
  currentActiveIndex = 0;
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
              //document.location = "../Views/InboxView.php?previousError=true";
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
            addRow(generateRowHtml(i, mail), "mailTable");
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

function generateRowHtml(indexer, mail) {
  if (mail == null) {
    setTimeout(function () {
      Swal.fire({
        title: "Failed!",
        text: "Failed internally.",
        timer: 3000,
        timerProgressBar: true,
      });
    }, 100);
    return "";
  }

  return (
    '<td class="table-row-field">' +
    indexer +
    1 +
    "</td>" +
    '<td class="table-row-field hidden" id="mailUuid">' +
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
    getJsDateTime(mail.At) +
    "</td>" +
    '<td class="table-row-field">' +
    '<button class="deletebttn" id="trash-inbox" onclick="onDeleteAnchorClicked(this);">Trash</button>' +
    "</td>"
  );
}

function getJsDateTime(timeStamp) {
  if (timeStamp == null) {
    return "";
  }

  if (!moment(timeStamp).isValid()) {
    return "";
  }

  return moment(timeStamp).fromNow();
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
      // Validate fields, if To and Subject fields are set, save as draft, else return
      if (
        !formValues.value ||
        isBlank(formValues.value[0]) ||
        isBlank(formValues.value[1])
      ) {
        return;
      }

      var formData = new FormData();
      var mailObject = {
        To: formValues.value[0],
        IsDraft: 1,
        IsTrash: 0,
        Subject: formValues.value[1],
        Body: formValues.value[2] ?? "",
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
              Swal.fire("Saved!", "Mail saved as draft!", result.Level).then(
                (value) => {
                  document.location = "../Views/InboxView.php";
                }
              );
              break;
            case "-1":
              Swal.fire(
                "Failed to save mail",
                result.Reason,
                result.Level
              ).then((value) => {
                document.location = "../Views/InboxView.php";
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

  quickReplySendTo = "";
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
              //document.location = "../Views/InboxView.php?previousError=true";
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

          quickReplySendTo = result.Emails[0].From;
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
            cancelButtonText: "Trash",
            showDenyButton: true,
            denyButtonText: "Edit",
          }).then((iResult) => {
            if (iResult.isDismissed) {
              switch (iResult.dismiss) {
                case "backdrop":
                case "close":
                case "esc":
                  return;
                case "cancel":
                  // handle delete mail
                  trashMail(selectedIndex);
                  break;
              }
            }

            if (iResult.isConfirmed) {
              // handle quick reply
              quickReply(quickReplySendTo);
            }

            if (iResult.isDenied) {
              // handle mail edit
              quickEdit(result.Emails[0]);
            }
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

function quickReply(sendTo) {
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
                location.reload();
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

function isBlank(str) {
  return !str || /^\s*$/.test(str);
}

// click event for each delete option click on row
function onDeleteAnchorClicked(anchor) {
  var index = anchor.parentNode.parentNode.rowIndex;
  switch (anchor.id) {
    case "trash-inbox":
      trashMail(index);
      break;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  // load inbox emails by default as its the first selection
  $("#mailTable tbody tr").remove(0);
  getInboxMails();
});
