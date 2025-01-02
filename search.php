<?php
include('header.php');
require_once('funcs.php');

$pref  = $_POST["pref"];
$selectPoint = isset($_POST["point"]) ? $_POST["point"] : [];

//DB接続
$pdo = db_conn();

//２．データ取得SQL作成
$stmt = $pdo->prepare("SELECT * FROM taville_table WHERE pref = :pref ");
$stmt->bindValue(':pref', $pref, PDO::PARAM_STR);
$status = $stmt->execute();

//３．データ表示
if ($status === false) {
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("ErrorQuery:" . $error[2]);
}

// 全データ取得
$values  = $stmt->fetchAll(PDO::FETCH_ASSOC);

// array_table取得
$stmt = $pdo->prepare("SELECT * FROM array_table WHERE id IN(SELECT id FROM taville_table WHERE pref = :pref)");
$stmt->bindValue(':pref', $pref, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status === false) {
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("ErrorQuery:" . $error[2]);
}

$points = $stmt->fetchAll(PDO::FETCH_ASSOC);

// var_dump($points);

// 子テーブルのデータをIDごとにまとめる
$pointsByID = [];
foreach ($points as $point) {
    $pointsByID[$point['id']][] = $point['point'];
}

$matchHotels = [];
foreach ($values as $data) {
    if (!empty($selectPoint)) {
        $hotelPoints = isset($pointsByID[$data['id']]) ? $pointsByID[$data['id']] : [];
        $matchPoints = !array_diff($selectPoint, $hotelPoints);
        if (!$matchPoints) {
            continue;
        }
    }

    $matchHotels[] = $data;
}

?>

<main>
    <section class="archive">
        <?php if (empty($matchHotels)): ?>
            <p class="null_mes">該当するホテルが見つかりませんでした。</p>
        <?php else: ?>
            <?php foreach ($matchHotels as $data): ?>
                <a href="detail.php?id=<?php echo $data['id']; ?>">
                    <div class="container">
                        <figure>
                            <img src="img/<?php echo h($data['images']); ?>" alt="<?php echo h($data['name']); ?>">
                        </figure>
                        <h2><?php echo h($data['name']); ?></h2>
                        <?php if (!empty($pointsByID[$data['id']])): ?>
                            <ul class="point">
                                <?php foreach ($pointsByID[$data['id']] as $point): ?>
                                    <li><?php echo h($point); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>

    </section>
    <div>
        <p class="back"><a href="index.php">戻る</a></p>
    </div>

</main>

<?php include('footer.php'); ?>