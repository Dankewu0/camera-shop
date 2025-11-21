<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: authorization.php');
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = :uid");
    $stmt->execute(['uid' => $userId]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cart) {
        $pdo->prepare("INSERT INTO cart (user_id) VALUES (:uid)")->execute(['uid' => $userId]);
        $cartId = $pdo->lastInsertId();
    } else {
        $cartId = $cart['id'];
    }

    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = :cid AND product_id = :pid");
    $stmt->execute(['cid' => $cartId, 'pid' => $productId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $newQty = $item['quantity'] + $quantity;
        $pdo->prepare("UPDATE cart_items SET quantity = :qty WHERE id = :id")
            ->execute(['qty' => $newQty, 'id' => $item['id']]);
    } else {
        $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cid, :pid, :qty)")
            ->execute(['cid' => $cartId, 'pid' => $productId, 'qty' => $quantity]);
    }

    header("Location: cart.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.price, ci.quantity,
           (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = TRUE LIMIT 1) AS image
    FROM cart_items ci
    JOIN cart c ON c.id = ci.cart_id
    JOIN products p ON p.id = ci.product_id
    WHERE c.user_id = :uid
");
$stmt->execute(['uid' => $userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<?php 
$totalPrice = 0;
foreach ($cartItems as $i): 
    $totalPrice += $i['price'] * $i['quantity'];
?>
<div class="item">
    <input type="checkbox" id="item<?=$i['id']?>" class="item-checkbox">
    <label for="item<?=$i['id']?>" class="item-label">
        <div class="item-info">
            <div class="item-name"><?=$i['name']?></div>
            <div class="item-price">Цена: <?=$i['price']?> руб.</div>
            <div class="item-quantity">Количество: <?=$i['quantity']?></div>
        </div>
    </label>
</div>
<?php endforeach; ?>
</div>

<div class="checkout-panel">
<h2  class="cart-text">Оплата</h2>
<div class="total">Итого: <?=$totalPrice?> руб.</div>
<div class="delivery-options">
<h3>Способ доставки</h3>
<label>
    <input type="radio" name="delivery" value="pickup" checked> Забрать из магазина
</label>
<label>
    <input type="radio" name="delivery" value="home"> Доставка на дом
</label>
</div>
<div class="payment-options">
<h3>Способ оплаты</h3>
<label>
    <input type="radio" name="payment" value="card" checked> Банковская карта
</label>
<label>
    <input type="radio" name="payment" value="sbp"> СБП
</label>
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
<button class="pay-button">Оплатить</button>
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
