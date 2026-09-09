<?php
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("درخواست نامعتبر است.");
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$studyfield = trim($_POST['studyfield'] ?? '');
$studyclass = trim($_POST['studyclass'] ?? '');

if (
    $name === '' ||
    $email === '' ||
    $phone === '' ||
    $password === '' ||
    $studyfield === '' ||
    $studyclass === ''
) {
    exit('لطفاً همه فیلدها را پر کنید.');
}
if (!in_array($studyfield, ["تجربی", "ریاضی", "انسانی"], true)) {
    exit("رشته تحصیلی باید تجربی ، ریاضی یا انسانی باشد.");
}
if (!in_array($studyclass, ['10','11','12'], true)) {
    exit("پایه تحصیلی باید 10، 11 یا 12 باشد.");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = "

        INSERT INTO users (name, email,phone , password , studyfield , studyclass)
        VALUES (:name, :email, :phone ,:password , :studyfield , :studyclass)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":phone" => $phone,
        ":password" => $hashedPassword,
        ":studyfield" => $studyfield,
        ":studyclass" => $studyclass

    ]);

    echo "ثبت‌نام با موفقیت انجام شد.";
  echo '<br><a href="/index.html">بازگشت به صفحه ورود</a>';
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "این ایمیل قبلاً ثبت شده است.";
    } else {
        echo "خطا در ثبت اطلاعات.";
    }
}
