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
            margin: 10mm 8mm 14mm;
        }

        body {
            margin: 0;
            color: #2a2622;
            font-family: "IPAexGothic", sans-serif;
            font-size: 8pt;
            line-height: 1.35;
        }

        h1 {
            margin: 0 0 6px;
            padding: 4px 8px;
            background: #3c3834;
            color: #fbf7f2;
            font-size: 12pt;
            font-weight: normal;
            letter-spacing: 0.04em;
        }

        h2 {
            margin: 6px 0 3px;
            padding-left: 6px;
            border-left: 3px solid #a15c38;
            font-size: 10pt;
            font-weight: normal;
        }

        .under-heading {
            padding-left: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta {
            margin-bottom: 4px;
        }

        .meta th,
        .meta td {
            padding: 1px 0;
            text-align: left;
            vertical-align: top;
            font-weight: normal;
        }

        .meta th {
            width: 28mm;
            color: #6a625a;
        }

        .projects {
            margin-top: 2px;
            table-layout: fixed;
            width: 100%;
        }

        .projects thead {
            display: table-header-group;
        }

        .projects tr,
        .projects td,
        .projects th {
            page-break-inside: avoid;
        }

        .projects th,
        .projects td {
            border: 1px solid #e5dfd6;
            padding: 3px 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        .projects th {
            background: #f3eee7;
            color: #4e463e;
            font-size: 7.5pt;
            font-weight: normal;
        }

        .no { width: 6%; text-align: center; }
        .period { width: 20%; white-space: nowrap; }
        .role { width: 16%; }
        .tech { width: 24%; }
        .phase { width: 12%; }

        .tech-table {
            width: auto;
            margin-left: 0;
            margin-right: 0;
            border-collapse: collapse;
        }

        .projects .tech-table th,
        .projects .tech-table td {
            border: none;
            background: transparent;
            padding: 0 0 1px;
            font-weight: normal;
            text-align: left;
            vertical-align: top;
        }

        .projects .tech-table th {
            width: auto;
            padding-right: 3pt;
            color: #6a625a;
            white-space: nowrap;
        }

        .industry {
            color: #7a3f22;
            font-size: 7.5pt;
        }

        .title {
            margin-top: 1px;
        }

        .desc {
            margin-top: 2px;
            color: #6a625a;
            font-size: 7.5pt;
        }

        .team {
            margin-top: 1px;
            color: #6a625a;
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

    <h2>業務経歴</h2>
    <table class="projects">
        <thead>
            <tr>
                <th class="no">No.</th>
                <th class="period">期間</th>
                <th>業務内容</th>
                <th class="role">役割・規模</th>
                <th class="tech">使用技術</th>
                <th class="phase">担当工程</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td class="no">{{ $project['no'] }}</td>
                    <td class="period">{{ $project['period']['label'] }}</td>
                    <td>
                        @if ($project['industry'] !== '')
                            <div class="industry">{{ $project['industry'] }}</div>
                        @endif
                        <div class="title">{{ $project['title'] }}</div>
                        @if ($project['description'] !== '')
                            <div class="desc">{{ $project['description'] }}</div>
                        @endif
                    </td>
                    <td class="role">
                        @if ($project['role'] !== '')
                            <div>{{ $project['role'] }}</div>
                        @endif
                        @if ($project['team'] !== '')
                            <div class="team">{{ $project['team'] }}</div>
                        @endif
                    </td>
                    <td class="tech">
                        <table class="tech-table">
                            @foreach ($project['technologies'] as $line)
                                @foreach ($line['values'] as $index => $value)
                                    <tr>
                                        <th>@if ($index === 0){{ $line['label'] }}@else&#160;@endif</th>
                                        <td>{{ $value }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </td>
                    <td class="phase">
                        @foreach ($project['phases'] as $phase)
                            <div>{{ $phase }}</div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
