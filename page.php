<?php
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';
$page = $slug ? getPage($slug) : null;
$meta_keywords = '';

if ($page) {
    $page_title = $page['meta_title'] ?: $page['title'];
    $meta_description = $page['meta_description'] ?: createExcerpt($page['content'], 160);
    $meta_keywords = $page['meta_keywords'] ?: generateKeywords($page['content']);
} else {
    $page_title = 'Seite nicht gefunden';
    $meta_description = '';
}

require_once 'includes/header.php';

if (!$page) {
    echo '<div class="container py-5"><h1>Seite nicht gefunden</h1></div>';
    require_once 'includes/footer.php';
    exit;
}
?>
<div class="container py-5">
    <?php echo $page['content']; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
