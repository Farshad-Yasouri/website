<?php

session_start();
require_once 'connection.php';
//چون درخواست از فایل دیگر و 
//کد html
//ارسال شده ابتدا چک میکند آیا پست هست یا نه!
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("درخواست نامعتبر است.");
}
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    exit("ایمیل و رمز عبور را وارد کنید.");
}

$stmt = $pdo->prepare(
    // باید بدانیم کدام جدول و کدام فیلدها ار دیتابیسی که به آن وصل شدیم
    "SELECT user_id, name, email, password
        FROM users
     WHERE email = :email
     LIMIT 1"
);

$stmt->execute([
    ":email" => $email
]);

$user = $stmt->fetch();

if ($user && password_verify($password, $user["password"])) {
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["user_name"] = $user["name"];

    header("Location: dashboard.php");
    exit;
} else {
    echo "ایمیل یا رمز عبور اشتباه است.";
}
