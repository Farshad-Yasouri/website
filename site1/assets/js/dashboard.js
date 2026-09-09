const menuItems = document.querySelectorAll(".access-item");
      const contentBoxes = document.querySelectorAll(".content-box");

      menuItems.forEach(function(item) {
        item.addEventListener("click", function() {

          const targetId = item.getAttribute("data-target");

          // حذف حالت فعال از منوها
          menuItems.forEach(function(menuItem) {
            menuItem.classList.remove("active");
          });

          // فعال کردن گزینه انتخاب‌شده
          item.classList.add("active");

          // مخفی کردن همه بخش‌ها
          contentBoxes.forEach(function(box) {
            box.classList.remove("active");
          });

          // نمایش بخش مربوط به گزینه انتخاب‌شده
          document.getElementById(targetId).classList.add("active");
        });
      });