<?php
session_start();
include 'db.php';

$logged = isset($_SESSION['user_id']);
$userId = $logged ? $_SESSION['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pay']) && $logged) {
        $selected = $_POST['selected'] ?? [];
        if (count($selected) > 0) {
            $in = implode(',', array_fill(0, count($selected), '?'));
            $stmt = $pdo->prepare("
                SELECT ci.id AS cart_item_id, p.id AS product_id, p.price, ci.quantity
                FROM cart_items ci
                JOIN cart c ON c.id = ci.cart_id
                JOIN products p ON p.id = ci.product_id
                WHERE c.user_id = ?
                AND ci.id IN ($in)
            ");
            $stmt->execute(array_merge([$userId], $selected));
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = 0;
            foreach ($items as $i) $total += $i['price'] * $i['quantity'];

            $pdo->prepare("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, 'new', NOW())")
                ->execute([$userId, $total]);
            $orderId = $pdo->lastInsertId();

            $iStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $i) {
                $iStmt->execute([$orderId, $i['product_id'], $i['quantity'], $i['price']]);
            }

            $pdo->prepare("DELETE FROM cart_items WHERE id IN ($in) AND cart_id IN (SELECT id FROM cart WHERE user_id = ?)")
                ->execute(array_merge($selected, [$userId]));

            header("Location: order_success.php");
            exit;
        }
    }

    if (!$logged) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_POST['update'])) {
            $id = $_POST['item_id'];
            $qty = max(1, (int)($_POST['quantity'] ?? 1));
            if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]['quantity'] = $qty;
        }
        if (isset($_POST['delete'])) {
            $id = $_POST['item_id'];
            unset($_SESSION['cart'][$id]);
        }
    } else {
        if (isset($_POST['update'])) {
            $itemId = (int)($_POST['item_id'] ?? 0);
            $qty = max(1, (int)($_POST['quantity'] ?? 1));
            $pdo->prepare("UPDATE cart_items SET quantity = :q WHERE id = :id AND cart_id IN (SELECT id FROM cart WHERE user_id = :uid)")
                ->execute(['q' => $qty, 'id' => $itemId, 'uid' => $userId]);
        }
        if (isset($_POST['delete'])) {
            $itemId = (int)($_POST['item_id'] ?? 0);
            $pdo->prepare("DELETE FROM cart_items WHERE id = :id AND cart_id IN (SELECT id FROM cart WHERE user_id = :uid)")
                ->execute(['id' => $itemId, 'uid' => $userId]);
        }
    }
    header('Location: cart.php');
    exit;
}

if ($logged) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price, ci.id AS item_id, ci.quantity,
               (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = TRUE LIMIT 1) AS image
        FROM cart_items ci
        JOIN cart c ON c.id = ci.cart_id
        JOIN products p ON p.id = ci.product_id
        WHERE c.user_id = :uid
    ");
    $stmt->execute(['uid' => $userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $ids = array_keys($_SESSION['cart']);
    if (count($ids) > 0) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("
            SELECT id, name, price,
                   (SELECT image_url FROM product_images WHERE product_id = products.id AND is_main = TRUE LIMIT 1) AS image
            FROM products
            WHERE id IN ($in)
        ");
        $stmt->execute($ids);
        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cartItems = [];
        foreach ($prods as $p) {
            $cartItems[] = [
                'id' => $p['id'],
                'item_id' => $p['id'],
                'name' => $p['name'],
                'price' => $p['price'],
                'image' => $p['image'],
                'quantity' => $_SESSION['cart'][$p['id']]['quantity']
            ];
        }
    } else $cartItems = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Корзина</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<main style="padding: 20px; max-width: 1200px; margin: 0 auto;">
<div class="checkout-panel">
<div class="cart-layout">
<div class="cart-items">
<h2 class="cart-text">Ваши товары</h2>
<form method="post">
<?php
$totalPrice = 0;
foreach ($cartItems as $i):
$totalPrice += $i['price'] * $i['quantity'];
?>
<div class="item">
<input type="checkbox" name="selected[]" value="<?=$i['item_id']?>" id="item<?=$i['id']?>" class="item-checkbox">
<label for="item<?=$i['id']?>" class="item-label">
<div class="item-info">
<div class="item-name"><?=$i['name']?></div>
<div class="item-price">Цена: <?=$i['price']?> руб.</div>
<div class="item-quantity">
<input type="hidden" name="item_id" value="<?=$i['item_id']?>">
<input type="number" name="quantity" value="<?=$i['quantity']?>" min="1" style="width: 70px;">
<button type="submit" name="update">Сохранить</button>
<button type="submit" name="delete">Удалить</button>
</div>
</div>
</label>
</div>
<?php endforeach; ?>
</div>

<div class="checkout-panel">
<h2 class="cart-text">Оплата</h2>
<div class="total">Итого: <?=$totalPrice?> руб.</div>

<div class="delivery-options">
<h3>Способ доставки</h3>
<label><input type="radio" name="delivery" value="pickup" checked> Забрать из магазина</label>
<label><input type="radio" name="delivery" value="home"> Доставка на дом</label>
</div>

<div class="payment-options">
<h3>Способ оплаты</h3>
<label><input type="radio" name="payment" value="card" checked> Банковская карта</label>
<label><input type="radio" name="payment" value="sbp"> СБП</label>
</div>

<div class="payment-details" id="card-details">
<h3>Данные карты</h3>
<input type="text" placeholder="Номер карты" class="input-field">
<input type="text" placeholder="Срок действия (MM/YY)" class="input-field">
<input type="text" placeholder="CVV" class="input-field">
<input type="text" placeholder="Имя владельца" class="input-field">
</div>

<div class="payment-details" id="sbp-details" style="display: none;">
<h3>Данные для СБП</h3>
<input type="text" placeholder="Номер телефона" class="input-field">
<p>Оплата через приложение банка.</p>
</div>

<button class="pay-button" type="submit" name="pay">Оплатить</button>
</form>
</div>
</div>
</div>
</main>

<footer class="footer">
<div class="footer-container">
<div class="footer-column">
<h3>О компании</h3>
<p>Мы занимаемся созданием качественных продуктов с 2010 года. Наша миссия - делать мир лучше.</p>
</div>
<div class="footer-column">
<h3>Контакты</h3>
<p>Email: info@example.com</p>
<p>Телефон: +7 (999) 123-45-67</p>
<p>Адрес: Москва, ул. Примерная, 123</p>
</div>
</div>
<div class="footer-bottom">
<p>&copy; 2025 Все права защищены</p>
</div>
</footer>
</body>
</html>
