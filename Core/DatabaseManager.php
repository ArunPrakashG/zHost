<?php
require_once 'Config.php';
require_once 'UserModel.php';

if(Config::DEBUG){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
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
        $this->Connection = mysqli_connect(Config::HOST, Config::DB_USER_NAME, Config::DB_USER_PASSWORD) or die("Failed to connect with database");
        mysqli_select_db($this->Connection, Config::DB_NAME);
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

    public function RegisterUser($userName, $email, $password, $isAdmin)
    {
        if (empty($userName) || empty($email) || empty($password)) {
            return false;
        }
        
        $sqlQuery = "INSERT INTO " . ($isAdmin ? "admin" : "users") . " (`MailID`, `UserName`, `PASSWORD`) VALUES ('" . $email . "','" . $userName . "','" . $password . "');";              
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

        $sqlQuery = "SELECT * FROM admin WHERE MailID='" . $email . "';";        
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

        $sqlQuery = "SELECT * FROM users WHERE MailID='" . $email . "';";    
        if($exeResult = $this -> ExecuteQuery($sqlQuery)){
            $rowsCount = mysqli_num_rows($exeResult);
            mysqli_free_result($exeResult);

            if($rowsCount > 0){
                return true;
            }
        }

        return false;
    }

    public function LoginUser($email, $password, $isAdminLogin)
    {
        $resultArray = array();

        if (empty($email) || empty($password)) {
            $resultArray["isError"] = true;
            $resultArray["errorMessage"] = "Email or Password is empty!";
            return $resultArray;
        }

        $sqlQuery = "SELECT * FROM " . ($isAdminLogin ? "admin" : "users") . " WHERE MailID='" . $email . "';";
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
                    if(!password_verify($password, $obj->PASSWORD)){
                        $resultArray["isError"] = true;
                        $resultArray["errorMessage"] = "Password is incorrect";
                        return $resultArray;
                    }

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
            unset($resultArray["errorMessage"]);
            return $resultArray;
        }

        $resultArray["isError"] = true;
        $resultArray["errorMessage"] = "Query execution failed!";
        return $resultArray;
    }
}
