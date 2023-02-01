<?php
require_once('../common/init.php');

$user_id = $_SESSION['id'];
$now = date('Y-m-d H:i:s');

$line_no_list = array();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if($_POST['save_report'] === '保存'){

        $set = array();
        $where = array();

        $set['sheet_id'] = $_POST['sheet_id'];
        $set['user_id'] = $user_id;
        $set['client_name'] = $_POST['client_name'];
        $set['system_name'] = $_POST['system_name'];
        $set['create_name'] = $_POST['create_name'];
        $set['create_date'] = $_POST['create_date'];
        $set['modify_date'] = $_POST['modify_date'];
        $set['finish_date'] = $_POST['finish_date'];
        $set['signer1'] = $_POST['signer1'];
        $set['signer2'] = $_POST['signer2'];
        $set['signer3'] = $_POST['signer3'];
        $set['signer4'] = $_POST['signer4'];
        $set['signer5'] = $_POST['signer5'];
        $set['note'] = $_POST['note'];

        $table = "reports";

        $saved = false;
        $updated = false;

        if($_POST['report_id'] === '0'){
            $saved = save($table, $set);
            $sql = "SELECT LAST_INSERT_ID() AS id;";
            $stmt = execute($sql);
            $LAST_INSERT_ID = $stmt->fetch(PDO::FETCH_ASSOC);
            $report_id = $LAST_INSERT_ID['id'];
        }else{
            $report_id = $_POST['report_id'];
            $where['id'] = $report_id;
            $updated = update($table, $set, $where);
        }

        $total = $_POST['total'];
        for($index = 1; $index <= $total; $index++){

            $set = array();
            $where = array();

            $set['report_id'] = $report_id;
            $set['line_no'] = $_POST['line_no_' . $index];
            $set['sheet_id'] = $_POST['sheet_id'];
            $set['sheet_detail_id'] = $_POST['id_' . $index];
            $set['user_id'] = $user_id;
            $set['title'] = $_POST['title_' . $index];
            $set['issue'] = $_POST['issue_' . $index];
            $set['cause'] = $_POST['cause_' . $index];
            $set['measures'] = $_POST['measures_' . $index];

            $table = "report_details";

            if($saved){
                $saved = save($table, $set);
            }else{
                $where['report_id'] = $report_id;
                $where['line_no'] = $_POST['line_no_' . $index];
                $updated = update($table, $set, $where);
            }
    
        }
    }

    echo alert($saved_message);
    header("Location: ../report_print/?id=".$report_id);
    exit;
}

