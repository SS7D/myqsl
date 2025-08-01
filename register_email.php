<?php
session_start();
require 'db.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$success = false;
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $callsign = strtoupper(trim($_POST['callsign'] ?? ''));
    $email = trim($_POST['email'] ?? '');
    $captcha = trim($_POST['captcha'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (isset($_SESSION['last_submit']) && time() - $_SESSION['last_submit'] < 30) {
        $errorMsg = "提交太频繁，请 30 秒后再试。";
    }
    elseif ($csrf_token !== ($_SESSION['csrf_token'] ?? '')) {
        $errorMsg = "非法提交，请刷新页面后再试。";
    }
    elseif (strcasecmp($_SESSION['captcha'] ?? '', $captcha) !== 0) {
        $errorMsg = "验证码错误，请重试。";
    }
    elseif (!$callsign || !$email) {
        $errorMsg = "呼号和邮箱不能为空。";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "请输入有效的邮箱地址。";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO callsign_emails (callsign, email) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param('ss', $callsign, $email);
            if ($stmt->execute()) {
                $success = true;
                $_SESSION['last_submit'] = time();
                unset($_SESSION['captcha']);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
            } else {
                if ($stmt->errno === 1062) {
                    // 重复呼号
                    $errorMsg = "此呼号已登记。如需修改，请联系 <你自己的联系方式>";
                } else {
                    $errorMsg = "数据库错误：" . $stmt->error;
                }
            }
            $stmt->close();
        } else {
            $errorMsg = "数据库准备失败：" . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8" />
  <title>登记呼号和邮箱 - MyQSL</title>
  <link href="bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <main class="container py-5" style="max-width:600px;">
    <h2 class="mb-4">📮 登记呼号和邮箱</h2>

    <?php if ($success): ?>
      <div class="alert alert-success">登记成功！感谢您的支持。</div>
    <?php elseif ($errorMsg): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <div class="mb-3">
        <label for="callsign" class="form-label">呼号</label>
        <input id="callsign" name="callsign" type="text" class="form-control" required
               value="<?= htmlspecialchars($_POST['callsign'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">验证码</label><br>
        <img src="captcha.php" alt="验证码" style="cursor:pointer; margin-bottom:10px;"
             onclick="this.src='captcha.php?'+Math.random()" title="点击刷新验证码"><br>
        <input name="captcha" type="text" class="form-control" required placeholder="请输入验证码">
      </div>

      <button type="submit" class="btn btn-primary">提交登记</button>
    </form>

    <div class="mt-4">
      <a href="index.php" class="btn btn-outline-secondary">查看 QSL 卡记录</a>
    </div>
  </main>

  <footer class="text-center text-muted mt-5 mb-3" style="font-size:0.9rem;">
    &copy; <?= date('Y') ?> MYQSL. Design by BD8FTC.
  </footer>
</body>
</html>
