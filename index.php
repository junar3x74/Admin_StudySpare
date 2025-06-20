<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: LogIn.php");
    exit();
}
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - StudySpare</title>
  <link rel="stylesheet" href="style/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <?php include 'include/sidebar.php'; ?>
  <div class="main">
    <?php include 'include/header.php'; ?>
    <?php
    include 'php/db.php';

    $userCount = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
    $resourceCount = $conn->query("SELECT COUNT(*) AS total FROM resources")->fetch_assoc()['total'];
    $pendingCount = $conn->query("SELECT COUNT(*) AS total FROM uploads WHERE status = 'pending'")->fetch_assoc()['total'];
    $uploadCount = $conn->query("SELECT COUNT(*) AS total FROM uploads")->fetch_assoc()['total'];
    ?>
    <div class="card-container">
      <div class="card"><h3><i class="fa-solid fa-users"></i> Users</h3><p><?= $userCount ?></p></div>
      <div class="card"><h3><i class="fa-solid fa-book"></i> Resources</h3><p><?= $resourceCount ?></p></div>
      <div class="card"><h3><i class="fa-solid fa-clock"></i> Pending Uploads</h3><p><?= $pendingCount ?></p></div>
      <div class="card"><h3><i class="fa-solid fa-upload"></i> Uploads</h3><p><?= $uploadCount ?></p></div>
    </div>

    <div class="tables">
      <div class="latest-upload">
        <div class="heading">
          <h2>Latest Upload</h2>
          <a href="uploads.php" class="btn">View All</a>
        </div>
        <table class="luser">
          <thead>
            <tr><td>Name</td><td>Type</td><td>Date</td><td>Action</td></tr>
          </thead>
          <tbody>
<?php
$latestUploads = $conn->query("SELECT u.id, u.title, u.type, u.file_path, u.submitted_at, a.username AS admin_name FROM uploads u LEFT JOIN admin a ON u.admin_id = a.id ORDER BY u.submitted_at DESC LIMIT 5");

if ($latestUploads && $latestUploads->num_rows > 0):
    while ($upload = $latestUploads->fetch_assoc()):
?>
  <tr>
    <td><?= htmlspecialchars($upload['title']) ?><br><small>by <?= htmlspecialchars($upload['admin_name'] ?? 'Unknown') ?></small></td>
    <td><?= htmlspecialchars($upload['type']) ?></td>
    <td><?= date('m/d/y', strtotime($upload['submitted_at'])) ?></td>
    <td>
      <a href="<?= htmlspecialchars($upload['file_path']) ?>" target="_blank" class="icon-button" title="View Resource">
        <i class="fas fa-eye warm-icon"></i>
      </a>
      <a href="delete_upload.php?id=<?= $upload['id'] ?>" onclick="return confirm('Are you sure you want to delete this upload?');" class="icon-button" title="Delete Resource">
        <i class="fas fa-trash delete-icon"></i>
      </a>
      <button class="icon-button" title="Comment" onclick="alert('Comment feature coming soon!')">
        <i class="fas fa-comment comment-icon"></i>
      </button>
    </td>
  </tr>
<?php endwhile; else: ?>
  <tr><td colspan="4">No uploads found.</td></tr>
<?php endif; ?>
</tbody>
        </table>
      </div>
    </div>

    <div class="tables">
      <div class="latest-user">
        <div class="heading">
          <h2>Latest User</h2>
          <a href="users.php" class="btn">View All</a>
        </div>
        <table class="luser">
          <thead>
            <tr><td>Name</td><td>Email</td><td>Date</td><td>Action</td></tr>
          </thead>
          <tbody>
<?php
$latestUsers = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
while ($user = $latestUsers->fetch_assoc()):
?>
  <tr>
    <td><?= htmlspecialchars($user['name']) ?></td>
    <td><?= htmlspecialchars($user['email']) ?></td>
    <td><?= date('m/d/y', strtotime($user['created_at'])) ?></td>
    <td>
      <a href="ban_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Ban this user?');" class="icon-button" title="Ban User">
        <i class="fas fa-ban warm-icon"></i>
      </a>
      <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?');" class="icon-button" title="Delete User">
        <i class="fas fa-trash delete-icon"></i>
      </a>
      <button class="icon-button" title="Comment" onclick="alert('Comment feature coming soon!')">
        <i class="fas fa-comment comment-icon"></i>
      </button>
    </td>
  </tr>
<?php endwhile; ?>
</tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
