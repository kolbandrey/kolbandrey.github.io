<?php
session_start();

// Проверка наличия сессии. Если сессии нет — перенаправляем на login.php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Защита от бездействия администратора (15 минут)
$timeout = 900; 
if (time() - ($_SESSION['last_activity'] ?? 0) > $timeout) {
    session_unset();
    session_destroy();
    header('Location: login.php?expired=1');
    exit;
}
$_SESSION['last_activity'] = time();

// Получение заявок
$file = 'requests.json';
$requests = [];
if (file_exists($file)) {
    $requests = json_decode(file_get_contents($file), true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f5f5f7;
            color: #1d1d1f;
            margin: 0;
            padding: 0;
        }
        .admin-header {
            background-color: #000000;
            color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .admin-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 500;
        }
        .btn-logout {
            background-color: #ff3b30;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 22px;
            border-bottom: 2px solid #f5f5f7;
            padding-bottom: 10px;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f5f5f7;
        }
        .admin-table th {
            background-color: #f5f5f7;
            color: #86868b;
            font-size: 12px;
            text-transform: uppercase;
        }
        .badge {
            background-color: #e0f0ff;
            color: #0071e3;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            color: #86868b;
            padding: 40px 0;
        }
    </style>
</head>
<body>

    <header class="admin-header">
        <h2>Панель управления (Администратор: <?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?>)</h2>
        <a href="logout.php" class="btn-logout">Выйти из системы</a>
    </header>

    <main class="container">
        <div class="card">
            <h3>Поступившие заявки</h3>
            
            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <p>Новых заявок от клиентов пока не поступало.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Дата и время</th>
                            <th>Телефон клиента</th>
                            <th>Тип события</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($requests) as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['date']) ?></td>
                                <td><strong><?= htmlspecialchars($item['phone']) ?></strong></td>
                                <td><span class="badge"><?= htmlspecialchars($item['type']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>