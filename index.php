<?php
require 'db.php';

$result = $mysqli->query("SELECT * FROM qsl_cards ORDER BY date DESC");
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
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>日期</th>
            <th>呼号</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['date']) ?></td>
              <td><?= htmlspecialchars($row['callsign']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

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
