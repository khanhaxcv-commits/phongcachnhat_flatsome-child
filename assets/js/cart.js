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
    var saving = 0;

    $('.custom-cart-item-check input:checked').each(function () {
      var $item = $(this).closest('.custom-cart-item');
      var quantity = parseInt($item.find('.custom-cart-quantity-input').val(), 10) || 1;
      var currentPrice = parseFloat($item.attr('data-current-price')) || 0;
      var regularPrice = parseFloat($item.attr('data-regular-price')) || currentPrice;

      subtotal += currentPrice * quantity;
      saving += Math.max(0, regularPrice - currentPrice) * quantity;
    });

    $('[data-cart-subtotal], [data-cart-total]').text(formatMoney(subtotal));
    $('[data-cart-saving]').text('- ' + formatMoney(saving));
    $('[data-direct-saving-row], [data-saved-line]').toggleClass('is-hidden', saving <= 0);
  }

  function syncQuantityControls() {
    $('.custom-cart-quantity').each(function () {
      var $control = $(this);
      var quantity = parseInt($control.find('.custom-cart-quantity-input').val(), 10) || 1;
      var min = parseInt($control.attr('data-min'), 10) || 1;
      var max = parseInt($control.attr('data-max'), 10) || 0;

      $control.find('.custom-cart-quantity-value').text(quantity);
      $control.find('.custom-cart-quantity-minus').prop('disabled', quantity <= min);
      $control.find('.custom-cart-quantity-plus').prop('disabled', max > 0 && quantity >= max);
    });
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
    $('.custom-cart-checkout-button, .custom-cart-buy-outline').prop('disabled', !$checked.length);
    $('.custom-cart-item').removeClass('is-unselected');
    $items.not(':checked').closest('.custom-cart-item').addClass('is-unselected');
    syncQuantityControls();
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
  $(document).on('click', '.custom-cart-coupon-toggle', function () {
    var $form = $('.custom-cart-coupon-form');
    $form.prop('hidden', !$form.prop('hidden'));
    if (!$form.prop('hidden')) $form.find('input').trigger('focus');
  });

  $(document).on('input', '.custom-cart-coupon-form input[name="coupon_code"]', function () {
    var hasCode = $.trim($(this).val()).length > 0;
    $(this).closest('.custom-cart-coupon-form').find('button[name="apply_coupon"]').prop('disabled', !hasCode);
  });

  $(document).on('click', '.custom-cart-coupon-form button[name="apply_coupon"]', function (event) {
    var value = $.trim($(this).closest('.custom-cart-coupon-form').find('input[name="coupon_code"]').val());
    if (!value) event.preventDefault();
  });

  updateSelection();
})(jQuery);
