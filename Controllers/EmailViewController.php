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
    'Level' => 'warning'
);

function SetResult($message, $reason, $status, $level)
{
    global $Result;
    $Result['ShortReason'] = $message;
    $Result['Reason'] = $reason;
    $Result['Status'] = $status;
    $Result['Level'] = $level;
}

function OnSetRequestReceived(){
    if(!isset($_POST['e'])){
        SetResult("Invalid Mail data!", "Mail data is not set.", "-1", "error");
        return;
    }

    $_SESSION['selectedMail'] = $_POST['e'];
    SetResult("Success!", "Mail set!", "0", "success");    
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    SetResult("Invalid request type.", "Expected: POST", "-1", "error");
    echo json_encode($Result);
    exit();
}

// since its ajax, we should 'print' the return value as its an http request and there is no concept of datatype in these requests
// only plain raw string formate, so return as string of the respective type and parse on client side
switch ($_POST['requestType']) {
    case "set_selected_row":
        OnSetRequestReceived();
        break;
    default:
        SetResult("Invalid!", "Unknown request type.", "-1", "error");
        break;
}

echo json_encode($Result);
?>