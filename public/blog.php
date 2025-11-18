<?php
$pageTitle = "Blog";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$posts = $pdo->query("SELECT * FROM blog ORDER BY date_posted DESC")->fetchAll();
?>

<h1>Blog</h1>

<div class="blog-list">
<?php foreach ($posts as $post): ?>
    <div class="blog-card">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p><?= substr(strip_tags($post['content']), 0, 150) ?>...</p>
        <a href="post.php?id=<?= $post['blog_id'] ?>">Read More</a>
    </div>
<?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
