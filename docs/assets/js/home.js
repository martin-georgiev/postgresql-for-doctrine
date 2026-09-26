(function () {
  function selectTab(tabs, selected) {
    tabs.forEach(function (tab) {
      var isSelected = tab === selected;
      var panel = document.getElementById(tab.getAttribute('aria-controls'));
      tab.setAttribute('aria-selected', isSelected ? 'true' : 'false');
      tab.tabIndex = isSelected ? 0 : -1;
      panel.hidden = !isSelected;
      if (isSelected) {
        panel.removeAttribute('data-just-shown');
        void panel.offsetWidth; // restarts the chevron nudge when the same panel is shown again
        panel.setAttribute('data-just-shown', '');
      }
    });
  }

  document.querySelectorAll('[role="tablist"]').forEach(function (tablist) {
    var tabs = Array.prototype.slice.call(tablist.querySelectorAll('[role="tab"]'));

    tablist.addEventListener('click', function (event) {
      var tab = event.target.closest('[role="tab"]');
      if (tab) {
        selectTab(tabs, tab);
      }
    });

    tablist.addEventListener('keydown', function (event) {
      var current = tabs.indexOf(document.activeElement);
      if (current === -1) {
        return;
      }
      var next = {
        ArrowRight: (current + 1) % tabs.length,
        ArrowLeft: (current - 1 + tabs.length) % tabs.length,
        Home: 0,
        End: tabs.length - 1
      }[event.key];
      if (next === undefined) {
        return;
      }
      event.preventDefault();
      tabs[next].focus();
      selectTab(tabs, tabs[next]);
    });
  });

  document.querySelectorAll('[data-copy-target]').forEach(function (button) {
    var source = document.getElementById(button.getAttribute('data-copy-target'));
    var status = button.parentElement.querySelector('[data-copy-status]');
    var label = button.textContent;

    button.addEventListener('click', function () {
      if (!navigator.clipboard) {
        return;
      }
      navigator.clipboard.writeText(source.textContent.trim()).then(function () {
        button.textContent = 'Copied';
        if (status) {
          status.textContent = 'Install command copied to the clipboard';
        }
        setTimeout(function () {
          button.textContent = label;
          if (status) {
            status.textContent = '';
          }
        }, 2000);
      });
    });
  });
}());
