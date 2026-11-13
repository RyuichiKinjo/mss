<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MY SKILL SHEETS</title>
    <link href="{{ asset('css/inquiry.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app"></div>
    <script id="career-data" type="application/json">{!! json_encode($career, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script id="skill-summary-data" type="application/json">{!! json_encode($skillSummary, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script id="profile-display" type="application/json">{!! json_encode(['birthday' => $birthdayLabel, 'admin' => $admin], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script src="{{ asset('js/inquiry.js') }}"></script>
</body>
</html>
