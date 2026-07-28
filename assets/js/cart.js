(function ($) {
  'use strict';

  var updateTimer;
  var updateRequest;

  function formatMoney(value) {
    return new Intl.NumberFormat('vi-VN', {
      maximumFractionDigits: 0
    }).format(Math.max(0, value)) + '₫';
  }

  function updateDisplayedTotals() {
    var subtotal = 0;

    $('.custom-cart-item-check input:checked').each(function () {
      var $item = $(this).closest('.custom-cart-item');
      var quantity = parseInt($item.find('.custom-cart-quantity-input').val(), 10) || 1;
      var currentPrice = parseFloat($item.attr('data-current-price')) || 0;

      subtotal += currentPrice * quantity;
    });

    $('[data-cart-subtotal], [data-cart-total]').text(formatMoney(subtotal));
  }

  function toggleMobileDetails(forceOpen) {
    var $summary = $('.custom-cart-summary');
    var isOpen = typeof forceOpen === 'boolean' ? forceOpen : !$summary.hasClass('is-mobile-open');

    $summary.toggleClass('is-mobile-open', isOpen);
    $('.custom-cart-mobile-backdrop').toggleClass('is-visible', isOpen);
    $('.custom-cart-mobile-total-toggle').attr('aria-expanded', isOpen ? 'true' : 'false');
    $('.custom-cart-mobile-total-toggle i').toggleClass('fa-chevron-up', !isOpen).toggleClass('fa-chevron-down', isOpen);
    $('body').toggleClass('custom-cart-details-open', isOpen);
  }

  function syncQuantityControls() {
    $('.custom-cart-quantity').each(function () {
      var $control = $(this);
      var $item = $control.closest('.custom-cart-item');
      var quantity = parseInt($control.find('.custom-cart-quantity-input').val(), 10) || 1;
      var min = parseInt($control.attr('data-min'), 10) || 1;
      var max = parseInt($control.attr('data-max'), 10) || 0;

      $control.find('.custom-cart-quantity-value').text(quantity);
      $control.find('.custom-cart-quantity-minus').prop('disabled', quantity <= min);
      $control.find('.custom-cart-quantity-plus').prop('disabled', max > 0 && quantity >= max);
      $item.toggleClass('is-min-quantity', quantity <= min);
    });
  }

  function syncHeaderCartCount() {
    var quantity = 0;

    $('.custom-cart-quantity-input').each(function () {
      quantity += parseInt($(this).val(), 10) || 0;
    });

    $('.header-cart-link [data-icon-label]').attr('data-icon-label', quantity);
  }

  function submitCartUpdate() {
    var $button = $('.custom-cart-update');
    if (!$button.length) return;

    window.clearTimeout(updateTimer);
    updateTimer = window.setTimeout(function () {
      var $form = $('.woocommerce-cart-form');

      if (updateRequest) updateRequest.abort();
      $form.addClass('is-updating');
      updateRequest = $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize() + '&update_cart=1'
      }).always(function () {
        $form.removeClass('is-updating');
        updateRequest = null;
      });
    }, 450);
  }

  function updateSelection() {
    var $items = $('.custom-cart-item-check input');
    var $checked = $items.filter(':checked');
    var quantity = 0;

    $checked.each(function () {
      quantity += parseInt($(this).closest('.custom-cart-item').find('.custom-cart-quantity-input').val(), 10) || 1;
    });

    $('.custom-cart-select-all').prop('checked', $items.length > 0 && $checked.length === $items.length);
    $('[data-selected-count]').text(quantity);
    $('.custom-cart-checkout-button, .custom-cart-mobile-checkout, .custom-cart-buy-outline').prop('disabled', !$checked.length);
    $('.custom-cart-item').removeClass('is-unselected');
    $items.not(':checked').closest('.custom-cart-item').addClass('is-unselected');
    syncQuantityControls();
    syncHeaderCartCount();
    updateDisplayedTotals();
  }

  $(document).on('change', '.custom-cart-select-all', function () {
    $('.custom-cart-item-check input').prop('checked', this.checked);
    updateSelection();
  });

  $(document).on('change', '.custom-cart-item-check input', updateSelection);
  $(document).on('click', '.custom-cart-quantity-button', function () {
    var $button = $(this);
    var $control = $button.closest('.custom-cart-quantity');
    var $input = $control.find('.custom-cart-quantity-input');
    var current = parseInt($input.val(), 10) || 1;
    var min = parseInt($control.attr('data-min'), 10) || 1;
    var max = parseInt($control.attr('data-max'), 10) || 0;
    var next = current + ($button.hasClass('custom-cart-quantity-plus') ? 1 : -1);

    next = Math.max(min, next);
    if (max > 0) next = Math.min(max, next);
    if (next === current) return;

    $input.val(next);
    updateSelection();
    submitCartUpdate();
  });
  $(document).on('input', '.custom-cart-coupon-form input[name="coupon_code"]', function () {
    var hasCode = $.trim($(this).val()).length > 0;
    $(this).closest('.custom-cart-coupon-form').find('button[name="apply_coupon"]').prop('disabled', !hasCode);
  });

  $(document).on('click', '.custom-cart-coupon-form button[name="apply_coupon"]', function (event) {
    var value = $.trim($(this).closest('.custom-cart-coupon-form').find('input[name="coupon_code"]').val());
    if (!value) event.preventDefault();
  });

  $(document).on('click', '.custom-cart-mobile-total-toggle', function () {
    toggleMobileDetails();
  });

  $(document).on('click', '.custom-cart-mobile-sheet-close, .custom-cart-mobile-backdrop', function () {
    toggleMobileDetails(false);
  });

  $(document).on('keydown', function (event) {
    if (event.key === 'Escape') toggleMobileDetails(false);
  });

  $(document.body).on('wc_fragments_loaded wc_fragments_refreshed added_to_cart removed_from_cart', syncHeaderCartCount);

  updateSelection();
})(jQuery);
