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
$error = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT id, name, password_hash FROM users WHERE email = ?'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        header('Location: /messages.php');
        exit;
    } else {
        $error = 'Неверный email или пароль';
    }
} 
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>
<main class="auth-page">
    <div class="login-card">
        <h2>Вход</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email" class ="auth-label">Email</label>
                <input type="email" id="email" name="email" placeholder="ivan@example.com" required>
            </div>
            <div class="form-group">
                <label for="password" class ="auth-label">Пароль</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required class ="auth-input">
            </div>
	    <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <button type="submit" class="button-submit">Вход</button>
        </form>
	<span class="divide-or">Или</span>
	<div class="github-login">
            <a href="/oauth-github.php" class="github-btn">Войти через GitHub</a>
        </div>
        <div class="auth-footer">
            Нет аккаунта? <a href="register.php">Регистрация</a>
        </div>
    </div>
</main>
<?php include __DIR__ . '/partials/foot.php'; ?>
