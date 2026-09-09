<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: ../index.html");
  exit;
}

require_once 'connection.php';
$stmt = $pdo->prepare(
  "SELECT user_id, name, email, phone, studyfield, studyclass
     FROM users
     WHERE user_id = :user_id
     LIMIT 1"
);

$stmt->execute([
  ":user_id" => $_SESSION["user_id"]
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  session_unset();
  session_destroy();

  header("Location: ../index.html");
  exit;
}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>پنل کاربری</title>

  <!-- Bootstrap RTL -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
    rel="stylesheet">
<link rel="stylesheet" href="/assets/css/dashboard.css">
  
</head>

<body>

  <div class="container dashboard">

    <div class="panel-card">

      <!-- عنوان پنل -->
      <div class="panel-title">
        <h3 class="mb-2">پنل کاربری</h3>
        <p class="mb-0">
          خوش آمدید،
          <strong><?= htmlspecialchars($user["name"], ENT_QUOTES, "UTF-8") ?></strong>
        </p>
        <a
          href="logout.php"
          class="btn btn-light btn-sm mt-3">
          خروج از حساب
        </a>

      </div>

      <div class="row g-0">

        <!-- بخش سمت راست: دسترسی‌ها -->
        <div class="col-md-4">
          <div class="access-menu">

            <h5>دسترسی‌ها</h5>

            <button
              class="access-item active"
              data-target="profile">
              مشخصات
            </button>

            <button
              class="access-item"
              data-target="email">
              ایمیل
            </button>

            <button
              class="access-item"
              data-target="address">
              آدرس
            </button>

            <button
              class="access-item"
              data-target="password">
              رمز عبور
            </button>

            <button
              class="access-item"
              data-target="phone">
              تلفن
            </button>

          </div>
        </div>

        <!-- بخش سمت چپ: محتوای صفحات -->
        <div class="col-md-8">
          <div class="content-area">

            <!-- مشخصات -->
            <div id="profile" class="content-box active">
              <h4>مشخصات کاربر</h4>

              <div class="mb-3">
                <label class="form-label">نام کاربر</label>
                <input
                  type="text"
                  class="form-control"
                  value="<?= htmlspecialchars($user["name"], ENT_QUOTES, "UTF-8") ?>">

              </div>
            </div>
              <!-- ایمیل -->
              <div id="email" class="content-box">
                <h4>ویرایش ایمیل</h4>

                <div class="mb-3">
                  <label for="emailInput" class="form-label">
                    ایمیل
                  </label>

                  <input
                    type="email"
                    id="emailInput"
                    class="form-control"
                    value="<?= htmlspecialchars($user["email"], ENT_QUOTES, "UTF-8") ?>">

                </div>
              </div>

              <!-- آدرس -->
              <div id="address" class="content-box">
                <h4>ویرایش آدرس</h4>

                <div class="mb-3">
                  <label for="addressInput" class="form-label">
                    آدرس
                  </label>

                  <textarea
                    id="addressInput"
                    class="form-control"
                    rows="4">تهران، خیابان ولیعصر، پلاک ۱۲</textarea>
                </div>
              </div>

              <!-- رمز عبور -->
              <div id="password" class="content-box">
                <h4>تغییر رمز عبور</h4>

                <div class="mb-3">
                  <label for="passwordInput" class="form-label">
                    رمز عبور جدید
                  </label>

                  <input
                    type="password"
                    id="passwordInput"
                    class="form-control"
                    placeholder="رمز عبور جدید را وارد کنید">
                </div>

                <div class="mb-3">
                  <label for="repeatPasswordInput" class="form-label">
                    تکرار رمز عبور
                  </label>

                  <input
                    type="password"
                    id="repeatPasswordInput"
                    class="form-control"
                    placeholder="رمز عبور را دوباره وارد کنید">
                </div>
              </div>

              <!-- تلفن -->
              <div id="phone" class="content-box">
                <h4>ویرایش شماره تلفن</h4>

                <div class="mb-3">
                  <label for="phoneInput" class="form-label">
                    شماره تلفن
                  </label>

                  <input
                    type="tel"
                    id="phoneInput"
                    class="form-control"
                    value="<?= htmlspecialchars($user["phone"], ENT_QUOTES, "UTF-8") ?>">

                </div>
              </div>

            
          </div>

        </div>
      </div>

    </div>

    <script src="/assets/js/dashboard.js"></script>

</body>

</html>