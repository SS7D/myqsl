<?php

require 'db.php';

$tableCheck = $mysqli->query("SHOW TABLES LIKE 'qsl_cards'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo "<h2>数据库表 <code>qsl_cards</code> 已存在，无需重复安装。</h2>";
    echo '<p><a href="index.php">返回首页</a></p>';
    exit;
}

$sql = "CREATE TABLE qsl_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  callsign VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($mysqli->query($sql)) {
    echo "<h2>✅ 数据库表 <code>qsl_cards</code> 创建成功！</h2>";
    echo '<p><a href="index.php">前往首页查看</a></p>';
    echo '<p><a href="admin.php">去后台添加记录</a></p>';
} else {
    echo "<h2>❌ 创建数据库表失败：</h2>";
    echo "<pre>" . htmlspecialchars($mysqli->error) . "</pre>";
}
