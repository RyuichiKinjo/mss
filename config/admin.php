<?php

return [
    // 管理者パスワードの bcrypt ハッシュ。平文は置かない。
    'password_hash' => env('ADMIN_PASSWORD_HASH', ''),
];
