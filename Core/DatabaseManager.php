<?php
require_once 'Config.php';
require_once 'UserModel.php';

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
        $sqlQuery = "INSERT INTO " . ($isAdmin ? Config::ADMIN_TABLE_NAME : Config::USER_TABLE_NAME) . "(`Email`, `UserName`, `Password`, `SecurityQuestion`, `SecurityAnswer`, `AvatarPath`, `PhoneNumber`) VALUES ('" . $postArray['email'] . "','" . $postArray['username'] . "','" . $postArray['password'] . "','" . $postArray['secquest'] . "','" . $postArray['secans'] . "','" . $avatarPath . "','" . $postArray['pnumber'] . "');";
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

        $sqlQuery = "SELECT * FROM " . Config::ADMIN_TABLE_NAME . " WHERE Email='" . $email . "';";
        error_log($sqlQuery);
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $rowsCount = mysqli_num_rows($exeResult);
            error_log("Admin Rows exist:" . $rowsCount);
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

        $sqlQuery = "SELECT * FROM " . Config::USER_TABLE_NAME . " WHERE Email='" . $email . "';";
        error_log($sqlQuery);
        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $rowsCount = mysqli_num_rows($exeResult);
            error_log("User Rows exist:" . $rowsCount);
            mysqli_free_result($exeResult);
            if ($rowsCount > 0) {
                return true;
            }
        }

        return false;
    }

    public function LoginUser($email, $password, $isAdminLogin)
    {
        $resultArray = array();

        if (!isset($email) || !isset($password)) {
            $resultArray["isError"] = true;
            $resultArray["errorMessage"] = "Email or Password is empty!";
            return $resultArray;
        }

        $sqlQuery = "SELECT * FROM " . ($isAdminLogin ? "admin" : "user") . " WHERE Email='" . $email . "';";

        if ($exeResult = $this->ExecuteQuery($sqlQuery)) {
            $resultArray["isExist"] = mysqli_num_rows($exeResult) > 0;

            if (!$resultArray["isExist"]) {
                $resultArray["isError"] = false;
                $resultArray["errorMessage"] = "No such user exist!";
                return $resultArray;
            }

            $resultArray["resultObj"] = new LoggedInUserResult(true, $isAdminLogin);
            while ($obj = mysqli_fetch_object($exeResult)) {
                if (isset($obj->Email)) {
                    $resultArray["resultObj"]->Email = $obj->Email;
                }

                if (isset($obj->Password)) {
                    if (!password_verify($password, $obj->Password)) {
                        $resultArray["isError"] = true;
                        $resultArray["errorMessage"] = "Password is incorrect";
                        return $resultArray;
                    }

                    $resultArray["resultObj"]->Password = $obj->Password;
                }

                if (isset($obj->UserName)) {
                    $resultArray["resultObj"]->UserName = $obj->UserName;
                }

                if (isset($obj->Id)) {
                    $resultArray["resultObj"]->Id = $obj->Id;
                }

                if (isset($obj->DateCreated)) {
                    $resultArray["resultObj"]->DateCreated = $obj->DateCreated;
                }

                if (isset($obj->SecurityQuestion)) {
                    $resultArray["resultObj"]->SecurityQuestion = $obj->SecurityQuestion;
                }

                if (isset($obj->SecurityAnswer)) {
                    $resultArray["resultObj"]->SecurityAnswer = $obj->SecurityAnswer;
                }

                if (isset($obj->AvatarPath)) {
                    $resultArray["resultObj"]->AvatarPath = $obj->AvatarPath;
                }

                if (isset($obj->PhoneNumber)) {
                    $resultArray["resultObj"]->PhoneNumber = $obj->PhoneNumber;
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
