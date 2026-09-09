<?php

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

if ($phone === '') {
    http_response_code(422);

    echo json_encode([
        'success' => false,
        'message' => 'شماره تلفن را وارد کنید.'
    ]);

    exit;
}
// حذف فاصله و خط تیره
$phone = preg_replace('/[\s\-]/', '', $phone);

// تبدیل شماره +98 به قالب 09
if (str_starts_with($phone, '+98')) {
    $phone = '0' . substr($phone, 3);
}

// بررسی شماره با قالب ذخیره‌شده در دیتابیس
if (!preg_match('/^09\d{9}$/', $phone)) {
    http_response_code(422);

    echo json_encode([
        'success' => false,
        'message' => 'شماره تلفن معتبر نیست.'
    ]);

    exit;
}

try {
    // پیدا کردن کاربر با شماره تلفن
    $stmt = $pdo->prepare(
        "SELECT user_id FROM users WHERE phone = :phone LIMIT 1"
    );

    $stmt->execute([
        ':phone' => $phone
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'کاربری با این شماره تلفن پیدا نشد.'
        ]);

        exit;
    }

    // تولید کد شش‌رقمی
    $otp = (string) random_int(100000, 999999);

    // ذخیره هش کد در دیتابیس
    $otpHash = password_hash($otp, PASSWORD_DEFAULT);

    // غیرفعال‌کردن کدهای قبلی
    $stmt = $pdo->prepare(
        "UPDATE password_otps
         SET is_used = 1
         WHERE user_id = :user_id AND is_used = 0"
    );

    $stmt->execute([
        ':user_id' => $user['user_id']
    ]);

    // ذخیره کد جدید
    $stmt = $pdo->prepare(
        "INSERT INTO password_otps
        (user_id, phone, otp_code, expires_at)
        VALUES
        (:user_id, :phone, :otp_code, :expires_at)"
    );

    $stmt->execute([
        ':user_id' => $user['user_id'],
        ':phone' => $phone,
        ':otp_code' => $otpHash,
        ':expires_at' => date('Y-m-d H:i:s', time() + 300)
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'کد بازیابی ایجاد شد.',
        'dev_otp' => $otp
    ]);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'خطایی در پردازش درخواست رخ داد.'
    ]);
}