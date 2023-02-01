<?php
ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);
require_once('../common/init.php');

$user_id = $_SESSION['id'];
$sheet_id = $_GET['id'];

$script = "";
$status_arr = array(
    '未対応' => '100',
    '対応済' => '200',
    '確認済' => '300',
    '保留' => '400',
    '完了' => '500',
);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $filePath = "./tmp_csv/" . $_FILES["up_csv"]["name"];
    if (move_uploaded_file($_FILES["up_csv"]["tmp_name"], $filePath)) {
        chmod($filePath, 0644);
        $file_obj = new SplFileObject($filePath);
        $file_obj->setFlags(
            \SplFileObject::READ_CSV |
                \SplFileObject::READ_AHEAD |
                \SplFileObject::SKIP_EMPTY |
                \SplFileObject::DROP_NEW_LINE
        );

        $count = 0;
        foreach ($file_obj as $row) {

            $count++;
            if ($count === 1) {
                continue;
            }
            $row = mb_convert_encoding($row, 'UTF-8', 'auto');

            $line_no = $row[0];
            $title = $row[1];
            $status = $row[2];
            $register = $row[3];
            $ask = $row[4];
            $amender = $row[5];
            $report = $row[6];
            $inspector = $row[7];

            $set = array();
            $set['line_no'] = $line_no;
            $set['user_id'] = $user_id;
            $set['sheet_id'] = $sheet_id;

            $sql = "SELECT * FROM sheet_details WHERE line_no = :line_no AND user_id = :user_id AND sheet_id = :sheet_id";
            $stmt = execute($sql, $set);
            $is_recode = $stmt->fetch(PDO::FETCH_ASSOC);

            $set = array();
            $where = array();

            $set['title'] = $title;
            $set['sheet_status'] = isset($status_arr[$status]) ? $status_arr[$status] : '100';
            $set['register_name'] = $register;
            $set['ask'] = $ask;
            $set['amender_name'] = $amender;
            $set['report'] = $report;
            $set['inspector_name'] = $inspector;
            $set['sheet_id'] = $sheet_id;
            $set['user_id'] = $user_id;
            $set['deleted_flg'] = 0;

            if ($is_recode) {
                $where['line_no'] = $line_no;
                $saved = update('sheet_details', $set, $where);

                $where = array();
                $where['user_id'] = $user_id;
                $where['id'] = $sheet_id;

                update('sheets', array(), $where);
            } else {

                $select = array();
                $select['user_id'] = $user_id;
                $select['id'] = $sheet_id;

                $sql = "SELECT detail_count FROM sheets WHERE user_id = :user_id AND id = :id";
                $stmt = execute($sql, $select);
                $detail_count_response = $stmt->fetch(PDO::FETCH_ASSOC);
                $next_line_no = $line_no ? $line_no : $detail_count_response['detail_count'] + 1;
                $set['line_no'] = $next_line_no;

                $saved = save('sheet_details', $set);

                $set = array();
                $where = array();

                $set['detail_count'] = $next_line_no;
                $where['user_id'] = $user_id;
                $where['id'] = $sheet_id;

                update('sheets', $set, $where);
            }
        }
    }

    foreach (glob("./tmp_csv/*.csv") as $delFile) {
        unlink($delFile);
    }

    echo alert($count . "件更新しました。");
    $script = "window.location.href='../sheet_detail/?id={$sheet_id}'";
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
    <title>CSV取込</title>
</head>

<body>
    <div class="base">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="up_csv">
            <input type="submit">
        </form>
    </div>
    <script>
        <?= $script; ?>
    </script>
</body>

</html>