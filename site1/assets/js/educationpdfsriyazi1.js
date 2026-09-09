const sections = document.querySelectorAll('.mathbooklet details');

sections.forEach(section => {
    section.addEventListener('toggle', () => {
        if (section.open) {
            sections.forEach(otherSection => {
                if (otherSection !== section) {
                    otherSection.removeAttribute('open');
                }
            });
        }
    });
});