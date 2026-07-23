(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    window.initLiveNotifications = function (options) {
        const root = document.getElementById(options.rootId);
        const toggle = document.getElementById(options.toggleId);
        const panel = document.getElementById(options.panelId);
        const closeBtn = document.getElementById(options.closeId);
        const listEl = document.getElementById(options.listId);
        const footerEl = document.getElementById(options.footerId);
        const sidebarBadge = options.sidebarBadgeId
            ? document.getElementById(options.sidebarBadgeId)
            : null;

        if (!root || !toggle || !panel || !listEl || !footerEl || !options.pollUrl) {
            return;
        }

        const iconMap = options.iconMap || {};
        const categoryStyles = options.categoryStyles || {};
        const emptyTitle = options.emptyTitle || "You're all caught up";
        const emptySubtitle = options.emptySubtitle || 'No new notifications';
        const viewAllUrl = options.viewAllUrl || '#';
        const readAllUrl = options.readAllUrl || '#';
        const accentIconWrap = options.accentIconWrap || 'bg-green-50 text-green-700';
        const clearBtnClass = options.clearBtnClass || 'bg-green-600 hover:bg-green-700';
        const intervalMs = options.intervalMs || 8000;

        let lastFingerprint = '';
        let pollTimer = null;

        function openPanel() {
            panel.classList.remove('hidden');
            toggle.setAttribute('aria-expanded', 'true');
            refresh(true);
        }

        function closePanel() {
            panel.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (panel.classList.contains('hidden')) {
                openPanel();
            } else {
                closePanel();
            }
        });

        closeBtn?.addEventListener('click', function (e) {
            e.stopPropagation();
            closePanel();
        });

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) {
                closePanel();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closePanel();
            }
        });

        function badgeHtml(count) {
            if (count < 1) {
                return '';
            }

            return '<span id="' + options.badgeId + '" class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">'
                + (count > 9 ? '9+' : count)
                + '</span>';
        }

        function updateBadge(count) {
            const existing = document.getElementById(options.badgeId);
            if (count < 1) {
                existing?.remove();
            } else if (existing) {
                existing.textContent = count > 9 ? '9+' : String(count);
            } else {
                toggle.insertAdjacentHTML('beforeend', badgeHtml(count));
            }

            toggle.setAttribute(
                'aria-label',
                count > 0 ? 'Notifications, ' + count + ' unread' : 'Notifications'
            );

            if (sidebarBadge) {
                if (count < 1) {
                    sidebarBadge.classList.add('hidden');
                    sidebarBadge.textContent = '';
                } else {
                    sidebarBadge.classList.remove('hidden');
                    sidebarBadge.textContent = count > 9 ? '9+' : String(count);
                }
            }
        }

        function renderList(notifications) {
            if (!notifications.length) {
                listEl.innerHTML = ''
                    + '<div class="px-4 py-10 text-center">'
                    + '<span class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 text-gray-300">'
                    + '<i class="fas fa-bell-slash text-xl"></i></span>'
                    + '<p class="text-sm font-medium text-gray-700">' + escapeHtml(emptyTitle) + '</p>'
                    + '<p class="mt-1 text-xs text-gray-400">' + escapeHtml(emptySubtitle) + '</p>'
                    + '</div>';
                return;
            }

            listEl.innerHTML = notifications.map(function (item) {
                const titleClass = categoryStyles[item.category] || categoryStyles.general || 'text-gray-700';
                const faIcon = iconMap[item.icon] || 'fa-bell';
                const token = escapeHtml(csrfToken());

                return ''
                    + '<div class="flex items-stretch border-b border-gray-100 last:border-b-0">'
                    + '<form method="POST" action="' + escapeHtml(item.read_url) + '" class="min-w-0 flex-1">'
                    + '<input type="hidden" name="_token" value="' + token + '">'
                    + '<button type="submit" class="flex w-full gap-3 px-4 py-3.5 text-left transition hover:bg-gray-50">'
                    + '<span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full ' + accentIconWrap + '">'
                    + '<i class="fas ' + faIcon + '"></i></span>'
                    + '<span class="min-w-0 flex-1">'
                    + '<span class="mb-0.5 flex items-center gap-1.5 text-sm font-semibold ' + titleClass + '">'
                    + '<span class="inline-block h-1.5 w-1.5 rounded-full bg-current opacity-80"></span>'
                    + escapeHtml(item.title) + '</span>'
                    + '<span class="block text-sm leading-snug text-gray-600">' + escapeHtml(item.body) + '</span>'
                    + '<span class="mt-1.5 block text-xs text-gray-400">' + escapeHtml(item.created_at_human) + '</span>'
                    + '</span></button></form>'
                    + '<form method="POST" action="' + escapeHtml(item.read_url) + '" class="flex items-center pr-2">'
                    + '<input type="hidden" name="_token" value="' + token + '">'
                    + '<input type="hidden" name="dismiss" value="1">'
                    + '<button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-600" title="Dismiss" aria-label="Dismiss notification">'
                    + '<i class="fas fa-times text-xs"></i></button></form></div>';
            }).join('');
        }

        function renderFooter(count) {
            let html = ''
                + '<a href="' + escapeHtml(viewAllUrl) + '" class="inline-flex items-center justify-center rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-50">View All</a>';

            if (count > 0) {
                footerEl.className = 'grid grid-cols-1 gap-2 border-t border-gray-100 bg-gray-50 p-3 sm:grid-cols-2';
                html += ''
                    + '<form method="POST" action="' + escapeHtml(readAllUrl) + '">'
                    + '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken()) + '">'
                    + '<button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold text-white transition ' + clearBtnClass + '">'
                    + '<i class="fas fa-check"></i> Clear All</button></form>';
            } else {
                footerEl.className = 'grid grid-cols-1 gap-2 border-t border-gray-100 bg-gray-50 p-3';
            }

            footerEl.innerHTML = html;
        }

        function applyPayload(data) {
            const fingerprint = String(data.unread_count) + ':' + (data.notifications || []).map(function (n) {
                return n.id;
            }).join(',');

            if (fingerprint === lastFingerprint) {
                return;
            }

            lastFingerprint = fingerprint;
            updateBadge(data.unread_count || 0);
            renderList(data.notifications || []);
            renderFooter(data.unread_count || 0);
        }

        function refresh(force) {
            fetch(options.pollUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Poll failed');
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (force) {
                        lastFingerprint = '';
                    }
                    applyPayload(data);
                })
                .catch(function () {
                    // Keep current UI if polling fails.
                });
        }

        function startPolling() {
            if (pollTimer) {
                clearInterval(pollTimer);
            }
            pollTimer = setInterval(function () {
                if (document.hidden) {
                    return;
                }
                refresh(false);
            }, intervalMs);
        }

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                refresh(false);
            }
        });

        refresh(true);
        startPolling();
    };
})();
