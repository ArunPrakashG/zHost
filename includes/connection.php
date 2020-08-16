<?php
require_once('../zHost/config.php');
require_once('../zHost/includes/connection.php');

class LoggedInUserResult
{
    public $UserName;
    public $MailID;
    public $Password;
    public $Id;
    public $DateCreated;
    public $IsAdmin;
    public $ResultStatus;

    public function __construct($executionResult, $isAdminQuery)
    {
        $IsAdmin = $isAdminQuery;
        $ResultStatus = $executionResult;
    }
}

class Database
{
    public $Connection;
    public $QueriesExecutedCount;
    public $Config;

    public function __construct()
    {
        $Config = new Config;
        $this->Connect();
    }

    private function Connect()
    {
        // ERROR -> Failed to connect to database
        $Connection = mysqli_connect($this->Config->Host, $this->Config->DBUserName, $this->Config->DBUserPassword) or die("Failed to connect with database");
        mysqli_select_db($this->Config->DBName, $Connection);
    }

    private function ExecuteQuery($query)
    {
        if (empty($query)) {
            throw new Exception("query is empty!");
        }

        if (!mysqli_ping($this->Connection)) {
            throw new Exception("Connection lost with database.");
        }

        return mysqli_query($this->Connection, $query);
    }

    public function RegisterUser($userName, $email, $encryptedPasswordHash, $isAdmin)
    {
        if (empty($userName) || empty($email) || empty($encryptedPasswordHash)) {
            return false;
        }

        $sqlQuery = "INSERT INTO " . $isAdmin ? "admin" : "users" . " (`MailID`, `UserName`, `PASSWORD`) VALUES (" . $email . "," . $userName . ", " . $encryptedPasswordHash . ");";
        return $this->ExecuteQuery($sqlQuery);
    }

    public function IsExistingUser($email){
        if(empty($email)){
            return false;
        }

        return $this->IsExistInAdminTable($email) || $this->IsExistInUserTable($email);
    }

    public function IsExistInAdminTable($email){ 
        if(empty($email)){
            return false;
        }

        $sqlQuery = "SELECT * FROM admin WHERE MailID=" . $email . ";";        
        if($exeResult = $this -> ExecuteQuery($sqlQuery)){
            $rowsCount = mysqli_num_rows($exeResult);
            mysqli_free_result($exeResult);

            if($rowsCount > 0){
                return true;
            }
        }

        return false;
    }

    public function IsExistInUserTable($email){
        if(empty($email)){
            return false;
        }

        $sqlQuery = "SELECT * FROM users WHERE MailID=" . $email . ";";    
        if($exeResult = $this -> ExecuteQuery($sqlQuery)){
            $rowsCount = mysqli_num_rows($exeResult);
            mysqli_free_result($exeResult);

            if($rowsCount > 0){
                return true;
            }
        }

        return false;
    }

    public function LoginUser($email, $encryptedPasswordHash, $isAdminLogin)
    {
        $resultArray = array();

        if (empty($email) || empty($encryptedPasswordHash)) {
            $resultArray["isError"] = true;
            $resultArray["errorMessage"] = "Email or Password is empty!";
            return $resultArray;
        }

        $sqlQuery = "SELECT * FROM " . $isAdminLogin ? "admin" : "users" . " WHERE MailID=" . $email . " AND PASSWORD=" . $encryptedPasswordHash . ";";
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $resultArray["isExist"] = mysqli_num_rows($exeResult) > 0;

            if(!$resultArray["isExist"]){
                $resultArray["isError"] = false;
                $resultArray["errorMessage"] = "No such user exist!";
                return $resultArray;
            }

            $resultArray["resultObj"] = new LoggedInUserResult(true, $isAdminLogin);
            while ($obj = mysqli_fetch_object($exeResult)) {
                if(isset($obj->MailID)){
                    $resultArray["resultObj"]->MailID = $obj->MailID;
                }
                if(isset($obj->PASSWORD)){
                    $resultArray["resultObj"]->Password = $obj->PASSWORD;
                }

                if(isset($obj->UserName)){
                    $resultArray["resultObj"]->UserName = $obj->UserName;
                }
                
                if(isset($obj->Id)){
                    $resultArray["resultObj"]->Id = $obj->Id;
                }

                if(isset($obj->DateCreated)){
                    $resultArray["resultObj"]->DateCreated = $obj->DateCreated;
                }
            }

            mysqli_free_result($exeResult);
            $resultArray["isError"] = false;            
            $resultArray["errorMessage"] = "Success!";
            return $resultArray;
        }

        $resultArray["isError"] = true;
        $resultArray["errorMessage"] = "Query execution failed!";
        return $resultArray;
    }
}

?>