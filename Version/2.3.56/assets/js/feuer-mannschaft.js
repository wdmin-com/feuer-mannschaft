document.addEventListener('DOMContentLoaded', function () {
    var singlePage = document.querySelector('.feuer-mannschaft-single-page');
    var runtimeData = typeof feuerMannschaftData !== 'undefined' ? feuerMannschaftData : {};
    var activeModal = null;

    var hideSidebars = function (rootElement) {
        var sidebarSelector = 'aside, #secondary, .sidebar, .widget-area, .sidebar-area, .site-sidebar, .content-sidebar, .primary-sidebar, [id*="sidebar"], [class*="sidebar"]';
        var current = rootElement;
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
    };

    var getFocusableElements = function (container) {
        return Array.prototype.filter.call(
            container.querySelectorAll('a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'),
            function (element) {
                return !element.hasAttribute('hidden');
            }
        );
    };

    var closeModal = function (modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.setAttribute('hidden', 'hidden');
        document.body.classList.remove('feuer-mannschaft-modal-open');

        if (modal.__trigger && typeof modal.__trigger.focus === 'function') {
            modal.__trigger.focus();
        }

        if (activeModal === modal) {
            activeModal = null;
        }
    };

    var openModal = function (modal, trigger) {
        if (!modal) {
            return;
        }

        if (activeModal && activeModal !== modal) {
            closeModal(activeModal);
        }

        modal.__trigger = trigger || null;
        modal.removeAttribute('hidden');
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('feuer-mannschaft-modal-open');
        activeModal = modal;

        window.setTimeout(function () {
            var focusable = getFocusableElements(modal);

            if (focusable.length && typeof focusable[0].focus === 'function') {
                focusable[0].focus();
                return;
            }

            if (typeof modal.focus === 'function') {
                modal.focus();
            }
        }, 0);
    };

    if (singlePage) {
        hideSidebars(singlePage);
    }

    document.addEventListener('click', function (event) {
        var modalTrigger = event.target.closest('[data-feuer-mannschaft-modal-target]');

        if (modalTrigger) {
            var modalTarget = modalTrigger.getAttribute('data-feuer-mannschaft-modal-target') || '';
            var modalId = modalTarget.replace(/^#/, '');
            var modal = modalId ? document.getElementById(modalId) : null;

            if (modal) {
                event.preventDefault();
                openModal(modal, modalTrigger);
            }

            return;
        }

        var closeTrigger = event.target.closest('[data-feuer-mannschaft-modal-close]');

        if (closeTrigger) {
            event.preventDefault();
            closeModal(closeTrigger.closest('[data-feuer-mannschaft-modal]'));
            return;
        }

        if (event.target.matches('[data-feuer-mannschaft-modal].is-open')) {
            event.preventDefault();
            closeModal(event.target);
            return;
        }

        var profileLink = event.target.closest('.js-feuer-mannschaft-profile-link');

        if (profileLink && runtimeData.archiveStorageKey) {
            window.sessionStorage.setItem(runtimeData.archiveStorageKey, window.location.href);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (!activeModal) {
            return;
        }

        if ('Escape' === event.key) {
            event.preventDefault();
            closeModal(activeModal);
            return;
        }

        if ('Tab' !== event.key) {
            return;
        }

        var focusable = getFocusableElements(activeModal);

        if (!focusable.length) {
            event.preventDefault();
            return;
        }

        var first = focusable[0];
        var last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
            return;
        }

        if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    Array.prototype.forEach.call(document.querySelectorAll('[data-feuer-mannschaft-carousel]'), function (carousel) {
        var items = carousel.querySelectorAll('.carousel-item');
        var currentIndex = 0;
        var interval = parseInt(carousel.getAttribute('data-feuer-mannschaft-interval') || '0', 10);
        var timerId = 0;
        var isAnimating = false;
        var transitionFallbackId = 0;
        var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!items.length) {
            return;
        }

        Array.prototype.forEach.call(items, function (item, index) {
            if (item.classList.contains('active')) {
                currentIndex = index;
            }
        });

        var render = function (nextIndex) {
            currentIndex = (nextIndex + items.length) % items.length;

            Array.prototype.forEach.call(items, function (item, index) {
                var isActive = index === currentIndex;

                item.classList.remove(
                    'feuer-mannschaft-carousel-next',
                    'feuer-mannschaft-carousel-prev',
                    'feuer-mannschaft-carousel-enter-active',
                    'feuer-mannschaft-carousel-leaving',
                    'feuer-mannschaft-carousel-leave-next',
                    'feuer-mannschaft-carousel-leave-prev'
                );
                item.classList.toggle('active', isActive);
                item.hidden = !isActive;
            });
        };

        var move = function (direction) {
            var nextIndex = (currentIndex + direction + items.length) % items.length;
            var currentItem = items[currentIndex];
            var nextItem = items[nextIndex];
            var isNextDirection = direction > 0;

            if (isAnimating || nextIndex === currentIndex || !currentItem || !nextItem) {
                return;
            }

            if (reducedMotion) {
                render(nextIndex);
                return;
            }

            isAnimating = true;
            window.clearTimeout(transitionFallbackId);

            nextItem.hidden = false;
            currentItem.hidden = false;

            currentItem.classList.add('feuer-mannschaft-carousel-leaving');
            nextItem.classList.add(isNextDirection ? 'feuer-mannschaft-carousel-next' : 'feuer-mannschaft-carousel-prev');

            // Force the browser to apply the off-screen start state before animating in.
            nextItem.offsetHeight;

            currentItem.classList.add(isNextDirection ? 'feuer-mannschaft-carousel-leave-next' : 'feuer-mannschaft-carousel-leave-prev');
            nextItem.classList.add('feuer-mannschaft-carousel-enter-active');

            var finish = function () {
                window.clearTimeout(transitionFallbackId);

                currentItem.classList.remove(
                    'active',
                    'feuer-mannschaft-carousel-leaving',
                    'feuer-mannschaft-carousel-leave-next',
                    'feuer-mannschaft-carousel-leave-prev'
                );
                currentItem.hidden = true;

                nextItem.classList.remove(
                    'feuer-mannschaft-carousel-next',
                    'feuer-mannschaft-carousel-prev',
                    'feuer-mannschaft-carousel-enter-active'
                );
                nextItem.classList.add('active');
                nextItem.hidden = false;

                currentIndex = nextIndex;
                isAnimating = false;
            };

            var onTransitionEnd = function (event) {
                if (event.target !== nextItem || 'transform' !== event.propertyName) {
                    return;
                }

                nextItem.removeEventListener('transitionend', onTransitionEnd);
                finish();
            };

            nextItem.addEventListener('transitionend', onTransitionEnd);
            transitionFallbackId = window.setTimeout(function () {
                nextItem.removeEventListener('transitionend', onTransitionEnd);
                finish();
            }, 700);
        };

        var stopAutoplay = function () {
            if (timerId) {
                window.clearInterval(timerId);
                timerId = 0;
            }
        };

        var startAutoplay = function () {
            stopAutoplay();

            if (interval > 0 && items.length > 1) {
                timerId = window.setInterval(function () {
                    move(1);
                }, interval);
            }
        };

        render(currentIndex);

        Array.prototype.forEach.call(
            document.querySelectorAll('[data-feuer-mannschaft-carousel-target="' + carousel.id + '"]'),
            function (control) {
                control.addEventListener('click', function (event) {
                    event.preventDefault();
                    move('prev' === control.getAttribute('data-feuer-mannschaft-carousel-action') ? -1 : 1);
                    startAutoplay();
                });
            }
        );

        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', startAutoplay);
        carousel.addEventListener('focusin', stopAutoplay);
        carousel.addEventListener('focusout', startAutoplay);
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopAutoplay();
                return;
            }

            startAutoplay();
        });

        startAutoplay();
    });

    if (!runtimeData.archiveStorageKey) {
        return;
    }

    var storageKey = runtimeData.archiveStorageKey;
    var archiveRoot = document.querySelector('[data-feuer-mannschaft-archive-url]');

    if (archiveRoot) {
        window.sessionStorage.setItem(storageKey, archiveRoot.getAttribute('data-feuer-mannschaft-archive-url') || window.location.href);
    }

    var backLink = document.querySelector('.feuer-mannschaft-back-link');
    var storedUrl = window.sessionStorage.getItem(storageKey);

    if (backLink && storedUrl) {
        backLink.setAttribute('href', storedUrl);
    }
});
