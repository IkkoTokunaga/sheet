<?php
require_once('../common/init.php');

$user_id = $_SESSION['id'];
$now = date('Y-m-d H:i:s');
$line_no_list = array();


$report_id = 0;
$details = array();

if ($_GET['id'] > 0) {
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
        $tmp['title'] = nl2br(h($report_detail['title']));
        $tmp['issue'] = nl2br(h($report_detail['issue']));
        $tmp['cause'] = nl2br(h($report_detail['cause']));
        $tmp['measures'] = nl2br(h($report_detail['measures']));

        $details[] = $tmp;
        $detail_count++;

        $line_no_list[] = $report_detail['line_no'];
    }


    $set = array();
    $set['user_id'] = $user_id;
    $set['id'] = $_GET['id'];
    $sql = "SELECT * FROM reports WHERE id = :id AND user_id = :user_id";
    $stmt = execute($sql, $set);
    $report_response = $stmt->fetch(PDO::FETCH_ASSOC);

    $report = array();
    $report['client_name'] = nl2br(h($report_response['client_name']));
    $report['system_name'] = nl2br(h($report_response['system_name']));
    $report['create_name'] = nl2br(h($report_response['create_name']));
    $report['create_date'] = nl2br(h($report_response['create_date']));
    $report['modify_date'] = nl2br(h($report_response['modify_date']));
    $report['finish_date'] = nl2br(h($report_response['finish_date']));
    $report['signer1'] = nl2br(h($report_response['signer1']));
    $report['signer2'] = nl2br(h($report_response['signer2']));
    $report['signer3'] = nl2br(h($report_response['signer3']));
    $report['signer4'] = nl2br(h($report_response['signer4']));
    $report['signer5'] = nl2br(h($report_response['signer5']));
    $report['note'] = nl2br(h($report_response['note']));

    $report_id = $report_response['id'];
    $sheet_id = $report_response['sheet_id'];
}

$line_no_list_txt = "";
if (count($line_no_list) > 0) {
    $line_no_list_txt = " ・ " . implode(", ", $line_no_list);
}

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
        <section class="page">
            <h1>作業報告書</h1>
            <table>
                <thead>
                    <tr>
                        <td colspan="4" class="section-line"></td>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <th rowspan="2" class="w120">顧客名</th>
                        <td rowspan="2" class="w300"><?= $report['client_name']; ?></td>
                        <th class="w120">作成者</th>
                        <td class="w200"><?= $report['create_name']; ?></td>
                    </tr>
                    <tr>
                        <th>作成日</th>
                        <td><?= $report['create_date']; ?></td>
                    </tr>
                    <tr>
                        <th rowspan="2">システム名</th>
                        <td rowspan="2"><?= $report['system_name']; ?></td>
                        <th>対応日</th>
                        <td><?= $report['modify_date']; ?></td>
                    </tr>
                    <tr>
                        <th>完了日</th>
                        <td><?= $report['finish_date']; ?></td>
                    </tr>
                    <tr>
                        <th colspan="4">作業詳細No.</th>
                    </tr>
                    <tr>
                        <td colspan="4" class="h40"><span><?= $line_no_list_txt; ?></span></td>
                    </tr>
                    <tr>
                        <th colspan="4">作業内容</th>
                    </tr>
                    <tr>
                        <td colspan="4" class="relative report_box">
                            <div class="sign">
                                <table class="w100p">
                                    <tr>
                                        <th>確認1</th>
                                    </tr>
                                    <tr>
                                        <td class="h60 center"><?= $report['signer1']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>確認2</th>
                                    </tr>
                                    <tr>
                                        <td class="h60 center"><?= $report['signer2']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>確認3</th>
                                    </tr>
                                    <tr>
                                        <td class="h60 center"><?= $report['signer3']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>確認4</th>
                                    </tr>
                                    <tr>
                                        <td class="h60 center"><?= $report['signer4']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>確認5</th>
                                    </tr>
                                    <tr>
                                        <td class="h60 center"><?= $report['signer5']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="base">
                                <h4>問題</h4>
                                <?php foreach ($details as $detail) : ?>
                                    <div class="w400">
                                        <span>■<?= $detail['line_no'] . " " . $detail['title']; ?></span>
                                        <span>
                                            <?= $detail['issue']; ?>
                                        </span>
                                        <input type="hidden" class="id" name="id_<?= $detail['line_no']; ?>" value="<?= $detail['id']; ?>">
                                        <input type="hidden" class="line_no" name="line_no_<?= $detail['line_no']; ?>" value="<?= $detail['line_no']; ?>">
                                        <input type="hidden" class="title" name="title_<?= $detail['line_no']; ?>" value="<?= $detail['title']; ?>">
                                    </div>
                                <?php endforeach; ?>
                                <h4>原因</h4>
                                <?php foreach ($details as $detail) : ?>
                                    <div class="w400">
                                        <span>■<?= $detail['line_no'] . " " . $detail['title']; ?></span>
                                        <span>
                                            <?= $detail['cause']; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                                <h4>対策</h4>
                                <?php foreach ($details as $detail) : ?>
                                    <div class="w400">
                                        <span>■<?= $detail['line_no'] . " " . $detail['title']; ?></span>
                                        <span>
                                            <?= $detail['measures']; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                                <div>
                                    <h4>アップするファイル</h4>
                                    <?= $report['note']; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" onclick="window.print();" class="no_print" id="save_btn" name="save_report" value="印刷">
            <input type="button" onclick="window.location.href='../report/?mode=edit&id=<?= $report_id; ?>';" class="no_print" id="edit_btn" value="修正">
            <input type="button" onclick="window.location.href='../sheet_detail/?id=<?= $sheet_id; ?>';" class="no_print" value="戻る">
        </section>
    </form>
    <script src="report.js?date=<?= date('YmdHis'); ?>" type="text/javascript"></script>
</body>

</html>