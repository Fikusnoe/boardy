<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
session_destroy();               // удалить файл /tmp/sess_*
setcookie('PHPSESSID', '', [     // удалить куку в браузере
    'expires' => time() - 3600,  // дата в прошлом → браузер удалит
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
header('Location: /messages.php');
exit;
