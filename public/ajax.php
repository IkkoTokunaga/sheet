<?php
require_once('common/init.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $_SESSION['input_user_name'] = str_replace(array("<",">","?","/",".","*"), "", $_POST['user_name']);
}else{
    header("Location: /");
    exit;
}