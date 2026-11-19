(function () {
    'use strict';
    document.querySelectorAll('[data-fm-directory]').forEach(function (root) {
        var search = root.querySelector('[data-fm-search]');
        var filters = Array.prototype.slice.call(root.querySelectorAll('[data-fm-filter]'));
        var sort = root.querySelector('[data-fm-sort]');
        var items = Array.prototype.slice.call(root.querySelectorAll('.fm-directory__item'));
        var results = root.querySelector('[data-fm-results]');
        var count = root.querySelector('[data-fm-count]');
        var empty = root.querySelector('[data-fm-empty]');
        var params = new URLSearchParams(window.location.search);
        if (search) search.value = params.get('person') || '';
        filters.forEach(function (field) { field.value = params.get(field.dataset.fmFilter) || ''; });
        if (sort) sort.value = params.get('sort') || 'name';
        function update() {
            var query = search ? search.value.trim().toLowerCase() : '';
            var visible = items.filter(function (item) {
                var matches = !query || item.dataset.search.indexOf(query) !== -1;
                filters.forEach(function (field) { if (field.value && !(item.dataset[field.dataset.fmFilter] || '').split(' ').includes(field.value)) matches = false; });
                item.hidden = !matches; return matches;
            });
            visible.sort(function (a, b) { var key = sort && sort.value === 'rank' ? 'rank' : 'name'; var direction = sort && sort.value === 'name-desc' ? -1 : 1; return direction * a.dataset[key].localeCompare(b.dataset[key], 'de'); });
            visible.forEach(function (item) { results.appendChild(item); });
            count.textContent = visible.length + ' Personen'; empty.hidden = visible.length > 0;
            var next = new URLSearchParams(window.location.search); if (query) next.set('person', query); else next.delete('person');
            filters.forEach(function (field) { if (field.value) next.set(field.dataset.fmFilter, field.value); else next.delete(field.dataset.fmFilter); });
            if (sort && sort.value !== 'name') next.set('sort', sort.value); else next.delete('sort');
            history.replaceState(null, '', window.location.pathname + (next.toString() ? '?' + next.toString() : '') + window.location.hash);
        }
        root.addEventListener('input', update); root.addEventListener('change', update); root.addEventListener('reset', function () { setTimeout(update, 0); }); update();
    });
}());
