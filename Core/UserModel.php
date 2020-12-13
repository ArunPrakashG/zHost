<?php
    class LoggedInUserResult
    {
        public $UserName;
        public $Email;
        public $DateOfBirth;
        public $Password;
        public $Id;
        public $DateCreated;
        public $IsAdmin;
        public $SecurityQuestion;
        public $SecurityAnswer;
        public $FirstName;
        public $LastName;
        public $Address;
        public $WorkLink;
        public $Profession;
        public $Bio;
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