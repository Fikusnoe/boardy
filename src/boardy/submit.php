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

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$message = $_POST['message'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_SESSION['user_name'] ?? '';
    $message = $_POST['message'] ?? '';
    if ($name && $message) {
        // Создаём пост (prepared statement!)
        $stmt = $pdo->prepare(
            'INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $message, $user_id]);
    }
}
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>

<main class="submit-page">
    <div class="submit-card">
        <h2>Новый пост</h2>

        <form method="POST">
            <div class="form-group">
                <label for="message">Текст</label>
                <textarea id="message" name="message" placeholder="Напишите ваше объявление..." required><?= htmlspecialchars($message) ?></textarea>
            </div>
            <div class="form-actions">
                <a href="/messages.php" <button type="submit" class="btn-submit">Опубликовать</button></a>
                <a href="/messages.php" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/partials/foot.php'; ?>




