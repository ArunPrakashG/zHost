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
        if($response['Count'] == 0){
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
        if($response['Count'] == 0){
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

function OnTrashMailRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order delete emails.", "-1", "warning");
        return;
    }

    if(!isset($_POST['emailUuid'])){
        SetResult("UUID isn't set!", "Please specify the mail UUID.", "-1", "error");
        return;
    }

    $Db = new Database;
    if($Db->TrashUserMailWithUuid(GetCurrentUserEmail(), $_POST['emailUuid'])){
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

    if(!isset($_POST['emailUuid'])){
        SetResult("UUID isn't set!", "Please specify the mail UUID.", "-1", "error");
        return;
    }

    $Db = new Database;
    if($Db->DraftUserMailWithUuid(GetCurrentUserEmail(), $_POST['emailUuid'])){
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

    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order to get requested emails.", "-1", "warning");
        return;
    }

    $attachmentFilePath = "";
    if (isset($_POST['mailObject']) && $_POST['mailObject']['HasAttachment']) {
        $serverAssignedPath = GetAndProcessFile('attachment');        
        if (isset($_FILES) && isset($_FILES['attachment'])) {
            if (isset($serverAssignedPath) && !empty($serverAssignedPath)) {
                $moveResult = move_uploaded_file(
                    $_FILES['attachment']['tmp_name'],
                    $serverAssignedPath
                );

                $attachmentFilePath = $moveResult ? $serverAssignedPath : "";
            }
        }
    }

    $_POST['mailObject']['AttachmentFilePath'] = $attachmentFilePath;
    $_POST['mailObject']['From'] = GetCurrentUserEmail();

    $Db = new Database;
    if($Db->ComposeEmail($_POST['mailObject'])){
        SetResult("Success!", "Mail composed.", "0", "success");
        return;
    }

    SetResult("Failed!", "Failed to compose.", "-1", "error");
}

function OnInboxRequestReceived()
{
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order to get requested emails.", "-1", "warning");
        return;
    }

    $Db = new Database;
    $response = $Db->GetUserInboxEmails(GetCurrentUserEmail());

    if ($response['Status'] == '-1') {
        if($response['Count'] == 0){
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

function OnTrashMailDeleteRequestReceived(){
    if (!IsUserLoggedIn() || GetCurrentUserEmail() == null) {
        SetResult("You are not logged in!", "Please login again in order delete emails.", "-1", "warning");
        return;
    }

    if(!isset($_POST['emailUuid'])){
        SetResult("UUID isn't set!", "Please specify the mail UUID.", "-1", "error");
        return;
    }

    $Db = new Database;
    if($Db->DeleteUserMailWithUuid(GetCurrentUserEmail(), $_POST['emailUuid'])){
        SetResult("Success!", "Mail deleted.", "0", "success");
        return;
    }

    SetResult("Failed!", "Failed to delete email.", "-1", "error");
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
    case "trash_mail":
        OnTrashMailRequestReceived();
        break;
    case "delete_trash_mail":
        OnTrashMailDeleteRequestReceived();
        break;
    case "draft_mail":
        OnDraftMailRequestReceived();
        break;
    case "inbox":
        OnInboxRequestReceived();
        break;
    case "compose":
        OnComposeRequestReceived();
        break;
    default:
        break;
}

echo json_encode($Result);
?>