<?php
require_once '../Core/Config.php';
require_once '../Core/UserModel.php';
require_once '../Common/Functions.php';
require_once '../Core/SessionCheck.php';
require_once '../Core/DatabaseManager.php';

if (Config::DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (!isset($_SESSION)) {
    session_start();
}

header("Content-Type", "application/json");
$Result = array(
    'ShortReason' => 'NA',
    'Reason' => 'NA',
    'Status' => '-1',
    'Level' => 'warning',
    'Emails' => ''
);

function SetResult($message, $reason, $status, $level, $emailArray = null)
{
    global $Result;
    $Result['ShortReason'] = $message;
    $Result['Reason'] = $reason;
    $Result['Status'] = $status;
    $Result['Level'] = $level;

    if (isset($emailArray)) {
        $Result['Emails'] = $emailArray;
    }
}

function OnDraftViewRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order to get requested emails.", "-1", "warning");
        return;
    }

    $Db = new Database;
    $response = $Db->GetUserDraftEmails(GetCurrentUserEmail());

    if ($response['Status'] == '-1') {
        if ($response['Count'] == 0) {
            SetResult("No mails found!", "You have no new mails.", "0", "success");
            return;
        }

        SetResult("Failed!", $response['Message'], "-1", "error");
        return;
    }

    if ($response['Count'] <= 0) {
        SetResult("No draft emails exist for you!", "Can't find any emails in your draft box.", "0", "success");
        return;
    }

    $_SESSION['Draft'] = $response['Emails'];
    SetResult("You have pending drafts!", "", "0", "success", $response['Emails']);
}

function OnTrashViewRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order to get requested emails.", "-1", "warning");
        return;
    }

    $Db = new Database;
    $response = $Db->GetUserTrashEmails(GetCurrentUserEmail());

    if ($response['Status'] == '-1') {
        if ($response['Count'] == 0) {
            SetResult("No mails found!", "You have no new mails.", "0", "success");
            return;
        }

        SetResult("Failed!", $response['Message'], "-1", "error");
        return;
    }

    if ($response['Count'] <= 0) {
        SetResult("No trash emails exist for you!", "Can't find any emails in your trash box.", "0", "success");
        return;
    }

    $_SESSION['Trash'] = $response['Emails'];
    SetResult("You have pending trash emails!", "", "0", "success", $response['Emails']);
}

function OnInboxViewRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order to get requested emails.", "-1", "warning");
        return;
    }

    $Db = new Database;
    $response = $Db->GetUserInboxEmails(GetCurrentUserEmail());

    if ($response['Status'] == '-1') {
        if ($response['Count'] == 0) {
            SetResult("No mails found!", "You have no new mails.", "0", "success");
            return;
        }

        SetResult("Failed!", $response['Message'], "-1", "error");
        return;
    }

    if ($response['Count'] <= 0) {
        SetResult("No emails exist for you!", "Can't find any emails in your inbox.", "0", "success");
        return;
    }

    $_SESSION['Inbox'] = $response['Emails'];
    SetResult("You have pending Mails!", "", "0", "success", $response['Emails']);
}

function OnTrashMailRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order delete emails.", "-1", "warning");
        return;
    }

    if (!isset($_POST['emailUuid'])) {
        SetResult("UUID isn't set!", "Please specify the mail UUID.", "-1", "error");
        return;
    }

    $Db = new Database;
    if ($Db->TrashMailWithUuid($_POST['emailUuid'])) {
        SetResult("Success!", "Mail trashed.", "0", "success");
        return;
    }

    SetResult("Failed!", "Failed to trash email.", "-1", "error");
}

function OnDraftMailRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order to draft emails.", "-1", "warning");
        return;
    }

    if (!isset($_POST['emailUuid'])) {
        SetResult("UUID isn't set!", "Please specify the mail UUID.", "-1", "error");
        return;
    }

    $Db = new Database;
    if ($Db->DraftMailWithUuid($_POST['emailUuid'])) {
        SetResult("Success!", "Mail drafted.", "0", "success");
        return;
    }

    SetResult("Failed!", "Failed to draft email.", "-1", "error");
}

