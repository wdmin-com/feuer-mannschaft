document.addEventListener('DOMContentLoaded', function () {
    var singlePage = document.querySelector('.feuer-mannschaft-single-page');

    if (singlePage) {
        var sidebarSelector = 'aside, #secondary, .sidebar, .widget-area, .sidebar-area, .site-sidebar, .content-sidebar, .primary-sidebar, [id*="sidebar"], [class*="sidebar"]';
        var current = singlePage;
        var steps = 0;

        while (current && current.parentElement && steps < 8) {
            Array.prototype.forEach.call(current.parentElement.children, function (sibling) {
                if (sibling === current || !sibling.matches(sidebarSelector)) {
                    return;
                }

                sibling.style.display = 'none';
                sibling.setAttribute('hidden', 'hidden');
            });

            current = current.parentElement;
            steps += 1;
        }
    }

    if (typeof feuerMannschaftData === 'undefined' || !feuerMannschaftData.archiveStorageKey) {
        return;
    }

    var storageKey = feuerMannschaftData.archiveStorageKey;
    var archiveRoot = document.querySelector('[data-feuer-mannschaft-archive-url]');

    if (archiveRoot) {
        window.sessionStorage.setItem(storageKey, archiveRoot.getAttribute('data-feuer-mannschaft-archive-url') || window.location.href);
    }

    document.addEventListener('click', function (event) {
        var profileLink = event.target.closest('.js-feuer-mannschaft-profile-link');

        if (profileLink) {
            window.sessionStorage.setItem(storageKey, window.location.href);
        }
    });

    var backLink = document.querySelector('.feuer-mannschaft-back-link');
    var storedUrl = window.sessionStorage.getItem(storageKey);

    if (backLink && storedUrl) {
        backLink.setAttribute('href', storedUrl);
    }
});
