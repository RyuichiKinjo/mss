<?php

use App\Http\Controllers\InquiryController;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$controller = $app->make(InquiryController::class);
$html = $controller->index()->render();
$html = preg_replace('/\s*<meta name="csrf-token"[^>]*>/', '', $html);
$html = preg_replace('#href="[^"]*inquiry\.css"#', 'href="css/inquiry.css"', $html);
$html = preg_replace('#src="[^"]*inquiry\.js"#', 'src="js/inquiry.js"', $html);
$html = preg_replace_callback(
    '/(<script id="profile-display" type="application\/json">)(.*?)(<\/script>)/s',
    function (array $match) {
        $data = json_decode($match[2], true);
        if (!is_array($data)) {
            fwrite(STDERR, "profile-display を読めません\n");
            exit(1);
        }
        $data['admin'] = false;
        $data['static'] = true;
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        return $match[1] . $json . $match[3];
    },
    $html,
    1
);

$docs = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'docs';
ensureDir($docs . DIRECTORY_SEPARATOR . 'css');
ensureDir($docs . DIRECTORY_SEPARATOR . 'js');
ensureDir($docs . DIRECTORY_SEPARATOR . 'pdf');

file_put_contents($docs . DIRECTORY_SEPARATOR . 'index.html', $html);
file_put_contents($docs . DIRECTORY_SEPARATOR . '.nojekyll', '');
copy(dirname(__DIR__) . '/public/css/inquiry.css', $docs . '/css/inquiry.css');
copy(dirname(__DIR__) . '/public/js/inquiry.js', $docs . '/js/inquiry.js');

writePdf($controller->summary(), $docs . '/pdf/MY_SKILL_SHEETS_summary.pdf');
writePdf($controller->career(), $docs . '/pdf/MY_SKILL_SHEETS_career.pdf');

echo "docs/ を更新しました\n";

function writePdf($response, string $path): void
{
    $bytes = $response->getContent();
    if (!is_string($bytes) || strncmp($bytes, '%PDF', 4) !== 0) {
        fwrite(STDERR, "PDF を作れません: {$path}\n");
        exit(1);
    }
    file_put_contents($path, $bytes);
}

function ensureDir(string $path): void
{
    if (!is_dir($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
        fwrite(STDERR, "フォルダを作れません: {$path}\n");
        exit(1);
    }
}
