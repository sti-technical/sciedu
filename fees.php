<?php require 'inc/auth.php'; require 'inc/layout.php';
$reg = trim($_GET['reg'] ?? ''); $s = null;
if ($reg !== '') {
    $st = $pdo->prepare('SELECT s.*, c.name course, c.fee course_fee FROM students s LEFT JOIN courses c ON c.id=s.course_id WHERE s.reg_no=?'); $st->execute([$reg]); $s = $st->fetch() ?: null;
}
if ($s && isset($_GET['del'])) {
    check_csrf($_GET['k'] ?? ''); $pdo->prepare('DELETE FROM fees WHERE id=? AND student_id=?')->execute([(int)$_GET['del'], $s['id']]);
    flash('Fee record delete ho gaya.'); go('fees.php?reg=' . urlencode($reg));
}
if ($s && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf($_POST['k'] ?? ''); $amt = (float)($_POST['amount'] ?? 0);
    if ($amt > 0) {
        $pdo->prepare('INSERT INTO fees (student_id,amount,paid_on,payment_mode,remark) VALUES (?,?,?,?,?)')
            ->execute([$s['id'], $amt, $_POST['paid_on'] ?: date('Y-m-d'), $_POST['payment_mode'], trim($_POST['remark'] ?? '')]);
        $fid = $pdo->lastInsertId(); $pdo->prepare('UPDATE fees SET receipt_no=? WHERE id=?')->execute(['RC' . str_pad($fid, 5, '0', STR_PAD_LEFT), $fid]);
        flash('Fee jama ho gayi.');
    } else flash('Amount 0 se zyada hona chahiye.');
    go('fees.php?reg=' . urlencode($reg));
}
admin_header('Fee'); ?>
<form method="get" class="card"><label>Registration number se student dhundhein</label><input name="reg" value="<?= e($reg) ?>" required><button>Dhundhein</button></form>
<?php if ($reg !== '' && !$s): ?><div class="err">Ye registration number nahi mila.</div><?php endif;
if ($s):
    $pay = $pdo->prepare('SELECT * FROM fees WHERE student_id=? ORDER BY paid_on DESC, id DESC'); $pay->execute([$s['id']]); $pays = $pay->fetchAll();
    $paid = array_sum(array_column($pays, 'amount')); $due = $s['course_fee'] - $paid; ?>
<div class="card"><b><?= e($s['name']) ?></b> (<?= e($s['reg_no']) ?>) - <?= e($s['course']) ?><br>
 Course fee: Rs. <?= number_format($s['course_fee']) ?> | Jama: Rs. <?= number_format($paid) ?> | Baaki: <span class="<?= $due > 0 ? 'due' : 'ok' ?>">Rs. <?= number_format(max(0, $due)) ?></span></div>
<form method="post" class="card"><h3>Fee jama karein</h3><input type="hidden" name="k" value="<?= csrf() ?>"><div class="row">
 <div><label>Amount</label><input type="number" step="any" name="amount" required></div>
 <div><label>Date</label><input type="date" name="paid_on" value="<?= date('Y-m-d') ?>"></div>
 <div><label>Mode</label><select name="payment_mode"><option>Cash</option><option>UPI</option><option>Bank</option><option>Cheque</option></select></div>
 <div><label>Remark</label><input name="remark"></div></div><button>Jama karein</button></form>
<table><tr><th>Receipt</th><th>Amount</th><th>Date</th><th>Mode</th><th>Remark</th><th>Action</th></tr>
<?php if (!$pays) echo '<tr><td colspan="6">Abhi koi fee jama nahi hui.</td></tr>';
foreach ($pays as $f): ?><tr><td><?= e($f['receipt_no']) ?></td><td><?= number_format($f['amount']) ?></td><td><?= e($f['paid_on']) ?></td><td><?= e($f['payment_mode']) ?></td><td><?= e($f['remark']) ?></td>
<td><a href="print.php?type=receipt&id=<?= $f['id'] ?>" target="_blank">Receipt</a><a style="color:#c0392b" href="?reg=<?= urlencode($reg) ?>&del=<?= $f['id'] ?>&k=<?= csrf() ?>" onclick="return confirm('Delete Fee Record?')">Delete_Fee_Record</a></td></tr><?php endforeach; ?></table>
<?php else: ?><h3>Latest fee entries</h3><table><tr><th>Reg no.</th><th>Naam</th><th>Amount</th><th>Date</th><th>Receipt</th></tr>
<?php foreach ($pdo->query('SELECT f.*, s.name, s.reg_no FROM fees f JOIN students s ON s.id=f.student_id ORDER BY f.id DESC LIMIT 30') as $f): ?>
<tr><td><a href="?reg=<?= urlencode($f['reg_no']) ?>"><?= e($f['reg_no']) ?></a></td><td><?= e($f['name']) ?></td><td><?= number_format($f['amount']) ?></td><td><?= e($f['paid_on']) ?></td><td><?= e($f['receipt_no']) ?></td></tr><?php endforeach; ?></table>
<?php endif; admin_footer();
