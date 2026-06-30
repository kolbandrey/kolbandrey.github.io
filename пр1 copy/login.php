<?php
session_start();

// Если администратор уже авторизован, перенаправляем в панель управления
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $valid_username = 'admin';
    
    // Динамически создаем хэш пароля 'admin123', как в вашей методичке
    $valid_password_hash = password_hash('admin123', PASSWORD_DEFAULT); 

    if ($username === $valid_username && password_verify($password, $valid_password_hash)) {
        session_regenerate_id(true); // Защита от фиксации сессий
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['last_activity'] = time(); // Время последней активности
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в панель управления</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            outline: none;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: var(--blue);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .error-msg {
            color: red;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Вход в админ-панель</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['expired'])): ?>
            <div class="error-msg" style="color: #e67e22;">Сессия истекла из-за бездействия.</div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Войти</button>
        </form>
    </div>
</body>
</html>