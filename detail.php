<?php
session_start();
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Товар не найден";
    exit;
}

$id = (int)$_GET['id'];

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

$imgStmt = $pdo->prepare("
    SELECT image_url
    FROM product_images
    WHERE product_id = :id
    ORDER BY is_main DESC
    LIMIT 1
");
$imgStmt->execute(['id' => $id]);
$mainImage = $imgStmt->fetchColumn();
if (!$mainImage) $mainImage = 'placeholder.jpg';

$reviewsStmt = $pdo->prepare("
    SELECT r.*, u.username
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.product_id = :id
    ORDER BY r.created_at DESC
");
$reviewsStmt->execute(['id' => $id]);
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

$logged = isset($_SESSION['user_id']);
$userId = $logged ? $_SESSION['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if ($logged) {
            $stmtCart = $pdo->prepare("SELECT id FROM cart WHERE user_id = :uid");
            $stmtCart->execute(['uid' => $userId]);
            $cart = $stmtCart->fetch(PDO::FETCH_ASSOC);
            if (!$cart) {
                $pdo->prepare("INSERT INTO cart (user_id) VALUES (:uid)")->execute(['uid' => $userId]);
                $cartId = $pdo->lastInsertId();
            } else $cartId = $cart['id'];

            $stmtItem = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = :cid AND product_id = :pid");
            $stmtItem->execute(['cid' => $cartId, 'pid' => $id]);
            $item = $stmtItem->fetch(PDO::FETCH_ASSOC);
            if ($item) {
                $newQty = $item['quantity'] + $quantity;
                $pdo->prepare("UPDATE cart_items SET quantity = :q WHERE id = :id")->execute(['q' => $newQty, 'id' => $item['id']]);
            } else {
                $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cid, :pid, :q)")
                    ->execute(['cid' => $cartId, 'pid' => $id, 'q' => $quantity]);
            }
        } else {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]['quantity'] += $quantity;
            else $_SESSION['cart'][$id] = ['quantity' => $quantity];
        }

        header('Location: cart.php');
        exit;
    }

    if (isset($_POST['comment']) && $logged) {
        $comment = trim($_POST['comment_text'] ?? '');
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        if ($comment !== '') {
            $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, created_at) VALUES (:pid, :uid, :r, :c, NOW())")
                ->execute(['pid' => $id, 'uid' => $userId, 'r' => $rating, 'c' => $comment]);
            header("Location: product_detail.php?id=$id");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($product['name']) ?></title>
<link rel="stylesheet" href="styles.css">
<style>
.detail-flex { display: flex; gap: 20px; align-items: flex-start; }
.detail-img-main { width: 300px; border: 2px solid #ccc; border-radius: 8px; }
</style>
</head>
<body>
<?php include 'header.php'; ?>

<main class="detail-main">
<h1 class="detail-title"><?= htmlspecialchars($product['name']) ?></h1>
<div class="detail-flex">
<img class="detail-img-main" src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
<div class="detail-info">
<p><strong>Цена:</strong> <?= number_format($product['price'],0,',',' ') ?> руб.</p>
<p><strong>Категория:</strong> <?= htmlspecialchars($product['category_name'] ?? 'Нет') ?></p>
<p><strong>Производитель:</strong> <?= htmlspecialchars($product['manufacturer_name'] ?? 'Нет') ?></p>
<p><strong>Наличие на складе:</strong> <?= (int)$product['stock'] ?> шт.</p>
<p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($product['description'] ?? 'Описание отсутствует')) ?></p>

<form class="detail-form" method="post">
<input type="hidden" name="product_id" value="<?= $product['id'] ?>">
<input class="detail-quantity" type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>">
<button class="detail-add-btn" type="submit" name="add_to_cart">Добавить в корзину</button>
</form>

<?php if ($logged): ?>
<form method="post" class="detail-comment-form">
<h3 class="review">Оставить отзыв</h3>
<label class="review">Оценка:
<select name="rating" class="review">
<option value="5">5</option>
<option value="4">4</option>
<option value="3">3</option>
<option value="2">2</option>
<option value="1">1</option>
</select>
</label>
<textarea name="comment_text" rows="4" class="review" placeholder="Ваш отзыв"></textarea>
<button type="submit" name="comment" class="review">Отправить</button>
</form>
<?php endif; ?>
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