$report_id = 0;
$details = array();
if (isset($_GET['mode'])) {
    if ($_GET['mode'] === 'create') {
        if ($_GET['id']) {

            $id_arr = json_decode($_GET['id'], true);
            $id_str = implode(",", $id_arr);

            $set = array();
            $set['user_id'] = $user_id;
            $sql = "SELECT * FROM sheet_details WHERE id IN ($id_str) AND user_id = :user_id";
            $stmt = execute($sql, $set);
            $sheet_detail_response = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $detail_count = 0;
            foreach ($sheet_detail_response as $sheet_detail) {
                $tmp = array();
                $tmp['id'] = h($sheet_detail['id']);
                $tmp['line_no'] = h($sheet_detail['line_no']);
                $tmp['title'] = h($sheet_detail['title']);
                $tmp['issue'] = h($sheet_detail['ask']);
                $tmp['cause'] = h($sheet_detail['ask']);
                $tmp['measures'] = h($sheet_detail['report']);
                /**
                 * textAreaの高さ調整用
                 */
                $tmp['issue_height'] = substr_count($sheet_detail['ask'], "\n") * 10;
                $tmp['cause_height'] = substr_count($sheet_detail['ask'], "\n") * 10;
                $tmp['measures_height'] = substr_count($sheet_detail['report'], "\n") * 10;
                $detail_count++;
                $tmp['index'] = $detail_count;
                $details[] = $tmp;
                $sheet_id = $sheet_detail['sheet_id'];

                $line_no_list[] = $sheet_detail['line_no'];
            }

            if ($sheet_id > 0) {

                $set = array();
                $set['user_id'] = $user_id;
                $set['id'] = $sheet_id;
                $sql = "SELECT * FROM sheets WHERE id = :id AND user_id = :user_id";
                $stmt = execute($sql, $set);
                $sheet = $stmt->fetch(PDO::FETCH_ASSOC);

                $report = array();
                $report['client_name'] = $sheet['sheet_name'];
                $report['system_name'] = $sheet['sheet_name'];
                $report['create_name'] = h($_SESSION['name']);
                $report['create_date'] = date('Y-m-d');
                $report['modify_date'] = date('Y-m-d');
                $report['finish_date'] = date('Y-m-d');
                $report['signer1'] = h($_SESSION['name']);
                $report['signer2'] = '';
                $report['signer3'] = '';
                $report['signer4'] = '';
                $report['signer5'] = '';
                $report['note'] = '';
                $tmp['note_height'] = 10;
        
            }
        }
    }

    if($_GET['mode'] === 'edit' && $_GET['id']){
        $set = array();
        $set['user_id'] = $user_id;
        $set['report_id'] = $_GET['id'];
        $sql = "SELECT * FROM report_details WHERE report_id = :report_id AND user_id = :user_id ORDER BY line_no";
        $stmt = execute($sql, $set);
        $report_detail_response = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $detail_count = 0;
        foreach ($report_detail_response as $report_detail) {
            $tmp = array();
            $tmp['id'] = h($report_detail['id']);
            $tmp['line_no'] = h($report_detail['line_no']);
            $tmp['title'] = h($report_detail['title']);
            $tmp['issue'] = h($report_detail['issue']);
            $tmp['cause'] = h($report_detail['cause']);
            $tmp['measures'] = h($report_detail['measures']);
            /**
             * textAreaの高さ調整用
             */
            $tmp['issue_height'] = substr_count($report_detail['issue'], "\n") * 1.8;
            $tmp['cause_height'] = substr_count($report_detail['cause'], "\n") * 1.8;
            $tmp['measures_height'] = substr_count($report_detail['measures'], "\n") * 1.8;

            $detail_count++;
            $tmp['index'] = $detail_count;
            $details[] = $tmp;

            $line_no_list[] = $report_detail['line_no'];

        }


        $set = array();
        $set['user_id'] = $user_id;
        $set['id'] = $_GET['id'];
        $sql = "SELECT * FROM reports WHERE id = :id AND user_id = :user_id";
        $stmt = execute($sql, $set);
        $report_response = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $report = array();
        $report['client_name'] = h($report_response['client_name']);
        $report['system_name'] = h($report_response['system_name']);
        $report['create_name'] = h($report_response['create_name']);
        $report['create_date'] = h($report_response['create_date']);
        $report['modify_date'] = h($report_response['modify_date']);
        $report['finish_date'] = h($report_response['finish_date']);
        $report['signer1'] = h($report_response['signer1']);
        $report['signer2'] = h($report_response['signer2']);
        $report['signer3'] = h($report_response['signer3']);
        $report['signer4'] = h($report_response['signer4']);
        $report['signer5'] = h($report_response['signer5']);
        $report['note'] = h($report_response['note']);
        $report['note_height'] = substr_count($report_response['note'], "\n") * 1.8;


        $report_id = $report_response['id'];
        $sheet_id = $report_response['sheet_id'];
    }
}

$line_no_list_txt = "";
if(count($line_no_list) > 0){
    $line_no_list_txt = " ・ ".implode(", ", $line_no_list);
}

if(!$report['create_name']) $report['create_name'] = $_SESSION['input_user_name'];
if(!$report['signer1']) $report['signer1'] = $_SESSION['input_user_name'];
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-language" content="ja">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/report.css?date=<?= date('YmdHis'); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <title>報告書</title>
</head>

