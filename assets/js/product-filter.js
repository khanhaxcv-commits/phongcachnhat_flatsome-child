document.addEventListener("DOMContentLoaded", function () {
  /*
  |--------------------------------------------------------------------------
  | ELEMENTS
  |--------------------------------------------------------------------------
  */

  const resultsContainer = document.querySelector(
    "#product-filter-results",
  );

  const resultsContent = document.querySelector(
    "#product-filter-results .product-filter-results__content",
  );

  const loadingElement = document.querySelector(
    "#product-filter-results .product-filter-loading",
  );

  const drawer = document.querySelector(".filter-drawer");
  const overlay = document.querySelector(".filter-drawer-overlay");
  const openBtn = document.querySelector(".open-filter-drawer");
  const closeBtn = document.querySelector(".close-filter-drawer");

  const applyBtn = document.querySelector(".apply-mobile-filter");
  const clearMobileBtn = document.querySelector(".clear-mobile-filter");
  const clearDesktopBtn = document.querySelector(".clear-desktop-filter");

  const mobileOrderingSelect = document.querySelector(
    ".mobile-ordering-select",
  );

  const desktopOrderingSelect = document.querySelector(
    ".product-filter-desktop .ordering-select",
  );

  if (!resultsContainer || !resultsContent) {
    return;
  }

  const categoryId = resultsContainer.dataset.categoryId || "";

  const perPage = Math.max(
    1,
    parseInt(resultsContainer.dataset.perPage || "12", 10) || 12,
  );

  let activeRequestController = null;

  /*
  |--------------------------------------------------------------------------
  | MOBILE DRAWER
  |--------------------------------------------------------------------------
  */

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
  | LOADING
  |--------------------------------------------------------------------------
  */

  function setResultsLoading(isLoading) {
    resultsContainer.setAttribute(
      "aria-busy",
      isLoading ? "true" : "false",
    );

    resultsContainer.classList.toggle(
      "is-loading",
      isLoading,
    );

    if (!loadingElement) {
      return;
    }

    loadingElement.hidden = !isLoading;

    loadingElement.setAttribute(
      "aria-hidden",
      isLoading ? "false" : "true",
    );
  }

  function setLoadMoreLoading(button, isLoading) {
    if (!button) {
      return;
    }

    button.disabled = isLoading;

    button.classList.toggle(
      "is-loading",
      isLoading,
    );

    button.setAttribute(
      "aria-busy",
      isLoading ? "true" : "false",
    );
  }

  /*
  |--------------------------------------------------------------------------
  | FILTER STATE
  |--------------------------------------------------------------------------
  */

  function getDesktopFilters() {
    const filters = {};

    document
      .querySelectorAll(
        ".product-filter-desktop .product-filter-select",
      )
      .forEach(function (select) {
        filters[select.dataset.filter] = select.value;
      });

    return filters;
  }

  function getMobileFilters() {
    const filters = {};

    document
      .querySelectorAll(".mobile-filter-select")
      .forEach(function (select) {
        filters[select.dataset.filter] = select.value;
      });

    return filters;
  }

  function getFiltersFromURL(url) {
    const filters = {};

    url.searchParams.forEach(function (value, key) {
      if (
        !key.startsWith("cs_") &&
        !key.startsWith("filter_")
      ) {
        return;
      }

      const filterKey = key.startsWith("cs_")
        ? key.replace("cs_", "")
        : key.replace("filter_", "");

      filters[filterKey] = value;
    });

    return filters;
  }

  function getOrderbyFromURL(url) {
    return url.searchParams.get("orderby") || "menu_order";
  }

  function hasActiveFilterState(url) {
    let hasFilter = false;

    url.searchParams.forEach(function (value, key) {
      if (
        (key.startsWith("cs_") || key.startsWith("filter_")) &&
        value
      ) {
        hasFilter = true;
      }
    });

    const orderby = getOrderbyFromURL(url);

    return hasFilter || orderby !== "menu_order";
  }

  /*
  |--------------------------------------------------------------------------
  | BUILD URL
  |--------------------------------------------------------------------------
  */

  function buildFilterURL(filters, orderby) {
    const url = new URL(window.location.href);

    /*
     * Loại bỏ URL phân trang dạng /page/2/.
     */
    url.pathname = url.pathname.replace(
      /\/page\/\d+\/?$/,
      "/",
    );

    /*
     * Xóa toàn bộ filter cũ.
     */
    Array.from(url.searchParams.keys()).forEach(function (key) {
      if (
        key.startsWith("cs_") ||
        key.startsWith("filter_") ||
        key.startsWith("query_type_")
      ) {
        url.searchParams.delete(key);
      }
    });

    /*
     * Gắn filter mới.
     */
    Object.keys(filters).forEach(function (key) {
      const value = filters[key];

      if (value) {
        url.searchParams.set(
          "cs_" + key,
          value,
        );
      }
    });

    /*
     * Xóa sắp xếp cũ.
     */
    url.searchParams.delete("orderby");

    /*
     * Chỉ giữ orderby nếu khác mặc định.
     */
    if (
      orderby &&
      orderby !== "menu_order"
    ) {
      url.searchParams.set(
        "orderby",
        orderby,
      );
    }

    /*
     * Không dùng phân trang trên URL.
     */
    url.searchParams.delete("paged");
    url.searchParams.delete("product-page");

    return url;
  }

  /*
  |--------------------------------------------------------------------------
  | SYNC FILTER UI
  |--------------------------------------------------------------------------
  */

  function setFilterSelectValue(select, value) {
    const requestedValue = value || "";

    select.value = requestedValue;

    if (select.value || !requestedValue) {
      return;
    }

    const matchingOption = Array.from(select.options).find(function (option) {
      return option.dataset.termSlug === requestedValue;
    });

    if (matchingOption) {
      select.value = matchingOption.value;
    }
  }

  function syncFilterUI(url) {
    const filters = getFiltersFromURL(url);
    const orderby = getOrderbyFromURL(url);

    document
      .querySelectorAll(
        ".product-filter-desktop .product-filter-select",
      )
      .forEach(function (select) {
        const filterKey = select.dataset.filter;

        setFilterSelectValue(
          select,
          filters[filterKey] || "",
        );
      });

    document
      .querySelectorAll(".mobile-filter-select")
      .forEach(function (select) {
        const filterKey = select.dataset.filter;

        setFilterSelectValue(
          select,
          filters[filterKey] || "",
        );
      });

    if (mobileOrderingSelect) {
      mobileOrderingSelect.value = orderby;
    }

    if (desktopOrderingSelect) {
      Array.from(
        desktopOrderingSelect.options,
      ).forEach(function (option) {
        const optionURL = new URL(
          option.value,
          window.location.origin,
        );

        const optionOrderby =
          optionURL.searchParams.get("orderby") ||
          "menu_order";

        option.selected = optionOrderby === orderby;
      });
    }

    /*
     * Hiện hoặc ẩn nút xóa desktop.
     */
    if (clearDesktopBtn) {
      clearDesktopBtn.classList.toggle(
        "hidden",
        !hasActiveFilterState(url),
      );
    }
  }

  function updateDynamicFilterOptions(filterData, targetURL) {
    if (!Array.isArray(filterData)) {
      return;
    }

    const filters = {};
    const selectedFilters = getFiltersFromURL(targetURL);

    filterData.forEach(function (filter) {
      if (filter && filter.key) {
        filters[filter.key] = filter;
      }
    });

    function updateSelect(select, filter, isMobile) {
      while (select.options.length) {
        select.remove(0);
      }

      const placeholder = document.createElement("option");
      placeholder.value = "";
      placeholder.textContent = isMobile
        ? "Chọn " + String(filter.label || "").toLowerCase()
        : filter.label || "";
      select.appendChild(placeholder);

      (Array.isArray(filter.terms) ? filter.terms : []).forEach(function (term) {
        const option = document.createElement("option");
        option.value = String(term.id);
        option.textContent = term.name || "";
        option.dataset.termSlug = term.slug || "";
        select.appendChild(option);
      });

      setFilterSelectValue(
        select,
        selectedFilters[filter.key] || "",
      );
    }

    document
      .querySelectorAll(".product-filter-item[data-filter-key]")
      .forEach(function (item) {
        const key = item.dataset.filterKey;
        const filter = filters[key];
        const hasOptions = !!filter && Array.isArray(filter.terms)
          && filter.terms.length > 0;
        const select = item.querySelector(".product-filter-select");

        item.hidden = !hasOptions;
        item.classList.toggle("hidden", !hasOptions);

        if (filter && select) {
          updateSelect(select, filter, false);
        }
      });

    document
      .querySelectorAll(".drawer-item[data-filter-key]")
      .forEach(function (item) {
        const key = item.dataset.filterKey;
        const filter = filters[key];
        const hasOptions = !!filter && Array.isArray(filter.terms)
          && filter.terms.length > 0;
        const select = item.querySelector(".mobile-filter-select");

        item.hidden = !hasOptions;
        item.classList.toggle("hidden", !hasOptions);

        if (filter && select) {
          updateSelect(select, filter, true);
        }
      });
  }

  /*
  |--------------------------------------------------------------------------
  | THEME EVENTS
  |--------------------------------------------------------------------------
  */

  function triggerProductLoopUpdated() {
    if (
      window.jQuery &&
      document.querySelector(".woocommerce-cart")
    ) {
      window
        .jQuery(document.body)
        .trigger("updated_wc_div");
    }

    window.dispatchEvent(new Event("resize"));
    window.dispatchEvent(new Event("scroll"));
  }

  /*
  |--------------------------------------------------------------------------
  | AJAX REQUEST
  |--------------------------------------------------------------------------
  */

  async function requestProducts(options) {
    const settings = Object.assign(
      {
        targetURL: new URL(window.location.href),
        page: 1,
        requestMode: "replace",
        updateHistory: true,
        loadMoreButton: null,
      },
      options,
    );

    if (
      typeof productFilterAjax === "undefined" ||
      !productFilterAjax.ajaxUrl ||
      !productFilterAjax.nonce
    ) {
      window.location.href =
        settings.targetURL.toString();

      return;
    }

    if (
      settings.requestMode === "replace" &&
      activeRequestController
    ) {
      activeRequestController.abort();
    }

    const requestController = new AbortController();

    activeRequestController = requestController;

    const filters = getFiltersFromURL(
      settings.targetURL,
    );

    const orderby = getOrderbyFromURL(
      settings.targetURL,
    );

    const formData = new FormData();

    formData.append(
      "action",
      "load_filtered_products",
    );

    formData.append(
      "nonce",
      productFilterAjax.nonce,
    );

    formData.append(
      "category_id",
      categoryId,
    );

    formData.append(
      "orderby",
      orderby,
    );

    formData.append(
      "paged",
      String(settings.page),
    );

    formData.append(
      "per_page",
      String(perPage),
    );

    formData.append(
      "request_mode",
      settings.requestMode,
    );

    Object.keys(filters).forEach(function (key) {
      formData.append(
        "filters[" + key + "]",
        filters[key],
      );
    });

    if (settings.requestMode === "append") {
      setLoadMoreLoading(
        settings.loadMoreButton,
        true,
      );
    } else {
      setResultsLoading(true);
    }

    try {
      const response = await fetch(
        productFilterAjax.ajaxUrl,
        {
          method: "POST",
          body: formData,
          credentials: "same-origin",
          signal: requestController.signal,
        },
      );

      if (!response.ok) {
        throw new Error(
          "AJAX request failed: " +
            response.status,
        );
      }

      const result = await response.json();

      if (
        !result.success ||
        !result.data ||
        typeof result.data.html !== "string"
      ) {
        throw new Error(
          "Invalid AJAX response",
        );
      }

      if (settings.requestMode === "append") {
        appendProducts(
          result.data,
          settings.loadMoreButton,
        );
      } else {
        replaceProducts(
          result.data,
          settings.targetURL,
          settings.updateHistory,
        );
      }

      triggerProductLoopUpdated();
    } catch (error) {
      if (error.name === "AbortError") {
        return;
      }

      console.error(
        "Product filter AJAX error:",
        error,
      );

      if (settings.requestMode === "replace") {
        window.location.href =
          settings.targetURL.toString();
      }
    } finally {
      const isCurrentRequest =
        activeRequestController === requestController;

      if (settings.requestMode === "append") {
        setLoadMoreLoading(
          settings.loadMoreButton,
          false,
        );
      } else if (isCurrentRequest) {
        setResultsLoading(false);
      }

      if (isCurrentRequest) {
        activeRequestController = null;
      }
    }
  }

  /*
  |--------------------------------------------------------------------------
  | REPLACE PRODUCTS
  |--------------------------------------------------------------------------
  */

  function replaceProducts(
    data,
    targetURL,
    updateHistory,
  ) {
    resultsContent.innerHTML = data.html;

    updateDynamicFilterOptions(
      data.filters,
      targetURL,
    );

    if (updateHistory) {
      window.history.pushState(
        {
          productFilter: true,
        },
        "",
        targetURL.toString(),
      );
    }

    syncFilterUI(targetURL);
  }

  /*
  |--------------------------------------------------------------------------
  | APPEND PRODUCTS
  |--------------------------------------------------------------------------
  */

  function appendProducts(data, loadMoreButton) {
    /*
     * Flatsome sử dụng .products dạng div,
     * không nhất thiết là ul.products.
     */
    const productGrid =
      resultsContent.querySelector(".products");

    if (!productGrid) {
      console.error(
        "Không tìm thấy product grid .products",
      );

      return;
    }

    if (data.html) {
      productGrid.insertAdjacentHTML(
        "beforeend",
        data.html,
      );
    }

    const loadMoreWrap = loadMoreButton
      ? loadMoreButton.closest(
          ".product-load-more-wrap",
        )
      : null;

    if (
      !data.hasMore ||
      Number(data.remaining) <= 0
    ) {
      if (loadMoreWrap) {
        loadMoreWrap.remove();
      }

      return;
    }

    if (!loadMoreButton) {
      return;
    }

    loadMoreButton.dataset.currentPage =
      String(data.currentPage);

    loadMoreButton.dataset.maxPages =
      String(data.maxPages);

    loadMoreButton.dataset.remaining =
      String(data.remaining);

    const countElement =
      loadMoreButton.querySelector(
        ".product-load-more__count",
      );

    if (countElement) {
      countElement.textContent =
        String(data.remaining);
    }
  }

  /*
  |--------------------------------------------------------------------------
  | DESKTOP FILTER
  |--------------------------------------------------------------------------
  */

  document
    .querySelectorAll(
      ".product-filter-desktop .product-filter-select",
    )
    .forEach(function (select) {
      select.addEventListener(
        "change",
        function () {
          const filters = getDesktopFilters();

          const currentURL = new URL(
            window.location.href,
          );

          const orderby =
            getOrderbyFromURL(currentURL);

          const targetURL = buildFilterURL(
            filters,
            orderby,
          );

          requestProducts({
            targetURL: targetURL,
            page: 1,
            requestMode: "replace",
            updateHistory: true,
          });
        },
      );
    });

  /*
  |--------------------------------------------------------------------------
  | DESKTOP SORTING
  |--------------------------------------------------------------------------
  */

  if (desktopOrderingSelect) {
    desktopOrderingSelect.removeAttribute(
      "onchange",
    );

    desktopOrderingSelect.addEventListener(
      "change",
      function () {
        const selectedURL = new URL(
          this.value,
          window.location.origin,
        );

        const filters = getDesktopFilters();

        const orderby =
          getOrderbyFromURL(selectedURL);

        const targetURL = buildFilterURL(
          filters,
          orderby,
        );

        requestProducts({
          targetURL: targetURL,
          page: 1,
          requestMode: "replace",
          updateHistory: true,
        });
      },
    );
  }

  /*
  |--------------------------------------------------------------------------
  | DESKTOP CLEAR
  |--------------------------------------------------------------------------
  */

  if (clearDesktopBtn) {
    clearDesktopBtn.addEventListener(
      "click",
      function () {
        const targetURL = buildFilterURL(
          {},
          "menu_order",
        );

        /*
         * Reset UI ngay khi bấm.
         */
        document
          .querySelectorAll(
            ".product-filter-select",
          )
          .forEach(function (select) {
            select.value = "";
          });

        document
          .querySelectorAll(
            ".mobile-filter-select",
          )
          .forEach(function (select) {
            select.value = "";
          });

        if (mobileOrderingSelect) {
          mobileOrderingSelect.value =
            "menu_order";
        }

        requestProducts({
          targetURL: targetURL,
          page: 1,
          requestMode: "replace",
          updateHistory: true,
        });
      },
    );
  }

  /*
  |--------------------------------------------------------------------------
  | MOBILE APPLY
  |--------------------------------------------------------------------------
  */

  if (applyBtn) {
    applyBtn.addEventListener(
      "click",
      function () {
        const filters = getMobileFilters();

        const orderby = mobileOrderingSelect
          ? mobileOrderingSelect.value
          : "menu_order";

        const targetURL = buildFilterURL(
          filters,
          orderby,
        );

        closeDrawer();

        requestProducts({
          targetURL: targetURL,
          page: 1,
          requestMode: "replace",
          updateHistory: true,
        });
      },
    );
  }

  /*
  |--------------------------------------------------------------------------
  | MOBILE CLEAR
  |--------------------------------------------------------------------------
  */

  if (clearMobileBtn) {
    clearMobileBtn.addEventListener(
      "click",
      function () {
        document
          .querySelectorAll(
            ".mobile-filter-select",
          )
          .forEach(function (select) {
            select.value = "";
          });

        if (mobileOrderingSelect) {
          mobileOrderingSelect.value =
            "menu_order";
        }
      },
    );
  }

  /*
  |--------------------------------------------------------------------------
  | LOAD MORE
  |--------------------------------------------------------------------------
  */

  resultsContent.addEventListener(
    "click",
    function (event) {
      const loadMoreButton =
        event.target.closest(
          ".product-load-more",
        );

      if (!loadMoreButton) {
        return;
      }

      event.preventDefault();

      if (
        loadMoreButton.disabled ||
        loadMoreButton.classList.contains(
          "is-loading",
        )
      ) {
        return;
      }

      const currentPage = Math.max(
        1,
        parseInt(
          loadMoreButton.dataset.currentPage ||
            "1",
          10,
        ) || 1,
      );

      const maxPages = Math.max(
        1,
        parseInt(
          loadMoreButton.dataset.maxPages ||
            "1",
          10,
        ) || 1,
      );

      const nextPage = currentPage + 1;

      if (nextPage > maxPages) {
        const wrap = loadMoreButton.closest(
          ".product-load-more-wrap",
        );

        if (wrap) {
          wrap.remove();
        }

        return;
      }

      requestProducts({
        targetURL: new URL(
          window.location.href,
        ),
        page: nextPage,
        requestMode: "append",
        updateHistory: false,
        loadMoreButton: loadMoreButton,
      });
    },
  );

  /*
  |--------------------------------------------------------------------------
  | BACK / FORWARD
  |--------------------------------------------------------------------------
  */

  window.addEventListener(
    "popstate",
    function () {
      const currentURL = new URL(
        window.location.href,
      );

      syncFilterUI(currentURL);

      requestProducts({
        targetURL: currentURL,
        page: 1,
        requestMode: "replace",
        updateHistory: false,
      });
    },
  );

  /*
  |--------------------------------------------------------------------------
  | INITIAL UI SYNC
  |--------------------------------------------------------------------------
  */

  syncFilterUI(
    new URL(window.location.href),
  );
});
