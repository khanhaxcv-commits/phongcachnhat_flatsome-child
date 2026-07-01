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
});