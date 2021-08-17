<?php

// https://gray-code.com/php/insert-data-by-using-pdo/ PDOでデータを新規登録（INSERT）
// https://qiita.com/hidepy/items/42220523cb2b3eb2c451 【PHP】JSONでPOSTされた値の取り出し方。file_get_contents("php://input") するようだ。

ini_set('display_errors', "On");
define("DSN", "mysql:host=mysql_host;dbname=test_database;");

function pdo($sql, $isPost, $post) {
    try {
        $pdo = new PDO(DSN, 'docker', 'docker');
        $stmt = $pdo->prepare($sql);
        if($isPost) {
            $stmt->bindParam( ':name', $post["Name"], PDO::PARAM_STR);
            $stmt->bindParam( ':age', $post["Age"], PDO::PARAM_STR);
            $stmt->bindParam( ':hire', $post["HireDate"], PDO::PARAM_STR);
        }
        $stmt->execute();
        $pdo = null; // DB接続解除
        if(!$isPost) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "POST succeeded!" . PHP_EOL;
            var_dump($result);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

function postData($post) {
    $sql = "INSERT INTO test_table (name, age, hire_date) VALUES (:name, :age, :hire)";
    pdo($sql, true, $post);
}

function getData() {
    $sql = "SELECT * FROM test_table";
    pdo($sql, false, null);
}

$json = file_get_contents("php://input"); // POSTされたJSON文字列を取り出し
$contents = json_decode($json, true); // JSON文字列をobjectに変換（第2引数をtrueにしないとハマるので注意）
if(!array_key_exists("Name", $contents)) {
    $contents = $_POST;
}

postData($contents);
getData();
