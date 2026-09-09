const main = document.getElementById("main");
    const centerCircle = document.getElementById("centerCircle");
    const cards = [...document.querySelectorAll(".starcube")];

    let opened = false;

    centerCircle.addEventListener("click", () => {
      opened = !opened;
      cards.forEach(c => c.classList.remove("active"));

      if (opened) {
        main.classList.add("open");
        arrangeCards();
      } else {
        main.classList.remove("open");
        cards.forEach(c => {
          c.style.left = "50%";
          c.style.top = "50%";
        });
      }
    });

    function arrangeCards() {
      const centerX = 50;
      const centerY = 50;
      const radius = 35; // کمتر شد تا داخل صفحه بماند
      const angleStep = (2 * Math.PI) / cards.length;

      cards.forEach((card, index) => {
        const angle = index * angleStep - Math.PI / 2;
        const x = centerX + Math.cos(angle) * radius;
        const y = centerY + Math.sin(angle) * radius;

        card.style.left = x + "%";
        card.style.top = y + "%";
      });
    }

    cards.forEach(card => {
      card.addEventListener("click", (e) => {
        e.stopPropagation();
        if (!opened) return;

        cards.forEach(c => c.classList.remove("active"));
        card.classList.add("active");
      });
    });
