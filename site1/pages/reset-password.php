<?php

session_start();

require_once 'connection.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'درخواست نامعتبر است.'
    ]);

    exit;
}

if (empty($_SESSION['reset_user_id'])) {
    http_response_code(403);

    echo json_encode([
        'success' => false,
        'message' => 'دسترسی تغییر رمز معتبر نیست.'
    ]);

    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$newPassword = $input['new_password'] ?? '';

if (strlen($newPassword) < 6) {
    http_response_code(422);

    echo json_encode([
        'success' => false,
        'message' => 'رمز عبور باید حداقل ۶ کاراکتر باشد.'
    ]);

    exit;
}

$userId = $_SESSION['reset_user_id'];
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare(
        "UPDATE users
         SET password = :password
         WHERE user_id = :user_id"
    );

    $stmt->execute([
        ':password' => $hashedPassword,
        ':user_id' => $userId
    ]);

    // حذف مجوز موقت تغییر رمز
    unset($_SESSION['reset_user_id']);

    echo json_encode([
        'success' => true,
        'message' => 'رمز عبور با موفقیت تغییر کرد.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'خطا در تغییر رمز عبور.'
    ]);
}
