<?php
    class LoggedInUserResult
    {
        public $UserName;
        public $Email;
        public $Password;
        public $Id;
        public $DateCreated;
        public $IsAdmin;
        public $SecurityQuestion;
        public $SecurityAnswer;
        public $AvatarPath;
        public $PhoneNumber;
        public $ResultStatus;
    
        public function __construct($executionResult, $isAdminQuery)
        {
            $IsAdmin = $isAdminQuery;
            $ResultStatus = $executionResult;
        }
    }
?>