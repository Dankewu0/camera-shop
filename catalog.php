<?php
session_start();
require_once 'db.php';

$stmt = $pdo->query("
    SELECT p.id, p.name, p.price
    FROM products p
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Каталог</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="category">
    <div class="logo">Категории</div>
    <ul>
        <li><a href="#"><i class="photo"></i>Фотокамеры</a></li>
        <li><a href="#"><i class="video"></i>Видеокамеры</a></li>
        <li><a href="#"><i class="obj"></i>Объективы</a></li>
        <li><a href="#"><i class="acs"></i>Аксессуары</a></li>
    </ul>
</div>

<div class="main-content">
<?php foreach ($products as $p): ?>
<div class="product-card" onclick="window.location.href='./detail.php?id=<?=$p['id']?>'">
    <h3><?=htmlspecialchars($p['name'])?></h3>
    <p>Цена: <?=number_format($p['price'], 0, ',', ' ')?> руб.</p>
</div>
<?php endforeach; ?>
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
