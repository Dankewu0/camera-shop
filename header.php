<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="container">
        <div class="logo"><img src="./iconkf.png" alt="Logo" />Wunsche</div>
        <nav>
            <ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="./logout.php">Выход</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] >= 2): ?>
                        <li><a href="./admin.php">Админ панель</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="./authorization.php">Вход</a></li>
                <?php endif; ?>
                <li><a href="./index.php">Главная</a></li>
                <li><a href="./catalog.php" target="_blank">Каталог</a></li>
                <li><a href="./order.php">Заказы</a></li>
                <li class="cart"><a href="./cart.php">Корзина</a></li>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart-icon lucide-shopping-cart">
                  <circle cx="8" cy="21" r="1" />
                  <circle cx="19" cy="21" r="1" />
                  <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                </svg>
            </ul>
        </nav>
    </div>
</header>
