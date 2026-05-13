<?php
$is_logged = !empty($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
?>
<nav class="nav-bar">
    <div class="nav-bar-left">
        <h1><a href="/" class="brand">Boardy</a></h1>
        <a href="/messages.php">Все посты</a>
        <?php if ($is_logged): ?>
            <a href="/submit.php">Добавить пост</a>
        <?php endif; ?>
    </div>

    <div class="nav-bar-auth">
        <?php if ($is_logged): ?>
            <span>Привет, <?= htmlspecialchars($user_name) ?>!</span>
            <a href="/logout.php" class="last-button">Выйти</a>
        <?php else: ?>
            <a href="/login.php">Вход</a>
            <a href="/register.php" class="last-button">Регистрация</a>
        <?php endif; ?>
    </div>
</nav>

