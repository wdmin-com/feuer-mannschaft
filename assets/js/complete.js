(function () {
    'use strict';
    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (character) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character];
        });
    }
    function initQr(root) {
        if (typeof window.qrcode !== 'function') return;
        var value = root.dataset.qrValue || '';
        var size = Math.max(96, Math.min(640, parseInt(root.dataset.qrSize || '220', 10)));
        var qr = window.qrcode(0, 'M');
        qr.addData(value, 'Byte'); qr.make();
        var output = root.querySelector('[data-qr-output]');
        output.innerHTML = qr.createSvgTag({cellSize: Math.max(2, Math.floor(size / qr.getModuleCount())), margin: 4, scalable: true});
        output.querySelector('svg').setAttribute('aria-label', 'QR-Code');
        var button = root.querySelector('[data-qr-download]');
        if (button) button.addEventListener('click', function () {
            var data = qr.createDataURL(Math.max(3, Math.floor(size / qr.getModuleCount())), 16);
            var link = document.createElement('a'); link.href = data; link.download = 'profil-qr.png'; link.click();
        });
    }
    function card(item) {
        var title = esc(item.name), caption = [item.rank, item.function].filter(Boolean).map(esc).join(' · ');
        var image = item.image ? '<img class="card-img-top" src="' + esc(item.image) + '" alt="" loading="lazy" style="object-position:' + esc(item.imagePosition) + ';aspect-ratio:' + esc(item.ratio) + '">' : '<div class="feuer-directory-placeholder" aria-hidden="true"></div>';
        var content = image + '<div class="card-img-overlay d-flex flex-column justify-content-end"><h3 class="h5 card-title">' + title + '</h3>' + (caption ? '<p class="card-text">' + caption + '</p>' : '') + (item.status === 'archived' ? '<span class="badge text-bg-secondary align-self-start">Archiv</span>' : '') + '</div>';
        return '<article class="col-12 col-sm-6 col-lg-4">' + (item.url ? '<a class="card h-100 feuer-directory-card" href="' + esc(item.url) + '">' + content + '</a>' : '<div class="card h-100 feuer-directory-card">' + content + '</div>') + '</article>';
    }
    function initDirectory(root) {
        var form = root.querySelector('[data-directory-filters]'), results = root.querySelector('[data-directory-results]'), status = root.querySelector('[data-directory-status]'), pagination = root.querySelector('[data-directory-pagination]');
        var controller, timer;
        function load(page) {
            if (controller) controller.abort(); controller = new AbortController();
            var params = new URLSearchParams(new FormData(form)); params.set('page', page || 1); params.set('per_page', root.dataset.perPage || 24);
            status.textContent = feuerMannschaftComplete.labels.loading; root.classList.add('is-loading');
            fetch(feuerMannschaftComplete.restUrl + '?' + params.toString(), {signal: controller.signal, headers: {'X-WP-Nonce': feuerMannschaftComplete.nonce}}).then(function (response) { if (!response.ok) throw new Error(); return response.json(); }).then(function (data) {
                results.innerHTML = data.items.map(card).join(''); status.textContent = data.total ? data.total + ' ' + feuerMannschaftComplete.labels.profiles + (data.truncated ? ' · Ergebnis auf 5.000 Profile begrenzt.' : '') : feuerMannschaftComplete.labels.empty; pagination.innerHTML = '';
                for (var i=1;i<=data.pages;i++) pagination.insertAdjacentHTML('beforeend','<button type="button" class="btn btn-sm ' + (i===data.page?'btn-primary':'btn-outline-secondary') + '" data-page="'+i+'" aria-current="'+(i===data.page?'page':'false')+'">'+i+'</button>');
                var url = new URL(location.href); Array.from(params.entries()).forEach(function(pair){if(pair[1] && pair[0] !== 'page' && pair[0] !== 'per_page') url.searchParams.set(pair[0],pair[1]); else url.searchParams.delete(pair[0]);}); history.replaceState(null,'',url);
            }).catch(function (error) { if (error.name !== 'AbortError') status.textContent = feuerMannschaftComplete.labels.error; }).finally(function(){root.classList.remove('is-loading');});
        }
        var current = new URL(location.href); Array.from(form.elements).forEach(function(field){if(field.name && current.searchParams.has(field.name))field.value=current.searchParams.get(field.name);}); if (!current.searchParams.has('status') && form.elements.status) form.elements.status.value = root.dataset.defaultStatus || 'active';
        form.addEventListener('submit',function(e){e.preventDefault();load(1);}); form.addEventListener('input',function(e){if(e.target.type==='search'){clearTimeout(timer);timer=setTimeout(function(){load(1);},300);}}); form.addEventListener('change',function(){load(1);}); form.addEventListener('reset',function(){setTimeout(function(){load(1);},0);}); pagination.addEventListener('click',function(e){var button=e.target.closest('[data-page]');if(button)load(parseInt(button.dataset.page,10));}); load(parseInt(current.searchParams.get('page')||'1',10));
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-local-qr]').forEach(initQr);
        document.querySelectorAll('[data-feuer-directory]').forEach(initDirectory);
    });
}());
