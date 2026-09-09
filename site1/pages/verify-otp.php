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

$input = json_decode(file_get_contents('php://input'), true);

$phone = trim($input['phone'] ?? '');
$otp = trim($input['otp'] ?? '');

if ($phone === '' || $otp === '') {
    http_response_code(422);

    echo json_encode([
        'success' => false,
        'message' => 'شماره تلفن و کد OTP الزامی است.'
    ]);

    exit;
}

if (str_starts_with($phone, '+98')) {
    $phone = '0' . substr($phone, 3);
}

try {
    $stmt = $pdo->prepare(
        "SELECT *
         FROM password_otps
         WHERE phone = :phone
         AND is_used = 0
         ORDER BY id DESC
         LIMIT 1"
    );

    $stmt->execute([
        ':phone' => $phone
    ]);

    $otpRecord = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$otpRecord) {
        echo json_encode([
            'success' => false,
            'message' => 'کد OTP پیدا نشد.'
        ]);

        exit;
    }

    if (strtotime($otpRecord['expires_at']) < time()) {
        echo json_encode([
            'success' => false,
            'message' => 'کد OTP منقضی شده است.'
        ]);

        exit;
    }

    if (!password_verify($otp, $otpRecord['otp_code'])) {
        echo json_encode([
            'success' => false,
            'message' => 'کد OTP صحیح نیست.'
        ]);

        exit;
    }

    // کد تأیید شد و دیگر قابل استفاده نیست
    $stmt = $pdo->prepare(
        "UPDATE password_otps
         SET is_used = 1
         WHERE id = :id"
    );

    $stmt->execute([
        ':id' => $otpRecord['id']
    ]);

    // ذخیره موقت شناسه کاربر برای مرحله تغییر رمز
    $_SESSION['reset_user_id'] = $otpRecord['user_id'];

    echo json_encode([
        'success' => true,
        'message' => 'کد با موفقیت تأیید شد.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'خطا در دیتابیس.'
    ]);
}
