<?php
// MySQL 连接配置
$host = 'localhost';     // 数据库主机
$user = '';     // 数据库用户名
$pass = ''; // 数据库密码
$dbname = '';         // 数据库名

$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_error) {
    die('数据库连接失败：' . $mysqli->connect_error);
}
?>
