<?php
session_start();
require_once 'db.php'; // Conexiune PDO

// Verifică dacă userul e logat
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    header("Location: login.php");
    exit;
}

// Obține datele userului
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 40px;
        }
        .profile-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 400px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="profile-card">
    <img src="assets/images/clients/c<?php echo rand(1, 8);?>.png" alt="client">
        <h2><?= htmlspecialchars($user['username']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'N/A') ?></p>
        <p><strong>Member since:</strong> <?= date('F Y', strtotime($user['created_at'])) ?></p>
        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>
</body>
</html>
