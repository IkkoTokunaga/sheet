<?php
require_once('common/init.php');
unset($_SESSION['token']);
$user_id = $_SESSION['id'];

$alert = "";
$now = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['sheet_name']) {
        /**
         * 重複確認
         */
        $sql = "SELECT count(sheet_name) 'count' FROM sheets WHERE sheet_name = '{$_POST['sheet_name']}' AND user_id = {$user_id}";
        $set = array();
        $set['sheet_name'] = $_POST['sheet_name'];
        $set['user_id'] = $user_id;
        $stmt = execute($sql, $set);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] === '0') {
            $sql = "SELECT MAX(line_no) as line_no FROM sheets WHERE user_id = :user_id";
            $set = array();
            $set['user_id'] = $user_id;
            $stmt = execute($sql, $set);
            $line_no_response = $stmt->fetch(PDO::FETCH_ASSOC);
            $next_line_no = $line_no_response['line_no'] +1;

            $set = array();
            $set['line_no'] = $next_line_no;
            $set['user_id'] = $user_id;
            $set['sheet_name'] = $_POST['sheet_name'];
            
            $response = save('sheets', $set);

            if ($response) {
                echo alert('保存しました。');
                header("Location: ./");
                exit;
            }
        } else {
            echo alert('すでに登録済みの名称です。');
        }
    }
}

$sql = "SELECT * FROM sheets WHERE user_id = :user_id ORDER BY created_at ASC";
$set = array();
$set['user_id'] = $user_id;
$stmt = execute($sql, $set);
$sheets = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sheet_list = array();
foreach ($sheets as $sheet) {
    $tmp = array();
    $tmp['sheet_id'] = $sheet['id'];
    $tmp['sheet_name'] = h($sheet['sheet_name']);
    $tmp['created_at'] = date('Y/m/d H:i:s', strtotime($sheet['created_at']));
    $tmp['updated_at'] = date('Y/m/d H:i:s', strtotime($sheet['updated_at']));
    $tmp['detail'] = "<input type='button' value='詳細' onClick='window.location.href=\"sheet_detail/?id={$sheet['id']}\"' class='w60'>";
    $sheet_list[] = $tmp;
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
    <title>シートを選択</title>
</head>

<body>
    <div class="base">
        <div>
            <form action="" method="post">
                <table class="head_table">
                    <tr>
                        <th>シート名</th>
                        <td><input type="text" name="sheet_name" id="sheet_name"></td>
                        <td><input type="submit" name="create" id="create" value="シートを作成する"></td>
                        <th>記録者名</th>
                        <td><input type="text" name="user_name" id="user_name" onChange="input_user_name();"></td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="mg-10"></div>
        <div class="w80p">
            <table border="1" class="list_table">
                <thead>
                    <th class="w30">No.</th>
                    <th class="">シート名</th>
                    <th class="w160">作成日</th>
                    <th class="w160">最終更新日</th>
                    <th class="w60">詳細</th>
                </thead>
                <tbody>
                    <?php foreach ($sheet_list as $sheet) : ?>
                        <tr>
                            <td class="center"><?= $sheet['sheet_id']; ?></td>
                            <td><span></span><?= $sheet['sheet_name']; ?></span></td>
                            <td class="center"><?= $sheet['created_at']; ?></td>
                            <td class="center"><?= $sheet['updated_at']; ?></td>
                            <td class="center"><?= $sheet['detail']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="sheet.js?date=<?= date('YmdHis'); ?>" type="text/javascript"></script>
</body>

</html>