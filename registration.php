<?php
session_start();
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$username || !$password || !$email) {
        $message = 'Все поля обязательны';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            $message = 'Пользователь с таким логином или email уже существует';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
            if ($stmt->execute(['username' => $username, 'password' => $hash, 'email' => $email])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: index.html");
                exit;
            } else {
                $message = 'Ошибка при регистрации';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Регистрация</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="glass-effect">
    <form class="login-form" method="post">
        <h2>Регистрация</h2>
        <p id="message" style="color: yellow; text-align:center;"><?php echo htmlspecialchars($message); ?></p>
        <input type="text" name="username" placeholder="Логин/Имя" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit" class="login-btn">Зарегистрироваться</button>
        <a href="authorization.php" class="redirect-string">Уже есть аккаунт?</a>
    </form>
</div>
</body>
</html>
