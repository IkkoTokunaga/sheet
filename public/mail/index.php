<?php
require_once('../common/init.php');
$user_id = $_SESSION['id'];

$headers = "From: " . mb_encode_mimeheader(mb_convert_encoding('徳永', "ISO-2022-JP", "AUTO")) . "<" . 'tokunaga@mail.com' . "> \n";
$a = mb_send_mail(
    'tokuppee1515@gmail.com',
    'test',
    'test本文'
);

echo '<pre>';
var_dump($a);
echo '</pre>';

$alert = "";
$now = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['title'] && $_POST['mail_body']) {

        $set = array();
        $set['user_id'] = $user_id;
        $sql = "SELECT MAX(line_no) as line_no FROM mails WHERE user_Id = :user_id";
        $stmt = execute($sql, $set);
        $line_no = $stmt->fetch(PDO::FETCH_ASSOC)['line_no'];

        $set = array();
        $set['line_no'] = $line_no + 1;
        $set['user_id'] = $user_id;
        $set['title'] = $_POST['title'];
        $set['body'] = $_POST['mail_body'];
        save('mails', $set);

        echo alert('送信しました。');
    }
}

$count = 0;
if ($user_id > 0) {

    $set = array();
    $set['user_id'] = $user_id;
    $sql = "SELECT * FROM mails WHERE user_Id = :user_id ORDER BY line_no DESC";
    $stmt = execute($sql, $set);
    $mail_response = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mail_list = array();
    foreach ($mail_response as $mail) {

        $tmp = array();
        $tmp['line_no'] = $mail['line_no'];
        $tmp['title'] = h($mail['title']);
        $tmp['body'] = nl2br(h($mail['body']));
        $tmp['created_at'] = $mail['created_at'];

        $mail_list[] = $tmp;
        $count++;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-language" content="ja">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main.css?date=<?= date('YmdHis'); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <title>メール送信</title>
</head>

<body>
    <div class="base">
        <div>
            <form action="" method="post">
                <table class="head_table">
                    <tr>
                        <th>タイトル</th>
                        <td><input type="text" name="title" id="title"></td>
                    </tr>
                    <tr>
                        <th>本文</th>
                        <td><textarea name="mail_body" id="mail_body" cols=100 rows=30></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="送信"></td>
                    </tr>
                    <tr>
                        <th colspan="2">履歴</th>
                    </tr>
                </table>
            </form>
        </div>
        <div class="mg-10"></div>
        <div class="w100p">
            <table border="1" class="w100p head_table">
                <thead>
                    <th class="w60">No.</th>
                    <th class="w200">タイトル</th>
                    <th class="">本文</th>
                    <th class="w160">作成日</th>
                </thead>
                <tbody>
                    <?php foreach ($mail_list as $mail) : ?>

                        <tr>
                            <td><?= $mail['line_no']; ?></td>
                            <td><?= $mail['title']; ?></td>
                            <td><?= $mail['body']; ?></td>
                            <td><?= $mail['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>