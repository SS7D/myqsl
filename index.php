<?php
require 'db.php';

// 每页显示多少条
$perPage = 10;
// 当前页数，默认1
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$offset = ($page - 1) * $perPage;
if ($search !== '') {
    $countStmt = $mysqli->prepare("SELECT COUNT(*) FROM qsl_cards WHERE callsign LIKE ?");
    $likeSearch = "%$search%";
    $countStmt->bind_param('s', $likeSearch);
} else {
    $countStmt = $mysqli->prepare("SELECT COUNT(*) FROM qsl_cards");
}
$countStmt->execute();
$countStmt->bind_result($totalRows);
$countStmt->fetch();
$countStmt->close();
$totalPages = ceil($totalRows / $perPage);
if ($search !== '') {
    $stmt = $mysqli->prepare("SELECT * FROM qsl_cards WHERE callsign LIKE ? ORDER BY date DESC LIMIT ? OFFSET ?");
    $stmt->bind_param('sii', $likeSearch, $perPage, $offset);
} else {
    $stmt = $mysqli->prepare("SELECT * FROM qsl_cards ORDER BY date DESC LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $perPage, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

function buildQuery($params = []) {
    $query = $_GET;
    foreach ($params as $key => $val) {
        $query[$key] = $val;
    }
    return http_build_query($query);
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8" />
  <title>QSL 卡记录</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <main class="container py-5" style="max-width:800px;">
    <h2 class="mb-4">📬 已收到的 QSL 卡片  Received QSL Cards</h2>
    <h2 class="mb-4">感谢您的来卡  Thank you for sending your QSL card</h2>

    <!-- 搜索框 -->
    <form method="GET" class="mb-4 d-flex" role="search">
      <input type="text" name="q" class="form-control me-2" placeholder="搜索呼号 Search callsign" value="<?= htmlspecialchars($search) ?>" />
      <button class="btn btn-outline-primary" type="submit">搜索</button>
    </form>

    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>日期</th>
            <th>呼号</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="2" class="text-center">没有找到符合条件的记录</td></tr>
          <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['callsign']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- 分页 -->
    <?php if ($totalPages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= buildQuery(['page' => $page - 1]) ?>" aria-label="上一页">&laquo;</a>
        </li>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        for ($i = $start; $i <= $end; $i++):
        ?>
          <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="?<?= buildQuery(['page' => $i]) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= buildQuery(['page' => $page + 1]) ?>" aria-label="下一页">&raquo;</a>
        </li>
      </ul>
    </nav>
    <?php endif; ?>

    <div class="mt-4 d-flex justify-content-between">
      <a href="admin.php" class="btn btn-outline-primary">➕ 添加新卡片</a>
      <a href="https://blog.bd8ftc.de" class="btn btn-outline-secondary">🏠 返回主站</a>
    </div>
  </main>

  <footer class="text-center text-muted mt-5 mb-3" style="font-size:0.9rem;">
    &copy; <?= date('Y') ?> MYQSL. Design by BD8FTC.
  </footer>
</body>
</html>
