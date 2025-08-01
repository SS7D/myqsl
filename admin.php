<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
}
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ids'])) {
    $ids = $_POST['delete_ids'];
    if (is_array($ids) && count($ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $mysqli->prepare("DELETE FROM qsl_cards WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        $stmt->execute();
    }
}
$success = false;
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['callsign']) && isset($_POST['date'])) {
    $date = $_POST['date'] ?? '';
    $callsign = strtoupper(trim($_POST['callsign'] ?? ''));

    if (!$date || !$callsign) {
        $errorMsg = "日期和呼号不能为空";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO qsl_cards (date, callsign) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param('ss', $date, $callsign);
            if ($stmt->execute()) {
                $success = true;
                require 'send_email.php';  // 你需要配置此文件，使用 PHPMailer 或类似库

$stmt = $mysqli->prepare("SELECT email FROM callsign_emails WHERE callsign = ?");
$stmt->bind_param("s", $callsign);
$stmt->execute();
$stmt->bind_result($email);
if ($stmt->fetch()) {
    send_thank_you_email($email, $callsign);
}
$stmt->close();
            } else {
                $errorMsg = "数据库插入失败：" . $stmt->error;
            }
        } else {
            $errorMsg = "数据库准备语句失败：" . $mysqli->error;
        }
    }
}
$search = $_GET['q'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

$where = '';
$params = [];
$types = '';

if (!empty($search)) {
    $where = "WHERE callsign LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM qsl_cards $where");
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();
$totalPages = ceil($total / $limit);
$query = "SELECT * FROM qsl_cards $where ORDER BY date DESC LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($query);
if ($params) {
    $types .= 'ii';
    $params[] = $limit;
    $params[] = $offset;
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <title>管理 QSL 卡</title>
  <link href="bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <main class="container py-5" style="max-width:800px;">
    <h2 class="mb-4">📋 管理 QSL 卡片
      <a href="logout.php" class="btn btn-sm btn-outline-danger float-end">退出登录</a>
    </h2>

    <?php if ($success): ?>
      <div class="alert alert-success">记录添加成功！</div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>
    <form method="POST" class="mb-4">
      <div class="row g-3">
        <div class="col-md-5">
          <input type="date" name="date" class="form-control" 
                 value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>" required />
        </div>
        <div class="col-md-5">
          <input type="text" name="callsign" class="form-control" placeholder="呼号" 
                 value="<?= htmlspecialchars($_POST['callsign'] ?? '') ?>" required />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">添加</button>
        </div>
      </div>
    </form>
    <form method="GET" class="mb-3 d-flex">
      <input type="text" name="q" class="form-control me-2" placeholder="搜索记录" value="<?= htmlspecialchars($search) ?>">
      <button class="btn btn-outline-primary">Search</button>
    </form>
    <form method="POST">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th style="width:30px;"><input type="checkbox" id="checkAll" /></th>
              <th>日期</th>
              <th>呼号</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><input type="checkbox" name="delete_ids[]" value="<?= $row['id'] ?>"></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['callsign']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <button type="submit" class="btn btn-danger btn-sm">删除所选</button>
    </form>

    <nav class="mt-4">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>

    <div class="mt-4 d-flex justify-content-between">
      <a href="index.php" class="btn btn-outline-secondary">📄 查看公开记录</a>
      <a href="https://blog.bd8ftc.de" class="btn btn-outline-secondary">🏠 返回主站</a>
    </div>
  </main>

  <footer class="text-center text-muted mt-5 mb-3" style="font-size:0.9rem;">
    &copy; <?= date('Y') ?> MYQSL. Design by BD8FTC.
  </footer>

  <script>
    document.getElementById('checkAll').addEventListener('click', function() {
      const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
      for (let box of checkboxes) box.checked = this.checked;
    });
  </script>
</body>
</html>
