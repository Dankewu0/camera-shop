<?php
session_start();
require_once 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usernameOrEmail && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :ue OR email = :ue");
        $stmt->execute(['ue' => $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = isset($user['role']) ? (int)$user['role'] : 1; // дефолт 1
            header("Location: index.php");
            exit;
        } else {
            $message = 'Неверный логин или пароль';
        }
    } else {
        $message = 'Введите логин и пароль';
    }
}
?>

<!doctype html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Вход</title>
<link rel="stylesheet" href="styles.css" />
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <div class="glass-effect">
        <form class="login-form" action="authorization.php" method="post">
            <h2>Вход</h2>
            <input type="text" name="username" placeholder="Логин/Имя" required />
            <input type="password" name="password" placeholder="Пароль" required />
            <button type="submit" class="login-btn">Войти</button>
            <a href="registration.php" class="redirect-string">Нет аккаунта?</a>
            <?php if($message): ?>
                <div class="error-message" style="color: red; margin-top: 10px">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</main>
</body>
</html>
