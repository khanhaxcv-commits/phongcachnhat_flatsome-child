document.addEventListener("DOMContentLoaded", function () {
  /*
    |--------------------------------------------------------------------------
    | Build URL
    |--------------------------------------------------------------------------
    */

  function updateFilterURL(filters) {
    const url = new URL(window.location.href);

    Object.keys(filters).forEach(function (key) {
      url.searchParams.delete("cs_" + key);
    });

    Object.keys(filters).forEach(function (key) {
      if (filters[key]) {
        url.searchParams.set("cs_" + key, filters[key]);
      }
    });

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

  if (desktopSelects.length) {
    desktopSelects.forEach(function (select) {
      select.addEventListener("change", function () {
        const filter = this.dataset.filter;

        const value = this.value;

        const filters = {};

        filters[filter] = value;

        updateFilterURL(filters);
      });
    });
  }

  /*
    |--------------------------------------------------------------------------
    | MOBILE DRAWER
    |--------------------------------------------------------------------------
    */

  const drawer = document.querySelector(".filter-drawer");

  const overlay = document.querySelector(".filter-drawer-overlay");
  console.log("drawer:", drawer);

  console.log("overlay:", overlay);
  const openBtn = document.querySelector(".open-filter-drawer");
  console.log("openBtn:", openBtn);

  const closeBtn = document.querySelector(".close-filter-drawer");

  function openDrawer() {
    console.log(`vao day`);
    console.log("!drawer || !overlay:", !drawer || !overlay);
    if (!drawer || !overlay) {
      return;
    }

    drawer.classList.add("active");

    overlay.style.display = "block";
  }

  function closeDrawer() {
    if (!drawer || !overlay) {
      return;
    }

    drawer.classList.remove("active");

    overlay.style.display = "none";
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

  /*
    |--------------------------------------------------------------------------
    | MOBILE APPLY
    |--------------------------------------------------------------------------
    */

  const applyBtn = document.querySelector(".apply-mobile-filter");

  if (applyBtn) {
    applyBtn.addEventListener("click", function () {
      const filters = {};

      document
        .querySelectorAll(".mobile-filter-select")
        .forEach(function (select) {
          filters[select.dataset.filter] = select.value;
        });

      closeDrawer();

      updateFilterURL(filters);
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

      /*
       * Chỉ reset UI
       *
       * Không:
       * - đổi URL
       * - reload
       * - query
       *
       */
    });
  }
});
