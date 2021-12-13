@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-auto">

                {{-- <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button type="button" class="accordion-button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <strong>Condition</strong>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">

                                <form class="row g-3" method="POST" action="{{ url('/search') }}">
                                    {{ csrf_field() }}

                                    <div class="col-md-12">
                                        <label for="lang" class="form-label">言語</label>
                                        <input type="text" class="form-control" id="lang">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="db" class="form-label">DB</label>
                                        <input type="text" class="form-control" id="db">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="env" class="form-label">環境</label>
                                        <input type="text" class="form-control" id="env">
                                    </div>
                                    <div class="col-md-12">
                                        <button class="btn btn-outline-primary" type="submit">Search</button>
                                        <button class="btn btn-outline-secondary ms-3" type="submit">Reset</button>
                                        <button class="btn btn-outline-success ms-3" type="submit">PrintOut</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="row">
                <div class="dropdown col-auto">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenu2"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        検索条件
                    </button>
                    <div class="dropdown-menu px-4 py-3">
                        {{-- <form class="px-4 py-3"> --}}
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <label for="exampleDropdownFormEmail1" class="col-form-label">Email address</label>
                                </div>
                                <div class="col-auto">
                                    <input type="email" class="form-control" id="exampleDropdownFormEmail1"
                                    placeholder="email@example.com">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <label for="exampleDropdownFormEmail1" class="col-form-label">Email address</label>
                                </div>
                                <div class="col-auto">
                                    <input type="email" class="form-control" id="exampleDropdownFormEmail1"
                                    placeholder="email@example.com">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <label for="exampleDropdownFormEmail1" class="col-form-label">Email address</label>
                                </div>
                                <div class="col-auto">
                                    <input type="email" class="form-control" id="exampleDropdownFormEmail1"
                                    placeholder="email@example.com">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-auto">
                                    <button class="btn btn-primary px-4">
                                        検索
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-secondary px-4">
                                        クリア
                                    </button>
                                </div>
                            </div>
                        {{-- </form> --}}
                    </div>
                    {{-- <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <li><button class="dropdown-item" type="button">Action</button></li>
                        <li><button class="dropdown-item" type="button">Another action</button></li>
                        <li><button class="dropdown-item" type="button">Something else here</button></li>
                    </ul> --}}
                </div>

                <div class="dropdown col-auto">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenu2"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        検索条件
                    </button>
                    <div class="dropdown-menu px-4 py-3">
                        {{-- <form class="px-4 py-3"> --}}
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <label for="exampleDropdownFormEmail1" class="col-form-label">Email address</label>
                                </div>
                                <div class="col-auto">
                                    <input type="email" class="form-control" id="exampleDropdownFormEmail1"
                                    placeholder="email@example.com">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <label for="exampleDropdownFormEmail1" class="col-form-label">Email address</label>
                                </div>
                                <div class="col-auto">
                                    <input type="email" class="form-control" id="exampleDropdownFormEmail1"
                                    placeholder="email@example.com">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <label for="exampleDropdownFormEmail1" class="col-form-label">Email address</label>
                                </div>
                                <div class="col-auto">
                                    <input type="email" class="form-control" id="exampleDropdownFormEmail1"
                                    placeholder="email@example.com">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-auto">
                                    <button class="btn btn-primary px-4">
                                        検索
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-secondary px-4">
                                        クリア
                                    </button>
                                </div>
                            </div>
                        {{-- </form> --}}
                    </div>
                    {{-- <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <li><button class="dropdown-item" type="button">Action</button></li>
                        <li><button class="dropdown-item" type="button">Another action</button></li>
                        <li><button class="dropdown-item" type="button">Something else here</button></li>
                    </ul> --}}
                </div>
                </div>

                {{-- <div class="dropdown-menu">
                    <form class="px-4 py-3">
                      <div class="mb-3">
                        <label for="exampleDropdownFormEmail1" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="exampleDropdownFormEmail1" placeholder="email@example.com">
                      </div>
                      <div class="mb-3">
                        <label for="exampleDropdownFormPassword1" class="form-label">Password</label>
                        <input type="password" class="form-control" id="exampleDropdownFormPassword1" placeholder="Password">
                      </div>
                      <div class="mb-3">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" id="dropdownCheck">
                          <label class="form-check-label" for="dropdownCheck">
                            Remember me
                          </label>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">Sign in</button>
                    </form>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">New around here? Sign up</a>
                    <a class="dropdown-item" href="#">Forgot password?</a>
                  </div> --}}

                <table class="table table-double-striped table-responsive-lg mt-3">
                    <thead>
                        <tr>
                            <th scope="col">No.</th>
                            <th scope="col">期間</th>
                            <th scope="col">業界</th>
                            <th scope="col">システム名</th>
                            <th scope="col">担当役割</th>
                            <th scope="col">工程</th>
                            <th scope="col">言語</th>
                            <th scope="col">DB</th>
                            <th scope="col">環境</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" rowspan="2">1</th>
                            <td>2021/2 ～ 2020/9<br>8ヶ月{{ $test }}</td>
                            <td>人材派遣</td>
                            <td>求人紹介システム</td>
                            <td>SE/PG</td>
                            <td>基本設計～結合テスト</td>
                            <td>PHP<br>typescript<br>sass</td>
                            <td>PostgreSQL12<br>dynamoDB</td>
                            <td>laravel<br>angular<br>ionic<br>monaca</td>
                        </tr>
                        <tr>
                            <td colspan="8">求人紹介システムの設計/開発を担当致しました。<br>
                                主にWebアプリの設計、WebAPIやモバイルアプリの設計及び開発を担当致しました。
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" rowspan="2">2</th>
                            <td>2021/2 ～ 2020/9<br>8ヶ月</td>
                            <td>人材派遣</td>
                            <td>求人紹介システム</td>
                            <td>SE/PG</td>
                            <td>基本設計～結合テスト</td>
                            <td>PHP<br>typescript<br>sass</td>
                            <td>PostgreSQL12<br>dynamoDB</td>
                            <td>laravel<br>angular<br>ionic<br>monaca</td>
                        </tr>
                        <tr>
                            <td colspan="8">求人紹介システムの設計/開発を担当致しました。<br>
                                主にWebアプリの設計、WebAPIやモバイルアプリの設計及び開発を担当致しました。
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" rowspan="2">3</th>
                            <td>2021/2 ～ 2020/9<br>8ヶ月</td>
                            <td>人材派遣</td>
                            <td>求人紹介システム</td>
                            <td>SE/PG</td>
                            <td>基本設計～結合テスト</td>
                            <td>PHP<br>typescript<br>sass</td>
                            <td>PostgreSQL12<br>dynamoDB</td>
                            <td>laravel<br>angular<br>ionic<br>monaca</td>
                        </tr>
                        <tr>
                            <td colspan="8">求人紹介システムの設計/開発を担当致しました。<br>
                                主にWebアプリの設計、WebAPIやモバイルアプリの設計及び開発を担当致しました。
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
