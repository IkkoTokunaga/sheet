<?php


try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ";charset=utf8;", DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->query("SET NAMES utf8;");
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}

/**
 * @param テーブル名、配列column=>$value
 * @return bool値かエラーメッセージ
 */
function save($table, $set = array())
{
    global $pdo;
    $now = date('Y-m-d H:i:s');

    $column = array();
    $params = array();
    $values = array();

    $set['created_at'] = $now;
    $set['updated_at'] = $now;

    foreach ($set as $key => $val) {
        $column[] = $key;
        $params[] = ":" . $key;
        $values[] = $val;
    }

    try {

        $COLUMN = implode(",", $column);
        $VALUES = implode(",", $params);
        $SQL = "INSERT INTO $table ($COLUMN) VALUES ($VALUES)";
        $stmt = $pdo->prepare($SQL);
        $max = count($set);
        for ($i = 0; $i < $max; $i++) {
            $stmt->bindValue($params[$i], $values[$i]);
        }
        return $stmt->execute();
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function update($table, $set = array(), $where)
{
    global $pdo;
    $now = date('Y-m-d H:i:s');

    $column = array();
    $params = array();
    $values = array();

    $set['updated_at'] = $now;

    foreach ($set as $key => $val) {
        $column[] = $key . " = :" . $key;
        $params[] = ":" . $key;
        $values[] = $val;
    }

    $where_column = array();
    foreach ($where as $key => $val) {
        $where_column[] = $key . " = :" . $key;
        $params[] = ":" . $key;
        $values[] = $val;
    }


    try {

        $COLUMN = implode(",", $column);
        $WHERE = implode(" AND ", $where_column);
        $SQL = "UPDATE $table SET $COLUMN WHERE $WHERE";
        $stmt = $pdo->prepare($SQL);
        $max = count($params);
        for ($i = 0; $i < $max; $i++) {
            $stmt->bindValue($params[$i], $values[$i]);
        }
        return $stmt->execute();
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function execute($sql, $array = array())
{
    global $pdo;

    $params = array();
    $values = array();
    foreach ($array as $key => $val) {
        $params[] = ":" . $key;
        $values[] = $val;
    }

    try {
        $stmt = $pdo->prepare($sql);
        $max = count($params);
        for ($i = 0; $i < $max; $i++) {
            $stmt->bindValue($params[$i], $values[$i]);
        }
        $stmt->execute();
        return $stmt;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}
