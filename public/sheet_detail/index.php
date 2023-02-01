<?php
require_once('../common/init.php');

$user_id = $_SESSION['id'];

$alert = "";
$search_title_display = "";
$search_status_display = "";
$search_created_at_display = "";
$search_updated_at_display = "";

$status_arr = array(
    '100' => '未対応',
    '200' => '対応済',
    '300' => '確認済',
    '400' => '保留',
    '500' => '完了',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['title']) {

        $now = date('Y-m-d H:i:s');
        $line_no = intval($_POST['detail_count']) + 1;

        $set = array();
        $set['sheet_id'] = $_POST['id'];
        $set['line_no'] = $line_no;
        $set['user_id'] = $user_id;
        $set['register_name'] = $_POST['register_name'];
        $set['title'] = $_POST['title'];
        $set['ask'] = $_POST['ask'];
        $set['report'] = $_POST['report'];
        $set['sheet_status'] = 100;

        $detail_response = save('sheet_details', $set);
        $set = array();
        $where = array();
        $set['detail_count'] = $line_no;
        $where['id'] = $_POST['id'];
        $response = update('sheets', $set, $where);

        if ($detail_response && $response) {
            echo alert('保存しました。');
            header("Location: ./?id=" . $_POST['id']);
            exit;
        }
    }
}

$id = $_GET['id'];
$sheet = array();
$sheet_detail_list = array();

