(function ($) {
  "use strict";

  /*
   * Kiểu icon Font Awesome 7 Pro.
   * Có thể đổi thành:
   * fa-thin
   * fa-light
   * fa-regular
   * fa-solid
   */
  var fontAwesomeStyle = "fa-light";

  /*
   * Icon cho từng menu cấp 1.
   * Tên bên trái phải tương ứng với tên menu trong WordPress.
   */
  var mobileMenuIcons = {
    "thiet bi dien dan dung": "fa-hard-drive",
    "thiet bi nha bep": "fa-utensils",
    "thiet bi nha tam": "fa-bath",
    "hang gia dung": "fa-kitchen-set",
    "thiet bi the thao": "fa-dumbbell",
    "thiet bi nghe nhin": "fa-tv",
    "thiet bi thong minh": "fa-microchip",
    "tin tuc": "fa-newspaper",
    "lien he": "fa-address-book",
    showroom: "fa-location-dot",
  };

  /**
   * Chuẩn hóa tên menu:
   * - Chuyển thành chữ thường.
   * - Loại bỏ dấu tiếng Việt.
   * - Loại bỏ khoảng trắng thừa.
   */
  function normalizeMenuText(text) {
    return String(text || "")
      .trim()
      .toLowerCase()
      .replace(/đ/g, "d")
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^\w\s]/g, " ")
      .replace(/\s+/g, " ")
      .trim();
  }

  /**
   * Lấy phần văn bản trực tiếp của link.
   * Không lấy nội dung từ icon đã được chèn vào.
   */
  function getMenuLinkText($link) {
    return $.trim($link.clone().children().remove().end().text());
  }

  /**
   * Thêm icon Font Awesome cho menu cấp 1.
   */
  function addMobileMenuIcons($nav) {
    $nav.children("li").each(function () {
      var $item = $(this);
      var $link = $item.children("a").first();

      if (!$link.length) {
        return;
      }

      var menuText = getMenuLinkText($link);
      var normalizedText = normalizeMenuText(menuText);
      var iconClass = mobileMenuIcons[normalizedText];

      var $icon = $link.children(".mobile-menu-item-icon").first();

      /*
       * Menu chưa được khai báo icon.
       */
      if (!iconClass) {
        $icon.remove();
        return;
      }

      /*
       * Chỉ tạo icon nếu chưa tồn tại.
       */
      if (!$icon.length) {
        $icon = $('<i class="mobile-menu-item-icon" aria-hidden="true"></i>');

        $link.prepend($icon);
      }

      /*
       * Gán icon Font Awesome 7 Pro.
       */
      $icon.attr(
        "class",
        ["mobile-menu-item-icon", fontAwesomeStyle, "fa-fw", iconClass].join(
          " ",
        ),
      );
    });
  }

  /**
   * Khởi tạo menu mobile Flatsome.
   */
  function initMobileSidebarMenu() {
    var $mobileMenu = $("#main-menu.mobile-sidebar");

    if (!$mobileMenu.length) {
      return;
    }

    var $nav = $mobileMenu.find(".nav-sidebar").first();

    if (!$nav.length) {
      return;
    }

    /*
     * Thêm icon cho menu cấp 1.
     */
    addMobileMenuIcons($nav);

    /*
     * Tìm tất cả li có submenu.
     */
    var $parentItems = $nav.find("li").filter(function () {
      return $(this).children("ul.sub-menu").length > 0;
    });

    $parentItems.each(function () {
      var $item = $(this);
      var $submenu = $item.children("ul.sub-menu").first();
      var $link = $item.children("a").first();

      if (!$submenu.length || !$link.length) {
        return;
      }

      $item.addClass("menu-item-has-children");

      /*
       * Nút toggle mặc định của Flatsome.
       */
      var $defaultToggle = $item.children("button.toggle").first();

      /*
       * Nút toggle custom dành cho các cấp sâu.
       */
      var $customToggle = $item
        .children("button.mobile-submenu-toggle")
        .first();

      var $toggle;

      if ($defaultToggle.length) {
        /*
         * Flatsome đã có toggle thì xóa nút custom dư.
         */
        $customToggle.remove();
        $toggle = $defaultToggle;
      } else {
        /*
         * Nếu chưa có custom toggle thì tạo mới.
         */
        if (!$customToggle.length) {
          $customToggle = $(
            '<button type="button" ' +
              'class="mobile-submenu-toggle" ' +
              'aria-label="Mở menu con" ' +
              'aria-expanded="false">' +
              '<i class="icon-angle-down" aria-hidden="true"></i>' +
              "</button>",
          );

          $customToggle.insertBefore($submenu);
        }

        $toggle = $customToggle;
      }

      /*
       * Đồng bộ trạng thái ban đầu.
       */
      var isOpen =
        $item.hasClass("is-open") ||
        $item.attr("aria-expanded") === "true" ||
        $toggle.attr("aria-expanded") === "true";

      $item.toggleClass("is-open", isOpen);

      $item.attr("aria-expanded", isOpen ? "true" : "false");

      $toggle.attr("aria-expanded", isOpen ? "true" : "false");

      /*
       * Xóa style inline để CSS quản lý display.
       */
      $submenu.removeAttr("style");
    });
  }

  /**
   * Bắt click bằng delegated event.
   * Flatsome render lại menu vẫn hoạt động.
   */
  $(document)
    .off("click.flatsomeMobileSubmenu")
    .on(
      "click.flatsomeMobileSubmenu",
      "#main-menu.mobile-sidebar button.toggle, " +
        "#main-menu.mobile-sidebar button.mobile-submenu-toggle",
      function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        var $toggle = $(this);
        var $item = $toggle.closest("li");
        var $submenu = $item.children("ul.sub-menu").first();

        if (!$submenu.length) {
          return;
        }

        var nowOpen = !$item.hasClass("is-open");

        /*
         * Chỉ thêm hoặc xóa class.
         * Không sử dụng slideToggle hoặc animate.
         */
        $item.toggleClass("is-open", nowOpen);

        $item.attr("aria-expanded", nowOpen ? "true" : "false");

        $toggle.attr("aria-expanded", nowOpen ? "true" : "false");

        /*
         * Xóa inline style của Flatsome.
         * CSS sẽ hiện hoặc ẩn submenu ngay lập tức.
         */
        $submenu.removeAttr("style");
      },
    );

  /**
   * Chạy sau khi DOM sẵn sàng.
   */
  $(function () {
    if (!window.matchMedia("(max-width: 849px)").matches) {
      return;
    }

    initMobileSidebarMenu();

    /*
     * Flatsome có thể render off-canvas sau DOM ready.
     */
    setTimeout(initMobileSidebarMenu, 300);
    setTimeout(initMobileSidebarMenu, 800);

    /*
     * Chạy lại khi mở menu mobile.
     */
    $(document)
      .off("click.flatsomeMobileMenuInit")
      .on(
        "click.flatsomeMobileMenuInit",
        ".mobile-nav .icon-menu, " +
          ".header-button a[href='#main-menu'], " +
          "a[data-open='#main-menu']",
        function () {
          setTimeout(initMobileSidebarMenu, 120);
          setTimeout(initMobileSidebarMenu, 420);
        },
      );
  });
})(jQuery);
