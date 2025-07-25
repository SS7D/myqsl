<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
}

require 'db.php';

$success = false;
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $callsign = strtoupper(trim($_POST['callsign'] ?? ''));

    if (!$date || !$callsign) {
        $errorMsg = "日期和呼号不能为空";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO qsl_cards (date, callsign) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param('ss', $date, $callsign);
            if ($stmt->execute()) {
                header("Location: admin.php?success=1");
                exit;
            } else {
                $errorMsg = "数据库插入失败：" . $stmt->error;
            }
        } else {
            $errorMsg = "数据库准备语句失败：" . $mysqli->error;
        }
    }
}

$success = isset($_GET['success']);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8" />
  <title>添加 QSL 卡</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <main class="container py-5" style="max-width:600px;">
    <h2 class="mb-4">✍️ 添加 QSL 卡片
      <a href="logout.php" class="btn btn-sm btn-outline-danger float-end">退出登录</a>
    </h2>

    <?php if ($success): ?>
      <div class="alert alert-success">记录添加成功！</div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">收到日期</label>
        <input type="date" name="date" class="form-control" 
               value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>" required />
      </div>

      <div class="mb-3">
        <label class="form-label">对方呼号</label>
        <input type="text" name="callsign" class="form-control" placeholder="例如 JA1ABC" 
               value="<?= htmlspecialchars($_POST['callsign'] ?? '') ?>" required />
      </div>

      <button type="submit" class="btn btn-primary">提交</button>
    </form>

    <div class="mt-4 d-flex justify-content-between">
      <a href="index.php" class="btn btn-outline-secondary">📄 查看记录</a>
      <a href="https://blog.bd8ftc.de" class="btn btn-outline-secondary">🏠 返回主站</a>
    </div>
  </main>
   <footer class="text-center text-muted mt-5 mb-3" style="font-size:0.9rem;">
  &copy; <?= date('Y') ?> MYQSL. Design by BD8FTC.
</footer>
</body>
</html>
