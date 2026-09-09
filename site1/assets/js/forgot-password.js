const form = document.getElementById("forgotPasswordForm");
const phoneInput = document.getElementById("phone");
const submitButton = document.getElementById("submitButton");
const message = document.getElementById("message");

const otpSection = document.getElementById("otpSection");
const otpInput = document.getElementById("otp");
const verifyButton = document.getElementById("verifyButton");
const passwordSection = document.getElementById("passwordSection");
const newPasswordInput = document.getElementById("newPassword");
const resetButton = document.getElementById("resetButton");


let currentPhone = "";

form.addEventListener("submit", async function (event) {
  event.preventDefault();

  const phone = phoneInput.value.trim();

  message.className = "message";
  message.textContent = "";

  if (!phone) {
    showMessage("لطفاً شماره تلفن خود را وارد کنید.", "error");
    phoneInput.focus();
    return;
  }

  if (!/^09\d{9}$/.test(phone)) {
    showMessage(
      "شماره تلفن باید مانند 09123456789 باشد.",
      "error"
    );
    phoneInput.focus();
    return;
  }

  currentPhone = phone;

  submitButton.disabled = true;
  submitButton.textContent = "در حال بررسی...";

  try {
    const response = await fetch("/pages/send-otp.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({ phone })
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || "خطا در دریافت کد");
    }

    showMessage(
      `کد آزمایشی شما: ${result.dev_otp}`,
      "success"
    );

    otpSection.style.display = "block";
    phoneInput.disabled = true;
    submitButton.disabled = true;
    submitButton.textContent = "کد ارسال شد";

  } catch (error) {
    showMessage(error.message, "error");

    submitButton.disabled = false;
    submitButton.textContent = "دریافت کد بازیابی";
  }
});

verifyButton.addEventListener("click", async function () {
  const otp = otpInput.value.trim();

  if (!/^\d{6}$/.test(otp)) {
    showMessage("لطفاً کد شش‌رقمی را وارد کنید.", "error");
    otpInput.focus();
    return;
  }

  verifyButton.disabled = true;
  verifyButton.textContent = "در حال بررسی...";

  try {
    const response = await fetch("/pages/verify-otp.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        phone: currentPhone,
        otp: otp
      })
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || "کد صحیح نیست");
    }

    showMessage(
      "کد با موفقیت تأیید شد.",
      "success"
    );

    otpInput.disabled = true;
    verifyButton.disabled = true;
    verifyButton.textContent = "کد تأیید شد";

    passwordSection.style.display = "block";


  } catch (error) {
    showMessage(error.message, "error");

    verifyButton.disabled = false;
    verifyButton.textContent = "تأیید کد";
  }
});
resetButton.addEventListener("click", async function () {
  const newPassword = newPasswordInput.value;

  if (newPassword.length < 6) {
    showMessage(
      "رمز عبور باید حداقل ۶ کاراکتر باشد.",
      "error"
    );
    newPasswordInput.focus();
    return;
  }

  resetButton.disabled = true;
  resetButton.textContent = "در حال تغییر رمز...";

  try {
    const response = await fetch("/pages/reset-password.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        new_password: newPassword
      })
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || "خطا در تغییر رمز");
    }

    showMessage(
      "رمز عبور با موفقیت تغییر کرد. اکنون می‌توانید وارد شوید.",
      "success"
    );

    newPasswordInput.disabled = true;
    resetButton.disabled = true;
    resetButton.textContent = "رمز تغییر کرد";

  } catch (error) {
    showMessage(error.message, "error");

    resetButton.disabled = false;
    resetButton.textContent = "تغییر رمز عبور";
  }
});

function showMessage(text, type) {
  message.textContent = text;
  message.className = `message ${type}`;
}
