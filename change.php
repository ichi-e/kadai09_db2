<?php

include('header.php');
require_once('funcs.php');

$pdo = db_conn();

require_once 'config.php';
$id = $_GET['id'];

//２．データ取得SQL作成
$stmt = $pdo->prepare("SELECT * FROM taville_table WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

//３．データ表示
if ($status === false) {
    sql_error($stmt);
}

// 全データ取得
$v  = $stmt->fetch(PDO::FETCH_ASSOC);

$hotelName = h($values['name']);

// array_table取得
$stmt = $pdo->prepare("SELECT * FROM array_table WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status === false) {
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("ErrorQuery:" . $error[2]);
}

$points = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pointArray = [];
foreach ($points as $point) {
    $pointArray[] = $point['point'];
}

// 都道府県
$pref = [
    "北海道",
    "青森県",
    "岩手県",
    "宮城県",
    "秋田県",
    "山形県",
    "福島県",
    "茨城県",
    "栃木県",
    "群馬県",
    "埼玉県",
    "千葉県",
    "東京都",
    "神奈川県",
    "新潟県",
    "富山県",
    "石川県",
    "福井県",
    "山梨県",
    "長野県",
    "岐阜県",
    "静岡県",
    "愛知県",
    "三重県",
    "滋賀県",
    "京都府",
    "大阪府",
    "兵庫県",
    "奈良県",
    "和歌山県",
    "鳥取県",
    "島根県",
    "岡山県",
    "広島県",
    "山口県",
    "徳島県",
    "香川県",
    "愛媛県",
    "高知県",
    "福岡県",
    "佐賀県",
    "長崎県",
    "熊本県",
    "大分県",
    "宮崎県",
    "鹿児島県",
    "沖縄県"
];

$selectedPref = $v["pref"];

?>

<main>
    <!-- エラーメッセージ表示 -->
    <?php if (!empty($errors)): ?>
        <div class="message">
            <p>未入力の項目があります。</p>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo h($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 成功メッセージ表示 -->
    <?php if (isset($success)): ?>
        <div class="message">
            <p style="color: green;"><?php echo h($success); ?></p>
        </div>
    <?php endif; ?>



    <form enctype="multipart/form-data" action="update.php" method="post">
        <p>＊は必須項目です</p>
        <div>
            <h2>ホテル名＊</h2>
            <input type="text" name="name" value="<?= $v["name"] ?>">
        </div>
        <div>
            <h2>都道府県＊</h2>
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
            <h2>URL</h2>
            <input type="text" name="url" value="<?= $v["url"] ?>">
        </div>
        <div class="range-group">
            <h2>オススメ度＊</h2>
            <input type="range" min="0" max="5" value="<?= $v["stars"] ?>" class="input-range" name="stars" />
            <div class="stars"></div>
        </div>
        <div>
            <h2>うれしいポイント＊</h2>
            <div>
                <label><input type="checkbox" name="point[]" value="バス・トイレ別" <?php if (in_array("バス・トイレ別", $pointArray)) echo 'checked'; ?>>バス・トイレ別</label>
                <label><input type="checkbox" name="point[]" value="小学生まで添い寝OK" <?php if (in_array("小学生まで添い寝OK", $pointArray)) echo 'checked'; ?>>小学生まで添い寝OK</label>
                <label><input type="checkbox" name="point[]" value="ベット幅110cm以上" <?php if (in_array("ベット幅110cm以上", $pointArray)) echo 'checked'; ?>>ベット幅110cm以上</label>
                <label><input type="checkbox" name="point[]" value="靴を脱ぐお部屋" <?php if (in_array("靴を脱ぐお部屋", $pointArray)) echo 'checked'; ?>>靴を脱ぐお部屋</label>
                <label><input type="checkbox" name="point[]" value="駅近" <?php if (in_array("駅近", $pointArray)) echo 'checked'; ?>>駅近</label>
                <label><input type="checkbox" name="point[]" value="ショッピングセンター併設" <?php if (in_array("ショッピングセンター併設", $pointArray)) echo 'checked'; ?>>ショッピングセンター併設</label>
            </div>
        </div>
        <div>
            <h2>写真＊</h2>
            <div class="image-preview-container">
                <div>
                    <input type="file" name="img" accept="image/*" multiple>
                    <img src="img/<?= h($v["images"]) ?>" alt="Uploaded Image">
                    <p><?= h($v["images"]) ?></p>
                </div>
            </div>
        </div>
        <div>
            <h2>コメント＊</h2>
            <textarea name="comment" cols="30" rows="10"><?= $v["comment"] ?></textarea>
        </div>
        <input type="hidden" name="id" value="<?= $v["id"] ?>">

        <div class="submit">
            <button type="submit">更新</button>
        </div>

    </form>
</main>

<?php include('footer.php'); ?>