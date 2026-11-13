const http = require('http');
const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const careerPath = path.join(root, 'resources', 'data', 'career.json');
const skillSummaryPath = path.join(root, 'resources', 'data', 'skill-summary.json');
const career = fs.readFileSync(careerPath, 'utf8').replace(/</g, '\\u003c');
const skillSummary = fs.readFileSync(skillSummaryPath, 'utf8').replace(/</g, '\\u003c');

const files = {
    '/css/inquiry.css': path.join(root, 'public', 'css', 'inquiry.css'),
    '/js/inquiry.js': path.join(root, 'public', 'js', 'inquiry.js')
};

const page = `<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MY SKILL SHEETS</title>
    <link href="/css/inquiry.css" rel="stylesheet">
</head>
<body>
    <div id="app"></div>
    <script id="career-data" type="application/json">${career}</script>
    <script id="skill-summary-data" type="application/json">${skillSummary}</script>
    <script src="/js/inquiry.js"></script>
</body>
</html>`;

function listen(port) {
    const server = http.createServer(function (req, res) {
        const url = (req.url || '/').split('?')[0];
        if (url === '/' || url === '/index.html') {
            res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
            res.end(page);
            return;
        }
        const file = files[url];
        if (!file) {
            res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
            res.end('Not found');
            return;
        }
        const type = url.endsWith('.css') ? 'text/css' : 'text/javascript';
        res.writeHead(200, { 'Content-Type': type + '; charset=utf-8' });
        fs.createReadStream(file).pipe(res);
    });

    server.on('error', function (err) {
        if (err.code === 'EADDRINUSE') {
            listen(port + 1);
            return;
        }
        throw err;
    });

    server.listen(port, '127.0.0.1', function () {
        console.log('Inquiry preview: http://127.0.0.1:' + port + '/');
    });
}

listen(8000);
