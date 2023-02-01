<?php

session_start();

require_once('define.php');
require_once('db.php');
require_once('function.php');

// $_SESSION['name'] = "徳永";
// $_SESSION['id'] = 1;

if(!isset($_SESSION['id'])){
    if(!$_SESSION['id']){
        header("Location: /login/");
        exit;
    }
}