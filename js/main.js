const container = $('.stars');
for (let i = 0; i < 5; i++) {
    container.append('<a class="rec_value"></a>');
}

// 初期状態で現在の値を反映
const initialValue = $('.input-range').val();

for (let i = 0; i < initialValue; i++) {
    container.find('.rec_value').eq(i).addClass('on');
}

$('form .stars').on('click', '.rec_value', function() {
    var index = $(this).index() + 1;
    var parent = $(this).parent();
    console.log(parent)

    parent.find('.rec_value').removeClass('on');

    for (var i = 0; i < index; i++) {
        parent.find('.rec_value').eq(i).addClass('on'); 
    }
    parent.siblings('.input-range').val(index);
    
});

// single.phpの星の数
const rec = parseInt($(".stars").data("rec"), 10);
const rec_star = $(".stars").find(".rec_value");

for (let i = 0; i < rec; i++) {
    rec_star.eq(i).addClass("on");
}

$(".file").change(function() {
    const file = $(this).prop("files")[0];

    // 画像以外は処理を停止
    if (! file.type.match("image.*")) {
        $(this).val("");
        return;
    }

    // 画像表示
    const reader    = new FileReader();
    reader.onload   = function() {
        $(".img_prev").prop("src", reader.result );
    }
    reader.readAsDataURL(file);
});
