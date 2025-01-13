<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    
    $sql = "SELECT * FROM activities WHERE user_id = ? ORDER BY created_at DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $activities = $stmt->fetchAll();
        echo json_encode($activities);
    } catch (PDOException $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
} 