function OnComposeRequestReceived()
{
    if (!isset($_POST['mailObject'])) {
        SetResult("Mail object is empty.", "Incompleted request: 'mailObject'", "-1", "error");
        return;
    }

    $_POST['mailObject'] = (array) json_decode($_POST['mailObject']);

    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order to execute this request.", "-1", "warning");
        return;
    }

    $attachmentFilePath = "";
    if (isset($_POST['mailObject']) && $_POST['mailObject']['HasAttachment']) {
        $serverAssignedPath = GetAndProcessFile('file');
        if (isset($_FILES) && isset($_FILES['file'])) {
            if (isset($serverAssignedPath) && !empty($serverAssignedPath)) {
                $moveResult = move_uploaded_file(
                    $_FILES['file']['tmp_name'],
                    $serverAssignedPath
                );

                $attachmentFilePath = $moveResult ? $serverAssignedPath : "";
            }
        }
    }

    $_POST['mailObject']['AttachmentFilePath'] = $attachmentFilePath;
    $_POST['mailObject']['From'] = GetCurrentUserEmail();

    if ($_POST['mailObject']['To'] == $_POST['mailObject']['From']) {
        SetResult("Failed!", "You cannot send mail to yourself.", "-1", "error");
        return;
    }

    $Db = new Database;
    if (!$Db->IsExistingUser($_POST['mailObject']['To'])) {
        SetResult("Failed!", "Entered email is invalid or such a user doesn't exist.", "-1", "error");
        return;
    }

    if ($Db->ComposeEmail($_POST['mailObject'])) {
        SetResult("Mail Composed!", $_POST['mailObject']['IsDraft'] == '1' ? "You can view this email in the 'Draft' Tab!" : "You can view this mail in the 'Send' Tab!", "0", "success");
        return;
    }

    SetResult("Failed!", "Failed to compose mail.", "-1", "error");
}

function OnTrashMailDeleteRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order delete emails.", "-1", "warning");
        return;
    }

    if (!isset($_POST['emailUuid'])) {
        SetResult("UUID isn't set!", "Please specify the mail UUID.", "-1", "error");
        return;
    }

    $Db = new Database;
    if ($Db->DeleteMailWithUuid($_POST['emailUuid'])) {
        SetResult("Success!", "Mail deleted.", "0", "success");
        return;
    }

    SetResult("Failed!", "Failed to delete email.", "-1", "error");
}

function OnTrashMailRestoreRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order delete emails.", "-1", "warning");
        return;
    }

    if (!isset($_POST['emailUuid'])) {
        SetResult("UUID isn't set!", "Please specify the mail UUID.", "-1", "error");
        return;
    }

    $Db = new Database;
    if ($Db->RestoreTrashUserMailWithUuid($_POST['emailUuid'])) {
        SetResult("Success!", "Mail Restored.", "0", "success");
        return;
    }

    SetResult("Failed!", "Failed to restore email.", "-1", "error");
}

function GetAndProcessFile($requestFileName)
{
    if (!isset($requestFileName)) {
        return "";
    }

    if (isset($_FILES) && isset($_FILES[$requestFileName])) {
        if ($_FILES[$requestFileName]['error'] == 0 && $_FILES[$requestFileName]['size'] > 0) {
            if (file_exists('../includes/images/Attachments/' . $_FILES[$requestFileName]['name'])) {
                $split = explode('.', $_FILES[$requestFileName]['name']);
                if (count($split) != 2) {
                    // not possible
                    return '../includes/images/Attachments/' . $_FILES[$requestFileName]['name'];
                }

                return '../includes/images/Attachments/' . $split[0] . ' (' . rand(1, 500) . ')' . '.' . $split[1];
            }

            return '../includes/images/Attachments/' . $_FILES[$requestFileName]['name'];
        }
    }

    return "";
}

function OnDraftMailCheckRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order delete emails.", "-1", "warning");
        return;
    }

    if (!isset($_POST['emailUuid'])) {
        SetResult("UUID isn't set!", "Please specify the mail UUID.", "-1", "error");
        return;
    }

    $Db =  new Database;
    if ($Db->IsDraftMail($_POST['emailUuid'])) {
        SetResult("Success", "Mail is draft.", "0", "success");
        return;
    }

    SetResult("Fail!", "Mail isn't draft or request failed.", "-1", "info");
}

