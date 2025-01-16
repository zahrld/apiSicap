<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = $_GET['user_id'] ?? null;

    if ($user_id === null) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID tidak disediakan'
        ]);
        exit;
    }

    $sql = "SELECT activities.*, users.nama AS nama_pengguna 
            FROM activities 
            JOIN users ON activities.user_id = users.id 
            WHERE activities.user_id = ? 
            ORDER BY activities.created_at DESC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
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