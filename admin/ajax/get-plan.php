<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || !isAdmin($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get plan ID
$planId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($planId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid plan ID']);
    exit;
}

try {
    // Get plan details
    $stmt = $pdo->prepare("SELECT * FROM subscription_plans WHERE id = :id");
    $stmt->bindParam(':id', $planId);
    $stmt->execute();
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$plan) {
        echo json_encode(['success' => false, 'message' => 'Plan not found']);
    } else {
        echo json_encode(['success' => true, 'plan' => $plan]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
