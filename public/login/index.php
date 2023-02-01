<?php

session_start();

$length = 32;
$bytes = random_bytes($length);
$token = bin2hex($bytes);

$_SESSION['token'] = $token;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-language" content="ja">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css?date=<?= date('YmdHis'); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <title>報告書</title>
</head>

<body>
    <div class="base">
        <div class="login-page">
            <div class="form">
                <div class="register-form">
                    <input type="text" name="crate_email" id="create_email" placeholder="メールアドレス" />
                    <input type="text" name="create_name" id="create_name" placeholder="お名前" />
                    <input type="password" name="create_password" id="create_password" placeholder="パスワード" />
                    <button id="create_btn">新規作成</button>
                    <p class="message">すでにアカウントをお持ちの方は<a href="#">ログイン</a></p>
                </div>
                <div class="login-form">
                    <input type="text" name="email" id="email" placeholder="メールアドレス" />
                    <input type="text" name="user_name" id="user_name"  placeholder="お名前" />
                    <input type="password" name="password" id="password"  placeholder="パスワード" />
                    <button id="login_btn">ログイン</button>
                    <p class="message">新規アカウント作成は<a href="#">こちら</a></p>
                    <input type="hidden" id="token" value="<?= $token; ?>">
                </div>
            </div>
        </div>
    </div>
    <script src="login.js?date=<?= date('YmdHis'); ?>" type="text/javascript"></script>
</body>