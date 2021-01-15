<?php
ob_start();
session_start();
require_once 'api_client.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// select logged in users detail
$rest_response = CallAPI("GET","http://rest:5000/users/".$_SESSION['user']);
$loged_in_user = json_decode($rest_response);


if (!$loged_in_user->is_admin) {
    echo "window.alert('You don`t have permissions for this operation')";
    header("Location: index.php");
    exit;
} else{
    $id=$_GET['id'];
    $rest_response = CallAPI("DELETE","http://rest:5000/users/".$id);
    header("Location: users.php"); 
}

?>