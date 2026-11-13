<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>MY SKILL SHEETS</title>
    <style>
        @font-face {
            font-family: "IPAexGothic";
            font-style: normal;
            font-weight: normal;
            src: url("{{ $fontPath }}") format("truetype");
        }

        @page {
            margin: 10mm 12mm;
        }

        body {
            margin: 0;
            color: #2a2622;
            font-family: "IPAexGothic", sans-serif;
            font-size: 9pt;
            line-height: 1.35;
        }

        h1 {
            margin: 0 0 8px;
            padding: 5px 8px;
            background: #3c3834;
            color: #fbf7f2;
            font-size: 13pt;
            font-weight: normal;
            letter-spacing: 0.04em;
        }

        h2 {
            margin: 8px 0 4px;
            padding-left: 6px;
            border-left: 3px solid #a15c38;
            font-size: 10.5pt;
            font-weight: normal;
        }

        .under-heading {
            padding-left: 9px;
        }

        h3 {
            margin: 6px 0 3px;
            color: #7a3f22;
            font-size: 9pt;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta th,
        .meta td {
            padding: 1px 0;
            text-align: left;
            vertical-align: top;
            font-weight: normal;
        }

        .meta th {
            width: 32mm;
            color: #6a625a;
        }

        .pair {
            width: 100%;
            margin: 0 0 6px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }

        .pair > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .skills th,
        .skills td {
            border: 1px solid #e5dfd6;
            padding: 2px 5px;
            text-align: left;
            vertical-align: middle;
        }

        .skills th {
            background: #f3eee7;
            color: #4e463e;
            font-size: 8pt;
            font-weight: normal;
        }

        .badge {
            display: inline-block;
            margin-left: 4px;
            padding: 0 4px;
            border: 1px solid #e6cbb8;
            background: #f8ebe3;
            color: #7a3f22;
            font-size: 7.5pt;
        }
    </style>
</head>
<body>
    <h1>スキルシート</h1>

    <h2>基本情報</h2>
    <div class="under-heading">
        <table class="meta">
            <tr>
                <th>氏名</th>
                <td>{{ $name }}</td>
            </tr>
            <tr>
                <th>フリガナ</th>
                <td>{{ $nameKana }}</td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td>{{ $birthday }}</td>
            </tr>
            <tr>
                <th>職種</th>
                <td>{{ $role }}</td>
            </tr>
            <tr>
                <th>プロフィールページ</th>
                <td>{{ $profileUrl }}</td>
            </tr>
            <tr>
                <th>資格</th>
                <td>{{ implode('、', $qualifications) }}</td>
            </tr>
        </table>
    </div>

    <h2>スキル要約</h2>
    <div class="under-heading">
        <h3>{{ $privateLabel }}</h3>
        @include('pdf.summary-groups', ['groups' => $privateGroups])

        <h3>{{ $businessLabel }}</h3>
        @include('pdf.summary-groups', ['groups' => $businessGroups])
    </div>
</body>
</html>
