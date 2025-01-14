<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT * FROM activities ORDER BY created_at DESC";

    try {
        $stmt = $pdo->query($sql);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($activities);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?>