<?php
require_once 'Config.php';
require_once 'UserModel.php';
require_once 'SessionCheck.php';

if (Config::DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    ini_set("log_errors", TRUE);
    ini_set('error_log', Config::ERROR_LOG_PATH);
}

class Database
{
    public $Connection;
    public $QueriesExecutedCount;
    public $Config;

    public function __construct()
    {       
        $this->Config = new Config;
        $this->Connect();
    }

    function __destruct()
    {
        if (!isset($this->Connection)) {
            return;
        }

        mysqli_close($this->Connection);
    }

    private function Connect()
    {        
        $this->Connection = mysqli_connect(Config::HOST, Config::DB_USER_NAME, Config::DB_USER_PASSWORD) or die("Failed to connect with database");
        mysqli_select_db($this->Connection, Config::DB_NAME);

        // run initial setups
        // create table
        // insert sample data
    }

    private function ExecuteQuery($query)
    {
        if (!isset($query)) {
            throw new Exception("query is empty!");
        }

        if (!mysqli_ping($this->Connection)) {
            throw new Exception("Connection lost with database, Reconnection failed as well.");
        }

        $this->QueriesExecutedCount++;
        return mysqli_query($this->Connection, $query);
    }

    public function RegisterUser($postArray, $avatarPath, $isAdmin)
    {
        if (!isset($postArray) || !isset($avatarPath)) {
            return false;
        }

        error_log(json_encode($postArray));
        $sqlQuery = "INSERT INTO " . ($isAdmin ? Config::ADMIN_TABLE_NAME : Config::USER_TABLE_NAME) . "(`EmailID`, `UserName`, `Password`, `SecurityQuestion`, `SecurityAnswer`, `AvatarPath`, `PhoneNumber`) VALUES ('" . $postArray['email'] . "','" . $postArray['username'] . "','" . $postArray['password'] . "','" . $postArray['secquest'] . "','" . $postArray['secans'] . "','" . $avatarPath . "','" . $postArray['pnumber'] . "');";
        error_log($sqlQuery);
        return $this->ExecuteQuery($sqlQuery);
    }

    public function IsExistingUser($email)
    {
        if (empty($email)) {
            return false;
        }

        return $this->IsExistInAdminTable($email) || $this->IsExistInUserTable($email);
    }

    public function IsExistInAdminTable($email)
    {
        if (empty($email)) {
            return false;
        }

        $sqlQuery = "SELECT * FROM " . Config::ADMIN_TABLE_NAME . " WHERE EmailID='" . $email . "';";        
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $rowsCount = mysqli_num_rows($exeResult);
            mysqli_free_result($exeResult);

            if ($rowsCount > 0) {
                return true;
            }
        }

