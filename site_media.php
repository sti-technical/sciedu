<?php require 'inc/auth.php'; require 'inc/layout.php';
$kinds = ['logo' => 'Logo', 'banner' => 'Banner', 'strip' => 'Header ke chhote logo'];
if (isset($_GET['del'])) {
    check_csrf($_GET['k'] ?? '');
    delete_media((int)$_GET['del']);
    flash('Delete ho gaya.'); go('site_media.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf($_POST['k'] ?? '');
    $kind = $_POST['kind'] ?? '';
    if (!isset($kinds[$kind])) { flash('Galat request.'); go('site_media.php'); }
    $old = $kind === 'logo' ? media_ids('logo') : [];
    $id = save_media('img', $kind);
    if ($id) {
        foreach ($old as $o) delete_media($o);   // logo sirf ek hota hai, purana hata do
        flash($kinds[$kind] . ' save ho gaya.');
    } else {
        flash('Image save nahi hui. JPG/PNG/WEBP chunein, size 2 MB se kam rakhein. Agar "media" table nahi bani to update_1.sql chalayein.');
    }
    go('site_media.php');
}
admin_header('Logo & Banner');
function media_block($title, $kind, $note, $ids) { ?>
<div class="card"><h3><?= e($title) ?></h3><p><?= e($note) ?></p>
<?php foreach ($ids as $id): ?><div style="display:inline-block;margin:0 .8rem .8rem 0;vertical-align:top">
  <img src="<?= media_url($id) ?>" alt="" style="max-height:90px;max-width:260px;display:block;border:1px solid #ccc">
  <a style="color:#c0392b" href="?del=<?= (int)$id ?>&k=<?= csrf() ?>" onclick="return confirm('Pakka delete?')">Delete</a></div><?php endforeach; ?>
<?php if (!$ids): ?><p><em>Abhi koi image nahi hai.</em></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="k" value="<?= csrf() ?>"><input type="hidden" name="kind" value="<?= e($kind) ?>">
<label><?= $kind === 'logo' ? 'Naya logo (purana badal jayega)' : 'Naya jodein' ?></label><input type="file" name="img" accept="image/*" required><p><button>Upload</button></p></form></div>
<?php }
media_block('Logo', 'logo', 'Website ke upar left me dikhta hai. Size: lagbhag 400x400 px, 2 MB se kam.', media_ids('logo'));
media_block('Banner (slider)', 'banner', 'Home page par badi photo. Ek se zyada jodoge to apne aap badalti rahengi. Size: 1920x600 px (chauda-chhota), JPG, 2 MB se kam.', media_ids('banner'));
media_block('Header ke chhote logo (ISO, MSME wagairah)', 'strip', 'Logo ke bagal me line me dikhte hain. Size: height lagbhag 120 px.', media_ids('strip'));
admin_footer();
