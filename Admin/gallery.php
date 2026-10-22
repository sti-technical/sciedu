<?php require 'inc/auth.php'; require 'inc/layout.php';
if (isset($_GET['del'])) {
    check_csrf($_GET['k'] ?? ''); $st = $pdo->prepare('SELECT * FROM gallery WHERE id=?'); $st->execute([(int)$_GET['del']]); $g = $st->fetch();
    if ($g) { if ($g['type'] === 'photo') { if (strpos($g['file_or_url'], 'media:') === 0) delete_media((int)substr($g['file_or_url'], 6)); else @unlink(__DIR__ . '/../uploads/' . basename($g['file_or_url'])); } $pdo->prepare('DELETE FROM gallery WHERE id=?')->execute([$g['id']]); }
    flash('Delete ho gaya.'); go('gallery.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf($_POST['k'] ?? ''); $title = trim($_POST['title'] ?? '');
    if (($_POST['type'] ?? '') === 'video') {
        $u = trim($_POST['url'] ?? '');
        if (preg_match('#^https?://#i', $u)) { $pdo->prepare('INSERT INTO gallery (type,title,file_or_url) VALUES ("video",?,?)')->execute([$title, $u]); flash('Video link jud gaya.'); }
        else flash('Video ka link http:// ya https:// se shuru hona chahiye.');
    } else {
        $f = save_media('photo', 'gallery'); if ($f) $f = 'media:' . $f;
        if ($f) { $pdo->prepare('INSERT INTO gallery (type,title,file_or_url) VALUES ("photo",?,?)')->execute([$title, $f]); flash('Photo upload ho gayi.'); }
        else flash('Photo chunein (JPG/PNG/WEBP, max 2 MB). Agar error aaye to photo chhoti karke try karein.');
    }
    go('gallery.php');
}
admin_header('Gallery'); ?>
<div class="row"><form method="post" enctype="multipart/form-data" class="card"><h3>Photo jodein</h3><input type="hidden" name="k" value="<?= csrf() ?>"><input type="hidden" name="type" value="photo">
<label>Title</label><input name="title"><label>Photo</label><input type="file" name="photo" accept="image/*" required><p><button>Upload</button></p></form>
<form method="post" class="card"><h3>Video link jodein</h3><input type="hidden" name="k" value="<?= csrf() ?>"><input type="hidden" name="type" value="video">
<label>Title</label><input name="title"><label>YouTube link</label><input name="url" required><p><button>Jodein</button></p></form></div>
<table><tr><th>Type</th><th>Title</th><th>File / Link</th><th>Action</th></tr>
<?php foreach ($pdo->query('SELECT * FROM gallery ORDER BY id DESC') as $g): ?><tr><td><?= e($g['type']) ?></td><td><?= e($g['title']) ?></td>
<td><?php if ($g['type'] === 'photo'): ?><img class="th" src="<?= e(gallery_src($g['file_or_url'])) ?>" alt=""><?php else: echo e($g['file_or_url']); endif; ?></td>
<td><a style="color:#c0392b" href="?del=<?= $g['id'] ?>&k=<?= csrf() ?>" onclick="return confirm('Pakka delete?')">Delete</a></td></tr><?php endforeach; ?></table>
<?php admin_footer();