        return false;
    }

    public function IsExistInUserTable($email)
    {
        if (empty($email)) {
            return false;
        }

        $sqlQuery = "SELECT * FROM " . Config::USER_TABLE_NAME . " WHERE EmailID='" . $email . "';";
       
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $rowsCount = mysqli_num_rows($exeResult);
            mysqli_free_result($exeResult);
            if ($rowsCount > 0) {
                return true;
            }
        }

        return false;
    }

    public function DeleteUserMailWithTitle($userEmail, $emailTitle){
        if(!isset($userEmail) || !isset($emailTitle)){
            return false;
        }

        $sqlQuery = "DELETE FROM emails WHERE SendTo='$userEmail' AND Title='$emailTitle';";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {            
            mysqli_free_result($exeResult);
            return true;
        }

        return false;
    }

    public function DoesMailWithUuidExist($userEmail, $uuid){
        if(!isset($uuid) || !isset($userEmail)){
            return false;
        }

        $sqlQuery = $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail' AND MailID='$uuid';";
        if($exeResult = $this->ExecuteQuery($sqlQuery)){
            $rowCount = mysqli_num_rows($exeResult);
            mysqli_free_result($exeResult);
            return $rowCount >= 1;
        }

        return false;
    }

    public function DraftUserMailWithUuid($userEmail, $uuid){
        if(!isset($uuid) || !isset($userEmail)){
            return false;
        }

        if(!$this->DoesMailWithUuidExist($userEmail, $uuid)){
           return false;
        }

        $sqlQuery = "UPDATE mails SET IsDraft=1 WHERE SendTo='$userEmail' AND MailID='$uuid';";        
        return $this->ExecuteQuery($sqlQuery);
    }

    public function DeleteUserMailWithUuid($userEmail, $uuid){
        if(!isset($uuid) || !isset($userEmail)){
            return false;
        }

        if(!$this->DoesMailWithUuidExist($userEmail, $uuid)){
            return false;
        }

        $sqlQuery = $sqlQuery = "DELETE FROM emails WHERE SendTo='$userEmail' AND MailID='$uuid';";
        return $this->ExecuteQuery($sqlQuery);
    }

    public function TrashUserMailWithUuid($userEmail, $uuid){
        if(!isset($uuid) || !isset($userEmail)){
            return false;
        }

        if(!$this->DoesMailWithUuidExist($userEmail, $uuid)){
            return false;
        }

        $sqlQuery = "UPDATE emails SET IsTrash=1 WHERE SendTo='$userEmail' AND MailID='$uuid';";
        return $this->ExecuteQuery($sqlQuery);
    }

    public function FetchMailWithUuid($userEmail, $uuid){
        $result = array(
            'Status'=> '-1',
            'Message'=> 'NA',
            'Count' => 0,
            'Emails' => array()
        );

        if(!isset($uuid) || !isset($userEmail)){
            return $result;
        }

        $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail' AND MailID='$uuid';";
        if($exeResult = $this->ExecuteQuery($sqlQuery)){
            $result['Count'] = mysqli_num_rows($exeResult);
            $result['Status'] = '0';
            $result['Message'] = 'Success';

            if($result['Count'] <= 0){
                $result['Status'] = '-1';
                $result['Message'] = 'No emails exist.';
                return $result;
            }

            while($row = mysqli_fetch_object($exeResult)){                
                $emailObj = array();
                $emailObj['To'] = $row->SendTo ?? "";
                $emailObj['From'] = $row->ReceviedFrom ?? "";
                $emailObj['At'] = $row->ReceivedTime ?? "";
                $emailObj['IsDraft'] = $row->IsDraft ?? "";
                $emailObj['IsTrash'] = $row->IsTrash ?? "";
                $emailObj['Title'] = $row->Title ?? "";
                $emailObj['Subject'] = $row->Subject ?? "";
                $emailObj['Body'] = $row->Body ?? "";
                $emailObj['MailID'] = $row->MailID ?? "";
                $emailObj['AttachmentPath'] = $row->AttachmentPath ?? "";
                array_push($result['Emails'], $emailObj);
            }

            mysqli_free_result($exeResult);
        }
    }

    public function ComposeEmail($userEmail, $mailObj){
        if(!isset($userEmail) || !isset($mailObj)){
            return false;
        }

        $sqlQuery = "INSERT INTO emails (SendTo, ReceivedFrom, IsDraft, IsTrash, Title, Subject, Body, AttachmentPath, MailID) VALUES ('" . $mailObj['To'] . "','" . $mailObj['From'] . "','" . $mailObj['IsDraft'] . "','" . $mailObj['IsTrash'] . "','" . $mailObj['Title'] . "','" . $mailObj['Subject'] . "','" . $mailObj['Body'] . "','" . $mailObj['AttachmentFilePath'] . "', UUID_SHORT());";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {            
            mysqli_free_result($exeResult);
            return true;
        }

        return false;
    }

    public function GetUserDraftEmails($userEmail){
        return $this->GetUserEmails($userEmail, true, false);
    }

    public function GetUserTrashEmails($userEmail){
        return $this->GetUserEmails($userEmail, false, true);
    }

    public function GetUserInboxEmails($userEmail){
        return $this->GetUserEmails($userEmail, false, false);
    }

    public function GetUserEmails($userEmail, $onlyDraft, $onlyTrash){
        $response = array(
            'Status' => '-1',
            'Message' => 'Invalid',
            'Count' => 0,
            'Emails' => array()
        );

        if(!isset($userEmail)){
            return $response;
        }

        if(!IsUserLoggedIn()){
            return $response;
        }

        $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail';";

        if($onlyDraft){
            $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail' AND IsDraft=1;";
        }

        if($onlyTrash){
            $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail' AND IsTrash=1;";
        }

        if(!$onlyDraft && !$onlyTrash){
            $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail' AND IsTrash=0 AND IsDraft=0;";
        }

        if($exeResult = $this->ExecuteQuery($sqlQuery)){
            $response['Count'] = mysqli_num_rows($exeResult);
            $response['Status'] = '0';
            $response['Message'] = 'Success';

            if($response['Count'] <= 0){
                $response['Status'] = '-1';
                $response['Message'] = 'No emails exist for current mail folder.';
                return $response;
            }

            while($row = mysqli_fetch_object($exeResult)){                
                $emailObj = array();
                $emailObj['To'] = $row->SendTo ?? "";
                $emailObj['From'] = $row->ReceivedFrom ?? "";
                $emailObj['At'] = $row->ReceivedTime ?? "";
                $emailObj['IsDraft'] = $row->IsDraft ?? "";
                $emailObj['IsTrash'] = $row->IsTrash ?? "";
                $emailObj['Title'] = $row->Title ?? "";
                $emailObj['Subject'] = $row->Subject ?? "";
                $emailObj['Body'] = $row->Body ?? "";
                $emailObj['AttachmentPath'] = $row->AttachmentPath ?? "";
                $emailObj['MailID'] = $row->MailID ?? "";
                array_push($response['Emails'], $emailObj);
            }

            mysqli_free_result($exeResult);
            return $response;
        }

        $response['Status'] = '-1';
        $response['Message'] = 'Database connection failed.';
        return $response;
    }

    public function LoginUser($email, $password, $isAdminLogin)
    {
        $resultArray = array();

        if (!isset($email) || !isset($password)) {
            $resultArray["isError"] = true;
            $resultArray["errorMessage"] = "Email or Password is empty!";
            return $resultArray;
        }

        $sqlQuery = "SELECT * FROM " . ($isAdminLogin ? Config::ADMIN_TABLE_NAME : Config::USER_TABLE_NAME) . " WHERE EmailID='" . $email . "';";

        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $resultArray["isExist"] = mysqli_num_rows($exeResult) > 0;

            if (!$resultArray["isExist"]) {
                $resultArray["isError"] = false;
                $resultArray["errorMessage"] = "No such user exist!";
                return $resultArray;
            }

            error_log(mysqli_num_rows($exeResult) > 0);
            $resultObject = new LoggedInUserResult(true, $isAdminLogin);
            while ($obj = mysqli_fetch_object($exeResult)) {
                if (isset($obj->EmailID)) {
                    $resultObject->Email = $obj->EmailID;
                }

                if (isset($obj->Password)) {
                    if (!password_verify($password, $obj->Password)) {
                        $resultArray["isError"] = true;
                        $resultArray["errorMessage"] = "Password is incorrect";
                        return $resultArray;
                    }

                    $resultObject->Password = $obj->Password;
                }

                if (isset($obj->UserName)) {
                    $resultObject->UserName = $obj->UserName;
                }

                if (isset($obj->Id)) {
                    $resultObject->Id = $obj->Id;
                }

                if (isset($obj->DateCreated)) {
                    $resultObject->DateCreated = $obj->DateCreated;
                }

                if (isset($obj->SecurityQuestion)) {
                    $resultObject->SecurityQuestion = $obj->SecurityQuestion;
                }

                if (isset($obj->SecurityAnswer)) {
                    $resultObject->SecurityAnswer = $obj->SecurityAnswer;
                }

                if (isset($obj->AvatarPath)) {
                    $resultObject->AvatarPath = $obj->AvatarPath;
                }

                if (isset($obj->PhoneNumber)) {
                    $resultObject->PhoneNumber = $obj->PhoneNumber;
                }
            }

            mysqli_free_result($exeResult);
            $resultArray["isError"] = false;
            unset($resultArray["errorMessage"]);
            $resultArray["resultObj"] = serialize($resultObject);
            return $resultArray;
        }

        $resultArray["isError"] = true;
        $resultArray["errorMessage"] = "Query execution failed!";
        return $resultArray;
    }
}

?>