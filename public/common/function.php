<?php

function h($value){
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function alert($message){
    return "<script>alert('{$message}')</script>";
}