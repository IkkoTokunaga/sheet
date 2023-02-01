<?php
require_once('../common/init.php');

$now = date('Y-m-d H:i:s');
$status_arr = array(
    '100' => '未対応',
    '200' => '対応済',
    '300' => '確認済',
    '400' => '保留',
    '500' => '完了',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /**
     * 更新処理
     */
    if ($_POST['act'] === 'update_sheet_detail') {

        $result = array();

        if ($_POST['data_id']) {
            $data_id_arr = explode("__", $_POST['data_id']);
            $type = $data_id_arr[0];
            $id = $data_id_arr[1];
            $sheet_id = $_POST['sheet_id'];
            $value = $_POST['value'];

            $sql = "UPDATE sheet_details SET updated_at = :updated_at, {$type} = :val WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':updated_at', $now, PDO::PARAM_STR);
            $stmt->bindParam(':val', $value, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $detail_response = $stmt->execute();

            $set = array();
            $where = array();
            $where['id'] = $sheet_id;

            $response = update('sheets', $set, $where);
            if ($detail_response && $response) {
                $result['response'] = 'OK';
                $result['date'] = date('n/j H:i', strtotime($now));
                $result['date2'] = date('Y/m/d H:i:s', strtotime($now));
            } else {
                $result['response'] = 'ERROR';
                $result['detail'] = '保存に失敗しました。';
            }
        } else {
            $result['response'] = 'ERROR';
            $result['detail'] = 'アクセスエラー';
        }

        header("Content-type: application/json; charset=UTF-8");
        echo json_encode($result);
        exit;
    }


    /**
     * 一括更新処理
     */
    if ($_POST['act'] === 'all_update_sheet_detail') {

        $result = array();
        $id_list = json_decode($_POST['id'], true);

        if (count($id_list) > 0) {

            $status = $_POST['status'];
            $register_name = $_POST['register_name'];
            $amender_name = $_POST['amender_name'];
            $inspector_name = $_POST['inspector_name'];
            $sheet_id = $_POST['sheet_id'];

            $update_list = array(
                'sheet_status' => $status,
                'register_name' => $register_name,
                'amender_name' => $amender_name,
                'inspector_name' => $inspector_name,
                'sheet_id' => $sheet_id,
                'updated_at' => $now
            );

            $update_array = array();
            $bindParam_list = array();

            foreach ($update_list as $key => $value) {
                if ($value) {
                    $update_array[] = $key . " = :" . $key;
                    $bindParam_list[$key] = $value;
                }
            }

            $update_sql = implode(", ", $update_array);
            $id_sql = implode(", ", $id_list);

            $sql = "UPDATE sheet_details SET {$update_sql} WHERE id IN ( $id_sql ) AND sheet_id = :sheet_id";
            $stmt = $pdo->prepare($sql);

            foreach ($bindParam_list as $key => $param) {
                $stmt->bindValue(":{$key}", $param, PDO::PARAM_STR);
            }
            $detail_response = $stmt->execute();

            $set = array();
            $where = array();
            $where['id'] = $sheet_id;

            $response = update('sheets', $set, $where);

            if ($detail_response && $response) {
                $result['response'] = 'OK';
                $result['date'] = date('n/j H:i', strtotime($now));
                $result['date2'] = date('Y/m/d H:i:s', strtotime($now));
            } else {
                $result['response'] = 'ERROR';
                $result['detail'] = '保存に失敗しました。';
            }
        } else {
            $result['response'] = 'ERROR';
            $result['detail'] = 'checkを入れてください。';
        }

        header("Content-type: application/json; charset=UTF-8");
        echo json_encode($result);
        exit;
    }


    /**
     * 削除処理
     */
    if ($_POST['act'] === 'row_delete') {

        $result = array();
        $id_list = json_decode($_POST['id'], true);

        if (count($id_list) > 0) {

            $sheet_id = $_POST['sheet_id'];
            $id_sql = implode(", ", $id_list);

            $sql = "UPDATE sheet_details SET deleted_flg = 1, updated_at = :updated_at WHERE id IN ( $id_sql ) AND sheet_id = :sheet_id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":updated_at", $now, PDO::PARAM_STR);
            $stmt->bindValue(":sheet_id", $sheet_id, PDO::PARAM_INT);
            $detail_response = $stmt->execute();

            $set = array();
            $where = array();
            $where['id'] = $sheet_id;

            $response = update('sheets', $set, $where);

            if ($detail_response && $response) {
                $result['response'] = 'OK';
                $result['date'] = date('Y/m/d H:i:s', strtotime($now));
            } else {
                $result['response'] = 'ERROR';
                $result['detail'] = '保存に失敗しました。';
            }
        } else {
            $result['response'] = 'ERROR';
            $result['detail'] = 'checkを入れてください。';
        }

        header("Content-type: application/json; charset=UTF-8");
        echo json_encode($result);
        exit;
    }


    /**
     * CSV作成
     */
    if ($_POST['act'] === 'create_csv') {

        $id_list = json_decode($_POST['id'], true);

        if (count($id_list) > 0) {

            $sheet_id = $_POST['sheet_id'];
            $id_sql = implode(", ", $id_list);

            $sql = "SELECT
                        line_no,
                        title,
                        sheet_status,
                        register_name,
                        ask,
                        amender_name,
                        report,
                        inspector_name,
                        created_at,
                        updated_at
                    FROM sheet_details WHERE sheet_id = :sheet_id AND id IN ( $id_sql )";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':sheet_id', $sheet_id, PDO::PARAM_INT);
            $response = $stmt->execute();
            $sheet_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $row_txt = "";
            foreach ($sheet_details as $sheet_detail) {
                $row_array = array();
                foreach ($sheet_detail as $key => $value) {
                    if ($key === 'sheet_status') {
                        $value = $status_arr[$value];
                    }
                    $value = '"' . $value . '"';
                    $row_array[] = $value;
                }
                $row_txt .= implode(",", $row_array) . PHP_EOL;
            }

            $sql = "SELECT * FROM sheets WHERE id = :id";
            $set = array();
            $set['id'] = $sheet_id;
            $stmt = execute($sql, $set);
            $sheet = $stmt->fetch(PDO::FETCH_ASSOC);


            $header = "No,タイトル,状況,登録者,内容,対応者,対応内容,確認者,作成日,最終更新日" . PHP_EOL;

            $csv_data = mb_convert_encoding($header . $row_txt, "sjis-win", "UTF-8");

            $file_name = date('Ymd_His') . "_" . $sheet['id'] . ".csv";
            $path = "csv/" . $sheet['id'];

            if (!file_exists($path)) {
                mkdir($path, 0775, true);
            }

            /**
             * 過去作成分を掃除
             */
            foreach (glob($path . "/*") as $file) {
                unlink($file);
            }
            file_put_contents($path . "/" . $file_name, $csv_data);

            echo $file_name;
        } else {
            echo 'NG';
        }

        exit;
    }
}

header("Location: ../");
exit;
