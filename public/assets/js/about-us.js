document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.aboutus-tabs li');
    const contents = document.querySelectorAll('.aboutus-description .content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            contents.forEach(c => c.classList.remove('active'));

            const contentId = tab.getAttribute('data-content');
            document.getElementById(contentId).classList.add('active');
        });
    });
});
