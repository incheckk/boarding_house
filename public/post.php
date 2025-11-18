<?php
$pageTitle = "Blog Post";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM blog WHERE blog_id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
?>

<h1><?= htmlspecialchars($post['title']) ?></h1>
<p><i>Posted on <?= $post['date_posted'] ?></i></p>
<div><?= nl2br($post['content']) ?></div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
