<?php

include('header.php');
include('funcs.php');
//DB接続
$pdo = db_conn();

$name  = $_POST["name"];
$pref  = $_POST["pref"];
$url   = $_POST["url"];
$stars = $_POST["stars"];
$comment  = $_POST["comment"];
$id = $_POST["id"];

// 今の画像をデータベースから取得
$sql = "SELECT images FROM taville_table WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status === false) {
    sql_error($stmt);
}

$currentImg = $stmt->fetchColumn();

// ファイル
$file = $_FILES["img"];
$filename = basename($file["name"]);
$tmp_path = $file["tmp_name"];
$file_err = $file["error"];
$filesize = $file["size"];
$upload_dir = __DIR__ . "/img/";
$sava_filename = $currentImg;

if (is_uploaded_file($tmp_path)) {
    $sava_filename = date("YmdHis") . $filename;
    move_uploaded_file($tmp_path, $upload_dir . $sava_filename);
}


//３．データ登録SQL作成
$sql = "UPDATE taville_table SET name=:name, pref=:pref, url=:url, stars=:stars, images=:images, comment=:comment WHERE id=:id";
$stmt = $pdo->prepare($sql);

//  2. バインド変数を用意
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':pref', $pref, PDO::PARAM_STR);
$stmt->bindValue(':url', $url, PDO::PARAM_STR);
$stmt->bindValue(':stars', $stars, PDO::PARAM_INT);
$stmt->bindValue(':images', $sava_filename, PDO::PARAM_STR);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

//４．データ登録処理後
if ($status === false) {
    sql_error($stmt);
}

// ポイントを別テーブルに格納
$hotel_id = $id;
$point = $_POST["point"];

// 既存のポイントデータを削除
$sql = "DELETE FROM array_table WHERE id=:id"; 
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $hotel_id, PDO::PARAM_INT);
$stmt->execute();

$sql = "INSERT INTO array_table (id, point) VALUES (:id, :point)";
$stmt = $pdo->prepare($sql);

// ポイントデータを挿入
foreach ($point as $p) {
    $stmt->execute([
        ':id' => $hotel_id,
        ':point' => $p
    ]);
}


// 登録完了後、リダイレクト
header('Location: index.php');
exit();
