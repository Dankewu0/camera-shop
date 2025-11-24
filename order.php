<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: authorization.php');
    exit;
}

$userId = $_SESSION['user_id'];

$activeOrders = $pdo->prepare("
    SELECT o.*, u.full_name
    FROM orders o
    LEFT JOIN user_order_info u ON o.user_info_id = u.id
    WHERE o.user_id = :uid AND o.status NOT IN ('Завершен', 'Отменен')
    ORDER BY o.created_at DESC
");
$activeOrders->execute(['uid' => $userId]);
$activeOrders = $activeOrders->fetchAll(PDO::FETCH_ASSOC);

$archivedOrders = $pdo->prepare("
    SELECT o.*, u.full_name
    FROM orders o
    LEFT JOIN user_order_info u ON o.user_info_id = u.id
    WHERE o.user_id = :uid AND o.status IN ('Завершен', 'Отменен')
    ORDER BY o.created_at DESC
");
$archivedOrders->execute(['uid' => $userId]);
$archivedOrders = $archivedOrders->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="section">
    <h2>Активные заказы</h2>
    <?php if($activeOrders): ?>
        <?php foreach($activeOrders as $order): ?>
            <div class="order-card active">
                <div class="order-id">Заказ #<?= htmlspecialchars($order['id']) ?></div>
                <div class="order-date">Дата: <?= date("d.m.Y", strtotime($order['created_at'])) ?></div>
                <div class="order-status">Статус: <?= htmlspecialchars($order['status']) ?></div>
                <div class="order-description">Сумма заказа: <?= number_format($order['total_price'], 0, ',', ' ') ?> руб.</div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-orders">У вас нет активных заказов.</p>
    <?php endif; ?>
</div>

<div class="section">
    <h2>Архив заказов</h2>
    <?php if($archivedOrders): ?>
        <?php foreach($archivedOrders as $order): ?>
            <div class="order-card archived">
                <div class="order-id">Заказ #<?= htmlspecialchars($order['id']) ?></div>
                <div class="order-date">Дата: <?= date("d.m.Y", strtotime($order['created_at'])) ?></div>
                <div class="order-status">Статус: <?= htmlspecialchars($order['status']) ?></div>
                <div class="order-description">Сумма заказа: <?= number_format($order['total_price'], 0, ',', ' ') ?> руб.</div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-orders">Архив пуст.</p>
    <?php endif; ?>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-column">
            <h3>О компании</h3>
            <p>Мы занимаемся созданием качественных продуктов с 2010 года. Наша миссия - делать мир лучше.</p>
        </div>
        <div class="footer-column">
            <h3>Контакты</h3>
            <p>Email: info@example.com</p>
            <p>Адрес: Москва, ул. Примерная, 123</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Все права защищены</p>
    </div>
</footer>
</body>
</html>
