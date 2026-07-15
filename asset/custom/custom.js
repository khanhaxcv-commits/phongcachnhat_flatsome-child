jQuery(document).ready(function ($) {
    function addProductTabIcons() {
        const icons = {
            'Thiết bị dân dụng': 'fa-house',
            'Thiết bị nhà bếp': 'fa-kitchen-set',
            'Thiết bị nhà tắm': 'fa-bath',
            'Khuyến mãi': 'fa-tag'
        };

        $('.row-1782200182414 .tabbed-content .nav li a').each(function () {
            const $a = $(this);
            const $span = $a.find('span');
            const text = $.trim($span.text());

            if (icons[text] && !$a.find('i').length) {
                $a.prepend('<i class="fa-solid ' + icons[text] + '"></i>');
            }
        });
    }

    addProductTabIcons();

    setTimeout(addProductTabIcons, 500);
	
	 // -------------------------------- xử lý ẩn tabs hiện videos sản phẩm, khi tabs ko có video.

    function hideEmptyProductVideos() {
        $('.product .desc-products .row-left .az-video-slider-container').each(function () {
            const $videoContainer = $(this);
            const hasVideo = $videoContainer.find('.az-video-thumb, .az-video-wrapper, a[href*="youtube.com"], a[href*="youtu.be"], iframe, video').length > 0;

            if (hasVideo) {
                return;
            }

            const $videoBlock = $videoContainer.closest('.col');
            ($videoBlock.length ? $videoBlock : $videoContainer).hide();
        });
    }

    hideEmptyProductVideos();

    setTimeout(hideEmptyProductVideos, 500);
	
});