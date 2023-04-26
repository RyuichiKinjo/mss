@extends('layouts.app')

@section('content')
    <div class="container">

        <form action="{{ route('mss.index') }}" method="POST">
            @csrf
            <table id="condition" class="table table-responsive">
                <div class="row">
                    <div class="col-2">
                        <label for="formLang" class="form-label">言語</label>
                        <input type="text" class="form-control" id="formLang" name="formLang" value="{{ $formLang }}">
                    </div>
                    <div class="col-2">
                        <label for="formDb" class="form-label">DB</label>
                        <input type="text" class="form-control" id="formDb" name="formDb" value="{{ $formDb }}">
                    </div>
                    <div class="col-2">
                        <label for="formEnv" class="form-label">環境</label>
                        <input type="text" class="form-control" id="formEnv" name="formEnv" value="{{ $formEnv }}">
                    </div>
                    <div class="col-2" style="align-self: self-end;">
                        <button class="btn btn-outline-primary me-3" type="submit" name="search">
                            検索
                        </button>
                        <div class="btn btn-outline-secondary" id="clear" name="clear">
                            クリア
                        </div>
                    </div>
                    <div class="col-4" style="align-self: self-end; text-align: right;">
                        <button class="btn btn-outline-primary me-3" type="button" data-bs-toggle="modal"
                            data-bs-target="#personalModal" id="personal">個人情報登録</button>
                        <button class="btn btn-outline-success me-3" type="button">印刷</button>
                        <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal"
                            data-bs-target="#upsertModal">新規登録</button>
                        <input id="id" type="hidden" value="{{ $id }}">
                    </div>
                </div>
            </table>
        </form>

        <table id="table" class="table table-striped table-responsive-lg mt-3">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col" nowrap>開始日</th>
                    <th scope="col" nowrap>終了日</th>
                    {{-- <th scope="col">業界</th> --}}
                    <th scope="col" nowrap>システム名</th>
                    <th scope="col" nowrap>役割</th>
                    <th scope="col">工程</th>
                    <th scope="col">言語</th>
                    <th scope="col">DB</th>
                    <th scope="col">環境</th>
                    <th scope="col">概要</th>
                    <th scope="col">更新</th>
                    <th scope="col">削除</th>
                </tr>
            </thead>
            <tbody>
                {{-- forelse：繰り返し処理（0件処理あり） --}}
                @forelse ($datas as $data)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td nowrap>{{ $data->start }}{{-- {{ $data['period'] }} --}}</td>
                        <td nowrap>{{ $data->end }}</td>
                        {{-- <td>{{ $data->industry }}</td> --}}
                        <td>{{ $data->system }}</td>
                        <td>{{ $data->role }}</td>
                        <td>{{ $data->phase }}</td>
                        <td>{!! nl2br($data->lang) !!}</td>
                        <td>{!! nl2br($data->db) !!}</td>
                        <td>{!! nl2br($data->env) !!}</td>
                        <td>{!! nl2br($data->overview) !!}</td>
                        {{-- data-bs-target：モーダルのID --}}
                        {{-- data-bs-whatever：jsにパラメータ渡す --}}
                        <td><button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#upsertModal"
                                data-bs-whatever="{{ $data->user_id }}">更新</button>
                        </td>
                        <td><button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-bs-whatever="{{ $data->user_id }}">削除</button>
                        </td>
                        <td style="display:none;">{{ $data->start }}</td>
                    </tr>
                    {{-- <tr>
                        <td colspan="10">{{ $data->overview }}
                        </td>
                    </tr> --}}
                @empty
                    <tr>
                        <p>検索結果が見つかりませんでした。</p>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    {{-- 削除用モーダル --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">削除確認</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- routeヘルパ --}}
                {{-- 引数１：ルート名 --}}
                {{-- 引数２：[プロパティ名 => 値] --}}
                <form action="{{ route('mss.destroy') }}" method="POST">
                    @method('DELETE')
                    @csrf
                    <div class="modal-body">
                        <p>本当に削除してよろしいですか？</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">削除</button>
                        <input type="hidden" id="deleteId" name="deleteId" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 登録・更新用モーダル --}}
    <div class="modal fade" id="upsertModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="upsertModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">新規登録</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- routeヘルパ --}}
                {{-- 引数１：ルート名 --}}
                {{-- 引数２：[プロパティ名 => 値] --}}
                <form action="{{ route('mss.upsert') }}" method="POST" class="needs-validation" novalidate>
                    {{-- novalidateが無いとブラウザ標準のメッセージが出る --}}
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start" class="form-label" id="startLabel">開始月</label>
                                <input type="text" class="form-control" id="start" name="start" required
                                    regexp="^\d{4}-\d{2}-\d{2}$" disp="YYYY-MM-DD">
                                {{-- nameをつけるとコントローラで受け取れる --}}
                                {{-- requiredをつけると必須チェック --}}
                                <div class="invalid-feedback" id="startInvalidFeedback">{{-- エラー時のメッセージ --}}
                                    {{-- 開始月を入力してください。 --}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="end" class="form-label" id="endLabel">終了月</label>
                                <input type="text" class="form-control" id="end" name="end" regexp="^\d{4}-\d{2}-\d{2}$"
                                    disp="YYYY-MM-DD">
                                <div class="invalid-feedback" id="endInvalidFeedback">
                                    YYYY-MM-DD形式で入力してください。
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="system" class="form-label">システム名</label>
                                <input type="text" class="form-control" id="system" name="system">
                            </div>
                            <div class="col-md-4">
                                <label for="role" class="form-label">役割</label>
                                <input type="text" class="form-control" id="role" name="role">
                            </div>
                            <div class="col-md-4">
                                <label for="phase" class="form-label">工程</label>
                                <input type="text" class="form-control" id="phase" name="phase">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="lang" class="form-label">言語</label>
                                <textarea class="form-control" id="lang" name="lang"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="db" class="form-label">DB</label>
                                <textarea class="form-control" id="db" name="db"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="env" class="form-label">環境</label>
                                <textarea class="form-control" id="env" name="env"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="overview" class="form-label">概要</label>
                                <textarea class="form-control" id="overview" name="overview"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">登録</button>
                        <input type="hidden" id="upsertId" name="upsertId" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 個人情報用モーダル --}}
    <div class="modal fade" id="personalModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="personalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">個人情報登録</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- routeヘルパ --}}
                {{-- 引数１：ルート名 --}}
                {{-- 引数２：[プロパティ名 => 値] --}}
                <form action="{{ route('mss.personal') }}" method="POST" class="needs-validation" novalidate>
                    {{-- novalidateが無いとブラウザ標準のメッセージが出る --}}
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label" id="nameLabel">氏名</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="name" class="form-label" id="initialLabel">イニシャル</label>
                                <input type="text" class="form-control" id="initial" name="initial" required>
                            </div>
                            <div class="col-md-4 rdo-btn-pad-top">
                                <input class="btn-check" type="radio" name="nameRadio" id="nameRadio1">
                                <label class="btn btn-outline-primary" for="nameRadio1">
                                    氏名表示
                                </label>
                                <input class="btn-check" type="radio" name="nameRadio" id="nameRadio2">
                                <label class="btn btn-outline-primary" for="nameRadio2">
                                    イニシャル表示
                                </label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="birthday" class="form-label" id="birthdayLabel">生年月日</label>
                                <input type="text" class="form-control" id="birthday" name="birthday"
                                    regexp="^\d{4}-\d{2}-\d{2}$" disp="YYYY-MM-DD" required>
                                <div class="invalid-feedback" id="endInvalidFeedback">
                                    YYYY-MM-DD形式で入力してください。
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="age" class="form-label" id="ageLabel">年齢</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="age" name="age" readonly>
                                    <div class="input-group-append"><div class="input-group-text">歳</div></div>
                                </div>
                            </div>
                            <div class="col-md-4 rdo-btn-pad-top">
                                <input class="btn-check" type="radio" name="sexRadio" id="sexRadio1">
                                <label class="btn btn-outline-primary" for="sexRadio1">
                                    男性
                                </label>
                                <input class="btn-check" type="radio" name="sexRadio" id="sexRadio2">
                                <label class="btn btn-outline-primary" for="sexRadio2">
                                    女性
                                </label>
                                <input class="btn-check" type="radio" name="sexRadio" id="sexRadio3" checked>
                                <label class="btn btn-outline-primary" for="sexRadio3">
                                    指定なし
                                </label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label" id="stationLabel">最寄り駅</label>
                                <textarea class="form-control" id="station" name="station"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="lang" class="form-label">資格</label>
                                <textarea class="form-control" id="lang" name="lang"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">登録</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
