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
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)){
        $error = 'Нужно заполнить все поля.';
    } elseif (strlen($password) < 6) {
        $error = 'Длина пароля должна быть не менее 6 символов.';
    } else {  
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким email уже существует.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([$name, $email, $hash]);
            $new_id = $pdo->lastInsertId();  

            $_SESSION['user_id'] = $new_id;
            $_SESSION['user_name'] = $name;

            header('Location: /messages.php');
            exit;
        }
    }
}
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>
<main class="auth-page">
    <div class= "register-card">
            <h2>Регистрация</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Имя</label>
                <input type="text" id="name" name="name" placeholder="Иванов Иван" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="ivan@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <button type="submit" class="button-submit">Зарегистрироваться</button>
        </form>
        <div class="auth-footer">
            Уже есть аккаунт? <a href="login.php">Войти</a>
        </div>
    </div>
</main>
<?php include __DIR__ . '/partials/foot.php'; ?>

