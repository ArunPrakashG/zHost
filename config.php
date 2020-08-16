<?php
session_start();

class Config
{
    public $Host;
    public $DBUserName;
    public $DBUserPassword;
    public $DBName;

    const BASE_URL = "http://localhost/zHost/";

    public function _construct()
    {
        $Host = "localhost";
        $DBUserName = "root";
        $DBUserPassword = "root";
        $DBName = "zhost";
    }
}

?>
