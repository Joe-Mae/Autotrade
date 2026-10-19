{{-- Alertes temps réel de la console admin : polling léger, toasts sonores
     et notification navigateur. --}}
<script>
(function () {
    var rack = document.getElementById('adminToastRack');
    if (!rack) return;

    var pollUrl = @json(route('admin.notifications'));
    var lastMessageId = {{ (int) ($adminBadges['latest_message_id'] ?? 0) }};
    var lastPurchaseId = {{ (int) ($adminBadges['latest_purchase_id'] ?? 0) }};

    var soundKey = 'admin-sound-enabled';
    var soundEnabled = true;
    try { soundEnabled = localStorage.getItem(soundKey) !== '0'; } catch (e) {}

    function beep() {
        if (!soundEnabled) return;
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            var ctx = new Ctx();
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 0.35);
            gain.gain.setValueAtTime(0.25, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.45);
            osc.start();
            osc.stop(ctx.currentTime + 0.45);
        } catch (e) {}
    }

    function showToast(title, message, danger) {
        var el = document.createElement('div');
        el.className = 'admin-toast' + (danger ? ' admin-toast--danger' : '');
        el.innerHTML =
            '<div class="admin-toast__body">' +
                '<div class="admin-toast__title">' + title + '</div>' +
                '<div class="admin-toast__msg">' + message + '</div>' +
            '</div>' +
            '<button type="button" class="admin-toast__close" aria-label="Fermer">&times;</button>';
        rack.appendChild(el);
        var close = el.querySelector('.admin-toast__close');
        var dismiss = function () { el.remove(); };
        close.addEventListener('click', dismiss);
        setTimeout(function () { el.classList.add('is-visible'); }, 20);
        setTimeout(dismiss, 12000);
    }

    function desktopNotify(title, body) {
        try {
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(title, { body: body });
            }
        } catch (e) {}
    }

    function updateBadges(unread, pending) {
        var clients = document.querySelector('[data-admin-link="clients"]');
        var ventes = document.querySelector('[data-admin-link="ventes"]');
        if (clients) {
            var b1 = clients.querySelector('.admin-nav__badge');
            if (unread > 0) {
                if (!b1) { b1 = document.createElement('span'); b1.className = 'admin-nav__badge'; clients.appendChild(b1); }
                b1.textContent = unread;
            } else if (b1) { b1.remove(); }
        }
        if (ventes) {
            var b2 = ventes.querySelector('.admin-nav__badge');
            if (pending > 0) {
                if (!b2) { b2 = document.createElement('span'); b2.className = 'admin-nav__badge'; ventes.appendChild(b2); }
                b2.textContent = pending;
            } else if (b2) { b2.remove(); }
        }
    }

    async function poll() {
        try {
            var res = await fetch(pollUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            var d = await res.json();

            if (d.latest_message_id > lastMessageId) {
                showToast('Nouveau message', 'Un visiteur vient de vous écrire. Ouvrez l’onglet Clients.', true);
                beep();
                desktopNotify('Nouveau message', 'Un visiteur vient de vous écrire.');
            }
            lastMessageId = Math.max(lastMessageId, d.latest_message_id);

            if (d.latest_purchase_id > lastPurchaseId) {
                showToast('Nouvelle demande', 'Une nouvelle demande d’achat vient d’être reçue. Ouvrez l’onglet Ventes.', false);
                beep();
                desktopNotify('Nouvelle demande', 'Une nouvelle demande d’achat vient d’être reçue.');
            }
            lastPurchaseId = Math.max(lastPurchaseId, d.latest_purchase_id);

            updateBadges(d.unread_messages, d.pending_purchases);
        } catch (e) {}
    }

    setInterval(poll, 5000);
    setTimeout(poll, 1500);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) poll(); });
    window.addEventListener('focus', function () { poll(); });

    try {
        if ('Notification' in window && Notification.permission === 'default') {
            document.addEventListener('click', function () { Notification.requestPermission().catch(function () {}); }, { once: true });
        }
    } catch (e) {}
})();
</script>
