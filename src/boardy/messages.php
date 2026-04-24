<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'db.php';

$stmt = $pdo->query('
    SELECT p.id, p.body, p.created_at,
           u.name AS author_name
    FROM posts p
    JOIN users u ON p.author_id = u.id
    ORDER BY p.created_at DESC
');
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>

<main class="post-list">
    <h2>Все посты</h2>

    <?php if (empty($posts)): ?>
        <p class="no-posts">Сообщений пока нет.</p>
    <?php else: ?>
        <div class="posts-container">
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <div class="post-header">
                        <span class="post-author"><?= htmlspecialchars($post['author_name']) ?></span>
                        <span class="post-date"><?= htmlspecialchars($post['created_at']) ?></span>
                    </div>
                    <div class="post-body">
                        <?= nl2br(htmlspecialchars($post['body']))  ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/partials/foot.php'; ?>
