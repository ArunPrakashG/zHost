function logoutUser() {
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
          swal(
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

  swal({
    title: "Are you sure?",
    text: "The mail will be deleted permentantly from your account.",
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
        text: "Delete",
        value: true,
        visible: true,
        className: "",
        closeModal: true,
      },
    },
  }).then((isConfirmed) => {
    if (isConfirmed) {
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
        success: function () {
          switch (result.Status) {
            case "0":
              swal(result.ShortReason, result.Reason, result.Level).then(
                (value) => {
                  // if success, delete from ui
                  document.getElementById("mailTable").deleteRow(rowIndex);
                }
              );
              break;
            case "-1":
              swal(
                result.ShortReason,
                result.Reason,
                result.Level
              ).then((value) => {});
              break;
          }
        },
        error: function (e) {
          swal(
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
          swal(result.ShortReason, result.Reason, result.Level).then(
            (value) => {
              // if success, delete from ui
              document.getElementById("mailTable").deleteRow(rowIndex);
            }
          );
          break;
        case "-1":
          swal(
            result.ShortReason,
            result.Reason,
            result.Level
          ).then((value) => {});
          break;
      }
    },
    error: function (e) {
      swal(
        "Request Exception!",
        "Exception occured during AJAX Request. Check console for more info.",
        "error"
      );
      console.log(e);
    },
  });
}

function getInboxMails() {
  $("#mailTable tbody tr").remove();

  $.ajax({
    method: "POST",
    url: "../Controllers/HomeController.php",
    data: {
      requestType: "inbox",
    },
    cache: false,
    dataType: "json",
    success: function (result) {
      switch (result.Status) {
        case "-1":
          swal(result.ShortReason, result.Reason, result.Level).then(
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
              swal(
                "All caught up!",
                "No new mails found in current mail folder.",
                {
                  buttons: false,
                  timer: 3000,
                }
              );
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
              mail.Subject +
              "</td>" +
              '<td class="table-row-field">' +
              mail.At +
              "</td>" +
              '<td class="table-row-field">' +
              '<a class="deletebttn" id="trash-inbox" onclick="onDeleteAnchorClicked(this);">Trash</a>' +
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
      swal(
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
      console.log(result);
      switch (result.Status) {
        case "-1":
          swal(result.ShortReason, result.Reason, result.Level).then(
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
              swal(
                "All caught up!",
                "No new mails found in current mail folder.",
                {
                  buttons: false,
                  timer: 3000,
                }
              );
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
              mail.Subject +
              "</td>" +
              '<td class="table-row-field">' +
              mail.At +
              "</td>" +
              '<td class="table-row-field">' +
              '<a class="deletebttn" id="trash-draft" onclick="onDeleteAnchorClicked(this);">Trash</a>' +
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
      swal(
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
      console.log(result);
      switch (result.Status) {
        case "-1":
          swal(result.ShortReason, result.Reason, result.Level).then(
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
              swal(
                "All caught up!",
                "No new mails found in current mail folder.",
                {
                  buttons: false,
                  timer: 3000,
                }
              );
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
              mail.Subject +
              "</td>" +
              '<td class="table-row-field">' +
              mail.At +
              "</td>" +
              '<td class="table-row-field">' +
              '<a class="deletebttn" id="del-trash" onclick="onDeleteAnchorClicked(this);">Delete</a>' +
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
      swal(
        "Request Exception!",
        "Exception occured during AJAX Request. Check console for more info.",
        "error"
      );
      console.log(e);
    },
  });
}

function draftMail(rowIndex) {}

function onSettingsButtonClicked() {
  console.log("settings bttn clicked");
}

function onComposeButtonClicked() {
  console.log("compose bttn clicked");
}

function composeMail() {}

function addRow(rowHtml, tableId) {
  var inboxTable = document
    .getElementById(tableId)
    .getElementsByTagName("tbody")[0];
  var newRow = inboxTable.insertRow(inboxTable.rows.length);
  newRow.setAttribute("onclick", "onRowClicked(this);");
  newRow.innerHTML = rowHtml;
}

function onRowClicked(row) {
  row.setAttribute("class", "active-row");
  var index = row.rowIndex;
  console.log("Selected row: " + index);

  // TODO: Display email ui
  // includes: Reply, delete, view attachments
}

// click event for each delete option click on row
function onDeleteAnchorClicked(anchor) {
  console.log("Click registered for anchor: " + anchor.id);
  switch (anchor.id) {
    case "del-trash":
      var index = anchor.parentNode.parentNode.rowIndex;
      console.log("Delete requested for row: " + index);
      deleteMail(index);
      break;
    case "trash-draft":
      var index = anchor.parentNode.parentNode.rowIndex;
      console.log("trash requested for row: " + index);
      trashMail(index);
      break;
    case "trash-inbox":
      var index = anchor.parentNode.parentNode.rowIndex;
      console.log("trash requested for row: " + index);
      trashMail(index);
      break;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  console.log("document ready");

  // load inbox emails by default as its the first selection
  getInboxMails();
});
