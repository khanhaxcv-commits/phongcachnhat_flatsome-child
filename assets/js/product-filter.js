document.addEventListener("DOMContentLoaded", function () {
  /*
  |--------------------------------------------------------------------------
  | BUILD FILTER URL
  |--------------------------------------------------------------------------
  */

  function updateFilterURL(filters, orderby = "") {
    const url = new URL(window.location.href);

    /*
     * Xóa các tham số filter hiện tại
     * trước khi gắn lại giá trị mới.
     */
    Object.keys(filters).forEach(function (key) {
      url.searchParams.delete("cs_" + key);
    });

    /*
     * Gắn lại các filter có giá trị.
     */
    Object.keys(filters).forEach(function (key) {
      if (filters[key]) {
        url.searchParams.set("cs_" + key, filters[key]);
      }
    });

    /*
     * Cập nhật sắp xếp.
     * menu_order là mặc định nên không cần giữ trên URL.
     */
    url.searchParams.delete("orderby");

    if (orderby && orderby !== "menu_order") {
      url.searchParams.set("orderby", orderby);
    }

    /*
     * Khi thay đổi bộ lọc hoặc sắp xếp,
     * đưa người dùng về trang sản phẩm đầu tiên.
     */
    url.searchParams.delete("product-page");
    url.searchParams.delete("paged");

    window.location.href = url.toString();
  }

  /*
  |--------------------------------------------------------------------------
  | DESKTOP FILTER
  |--------------------------------------------------------------------------
  */

  const desktopSelects = document.querySelectorAll(
    ".product-filter-desktop .product-filter-select",
  );

  desktopSelects.forEach(function (select) {
    select.addEventListener("change", function () {
      const filters = {};
      const filter = this.dataset.filter;

      filters[filter] = this.value;

      updateFilterURL(filters);
    });
  });

  /*
  |--------------------------------------------------------------------------
  | MOBILE DRAWER
  |--------------------------------------------------------------------------
  */

  const drawer = document.querySelector(".filter-drawer");
  const overlay = document.querySelector(".filter-drawer-overlay");
  const openBtn = document.querySelector(".open-filter-drawer");
  const closeBtn = document.querySelector(".close-filter-drawer");

  function openDrawer() {
    if (!drawer || !overlay) {
      return;
    }

    drawer.classList.add("active");
    overlay.style.display = "block";
    document.body.classList.add("filter-drawer-open");
  }

  function closeDrawer() {
    if (!drawer || !overlay) {
      return;
    }

    drawer.classList.remove("active");
    overlay.style.display = "none";
    document.body.classList.remove("filter-drawer-open");
  }

  if (openBtn) {
    openBtn.addEventListener("click", openDrawer);
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", closeDrawer);
  }

  if (overlay) {
    overlay.addEventListener("click", closeDrawer);
  }

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeDrawer();
    }
  });

  /*
  |--------------------------------------------------------------------------
  | MOBILE APPLY
  |--------------------------------------------------------------------------
  */

  const applyBtn = document.querySelector(".apply-mobile-filter");
  const mobileOrderingSelect = document.querySelector(
    ".mobile-ordering-select",
  );

  if (applyBtn) {
    applyBtn.addEventListener("click", function () {
      const filters = {};

      document
        .querySelectorAll(".mobile-filter-select")
        .forEach(function (select) {
          filters[select.dataset.filter] = select.value;
        });

      const orderby = mobileOrderingSelect
        ? mobileOrderingSelect.value
        : "menu_order";

      closeDrawer();
      updateFilterURL(filters, orderby);
    });
  }

  /*
  |--------------------------------------------------------------------------
  | MOBILE CLEAR
  |--------------------------------------------------------------------------
  */

  const clearBtn = document.querySelector(".clear-mobile-filter");

  if (clearBtn) {
    clearBtn.addEventListener("click", function () {
      document
        .querySelectorAll(".mobile-filter-select")
        .forEach(function (select) {
          select.value = "";
        });

      if (mobileOrderingSelect) {
        mobileOrderingSelect.value = "menu_order";
      }
    });
  }
});
