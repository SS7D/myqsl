<?php

require 'db.php';

// 检查两个表是否都存在
$qslCheck = $mysqli->query("SHOW TABLES LIKE 'qsl_cards'");
$emailCheck = $mysqli->query("SHOW TABLES LIKE 'callsign_emails'");

if ($qslCheck && $qslCheck->num_rows > 0 && $emailCheck && $emailCheck->num_rows > 0) {
    echo "<h2>数据库表 <code>qsl_cards</code> 和 <code>callsign_emails</code> 已存在，无需重复安装。</h2>";
    echo '<p><a href="index.php">返回首页</a></p>';
    exit;
}

// 创建 qsl_cards 表
$sql1 = "CREATE TABLE qsl_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  callsign VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// 创建 callsign_emails 表
$sql2 = "CREATE TABLE callsign_emails (
  id INT AUTO_INCREMENT PRIMARY KEY,
  callsign VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$errors = [];

if (!$mysqli->query($sql1)) {
    $errors[] = "创建数据库表 qsl_cards 失败：<br>" . htmlspecialchars($mysqli->error);
}

if (!$mysqli->query($sql2)) {
    $errors[] = "创建数据库表 callsign_emails 失败：<br>" . htmlspecialchars($mysqli->error);
}

if (empty($errors)) {
    echo "<h2>✅ 数据库表 <code>qsl_cards</code> 和 <code>callsign_emails</code> 创建成功！</h2>";
    echo '<p><a href="index.php">前往首页查看</a></p>';
    echo '<p><a href="admin.php">去后台添加记录</a></p>';
} else {
    echo "<h2>❌ 安装过程中出现错误：</h2>";
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
}
