<?php
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
?>