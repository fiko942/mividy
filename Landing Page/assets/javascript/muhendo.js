$(document).ready(() => {
    AOS.init({
        once: true
    });

    const previews = document.querySelectorAll('.preview-image img');
    previews.forEach(preview => {
        preview.addEventListener('click', (event) => {
            showPreview(preview.getAttribute('src'), preview.getAttribute('title'), preview.dataset.description);
        });
    });

    const modalPreview = $('#modalPreview');
    const navbar = $(".navbar");

    function showPreview(imagePath, title, description) {
        modalPreview.modal('show');
        $(".modal-header #modalPreviewLabel").text(title);
        $(".modal-body .img img").attr('src', imagePath);
        $(".modal-body .img img").attr('alt', title);
        $(".modal-body .img img").attr('title', title);
        $(".modal-body .title").text(title);
        $(".modal-body .description").text(description);
    }

    modalPreview.on('shown.bs.modal', () => {
        navbar.attr('style', 'top: -60px;');
        $(".fixed-bottom-notice").attr('style', 'bottom: -40px;');
    });
    modalPreview.on('hidden.bs.modal', () => {
        navbar.attr('style', 'top: 10;');
        $(".fixed-bottom-notice").attr('style', 'bottom: 0;');
    });
});