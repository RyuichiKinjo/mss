/* 
 * イベント
 */
// 削除ボタン押下
const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const recipient = button.getAttribute('data-bs-whatever');
    document.getElementById('deleteId').value = recipient;
});

// 更新ボタン押下
const upsertModal = document.getElementById('upsertModal');
upsertModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const recipient = button.getAttribute('data-bs-whatever');
    document.getElementById('upsertId').value = recipient;

    // 一覧から取得してモーダルに初期表示する
    const trs = document.querySelectorAll('#table tbody tr');
    // console.log(trs);
    // trs.forEach(tr => console.log(tr));
    trs.forEach(tr => {
        const updBtn = tr.querySelector('.btn-outline-success');
        const id = updBtn.getAttribute('data-bs-whatever');
        if (id == recipient) {
            const parent = updBtn.parentNode.parentNode.children;
            document.getElementById('start').value = parent[1].innerHTML;
            document.getElementById('end').value = parent[2].innerHTML;
            document.getElementById('system').value = parent[3].innerHTML;
            document.getElementById('role').value = parent[4].innerHTML;
            document.getElementById('phase').value = parent[5].innerHTML;
            document.getElementById('lang').value = parent[6].innerHTML.replaceAll('<br>', '');
            document.getElementById('db').value = parent[7].innerHTML.replaceAll('<br>', '');
            document.getElementById('env').value = parent[8].innerHTML.replaceAll('<br>', '');
            document.getElementById('overview').value = parent[9].innerHTML.replaceAll('<br>', '');
        }
    });
});

// 個人情報登録ボタン押下
const personalBtn = document.getElementById('personal');
personalBtn.addEventListener('click', function () {
    let id = document.getElementById('id').value;
    alert(id);
});

// クリアボタン押下
const clearBtn = document.getElementById('clear');
clearBtn.addEventListener('click', function () {
    document.getElementById('formLang').value = "";
    document.getElementById('formDb').value = "";
    document.getElementById('formEnv').value = "";
});

// 個人情報登録_年齢入力時
const birthday = document.getElementById('birthday');
birthday.addEventListener('input', function () {
    console.log('start');
    document.getElementById('age').value = dateToAge(birthday.value);
});

/* 
 * 初期処理
 */
(function () {
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                form.querySelectorAll('.form-control').forEach(function (elm) {
                    const required = elm.required;
                    const regexp = elm.getAttribute('regexp');

                    // 必須チェック
                    if (required && elm.value.length == 0) {
                        const label = document.getElementById(elm.id + 'Label').innerHTML;
                        document.getElementById(elm.id).setCustomValidity(label + 'を入力してください');
                        document.getElementById(elm.id + 'InvalidFeedback').innerHTML = label + 'を入力してください。';
                        error(elm, event);
                        return;
                    }

                    // 形式チェック
                    if (regexp && !(elm.value.match(regexp))) {
                        const disp = elm.getAttribute('disp');
                        document.getElementById(elm.id + 'InvalidFeedback').innerHTML = disp + '形式で入力してください。';
                        error(elm, event);
                        return;
                    }

                    // 日付妥当性チェック
                    if (regexp && isBadDate(elm.value)) {
                        document.getElementById(elm.id + 'InvalidFeedback').innerHTML = '正しい日付を入力してください。';
                        error(elm, event);
                        return;
                    }

                    elm.classList.add('is-valid');
                    elm.classList.remove('is-invalid');
                });
            });
        });
})();

/* 
 * 内部関数
 */
// エラー処理
function error(elm, event) {
    elm.classList.add('is-invalid');
    elm.classList.remove('is-valid');
    event.preventDefault(); // イベントを無効にする
    event.stopPropagation(); // イベントの伝播をキャンセルする
}

// 日付妥当性チェック
function isBadDate(val) {
    const y = val.split("-")[0];
    const m = val.split("-")[1] - 1;
    const d = val.split("-")[2];
    const date = new Date(y, m, d);
    return date.getFullYear() != y || date.getMonth() != m || date.getDate() != d;
}

// 年齢算出
function dateToAge(val) {
    console.log(val);
    if (isBadDate(val)) {
        return "";
    }
    const now = new Date();
    const date = new Date(val);
    console.log(date);
    const birthNumber = date.getFullYear() * 10000 
        + (date.getMonth() + 1 ) * 100 
        + date.getDate();
    const nowNumber = now.getFullYear() * 10000 
        + (now.getMonth() + 1 ) * 100 
        + now.getDate();
    console.log(Math.floor( (nowNumber - birthNumber) / 10000 ));
    return Math.floor( (nowNumber - birthNumber) / 10000 );
}

// $("#clear").on("click", function (e) {
//     e.stopPropagation();
// });