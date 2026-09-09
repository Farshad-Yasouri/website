const chapters= document.querySelectorAll(".Chapter");

   chapters.forEach(chapter => {
        chapter.addEventListener("toggle", () => {
            if (chapter.open) {
               chapters.forEach(otherChapter => {
                    if (otherChapter !== chapter) {
                        otherChapter.removeAttribute("open");
                    }
                });
            }
        });
    });

    // فقط یک درس در هر فصل هم‌زمان باز باشد
    document.querySelectorAll(".Chapter").forEach(elem => {
        const lessons = elem.querySelectorAll(":scope > .lesson");

        lessons.forEach(el => {
            el.addEventListener("toggle", () => {
                if (el.open) {
                    lessons.forEach(otherLesson => {
                        if (otherLesson !== el) {
                            otherLesson.removeAttribute("open");
                        }
                    });
                }
            });
        });
    });