function OnSendViewRequestReceived(){
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order to get requested emails.", "-1", "warning");
        return;
    }

    $Db = new Database;
    $response = $Db->GetUserSendEmails(GetCurrentUserEmail());

    if ($response['Status'] == '-1') {
        if ($response['Count'] == 0) {
            SetResult("No mails found!", "You didn't send any emails yet.", "0", "success");
            return;
        }

        SetResult("Failed!", $response['Message'], "-1", "error");
        return;
    }

    if ($response['Count'] <= 0) {
        SetResult("No send emails exist!", "You havn't send any emails yet.", "0", "success");
        return;
    }

    $_SESSION['Inbox'] = $response['Emails'];
    SetResult("You have pending Mails!", "", "0", "success", $response['Emails']);
}

function OnGetMailRequestReceived(){
    if(!isset($_POST['uuid'])){
        SetResult("Failed!", "Uuid is invalid.", "-1", "error");
        return;
    }

    $Db = new Database;
    $response = $Db->GetMailWithUuid($_POST['uuid']);

    if ($response['Status'] == '-1') {
        if ($response['Count'] == 0) {
            SetResult("No mails found!", "Can't find any mail with this uuid.", "0", "success");
            return;
        }

        SetResult("Failed!", $response['Message'], "-1", "error");
        return;
    }

    if ($response['Count'] <= 0) {
        SetResult("Doesn't exist!", "A mail with this uuid doesn't exist!", "0", "success");
        return;
    }

    $_SESSION['Trash'] = $response['Emails'];
    SetResult("Success!", "", "0", "success", $response['Emails']);
}

function OnUpdateMailRequestReceived(){
    if(!isset($_POST['uuid'])){
        SetResult("Failed!", "Uuid is invalid.", "-1", "error");
        return;
    }

    if(!isset($_POST['nBody']) || !isset($_POST['nSubject'])){
        SetResult("Failed!", "Failed to update message, will be as is.", "-1", "error");
        return;
    }

    $Db = new Database;
    $response = $Db->GetMailWithUuid($_POST['uuid']);

    if ($response['Status'] == '-1') {
        if ($response['Count'] == 0) {
            SetResult("No mails found!", "Can't find any mail with this uuid.", "0", "success");
            return;
        }

        SetResult("Failed!", $response['Message'], "-1", "error");
        return;
    }

    if($response['Count'] > 1){
        SetResult("Failed!", "There is more than one mail with the same unique id. Datebase has been modified!", "-1", "error");
        return;
    }

    if(!$Db->UpdateMailWithUuid($_POST['uuid'], $_POST['nSubject'], $_POST['nBody'])){
        SetResult("Failed!", "Error occured while updating mail data.", "-1", "error");
        return;
    }

    SetResult("Success!", "Mail updated!", "0", "success");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    SetResult("Invalid request type.", "Expected: POST", "-1", "error");
    echo json_encode($Result);
    exit();
}

// since its ajax, we should 'print' the return value as its an http request and there is no concept of datatype in these requests
// only plain raw string formate, so return as string of the respective type and parse on client side
switch ($_POST['requestType']) {
    case "draft_view":
        OnDraftViewRequestReceived();
        break;
    case "trash_view":
        OnTrashViewRequestReceived();
        break;
    case "inbox_view":
        OnInboxViewRequestReceived();
        break;
    case "send_view":
        OnSendViewRequestReceived();
        break;
    case "trash_mail":
        OnTrashMailRequestReceived();
        break;
    case "delete_trash_mail":
        OnTrashMailDeleteRequestReceived();
        break;
    case "restore_trash_mail":
        OnTrashMailRestoreRequestReceived();
        break;
    case "draft_mail":
        OnDraftMailRequestReceived();
        break;
    case "compose":
        OnComposeRequestReceived();
        break;
    case "draft_check":
        OnDraftMailCheckRequestReceived();
        break;
    case "get_mail":
        OnGetMailRequestReceived();
        break;
    case "update_mail":
        OnUpdateMailRequestReceived();
        break;
    default:
        break;
}

echo json_encode($Result);
?>