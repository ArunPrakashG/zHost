<?php
require_once 'Config.php';
require_once 'UserModel.php';
require_once 'SessionCheck.php';

if (Config::DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set("log_errors", TRUE);
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
        $this->Connection = mysqli_connect(Config::HOST, Config::DB_USER_NAME, Config::DB_USER_PASSWORD) or exit("Failed to connect with database");
        mysqli_select_db($this->Connection, Config::DB_NAME);
    }

    public function ExecuteQuery($query)
    {
        if (!isset($query)) {
            throw new Exception("query is empty!");
        }

        if (!isset($this->Connection)) {
            throw new Exception("Database connection failed.");
        }

        if (!mysqli_ping($this->Connection)) {
            throw new Exception("Connection lost with database, Reconnection failed as well.");
        }

        $this->QueriesExecutedCount++;
        return mysqli_query($this->Connection, $query);
    }

    public function RegisterUser($postArray, $avatarPath, $isAdmin, $withExistingCheck = false)
    {
        if (!isset($postArray) || !isset($avatarPath)) {
            return false;
        }

        if ($withExistingCheck) {
            if ($this->IsExistingUser($postArray['email'])) {
                return false;
            }
        }

        $sqlQuery = "INSERT INTO " . ($isAdmin ? Config::ADMIN_TABLE_NAME : Config::USER_TABLE_NAME) . "(`EmailID`, `UserName`, `Password`, `SecurityQuestion`, `SecurityAnswer`, `AvatarPath`, `PhoneNumber`) VALUES ('" . $postArray['email'] . "','" . $postArray['username'] . "','" . $postArray['password'] . "','" . $postArray['secquest'] . "','" . $postArray['secans'] . "','" . $avatarPath . "','" . $postArray['pnumber'] . "');";
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

    public function DoesMailWithUuidExist($uuid)
    {
        if (!isset($uuid)) {
            return false;
        }

        $sqlQuery = $sqlQuery = "SELECT * FROM emails WHERE MailID='$uuid';";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $rowCount = mysqli_num_rows($exeResult);
            mysqli_free_result($exeResult);
            return $rowCount >= 1;
        }

        return false;
    }

    public function DraftMailWithUuid($uuid)
    {
        if (!isset($uuid)) {
            return false;
        }

        if (!$this->DoesMailWithUuidExist($uuid)) {
            return false;
        }

        $sqlQuery = "UPDATE mails SET IsDraft=1 WHERE MailID='$uuid';";
        return $this->ExecuteQuery($sqlQuery);
    }

    public function DeleteMailWithUuid($uuid)
    {
        if (!isset($uuid)) {
            return false;
        }

        if (!$this->DoesMailWithUuidExist($uuid)) {
            return false;
        }

        $sqlQuery = $sqlQuery = "DELETE FROM emails WHERE MailID='$uuid';";
        return $this->ExecuteQuery($sqlQuery);
    }

    public function TrashMailWithUuid($uuid)
    {
        if (!isset($uuid)) {
            return false;
        }

        if (!$this->DoesMailWithUuidExist($uuid)) {
            error_log("mail doesnt exist");
            return false;
        }

        $sqlQuery1 = "UPDATE emails SET IsTrash=1 WHERE MailID='$uuid';";
        return $this->ExecuteQuery($sqlQuery1);
    }

    public function RestoreTrashUserMailWithUuid($uuid)
    {
        if (!isset($uuid)) {
            return false;
        }

        if (!$this->DoesMailWithUuidExist($uuid)) {
            return false;
        }

        $sqlQuery = "UPDATE emails SET IsTrash=0 WHERE MailID='$uuid';";
        return $this->ExecuteQuery($sqlQuery);
    }

    public function UpdateUserPassword($userEmail, $newPassword)
    {
        if (!isset($userEmail) || !isset($newPassword)) {
            return false;
        }

        if (!$this->IsExistingUser($userEmail)) {
            return false;
        }

        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sqlQuery = "UPDATE users SET Password='$newPassword' WHERE EmailID='$userEmail';";
        if ($this->ExecuteQuery($sqlQuery)) {
            // send a password changed email to user inbox as other email services do, for confirmation
            $mailObj = array(
                'To' => $userEmail,
                'From' => 'admin@zhost.com',
                'IsDraft' => 0,
                'IsTrash' => 0,
                'Title' => "Password Changed!",
                'Subject' => 'Your password was recently changed.',
                'Body' => "Hey there $userEmail !\nWe hope you are having a good day!\nThis is just a confirmation mail to let you know that your password was recently changed.If it wasn't you, please change your password as soon as possible!\n-zHost Admin team",
                'AttachmentFilePath' => ''
            );

            $this->ComposeEmail($mailObj);
            return true;
        }

        return false;
    }

    public function GetUserSecurityData($userEmail)
    {
        $result = array(
            'Status' => '-1',
            'Message' => 'NA',
            'Data' => array()
        );

        if (!isset($userEmail)) {
            return $result;
        }

        if (!$this->IsExistingUser($userEmail)) {
            $result['Message'] = "User doesn't exist.";
            return $result;
        }

        $sqlQuery = "SELECT SecurityQuestion,SecurityAnswer,PhoneNumber FROM users WHERE EmailID='$userEmail';";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $result['Status'] = '0';
            $result['Message'] = 'Success';

            while ($row = mysqli_fetch_object($exeResult)) {
                $dataObj = array();
                $dataObj['SecurityQuestion'] = $row->SecurityQuestion ?? "";
                $dataObj['SecurityAnswer'] = $row->SecurityAnswer ?? "";
                $dataObj['PhoneNumber'] = $row->PhoneNumber ?? "";
                $dataObj['Email'] = $userEmail;
                $result['Data'] =  $dataObj;
            }

            mysqli_free_result($exeResult);
        } else {
            $result['Message'] = "Database query execution failed.";
        }

        return $result;
    }

    public function FetchMailWithUuid($uuid)
    {
        $result = array(
            'Status' => '-1',
            'Message' => 'NA',
            'Count' => 0,
            'Emails' => array()
        );

        if (!isset($uuid)) {
            return $result;
        }

        $sqlQuery = "SELECT * FROM emails WHERE MailID='$uuid';";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $result['Count'] = mysqli_num_rows($exeResult);
            $result['Status'] = '0';
            $result['Message'] = 'Success';

            if ($result['Count'] <= 0) {
                $result['Status'] = '-1';
                $result['Message'] = 'No emails exist.';
                return $result;
            }

            while ($row = mysqli_fetch_object($exeResult)) {
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

    public function ComposeEmail($mailObj)
    {
        if (!isset($mailObj)) {
            return false;
        }

        $sqlQuery = "INSERT INTO emails(
            MailID,
            SendTo,
            ReceivedFrom,
            IsDraft,
            IsTrash,
            SUBJECT,
            Body,
            AttachmentPath
        )
        VALUES(
            UUID_SHORT(),'" .
            $mailObj['To'] . "','" .
            $mailObj['From'] . "'," .
            $mailObj['IsDraft'] . "," .
            $mailObj['IsTrash'] . ",'" .
            $mailObj['Subject'] . "','" .
            $mailObj['Body'] . "','" .
            $mailObj['AttachmentFilePath'] . "');";

        return $this->ExecuteQuery($sqlQuery);
    }

    public function IsDraftMail($uuid)
    {
        if (!isset($uuid)) {
            return false;
        }

        $sqlQuery = "SELECT * FROM emails WHERE MailID='$uuid' AND IsDraft=1";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            return mysqli_num_rows($exeResult) > 0;
        }

        return false;
    }

    public function GetUserDraftEmails($userEmail)
    {
        return $this->GetUserEmails($userEmail, true, false);
    }

    public function GetUserTrashEmails($userEmail)
    {
        return $this->GetUserEmails($userEmail, false, true);
    }

    public function GetUserInboxEmails($userEmail)
    {
        return $this->GetUserEmails($userEmail, false, false);
    }

    public function GetUserSendEmails($userEmail)
    {
        return $this->GetSendEmails($userEmail);
    }

    public function GetAllDraftsSendFrom($sendFrom)
    {
        $response = array(
            'Status' => '-1',
            'Message' => 'Invalid',
            'Count' => 0,
            'Emails' => array()
        );

        if (!isset($sendFrom)) {
            return $response;
        }

        if (!IsUserLoggedIn()) {
            return $response;
        }

        $sqlQuery = "SELECT * FROM emails where ReceivedFrom='$sendFrom' WHERE IsDraft=1;";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $response['Count'] = mysqli_num_rows($exeResult);
            $response['Status'] = '0';
            $response['Message'] = 'Success';

            if ($response['Count'] <= 0) {
                $response['Status'] = '-1';
                $response['Message'] = 'No emails exist for current mail folder.';
                return $response;
            }

            while ($row = mysqli_fetch_object($exeResult)) {
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

    public function GetMailWithUuid($uuid)
    {
        $response = array(
            'Status' => '-1',
            'Message' => 'Invalid',
            'Count' => 0,
            'Emails' => array()
        );

        if (!isset($uuid)) {
            return $response;
        }

        $sqlQuery = "SELECT * FROM emails WHERE MailID='$uuid';";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $response['Count'] = mysqli_num_rows($exeResult);
            $response['Status'] = '0';
            $response['Message'] = 'Success';

            if ($response['Count'] <= 0) {
                $response['Status'] = '-1';
                $response['Message'] = 'No emails exist with this id.';
                return $response;
            }

            while ($row = mysqli_fetch_object($exeResult)) {
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

    public function GetSendEmails($userEmail)
    {
        $response = array(
            'Status' => '-1',
            'Message' => 'Invalid',
            'Count' => 0,
            'Emails' => array()
        );

        if (!isset($userEmail)) {
            return $response;
        }

        if (!IsUserLoggedIn()) {
            return $response;
        }

        $sqlQuery = "SELECT * FROM emails WHERE ReceivedFrom='$userEmail';";

        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $response['Count'] = mysqli_num_rows($exeResult);
            $response['Status'] = '0';
            $response['Message'] = 'Success';

            if ($response['Count'] <= 0) {
                $response['Status'] = '-1';
                $response['Message'] = 'No emails exist for current mail folder.';
                return $response;
            }

            while ($row = mysqli_fetch_object($exeResult)) {
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

    public function GetUserEmails($userEmail, $onlyDraft, $onlyTrash)
    {
        $response = array(
            'Status' => '-1',
            'Message' => 'Invalid',
            'Count' => 0,
            'Emails' => array()
        );

        if (!isset($userEmail)) {
            return $response;
        }

        if (!IsUserLoggedIn()) {
            return $response;
        }

        $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail';";

        if ($onlyDraft) {
            $sqlQuery = "SELECT * FROM emails WHERE ReceivedFrom='$userEmail' AND IsDraft=1;";
        }

        if ($onlyTrash) {
            $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail' AND IsTrash=1;";
        }

        if (!$onlyDraft && !$onlyTrash) {
            $sqlQuery = "SELECT * FROM emails WHERE SendTo='$userEmail' AND IsTrash=0 AND IsDraft=0;";
        }

        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $response['Count'] = mysqli_num_rows($exeResult);
            $response['Status'] = '0';
            $response['Message'] = 'Success';

            if ($response['Count'] <= 0) {
                $response['Status'] = '-1';
                $response['Message'] = 'No emails exist for current mail folder.';
                return $response;
            }

            while ($row = mysqli_fetch_object($exeResult)) {
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

    public function UpdateMailWithUuid($uuid, $newSubject, $newBody){
        if(!isset($uuid) || !isset($newSubject) || !isset($newBody)){
            return false;
        }

        $sqlQuery = "UPDATE emails SET Subject='" . $newSubject . "', Body='" . $newBody . "' WHERE MailID='" . $_POST['uuid'] . "';";
        return $this->ExecuteQuery($sqlQuery);
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

                if(isset($obj->FirstName)){
                    $resultObject->FirstName = $obj->FirstName;
                }

                if(isset($obj->LastName)){
                    $resultObject->LastName = $obj->LastName;
                }

                if(isset($obj->Address)){
                    $resultObject->Address = $obj->Address;
                }

                if(isset($obj->WorkLink)){
                    $resultObject->WorkLink = $obj->WorkLink;
                }

                if(isset($obj->Profession)){
                    $resultObject->Profession = $obj->Profession;
                }

                if(isset($obj->Bio)){
                    $resultObject->Bio = $obj->Bio;
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