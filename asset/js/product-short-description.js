(function () {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProductShortDescription);
    } else {
        initProductShortDescription();
    }

    function initProductShortDescription() {
        document.querySelectorAll('.product-short-description').forEach(function (wrapper) {
            addMainTitle(wrapper);
            decorateListItems(wrapper);
        });
    }

    function addMainTitle(wrapper) {
        if (wrapper.querySelector('.psd-main-title')) return;

        var title = document.createElement('div');
        title.className = 'psd-main-title';
        title.innerHTML = '<i class="fa-solid fa-gift" aria-hidden="true"></i><span>Khuyến Mãi và Bảo Hành</span>';

        wrapper.insertBefore(title, wrapper.firstElementChild);
    }

    function decorateListItems(wrapper) {
        wrapper.querySelectorAll('li').forEach(function (item) {
            if (item.dataset.psdDecorated === '1') return;

            var strong = item.querySelector(':scope > strong');

            if (strong) {
                item.classList.add('psd-section-title');
                prependIcon(item, getHeadingIcon(strong.textContent));
            } else {
                prependIcon(item, 'fa-solid fa-check');
            }

            item.dataset.psdDecorated = '1';
        });
    }

    function prependIcon(item, iconClass) {
        if (!iconClass || item.querySelector(':scope > i')) return;

        var icon = document.createElement('i');
        icon.className = iconClass;
        icon.setAttribute('aria-hidden', 'true');

        item.insertBefore(icon, item.firstChild);
    }

    function getHeadingIcon(text) {
        var value = normalizeText(text);

        if (value.includes('khuyen') || value.includes('khuy')) {
            return 'fa-solid fa-tag';
        }

        if (value.includes('bao hanh') || value.includes('bao')) {
            return 'fa-solid fa-shield-halved';
        }

        return 'fa-solid fa-circle-info';
    }

    function normalizeText(text) {
        return String(text || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    }
})();
