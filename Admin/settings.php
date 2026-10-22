<?php require 'inc/auth.php'; require 'inc/layout.php';
$fields = ['institute_name' => 'Institute ka naam', 'phone' => 'Phone', 'email' => 'Email', 'address' => 'Address', 'reg_prefix' => 'Registration number prefix (SCI)',
           'whatsapp' => 'WhatsApp number (10 digit, floating button ke liye)', 'facebook' => 'Facebook page link (https://...)', 'instagram' => 'Instagram link', 'twitter' => 'Twitter/X link', 'linkedin' => 'LinkedIn link', 'youtube' => 'YouTube channel link',
           'about_text' => 'About us text', 'director_message' => 'Director message'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf($_POST['k'] ?? '');
    if (isset($_POST['chpw'])) {
        $st = $pdo->prepare('SELECT password_hash FROM admins WHERE id=?'); $st->execute([$_SESSION['admin']['id']]);
        if (!password_verify($_POST['old'] ?? '', $st->fetchColumn())) flash('Purana password galat hai.');
        elseif (strlen($_POST['new'] ?? '') < 8) flash('Naya password kam se kam 8 characters ka rakhein.');
        else { $pdo->prepare('UPDATE admins SET password_hash=? WHERE id=?')->execute([password_hash($_POST['new'], PASSWORD_DEFAULT), $_SESSION['admin']['id']]); flash('Password badal gaya.'); }
    } else {
        $up = $pdo->prepare('INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        foreach ($fields as $k => $l) $up->execute([$k, trim($_POST[$k] ?? '')]);
        flash('Settings save ho gayi.');
    }
    go('settings.php');
}
$cur = $pdo->query('SELECT setting_key,setting_value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
admin_header('Settings'); ?>
<form method="post" class="card"><input type="hidden" name="k" value="<?= csrf() ?>">
<?php foreach ($fields as $k => $l): ?><label><?= e($l) ?></label>
<?php if (in_array($k, ['address', 'about_text', 'director_message'])): ?><textarea name="<?= $k ?>" rows="4"><?= e($cur[$k] ?? '') ?></textarea>
<?php else: ?><input name="<?= $k ?>" value="<?= e($cur[$k] ?? '') ?>"><?php endif; endforeach; ?><p><button>Save settings</button></p></form>
<form method="post" class="card"><h3>Password badlein</h3><input type="hidden" name="k" value="<?= csrf() ?>"><input type="hidden" name="chpw" value="1">
<label>Purana password</label><input type="password" name="old" required><label>Naya password (min 8)</label><input type="password" name="new" required><p><button>Password badlein</button></p></form>
<?php admin_footer();
