<?php
require_once 'DatabaseManager.php';
require_once '../Common/Functions.php';
// this script is purely used for generating sample data for quickly setting up database

// Usage:
// * Connect to database
// * Create tables
// * Process an insert operation to insert 9 mail accounts
// * Process an insert operation to insert emails depending on count received from the GET parameter
//   If no count is specified, default will be 8, on Inbox, Drafts, Trash mail folders.

function generateUser($userName, $email)
{
    return array(
        'email' => $email,
        'username' => $userName,
        'password' => password_hash("root", PASSWORD_DEFAULT),
        'secquest' => "Favarite Car",
        'secans' => "GTR",
        'pnumber' => "0000000000"
    );
}

// generate emails
// Inbox
function generateMail($to, $from, $isDraft, $isTrash, $subject, $body)
{
    return array(
        'To' => $to,
        'From' => $from,
        'IsDraft' => $isDraft ? 1 : 0,
        'IsTrash' => $isTrash ? 1 : 0,
        'Subject' => $subject,
        'Body' => $body,
        'AttachmentFilePath' => "../includes/images/Attachments/default-attachment.jpg"
    );
}

$defaultAvatarPath = "../includes/images/default-avatar.png";
$Db = new Database;

// terminate the script if its not a GET request
if ($_SERVER['REQUEST_METHOD'] != "GET") {
    exit();
}

if (!isset($_GET['auth'])) {
    exit();
}

if($_GET['auth'] != "root_sample"){
    exit();
}

$Db->ExecuteQuery('DROP TABLE admin,users,emails;');

$Db->ExecuteQuery('
CREATE TABLE IF NOT EXISTS Users(
    Id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    EmailID VARCHAR(80) UNIQUE NOT NULL,
    UserName VARCHAR(100) NOT NULL,
    DateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Password VARCHAR(255) NOT NULL,
    SecurityQuestion VARCHAR(80),
    SecurityAnswer VARCHAR(80),
    AvatarPath VARCHAR(80),
    PhoneNumber VARCHAR(80)
);');

$Db->ExecuteQuery('
CREATE TABLE IF NOT EXISTS Admin (
    Id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    EmailID VARCHAR(80) UNIQUE NOT NULL,
    UserName VARCHAR(100) NOT NULL,
    DateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Password VARCHAR(255) NOT NULL,
	SecurityQuestion VARCHAR(80),
	SecurityAnswer VARCHAR(80),
	AvatarPath VARCHAR(80),
	PhoneNumber VARCHAR(80),
	AccessLevel int
);');

$Db->ExecuteQuery('
CREATE TABLE IF NOT EXISTS Emails(
	MailID varchar(80) UNIQUE NOT NULL,
	ReceivedTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	SendTo varchar(60) NOT NULL,
	ReceivedFrom varchar(80) NOT NULL,
	IsDraft BOOLEAN,
	IsTrash BOOLEAN,
	Title varchar(80),
	Subject varchar(90),
	Body varchar(100),
	AttachmentPath varchar(80)
);');

// We will be using RegisterUser() Function
// we wont be parsing the register status as its not logical as this is just a sample data
$Db->RegisterUser(generateUser("Arun Prakash", "arun@zhost.com"), $defaultAvatarPath, false, true);
$Db->RegisterUser(generateUser("Jobin Joseph", "jobin@zhost.com"), $defaultAvatarPath, false, true);
$Db->RegisterUser(generateUser("Shijo Sam", "shijo@zhost.com"), $defaultAvatarPath, false, true);
$Db->RegisterUser(generateUser("Gotam Ritika", "gotam@zhost.com"), $defaultAvatarPath, false, true);
$Db->RegisterUser(generateUser("Kshitija Shrivatsa", "kshitija@zhost.com"), $defaultAvatarPath, false, true);
$Db->RegisterUser(generateUser("Bhaskar Rupa", "bhaskar@zhost.com"), $defaultAvatarPath, false, true);
$Db->RegisterUser(generateUser("Praveen Geevarghese", "praveen@zhost.com"), $defaultAvatarPath, false, true);
$Db->RegisterUser(generateUser("Prabhat Esha", "prabhat@zhost.com"), $defaultAvatarPath, false, true);
$Db->RegisterUser(generateUser("Jay Vipin", "jay@zhost.com"), $defaultAvatarPath, false, true);

$mailObj1 = generateMail("arun@zhost.com", "gotam@zhost.com", false, false, "Abstract submittion date", "Hey arun, You have to submit your project before 10th this month.");
$mailObj2 = generateMail("arun@zhost.com", "Bhaskar@zhost.com", false, false, "Abstract submittion date", "Hey arun, You have to submit your project before 10th this month.");
$mailObj3 = generateMail("arun@zhost.com", "prabhat@zhost.com", false, false, "Abstract submittion date", "Hey arun, You have to submit your project before 10th this month.");
$mailObj4 = generateMail("jobin@zhost.com", "praveen@zhost.com", false, false, "Abstract submittion date", "Hey jobin, You have to submit your project before 10th this month.");
$mailObj5 = generateMail("shijo@zhost.com", "praveen@zhost.com", false, false, "Abstract submittion date", "Hey shijo, You have to submit your project before 10th this month.");
$mailObj6 = generateMail("jobin@zhost.com", "jay@zhost.com", false, false, "Abstract submittion date", "Hey jobin, You have to submit your project before 10th this month.");
$mailObj7 = generateMail("shijo@zhost.com", "prabhat@zhost.com", false, false, "Abstract submittion date", "Hey shijo, You have to submit your project before 10th this month.");
$mailObj8 = generateMail("arun@zhost.com", "jay@zhost.com", false, false, "Abstract submittion date", "Hey arun, You have to submit your project before 10th this month.");

$Db->ComposeEmail($mailObj1);
$Db->ComposeEmail($mailObj2);
$Db->ComposeEmail($mailObj3);
$Db->ComposeEmail($mailObj4);
$Db->ComposeEmail($mailObj5);
$Db->ComposeEmail($mailObj6);
$Db->ComposeEmail($mailObj7);
$Db->ComposeEmail($mailObj8);
Functions::Alert("Sample data generated!");
Functions::Redirect("../Index.php");
?>