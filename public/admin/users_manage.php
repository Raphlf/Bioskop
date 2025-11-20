<?php
require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/auth.php';

require_admin();

$users = $pdo->query("SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC")->fetchAll();
?>

<?php include __DIR__ . '/../../src/templates/header.php'; ?>

<h2>Manage Users</h2>

<table class="table">
    <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Created</th></tr></thead>
    <tbody>
        <?php foreach($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= esc($u['name']) ?></td>
                <td><?= esc($u['email']) ?></td>
                <td><?= esc($u['role']) ?></td>
                <td><?= esc($u['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../../src/templates/footer.php'; ?>
