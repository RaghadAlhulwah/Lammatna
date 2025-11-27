<?php
require_once 'config.php';
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$gathering_id = $_GET['id'] ?? null;
if ($gathering_id) {
    // Remove user from participants
    $stmt = $pdo->prepare("DELETE FROM Participant WHERE UserID = ? AND GatheringID = ?");
    $stmt->execute([$_SESSION['user_id'], $gathering_id]);
    
    // Remove user from assigned tasks
    $stmt = $pdo->prepare("UPDATE Task SET UserID = NULL WHERE UserID = ? AND GatheringID = ?");
    $stmt->execute([$_SESSION['user_id'], $gathering_id]);
    
    $_SESSION['success'] = "تم الخروج من الفعالية بنجاح";
}

header("Location: gatherings.php");
exit;
?>