<?php
session_start();
require_once 'db.php'; // conexiunea $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $city = $_POST['city'];
    $review = $_POST['review'];
    $rating = $_POST['rating'];

    if ($user_id > 0 && $city && $review && $rating) {
         $stmt = $pdo->prepare("INSERT INTO reviews (user_id, city, review_text, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
         $stmt->execute([$user_id, $city, $review, $rating]);

        $_SESSION['flash_success'] = "✅ Review submitted successfully!";
        header("Location: index.php");
        exit;
    } else {
        echo "Please make sure you're logged in and all fields are filled out.";
    }
} else {
    echo "Invalid request.";
}
