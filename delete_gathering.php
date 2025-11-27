<?php
require_once 'config.php';
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$gathering_id = $_GET['id'] ?? null;
if ($gathering_id) {
    // Verify user is admin of this gathering
    $stmt = $pdo->prepare("SELECT adminID FROM Gathering WHERE GatheringID = ?");
    $stmt->execute([$gathering_id]);
    $gathering = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($gathering && $gathering['adminID'] == $_SESSION['user_id']) {
        // Delete related records first
        $pdo->prepare("DELETE FROM Task WHERE GatheringID = ?")->execute([$gathering_id]);
        $pdo->prepare("DELETE FROM Participant WHERE GatheringID = ?")->execute([$gathering_id]);
        $pdo->prepare("DELETE FROM Gathering WHERE GatheringID = ?")->execute([$gathering_id]);
        
        $_SESSION['success'] = "تم حذف الفعالية بنجاح";
    }
}

header("Location: gatherings.php");
exit;
?>