<?php
// Ek hi function se list + add + edit + delete wale pages ban jaate hain
function crud(array $c) {
    global $pdo;
    $t = $c['table']; $f = $c['fields'] ?? []; $fixed = $c['fixed'] ?? []; $cols = $c['cols'];
    $q = $_GET; unset($q['del'], $q['k'], $q['edit']);
    $self = basename($_SERVER['PHP_SELF']) . ($q ? '?' . http_build_query($q) : '');

    if (isset($_GET['del'])) {
        check_csrf($_GET['k'] ?? '');
        $pdo->prepare("DELETE FROM $t WHERE id=?")->execute([(int)$_GET['del']]);
        flash('Delete ho gaya.'); go($self);
    }
    if ($f && $_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf($_POST['k'] ?? '');
        $v = []; $bad = false;
        foreach ($f as $n => $d) {
            $x = trim($_POST[$n] ?? '');
            if ($x === '') {
                if (!empty($d['req'])) $bad = true;
                if ($d['type'] === 'date') $x = null; elseif ($d['type'] === 'number') $x = 0;
            }
            $v[$n] = $x;
        }
        if ($bad) { flash('Zaroori fields bhariye.'); go($self); }
        foreach ($fixed as $n => $x) $v[$n] = $x;
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $set = implode(',', array_map(fn($n) => "$n=?", array_keys($v)));
            $pdo->prepare("UPDATE $t SET $set WHERE id=?")->execute([...array_values($v), $id]);
        } else {
            $pdo->prepare("INSERT INTO $t (" . implode(',', array_keys($v)) . ") VALUES (" . implode(',', array_fill(0, count($v), '?')) . ")")->execute(array_values($v));
        }
        flash('Save ho gaya.'); go($self);
    }
    $edit = null;
    if ($f && isset($_GET['edit'])) {
        $st = $pdo->prepare("SELECT * FROM $t WHERE id=?"); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: null;
    }
    if ($f): ?>
    <form method="post" class="card"><h3><?= $edit ? 'Edit karein' : 'Naya jodein' ?></h3>
      <input type="hidden" name="k" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
      <div class="row">
      <?php foreach ($f as $n => $d): $val = $edit[$n] ?? ($d['default'] ?? ''); ?>
        <div><label for="<?= $n ?>"><?= e($d['label']) ?></label>
        <?php if ($d['type'] === 'select'): ?>
          <select id="<?= $n ?>" name="<?= $n ?>"><?php foreach ($d['options'] as $ov => $ol): ?>
            <option value="<?= e($ov) ?>" <?= (string)$ov === (string)$val ? 'selected' : '' ?>><?= e($ol) ?></option><?php endforeach; ?></select>
        <?php elseif ($d['type'] === 'textarea'): ?>
          <textarea id="<?= $n ?>" name="<?= $n ?>" rows="3"><?= e($val) ?></textarea>
        <?php else: ?>
          <input id="<?= $n ?>" name="<?= $n ?>" type="<?= $d['type'] === 'number' ? 'number' : e($d['type']) ?>" <?= $d['type'] === 'number' ? 'step="any"' : '' ?> value="<?= e($val) ?>" <?= !empty($d['req']) ? 'required' : '' ?>>
        <?php endif; ?></div>
      <?php endforeach; ?></div>
      <button type="submit">Save</button> <?php if ($edit): ?><a class="btn grey" href="<?= e($self) ?>">Cancel</a><?php endif; ?>
    </form>
    <?php endif;
    $w = ''; $p = [];
    foreach ($fixed as $n => $x) { $w .= ($w ? ' AND ' : ' WHERE ') . "t.$n=?"; $p[] = $x; }
    $st = $pdo->prepare(($c['sql'] ?? "SELECT t.* FROM $t t") . $w . ' ORDER BY t.id DESC LIMIT 300'); $st->execute($p); $rows = $st->fetchAll(); ?>
    <table><tr><?php foreach ($cols as $l): ?><th><?= e($l) ?></th><?php endforeach; ?><th>Action</th></tr>
    <?php if (!$rows): ?><tr><td colspan="<?= count($cols) + 1 ?>">Abhi koi record nahi hai.</td></tr><?php endif;
    foreach ($rows as $r): ?><tr>
      <?php foreach ($cols as $k => $l): ?><td><?= e(mb_strimwidth((string)($r[$k] ?? ''), 0, 90, '...')) ?></td><?php endforeach; ?>
      <td><?php if (!empty($c['extra'])) echo $c['extra']($r);
        if ($f): ?><a href="<?= e($self . ($q ? '&' : '?') . 'edit=' . $r['id']) ?>">Edit</a><?php endif; ?>
        <a href="<?= e($self . ($q ? '&' : '?') . 'del=' . $r['id'] . '&k=' . csrf()) ?>" style="color:#c0392b" onclick="return confirm('Pakka delete karein?')">Delete</a></td>
    </tr><?php endforeach; ?></table>
<?php }
