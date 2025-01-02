<?php
//共通に使う関数を記述
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($stg)
{
    return htmlspecialchars($stg, ENT_QUOTES);
}

//DB接続
function db_conn(){
    try {
        return new PDO('mysql:dbname=taville;charset=utf8;host=localhost', 'root', '');
    } catch (PDOException $e) {
        exit('DBConnectError:' . $e->getMessage());
    }
}

//execute（SQL実行時にエラーがある場合）

function sql_error($stmt)
{
    $error = $stmt->errorInfo();
    exit("ErrorQuery:" . $error[2]);
}

