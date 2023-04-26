<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// コントローラーをuseして::classで指定
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// welcomeは不要
// Route::get('/', function () {
//     return view('welcome');
// });

// 認証で使用するのでそのまま
Auth::routes();

// リダイレクト
Route::redirect('/home', '/mss');
Route::redirect('/', '/mss');

// 検索ボタン（初期表示を実行）
// Route::post('/mss/search', [HomeController::class, 'index'])->name('mss.search');
// Route::redirect('/mss/search', '/mss');
// Route::redirect('/mss/clear', '/mss');

// Laravel7までの書き方
// Route::get('/user', 'UserController@index');

// Laravel8は第2引数がarray
// Route::get('home', [HomeController::class, 'index']);

// 一括で作る方法もあるが、不要なメソッドを塞ぐのが面倒なので不使用
// Route::resources(['mss' => HomeController::class]);
// index:初期表示
// store:保存処理
// create:新規作成画面表示
// destroy:削除処理
// update:更新画面表示
// show:詳細画面表示
// edit:更新処理

Route::prefix('mss')->group(function() {

    // 初期表示
    Route::get('/', [HomeController::class, 'index'])->name('mss.index');
    Route::post('/', [HomeController::class, 'index'])->name('mss.index');

    // 削除ボタン（'mss'=URI、クラス名、メソッド名、name=formのactionで呼び出す名前）
    Route::delete('/', [HomeController::class, 'destory'])->name('mss.destroy');

    // 新規登録
    Route::put('/', [HomeController::class, 'upsert'])->name('mss.upsert');

    // 個人情報登録
    Route::put('/personal', [HomeController::class, 'personal'])->name('mss.personal');

});
