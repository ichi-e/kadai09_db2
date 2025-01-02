<?php

include('header.php');
include('funcs.php');

$id = $_GET['id'];
var_dump($id);

//DB接続
$pdo = db_conn();

$sql = "DELETE taville_table, array_table FROM taville_table INNER JOIN array_table ON taville_table.id=array_table.id WHERE taville_table.id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status === false) {
    sql_error($stmt);
}
else{
    header('Location: index.php');
    exit();
}
?>