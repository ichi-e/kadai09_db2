<?php

session_start();

include('funcs.php');

$name = $_POST['name'] ?? '';
$pref = $_POST['pref'];
$url   = $_POST["url"];
$stars = $_POST['stars'] ?? '0';
$comment  = $_POST["comment"] ?? '';
$points = $_POST["point"];

// ファイル
$file = $_FILES["img"];
$filename = basename($file["name"]);
$tmp_path = $file["tmp_name"];
$file_err = $file["error"];
$filesize = $file["size"];
$upload_dir = __DIR__ . "/img/";

$errors = [];
// 必須項目のチェック
if (empty($name)) {
    $errors['name'] = 'ホテル名は必須です。';
}
if ($pref == "選択して下さい") {
    $errors['pref'] = '都道府県は必須です。';
}
if ($stars === '0') {
    $errors['stars'] = 'オススメ度は必須です。';
}
if (empty($_POST['point'])) {
    $errors['point'] = 'うれしいポイントを選んでください。';
}
// 画像ファイルのエラーチェック
if ($_FILES['img']['error'] === UPLOAD_ERR_NO_FILE) {
    if (empty($_SESSION['img_data'])) {
        $errors['img'] = '画像を登録してください。';
    } else {
        $sava_filename = $_SESSION['img_data']; // 既存の画像を使用
    }
} else {
    // 新しい画像がアップロードされた場合
    $sava_filename = date("YmdHis") . $filename;
    move_uploaded_file($tmp_path, $upload_dir . $sava_filename);
    $_SESSION['img_data'] = $sava_filename;  // 新しい画像をセッションに保存
}
if (empty($_POST['comment'])) {
    $errors['comment'] = 'コメントは必須です。';
}
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['input_data'] = $_POST;
    $_SESSION['from_insert'] = true;
    header('Location: input.php');
    exit(); // エラーがあれば以降の処理を止める
}

//DB接続
$pdo = db_conn();

//３．データ登録SQL作成

// 1. SQL文を用意
$stmt = $pdo->prepare("INSERT INTO taville_table(id, name, pref, url, stars, images, comment, date) 
VALUES(NULL, :name, :pref, :url, :stars, :images, :comment, now())");

//  2. バインド変数を用意
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':pref', $pref, PDO::PARAM_STR);
$stmt->bindValue(':url', $url, PDO::PARAM_STR);
$stmt->bindValue(':stars', $stars, PDO::PARAM_INT);
$stmt->bindValue(':images', $sava_filename, PDO::PARAM_STR);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
$status = $stmt->execute();

//４．データ登録処理後
if ($status === false) {
    sql_error($stmt);
}

$hotel_id = $pdo->lastInsertId();

// ポイントを別テーブルに格納
$point = $_POST["point"];

$stmt = $pdo->prepare("INSERT INTO array_table(id, point) 
VALUES(:id, :point)");

foreach ($point as $p) {
    $status = $stmt->execute([
        ':id' => $hotel_id,
        ':point' => $p
    ]);

    if ($status === false) {
        $error = $stmt->errorInfo();
        exit('ErrorMessage:' . $error[2]);
    }
}

// 登録完了後、リダイレクト
$_SESSION['success'] = "登録が完了しました。";
header('Location: input.php');
exit();
