<?php
session_start();

// Очищаем массив сессии
$_SESSION = [];

// Удаляем куку сессии из браузера
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Уничтожаем сессию
session_destroy();

// Перенаправляем на страницу входа
header('Location: login.php');
exit;