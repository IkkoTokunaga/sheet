<?php
session_start();

require_once('../common/define.php');
require_once('../common/db.php');
require_once('../common/function.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_SESSION['token']) && isset($_POST['token'])){
        if($_SESSION['token'] === $_POST['token']){

            $email = $_POST['email'];
            $user_name = $_POST['user_name'];
            $password = $_POST['password'];
            if($email && $user_name && $password){
                $sql = "SELECT
                            id,
                            user_name,
                            email,
                            password
                        FROM users
                        WHERE user_name = :user_name
                        AND email = :email
                        ";
                $set = array();
                $set['user_name'] = $user_name;
                $set['email'] = $email;
                $stmt = execute($sql, $set);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if($password === $user['password']){
                    echo 'OK';

                    $_SESSION['id'] = $user['id'];
                    $_SESSION['user_name'] = $user['user_name'];
                    $_SESSION['name'] = $_POST['name'];

                }else{
                    echo 'NG';
                }
            }
        }
    }

    exit;
}

header("Location: /");
exit;

