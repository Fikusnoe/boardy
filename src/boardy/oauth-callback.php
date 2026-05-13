<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once 'db.php';
$client_id = 'Ov23lin1w0Xn9sAvLgI7';
$client_secret = 'fa06c0c80b22b384f348ddfdd7313bb615d47105';

if (($_GET['state'] ?? '') !== ($_SESSION['oauth_state'] ?? '')) {
    die('Invalid state — possible CSRF attack');
}


$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $_GET['code'],
    ]),
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
$response = json_decode(curl_exec($ch), true);
$response_raw = curl_exec($ch);
var_dump($response_raw);
curl_close($ch);
$access_token = $response['access_token'];

$ch = curl_init('https://api.github.com/user');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $access_token",
        'User-Agent: Boardy'
    ],
    CURLOPT_RETURNTRANSFER => true,
]);
$profile = json_decode(curl_exec($ch), true);
curl_close($ch);

$ch = curl_init('https://api.github.com/user/emails');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => ["Authorization: Bearer $access_token", 'User-Agent: Boardy'],
    CURLOPT_RETURNTRANSFER => true,
]);
$emails = json_decode(curl_exec($ch), true);
$email = $emails[0]['email'] ?? null;
if (!$email) {
    $email = 'github_' . $profile['id'] . '@github.local';
}
curl_close($ch);


$stmt = $pdo->prepare('SELECT id, name FROM users WHERE github_id = ?');
$stmt->execute([$profile['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$user) {
    $stmt = $pdo->prepare('INSERT INTO users (name, github_id, email) VALUES (?, ?, ?)');
    $stmt->execute([$profile['login'], $profile['id'], $email]);
    $user = ['id' => $pdo->lastInsertId(), 'name' => $profile['login']];
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
header('Location: /messages.php');
exit;

