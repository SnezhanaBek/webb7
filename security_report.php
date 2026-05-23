<?php
require_once 'config.php';
session_start();
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчёт по безопасности — Задание 7</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h1, h2 { color: #2c3e50; }
        .vulnerability { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid; border-radius: 8px; }
        .xss { border-left-color: #e74c3c; }
        .sqli { border-left-color: #e67e22; }
        .csrf { border-left-color: #f39c12; }
        .info { border-left-color: #3498db; }
        .include { border-left-color: #2ecc71; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 8px; overflow-x: auto; }
        .badge { display: inline-block; background: #27ae60; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; margin-left: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Отчёт по аудиту безопасности</h1>
    <p><strong>Студент:</strong> ID 82647 (u82647)</p>
    <p><strong>Дата:</strong> <?php echo date('Y-m-d'); ?></p>
    
    <h2>1. XSS (межсайтовый скриптинг) <span class="badge">2 балла</span></h2>
    <div class="vulnerability xss">
        <p><strong>Метод защиты:</strong> Использование <code>htmlspecialchars($data, ENT_QUOTES, 'UTF-8')</code> при любом выводе данных.</p>
        <p><strong>Пример кода:</strong></p>
        <pre>
&lt;?php
echo htmlspecialchars($user['fio'], ENT_QUOTES, 'UTF-8');
?&gt;
        </pre>
        <p><strong>Где применено:</strong> Во всех файлах (<code>index.php</code>, <code>admin.php</code>) при выводе данных из БД и от пользователя.</p>
    </div>
    
    <h2>2. SQL Injection <span class="badge">2 балла</span></h2>
    <div class="vulnerability sqli">
        <p><strong>Метод защиты:</strong> Использование подготовленных запросов (PDO prepared statements).</p>
        <p><strong>Пример кода:</strong></p>
        <pre>
&lt;?php
$stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
$stmt->execute([$login]);
?&gt;
        </pre>
        <p><strong>Где применено:</strong> Все запросы к БД используют подготовленные запросы.</p>
    </div>
    
    <h2>3. CSRF (подделка межсайтовых запросов) <span class="badge">2 балла</span></h2>
    <div class="vulnerability csrf">
        <p><strong>Метод защиты:</strong> Генерация уникального токена для каждой формы и его проверка на сервере.</p>
        <p><strong>Пример кода:</strong></p>
        <pre>
&lt;?php
// Генерация токена
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Проверка токена
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF токен недействителен');
}
?&gt;
        </pre>
        <p><strong>Где применено:</strong> В <code>index.php</code> (скрытое поле формы) и в <code>save.php</code> (проверка).</p>
    </div>
    
    <h2>4. Information Disclosure <span class="badge">1 балл</span></h2>
    <div class="vulnerability info">
        <p><strong>Метод защиты:</strong> Отключение показа ошибок и создание файла <code>.htaccess</code>.</p>
        <p><strong>Пример кода:</strong></p>
        <pre>
&lt;?php
error_reporting(0);
ini_set('display_errors', 0);
?&gt;

# .htaccess
Options -Indexes
&lt;FilesMatch "\.(ini|log|sql|bak)$"&gt;
    Deny from all
&lt;/FilesMatch&gt;
        </pre>
        <p><strong>Где применено:</strong> В <code>config.php</code> отключён display_errors; добавлен <code>.htaccess</code>.</p>
    </div>
    
    <h2>5. Include / Upload <span class="badge">1 балл</span></h2>
    <div class="vulnerability include">
        <p><strong>Метод защиты:</strong> В приложении отсутствует динамическое включение файлов. Все include пути жёстко прописаны.</p>
        <p><strong>Пример кода:</strong></p>
        <pre>
&lt;?php
// Только статические include:
require_once 'config.php';
include('login.php');
// Нет динамических include типа:
// include($_GET['page'] . '.php');
?&gt;
        </pre>
        <p><strong>Дополнительно:</strong> Функционал загрузки файлов (Upload) в приложении отсутствует.</p>
    </div>
    
    <h2>Заключение</h2>
    <div class="vulnerability" style="border-left-color: #27ae60;">
        <p>✅ Все выявленные уязвимости исправлены.</p>
        <p><strong>Общая оценка:</strong> 8/8 баллов</p>
    </div>
    
    <p><a href="admin.php">← Вернуться в админ-панель</a> | <a href="index.php">← На главную</a></p>
</div>
</body>
</html>