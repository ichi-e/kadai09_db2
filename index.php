<?php
include('header.php');
include('funcs.php');
$pdo = db_conn();

//２．データ取得SQL作成
$stmt = $pdo->prepare("SELECT * FROM taville_table;");
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
$stmt = $pdo->prepare("SELECT * FROM array_table");
$status = $stmt->execute();

if ($status === false) {
    sql_error($stmt);
}

$points = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 子テーブルのデータをIDごとにまとめる
$pointsByID = [];
foreach ($points as $point) {
    $pointsByID[$point['id']][] = $point['point'];
}
// 都道府県
include('pref.php');

$selectedPref = "選択して下さい";

?>

<main>
    <section class="search">
        <form action="search.php" method="post">
            <div>
                <h2>都道府県から検索</h2>
                <select name="pref">
                    <?php foreach ($pref as $pref): ?>
                        <option value="<?= h($pref) ?>"
                            <?= $pref === $selectedPref ? 'selected' : '' ?>>
                            <?= h($pref) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <h2>こだわり条件を選ぶ</h2>
                <div class="check_area">
                    <label><input type="checkbox" name="point[]" value="バス・トイレ別">バス・トイレ別</label>
                    <label><input type="checkbox" name="point[]" value="小学生まで添い寝OK">小学生まで添い寝OK</label>
                    <label><input type="checkbox" name="point[]" value="ベット幅110cm以上">ベット幅110cm以上</label>
                    <label><input type="checkbox" name="point[]" value="靴を脱ぐお部屋">靴を脱ぐお部屋</label>
                    <label><input type="checkbox" name="point[]" value="駅近">駅近</label>
                    <label><input type="checkbox" name="point[]" value="ショッピングセンター併設">ショッピングセンター併設</label>
                </div>
            </div>

            <div class="submit">
                <button type="submit">検索</button>
            </div>
        </form>

    </section>

    <section class="archive">
        <?php foreach ($values as $data): ?>
            <div class="container">
                <a href="detail.php?id=<?php echo $data['id']; ?>">
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
                </a>
                <ul class="edit">
                    <li><a href="change.php?id=<?php echo $data['id']; ?>">編集</a></li>
                    <li><a href="delete.php?id=<?php echo $data['id']; ?>">削除</a></li>
                </ul>
            </div>
        <?php endforeach; ?>
    </section>
</main>

<?php include('footer.php'); ?>