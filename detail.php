<?php
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Товар не найден";
    exit;
}

$id = (int)$_GET['id'];

// Получаем товар
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name, m.name AS manufacturer_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN manufacturers m ON p.manufacturer_id = m.id
    WHERE p.id = :id
");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Товар не найден";
    exit;
}

// Получаем главное изображение или первую доступную
$imgStmt = $pdo->prepare("
    SELECT image_url
    FROM product_images
    WHERE product_id = :id
    ORDER BY is_main DESC
    LIMIT 1
");
$imgStmt->execute(['id' => $id]);
$mainImage = $imgStmt->fetchColumn();

// Если нет изображений, ставим заглушку
if (!$mainImage) {
    $mainImage = 'placeholder.jpg';
}

// Получаем отзывы
$reviewsStmt = $pdo->prepare("
    SELECT r.*, u.username
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.product_id = :id
    ORDER BY r.created_at DESC
");
$reviewsStmt->execute(['id' => $id]);
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .detail-flex {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .detail-img-main {
            width: 300px;
            border: 2px solid #ccc;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main class="detail-main">
    <h1 class="detail-title"><?= htmlspecialchars($product['name']) ?></h1>
<div class="detail-flex">
    <?php if ($mainImage): ?>
        <img class="detail-img-main" 
             src="<?= htmlspecialchars($mainImage) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>">
    <?php endif; ?>

    <div class="detail-info">
        <p><strong>Цена:</strong> <?= number_format($product['price'], 0, ',', ' ') ?> руб.</p>
        <p><strong>Категория:</strong> <?= htmlspecialchars($product['category_name'] ?? 'Нет') ?></p>
        <p><strong>Производитель:</strong> <?= htmlspecialchars($product['manufacturer_name'] ?? 'Нет') ?></p>
        <p><strong>Наличие на складе:</strong> <?= (int)$product['stock'] ?> шт.</p>
        <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($product['description'] ?? 'Описание отсутствует')) ?></p>

        <form class="detail-form" action="cart.php" method="post">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input class="detail-quantity" type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>">
            <button class="detail-add-btn" type="submit">Добавить в корзину</button>
        </form>
    </div>
</div>


    <div class="detail-reviews">
        <h2 class="detail-reviews-title">Отзывы покупателей</h2>
        <?php if ($reviews): ?>
            <?php foreach($reviews as $rev): ?>
                <div class="detail-review">
                    <p><strong><?= htmlspecialchars($rev['username'] ?? 'Гость') ?>:</strong></p>
                    <p>Оценка: <?= (int)$rev['rating'] ?> / 5</p>
                    <p><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                    <p class="detail-review-date"><?= $rev['created_at'] ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="detail-no-reviews">Пока нет отзывов.</p>
        <?php endif; ?>
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