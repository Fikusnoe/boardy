<?php
// Паттерн: перенаправить пользователя на OAuth-провайдер
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;
$client_id = 'Ov23lin1w0Xn9sAvLgI7';

$params = http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => 'https://fgsfds.ai-info.ru/oauth-callback.php',
    'scope' => 'read:user',
    'state' => $state,
]);
 
header("Location: https://github.com/login/oauth/authorize?$params");
exit;

