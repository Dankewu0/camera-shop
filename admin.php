<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] < 2) {
    echo "Доступ запрещён";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['change_role'], $_POST['user_id'], $_POST['role_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :uid");
        $stmt->bindValue(':role', intval($_POST['role_id']), PDO::PARAM_INT);
        $stmt->bindValue(':uid', intval($_POST['user_id']), PDO::PARAM_INT);
        $stmt->execute();
        header("Location: admin_panel.php");
        exit;
    }

    if (isset($_POST['edit_product'], $_POST['product_id'], $_POST['price'])) {
        $stmt = $pdo->prepare("UPDATE products SET price = :price WHERE id = :pid");
        $stmt->execute([
            'price' => floatval($_POST['price']),
            'pid' => intval($_POST['product_id'])
        ]);
        header("Location: admin_panel.php");
        exit;
    }

    if (isset($_POST['update_order_status'], $_POST['order_id'], $_POST['status'])) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :oid");
        $stmt->execute([
            'status' => $_POST['status'],
            'oid' => intval($_POST['order_id'])
        ]);
        header("Location: admin_panel.php");
        exit;
    }

    if (isset($_POST['archive_order'], $_POST['order_id'])) {
        $oid = intval($_POST['order_id']);
        $pdo->prepare("INSERT INTO order_archive_items (order_id) VALUES (:oid)")
            ->execute(['oid' => $oid]);
        header("Location: admin_panel.php");
        exit;
    }

}

$users = $pdo->query("SELECT u.id, u.username, u.email, u.role, r.name AS role_name 
                      FROM users u
                      LEFT JOIN roles r ON u.role = r.id
                      ORDER BY u.id ASC")->fetchAll(PDO::FETCH_ASSOC);

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$roles = $pdo->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$orders = $pdo->query("SELECT o.*, u.full_name 
                       FROM orders o
                       LEFT JOIN user_order_info u ON o.user_info_id = u.id
                       ORDER BY o.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Админ панель</title>
<link rel="stylesheet" href="styles.css">
<style>
body { font-family: Arial, sans-serif; background-color: #f8f8f8; color: #444; margin:0; padding:0; }
main { display:flex; flex-wrap:wrap; gap:15px; padding:20px; max-width:1200px; margin:0 auto; }
section { flex:1; min-width:350px; margin:15px; }
h1,h2 { color:#fff; }
h1 { width:100%; margin-bottom:20px; }
table { width:100%; border-collapse:collapse; background-color:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; }
th,td { border-bottom:1px solid #ddd; padding:12px; text-align:left; }
th { background-color:#452c5f; color:#fff; }
tr:last-child td { border-bottom:none; }
button { padding:6px 12px; background-color:#452c5f; color:#fff; border:none; border-radius:4px; cursor:pointer; transition:0.3s; margin-top:2px; }
button:hover { background-color:#4caf50; }
select,input[type=text],input[type=number] { padding:5px; border-radius:4px; border:1px solid #ccc; }
</style>
</head>
<body>
<?php include 'header.php'; ?>
<main>

<section>
<h2>Пользователи</h2>
<table>
<tr>
<th>ID</th>
<th>Логин</th>
<th>Email</th>
<th>Роль</th>
<th>Действие</th>
</tr>
<?php foreach($users as $u): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td>
<form method="post">
<input type="hidden" name="user_id" value="<?= $u['id'] ?>">
<select name="role_id">
<?php foreach($roles as $r): ?>
<option value="<?= $r['id'] ?>" <?= $r['id'] == $u['role'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?></option>
<?php endforeach; ?>
</select>
<button type="submit" name="change_role">Сменить</button>
</form>
</td>
<td></td>
</tr>
<?php endforeach; ?>
</table>
</section>

<section>
<h2>Товары</h2>
<table>
<tr>
<th>ID</th>
<th>Название</th>
<th>Цена</th>
<th>Действие</th>
</tr>
<?php foreach($products as $p): ?>
<tr>
<form method="post">
<td><?= $p['id'] ?><input type="hidden" name="product_id" value="<?= $p['id'] ?>"></td>
<td><?= htmlspecialchars($p['name']) ?></td>
<td><input type="number" name="price" value="<?= htmlspecialchars($p['price']) ?>" step="0.01"></td>
<td><button type="submit" name="edit_product">Обновить</button></td>
</form>
</tr>
<?php endforeach; ?>
</table>
</section>

<section>
<h2>Заказы</h2>
<table>
<tr>
<th>ID</th>
<th>Покупатель</th>
<th>Сумма</th>
<th>Статус</th>
<th>Действие</th>
</tr>
<?php foreach($orders as $o): ?>
<tr>
<form method="post">
<td><?= $o['id'] ?><input type="hidden" name="order_id" value="<?= $o['id'] ?>"></td>
<td><?= htmlspecialchars($o['full_name'] ?? '-') ?></td>
<td><?= number_format($o['total_price'],0,',',' ') ?> руб.</td>
<td>
<select name="status">
<option value="Новый" <?= $o['status']=='Новый'?'selected':'' ?>>Новый</option>
<option value="В обработке" <?= $o['status']=='В обработке'?'selected':'' ?>>В обработке</option>
<option value="Завершен" <?= $o['status']=='Завершен'?'selected':'' ?>>Завершен</option>
<option value="Отменен" <?= $o['status']=='Отменен'?'selected':'' ?>>Отменен</option>
</select>
</td>
<td>
<button type="submit" name="update_order_status">Обновить</button>
</form>
<form method="post" style="margin-top:5px;">
<input type="hidden" name="order_id" value="<?= $o['id'] ?>">
<button type="submit" name="archive_order">Отправить в архив заказов</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</section>

</main>
</body>
</html>