<body>
    <form action="" method="post">
        <div>
            <input type="hidden" name="total" value="<?= $detail_count; ?>">
            <input type="hidden" name="sheet_id" value="<?= $sheet_id; ?>">
            <input type="hidden" name="report_id" value="<?= $report_id; ?>">
        </div>
        <section class="page">
            <h1>作業報告書</h1>
            <table>
                    <tr>
                        <th rowspan="2" class="w120">顧客名</th>
                        <td rowspan="2" class="w300"><input type="text" name="client_name" value="<?= $report['client_name'];?>"></td>
                        <th class="w120">作成者</th>
                        <td class="w200"><input type="text" name="create_name" value="<?= $report['create_name']; ?>"></td>
                    </tr>
                    <tr>
                        <th>作成日</th>
                        <td><input type="date" name="create_date" value="<?= $report['create_date']; ?>"></td>
                    </tr>
                    <tr>
                        <th rowspan="2">システム名</th>
                        <td rowspan="2"><input type="text" name="system_name" value="<?= $report['system_name'];?>"></td>
                        <th>対応日</th>
                        <td><input type="date" name="modify_date" value="<?= $report['modify_date']; ?>"></td>
                    </tr>
                    <tr>
                        <th>完了日</th>
                        <td><input type="date" name="finish_date" value="<?= $report['finish_date']; ?>"></td>
                    </tr>
                    <tr>
                        <th colspan="4">作業詳細No.</th>
                    </tr>
                    <tr>
                        <td colspan="4" class="h40"><span><?= $line_no_list_txt;?></span></td>
                    </tr>
                    <tr>
                        <th colspan="4">作業内容</th>
                    </tr>
                    <tr>
                        <td colspan="4" class="relative">
                            <div class="sign">
                                <table class="w100p">
                                    <tr>
                                        <th>確認1</th>
                                    </tr>
                                    <tr>
                                        <td class="h60">
                                            <input class="center" type="text" name="signer1" value="<?= $report['signer1']; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>確認2</th>
                                    </tr>
                                    <tr>
                                        <td class="h60">
                                            <input class="center" type="text" name="signer2" value="<?= $report['signer2']; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>確認3</th>
                                    </tr>
                                    <tr>
                                        <td class="h60">
                                            <input class="center" type="text" name="signer3" value="<?= $report['signer3']; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>確認4</th>
                                    </tr>
                                    <tr>
                                        <td class="h60">
                                            <input class="center" type="text" name="signer4" value="<?= $report['signer4']; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>確認5</th>
                                    </tr>
                                    <tr>
                                        <td class="h60">
                                            <input class="center" type="text" name="signer5" value="<?= $report['signer5']; ?>">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="base">
                                <h4>問題</h4>
                                <?php foreach ($details as $detail) : ?>
                                    <div class="sheet_detail_box">
                                        <h3><?= $detail['line_no'] . "." . $detail['title']; ?></h3>
                                        <ul>
                                            <li>
                                                <textarea name="issue_<?= $detail['index']; ?>" class="issue" rows="<?= $detail['issue_height']; ?>"><?= $detail['issue']; ?></textarea>
                                            </li>
                                        </ul>
                                        <input type="hidden" class="id" name="id_<?= $detail['index']; ?>" value="<?= $detail['id']; ?>">
                                        <input type="hidden" class="line_no" name="line_no_<?= $detail['index']; ?>" value="<?= $detail['line_no']; ?>">
                                        <input type="hidden" class="title" name="title_<?= $detail['index']; ?>" value="<?= $detail['title']; ?>">
                                    </div>
                                <?php endforeach; ?>
                                <h4>原因</h4>
                                <?php foreach ($details as $detail) : ?>
                                    <div class="sheet_detail_box">
                                        <h3><?= $detail['line_no'] . "." . $detail['title']; ?></h3>
                                        <ul>
                                            <li>
                                                <textarea name="cause_<?= $detail['index']; ?>" class="cause" rows="<?= $detail['cause_height']; ?>"><?= $detail['cause']; ?></textarea>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                                <h4>対策</h4>
                                <?php foreach ($details as $detail) : ?>
                                    <div class="sheet_detail_box">
                                        <h3><?= $detail['line_no'] . "." . $detail['title']; ?></h3>
                                        <ul>
                                            <li>
                                                <textarea name="measures_<?= $detail['index']; ?>" class="measures" rows="<?= $detail['measures_height']; ?>"><?= $detail['measures']; ?></textarea>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                                <div>
                                    <h4>備考欄</h4>
                                    <textarea name="note" class="note" rows="<?= $report['note_height']; ?>"><?= $report['note']; ?></textarea>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" id="save_btn" name="save_report" value="保存">
            <input type="button" onclick="window.location.href='../sheet_detail/?id=<?= $sheet_id; ?>';" class="no_print" value="戻る">

        </section>
    </form>
    <script src="report.js?date=<?= date('YmdHis'); ?>" type="text/javascript"></script>
</body>

</html>