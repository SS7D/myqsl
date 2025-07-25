<?php
session_start();

$USERNAME = '';       // 请修改为你的账号
$PASSWORD = 'c'; // 请修改为你的密码

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['username'] === $USERNAME && $_POST['password'] === $PASSWORD) {
        $_SESSION['logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "账号或密码错误";
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8" />
  <title>登录 - QSL 管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <main class="container py-5" style="max-width:400px;">
    <h2 class="mb-4">🔐 登录 QSL 管理后台</h2>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">账号</label>
        <input type="text" name="username" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">密码</label>
        <input type="password" name="password" class="form-control" required />
      </div>

      <button type="submit" class="btn btn-primary w-100">登录</button>
    </form>
  </main>
   <footer class="text-center text-muted mt-5 mb-3" style="font-size:0.9rem;">
  &copy; <?= date('Y') ?> MYQSL. Design by BD8FTC.
</footer>
</body>
</html>
