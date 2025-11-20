// public/js/rooms-filter.js
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('.availability-form') || document.getElementById('nonAjaxControls');
  const roomsContainer = document.getElementById('roomsGrid');
  const resultsWrapperSelector = '.rooms-section'; // root wrapper to update
  const root = document.querySelector(resultsWrapperSelector);
  if (!root) return;

  // helper to fetch and update grid
  async function fetchAndRender(queryParams) {
    // always add ajax=1 so server returns only grid + pagination fragment
    queryParams.append('ajax', '1');

    const url = 'rooms.php?' + queryParams.toString();
    const res = await fetch(url, { method: 'GET' });
    const text = await res.text();

    // server returns grid + pagination fragment; replace the grid area
    // We will replace root's innerHTML from first <div class="rooms-grid"...> to end of returned HTML
    // Simpler: insert response into a temp div and extract #roomsGrid and #ajaxPagination
    const temp = document.createElement('div');
    temp.innerHTML = text;

    const newGrid = temp.querySelector('#roomsGrid');
    const newPagination = temp.querySelector('#ajaxPagination');

    if (newGrid) {
      const oldGrid = document.getElementById('roomsGrid');
      if (oldGrid) oldGrid.replaceWith(newGrid);
      else root.insertAdjacentElement('beforeend', newGrid);
    }

    // replace or insert ajaxPagination (under grid)
    const oldAjaxPag = document.getElementById('ajaxPagination');
    if (newPagination) {
      if (oldAjaxPag) oldAjaxPag.replaceWith(newPagination);
      else {
        // append after grid
        const grid = document.getElementById('roomsGrid');
        if (grid) grid.insertAdjacentElement('afterend', newPagination);
      }
    }

    // re-bind pagination click handlers
    bindAjaxPagination();
  }

  // bind pagination links returned via AJAX
  function bindAjaxPagination() {
    const pagLinks = document.querySelectorAll('.ajax-page');
    pagLinks.forEach(a => {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        const page = a.dataset.page;
        // build params from current URL + page
        const qs = new URLSearchParams(window.location.search);
        qs.set('page', page);
        // keep per_page if present
        fetchAndRender(qs);
        // update the browser URL (optional)
        history.replaceState(null, '', 'rooms.php?' + qs.toString());
      });
    });
  }

  // if there's an availability form (could be on index.php, but we only bind here if exists)
  if (form && form.classList.contains('availability-form')) {
    form.addEventListener('submit', function (e) {
      // allow standard submit when JS disabled, but with JS perform AJAX
      e.preventDefault();
      const data = new FormData(form);
      const qs = new URLSearchParams(data);
      // reset to page 1
      qs.set('page', 1);
      // update URL in browser (optional)
      history.replaceState(null, '', 'rooms.php?' + qs.toString());
      fetchAndRender(qs);
    });
  }

  // If on rooms.php and there are controls (sort/per_page) form
  const nonAjaxControls = document.getElementById('nonAjaxControls');
  if (nonAjaxControls) {
    nonAjaxControls.addEventListener('change', function (e) {
      // when user changes sort/per_page, fetch via AJAX
      const data = new FormData(nonAjaxControls);
      const qs = new URLSearchParams(data);
      qs.set('page', 1);
      history.replaceState(null, '', 'rooms.php?' + qs.toString());
      fetchAndRender(qs);
    });
  }

  // initially bind pagination if exists
  bindAjaxPagination();
});