if ($id) {

    $set = array();
    $set['id'] = $id;
    $set['user_id'] = $user_id;
    $sql = "SELECT * FROM sheets WHERE id = :id AND user_id = :user_id";
    $stmt = execute($sql, $set);
    $sheet_response = $stmt->fetch(PDO::FETCH_ASSOC);

    $detail_count = $sheet_response['detail_count'];
    $sheet_name = h($sheet_response['sheet_name']);
    $sheet['シート番号'] = $sheet_response['id'] . "<input type='hidden' class='sheet_id' value='{$sheet_response['id']}'>";
    $sheet['シート名'] = $sheet_name . "<input type='hidden' id='sheet_name' value='{$sheet_name}'>";
    $sheet['作成日'] = date('Y/m/d H:i:s', strtotime($sheet_response['created_at']));
    $sheet['最終更新日'] = "<span class='sheet_update_at'>" . date('Y/m/d H:i:s', strtotime($sheet_response['updated_at'])) . "</span>";

    $set = array();
    $set['id'] = $id;

    $sql = "SELECT * FROM sheet_details WHERE sheet_id = :id AND deleted_flg = 0";

    if ($_GET['search_status']) {
        $sql .= " AND sheet_status = :sheet_status";
        $search_status_display = h($_GET['search_status']);
    }
    if ($_GET['search_title']) {
        $sql .= " AND title = :title";
        $search_title_display = h($_GET['search_title']);
    }
    if ($_GET['search_created_at']) {
        $sql .= " AND created_at >= :created_at";
        $search_created_at_display = h($_GET['search_created_at']);
    }
    if ($_GET['search_updated_at']) {
        $sql .= " AND updated_at >= :updated_at";
        $search_updated_at_display = h($_GET['search_updated_at']);
    }

    $sql .= " ORDER BY `line_no` DESC";

    if ($_GET['search_status']) {
        $set['sheet_status'] = $_GET['search_status'];
    }
    if ($_GET['search_title']) {
        $set['title'] = $_GET['search_title'];
    }
    if ($_GET['search_created_at']) {
        $set['created_at'] = $_GET['search_created_at'] . " 00:00:00";
    }
    if ($_GET['search_updated_at']) {
        $set['updated_at'] = $_GET['search_updated_at'] . " 00:00:00";
    }

    $stmt = execute($sql, $set);

    $sheet_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($sheet_details as $sheet_detail) {

        $tmp = array();
        $tmp['id'] = $sheet_detail['id'];
        $tmp['index'] = $sheet_detail['line_no'];
        $tmp['title'] = nl2br(h($sheet_detail['title']));
        $tmp['ask'] = nl2br(h($sheet_detail['ask']));
        $tmp['ask_display'] = h($sheet_detail['ask']);
        $tmp['report'] = nl2br(h($sheet_detail['report']));
        $tmp['report_display'] = h($sheet_detail['report']);
        $tmp['status'] = $sheet_detail['sheet_status'];
        $tmp['register_name'] = h($sheet_detail['register_name']);
        $tmp['amender_name'] = h($sheet_detail['amender_name']);
        $tmp['inspector_name'] = h($sheet_detail['inspector_name']);
        $tmp['created_at'] = date('n/j H:i', strtotime($sheet_detail['created_at']));
        $tmp['updated_at'] = date('n/j H:i', strtotime($sheet_detail['updated_at']));

        if($sheet_detail['sheet_status'] === '500'){
            $tmp['bg-color'] = "gray";
        }elseif($sheet_detail['sheet_status'] === '400'){
            $tmp['bg-color'] = "red";
        }else{
            $tmp['bg-color'] = "";
        }
        $sheet_detail_list[] = $tmp;
    }
} else {
    header("Location: ../");
    exit;
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
    <title><?= $sheet['シート名']; ?></title>
</head>

<body>
    <div class="base">
        <a href="../">メニューに戻る</a>
        <table border="1" class="head_table">
            <tr>
                <?php foreach ($sheet as $key => $value) : ?>
                    <th><?= $key; ?></th>
                    <td><?= $value; ?></td>
                <?php endforeach; ?>
            </tr>
        </table>

        <div class="mg-10"></div>
        <form action="" method="post">
            <div>
                <input type="hidden" name="id" id="id" value="<?= $sheet_response['id']; ?>">
                <input type="hidden" name="detail_count" id="detail_count" value="<?= $detail_count; ?>">
                <input type="hidden" name="input_user_name" id="input_user_name" value="<?= $_SESSION['input_user_name']; ?>">
            </div>
            <table class="create_table">
                <tr>
                    <th class="w120 h30 bd">登録者名</th>
                    <td class="w130"><input type="text" name="register_name" id="register_name" class=" input_name"></td>
                    <th class="w120 h60 bd" rowspan="2">内容</th>
                    <td class="w240" rowspan="2"><textarea name="ask" id="ask" cols="50" rows="4"></textarea></td>
                </tr>
                <tr>
                    <th class="w120 h30 bd">タイトル</th>
                    <td><input type="text" name="title" id="title"></td>
                    <td><input type="submit" name="create" id="create" value="作成" class="w100"></td>
                    <td><input type="button" name="import_csv" id="import_csv" value="CSV取込" class="w100"></td>
                </tr>
            </table>
        </form>
        <div class="search_area create_table">
            <form action="" method="get">
                <input type="hidden" name="id" value="<?= $sheet['シート番号']; ?>" />
                <table>
                    <tr>
                    <th class="w100 bd">ステータス</th>
                        <td>
                            <select id="search_status" name="search_status">
                                <option value=""></option>
                                <?php foreach ($status_arr as $key => $status) : ?>
                                    <?php if ($key == $search_status_display) : ?>
                                        <option value="<?= $key; ?>" selected><?= $status; ?></option>
                                    <?php else : ?>
                                        <option value="<?= $key; ?>"><?= $status; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <th class="w100 bd">作成日</th>
                        <td><input type="date" name="search_created_at" id="search_created_at" value="<?=$search_created_at_display;?>">~</td>
                    </tr>
                    <tr>
                        <th class="w100 bd">タイトル</th>
                        <td><input type="text" id="search_title" name="search_title" value="<?= $search_title_display; ?>"></td>
                        <th class="w100 bd">最終更新日</th>
                        <td><input type="date" name="search_updated_at" id="search_updated_at" value="<?=$search_updated_at_display;?>">~</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><input type="submit" id="search" value="絞り込み" class="w100"></td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="mg-10"></div>

        <table border="1" class="list_table">
            <thead>
                <th class="w40">No.</th>
                <th class="w20"><input type="checkbox" id="all_check"></th>
                <th class="">タイトル</th>
                <th class="w260">依頼内容</th>
                <th class="w80">状況</th>
                <th class="w60">登録者</th>
                <th class="w60">対応者</th>
                <th class="w260">対応内容</th>
                <th class="w60">確認者</th>
                <th class="w80">作成日</th>
                <th class="w80">最終更新日</th>
            </thead>
            <tbody>
                <?php foreach ($sheet_detail_list as $sheet_detail) : ?>
                    <tr class="<?= $sheet_detail['bg-color'];?>">
                        <!-- INDEX -->
                        <td class="center check_box"><span><?= $sheet_detail['index']; ?></span></td>

                        <!-- CHECKBOX -->
                        <td class="center"><label for="check_<?= $sheet_detail['id']; ?>" class="check_label"><input type="checkbox" class="check" id="check_<?= $sheet_detail['id']; ?>"></label></td>

                        <!-- タイトル -->
                        <td>
                            <span class="display_input"><?= $sheet_detail['title']; ?></span>
                            <input type="text" data-id="title__<?= $sheet_detail['id']; ?>" value="<?= $sheet_detail['title']; ?>" class="hidden title hidden_input">
                        </td>

                        <!-- 依頼内容 -->
                        <td>
                            <span class="display_input"><?= $sheet_detail['ask']; ?></span>
                            <textarea data-id="ask__<?= $sheet_detail['id']; ?>" class="hidden ask hidden_input text_area"><?= $sheet_detail['ask_display']; ?></textarea>
                        </td>

                        <!-- 状況 -->
                        <td>
                            <select class="status" data-id="sheet_status__<?= $sheet_detail['id']; ?>">
                                <?php foreach ($status_arr as $key => $status) : ?>
                                    <?php if ($key == $sheet_detail['status']) : ?>
                                        <option value="<?= $key; ?>" selected><?= $status; ?></option>
                                    <?php else : ?>
                                        <option value="<?= $key; ?>"><?= $status; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- 登録者 -->
                        <td>
                            <span class="display_input"><?= $sheet_detail['register_name']; ?></span>
                            <input type="text" id="register_name" data-id="register_name__<?= $sheet_detail['id']; ?>" value="<?= $sheet_detail['register_name']; ?>" class="hidden hidden_input input_name">
                        </td>

                        <!-- 対応者 -->
                        <td>
                            <span class="display_input"><?= $sheet_detail['amender_name']; ?></span>
                            <input type="text" id="amender_name" data-id="amender_name__<?= $sheet_detail['id']; ?>" value="<?= $sheet_detail['amender_name']; ?>" class="hidden hidden_input input_name">
                        </td>

                        <!-- 依頼内容 -->
                        <td>
                            <span class="display_input"><?= $sheet_detail['report']; ?></span>
                            <textarea data-id="report__<?= $sheet_detail['id']; ?>" class="hidden report hidden_input text_area"><?= $sheet_detail['report_display']; ?></textarea>
                        </td>

                        <!-- 確認者 -->
                        <td>
                            <span class="display_input"><?= $sheet_detail['inspector_name']; ?></span>
                            <input type="text" id="inspector_name" data-id="inspector_name__<?= $sheet_detail['id']; ?>" value="<?= $sheet_detail['inspector_name']; ?>" class="hidden hidden_input input_name">
                        </td>

                        <!-- 作成日付 -->
                        <td class="center"><span><?= $sheet_detail['created_at']; ?></span></td>

                        <!-- 更新日付 -->
                        <td class="center"><span class="updated_at"><?= $sheet_detail['updated_at']; ?></span></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mg-80"></div>
        <div class="all_update_area">
            <table class="create_table">
                <tr>
                    <th class="w60 bd">状況</th>
                    <td>
                        <select id="all_status">
                            <option value=""></option>
                            <?php foreach ($status_arr as $key => $status) : ?>
                                <option value="<?= $key; ?>"><?= $status; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <th class="w60 bd">登録者</th>
                    <td><input type="text" id="all_register_name" class="input_name"></td>
                    <th class="w60 bd">対応者</th>
                    <td><input type="text" id="all_amender_name" class="input_name"></td>
                    <th class="w60 bd">確認者</th>
                    <td><input type="text" id="all_inspector_name" class="input_name"></td>
                    <td class="flex">
                        <span>チェックした行を</span>
                        <input type="button" value="一括更新" onclick="all_update();">
                        <input type="button" value="削除" onclick="row_delete();" style="margin-left: 2px;">
                        <input type="button" value="CSV" onclick="create_csv();" style="margin-left: 2px;">
                        <input type="button" value="報告書" onclick="create_report();" style="margin-left: 2px;">
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script src="sheet_detail.js?date=<?= date('YmdHis'); ?>" type="text/javascript"></script>
</body>

</html>