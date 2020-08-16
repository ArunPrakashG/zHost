<?php

session_start();

$Username = "";
$Password = "";

function getAllPosts(){
    global $connection;
    $sql = "SELECT * FROM posts WHERE published=true";
	$result = mysqli_query($connection, $sql);
}
