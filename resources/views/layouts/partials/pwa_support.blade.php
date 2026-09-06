<!-- PWA Service Worker & Session Keep-Alive Engine (Anti-Expired & Auto-Update) -->
<script>
    (function () {
        const pingUrl = "{{ url('/ping') }}";
        let isRefreshing = false;
        let lastPingTime = Date.now();

        // 1. Service Worker Registration with Seamless Auto-Update (No More Hard Refresh)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        // Check for SW updates every 15 minutes
                        setInterval(() => {
                            registration.update().catch(() => {});
                        }, 15 * 60 * 1000);

                        // If an update is detected, monitor state
                        registration.onupdatefound = () => {
                            const newWorker = registration.installing;
                            if (newWorker) {
                                newWorker.onstatechange = () => {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // New service worker version installed! Auto-reload so user gets latest version without hard-refreshing
                                        if (!isRefreshing) {
                                            isRefreshing = true;
                                            window.location.reload();
                                        }
                                    }
                                };
                            }
                        };
                    })
                    .catch((err) => console.log('Yoimo PWA Service Worker registration error:', err));
            });

            // When new SW controller takes over, refresh smoothly
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!isRefreshing) {
                    isRefreshing = true;
                    window.location.reload();
                }
            });
        }

        // 2. Session Heartbeat: Keep Laravel Session & CSRF Token Permanently Fresh
        function updatePageCsrfTokens(newToken) {
            if (!newToken) return;

            // Update <meta name="csrf-token">
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                metaToken.setAttribute('content', newToken);
            }

            // Update all hidden _token inputs in forms
            document.querySelectorAll('input[name="_token"]').forEach(input => {
                input.value = newToken;
            });

            // Update global csrfToken variable if defined
            if (typeof csrfToken !== 'undefined') {
                try { csrfToken = newToken; } catch (e) {}
            }
        }

        async function pingSession(force = false) {
            const now = Date.now();
            // Debounce: don't ping more than once every 30 seconds unless forced
            if (!force && (now - lastPingTime < 30000)) {
                return;
            }
            lastPingTime = now;

            try {
                const res = await fetch(pingUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    cache: 'no-store'
                });

                if (res.ok) {
                    const data = await res.json();
                    if (data && data.csrf_token) {
                        updatePageCsrfTokens(data.csrf_token);
                    }
                }
            } catch (err) {
                // Silently handle temporary network loss
            }
        }

        // Heartbeat every 8 minutes (keeps PHP session alive continuously)
        setInterval(() => pingSession(false), 8 * 60 * 1000);

        // Ping immediately when user wakes up device or returns to PWA
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                pingSession(true);
                if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                    navigator.serviceWorker.ready.then(reg => reg.update()).catch(() => {});
                }
            }
        });
        window.addEventListener('focus', () => pingSession(true));

        // 3. Global Fetch Interceptor: Catch 419 (Page Expired) & Transparently Auto-Recover
        const originalFetch = window.fetch;
        window.fetch = async function (...args) {
            const response = await originalFetch.apply(this, args);

            // If Laravel responds with 419 Page Expired (CSRF Mismatch)
            if (response.status === 419) {
                console.warn('[PWA] Detected 419 Page Expired. Auto-refreshing session and retrying request...');
                try {
                    const pingRes = await originalFetch(pingUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        cache: 'no-store'
                    });

                    if (pingRes.ok) {
                        const pingData = await pingRes.json();
                        if (pingData.csrf_token) {
                            updatePageCsrfTokens(pingData.csrf_token);

                            // Update headers and retry original request
                            let [resource, config] = args;
                            config = config || {};
                            config.headers = config.headers || {};

                            if (config.headers instanceof Headers) {
                                config.headers.set('X-CSRF-TOKEN', pingData.csrf_token);
                            } else {
                                config.headers['X-CSRF-TOKEN'] = pingData.csrf_token;
                            }

                            if (config.body instanceof FormData && config.body.has('_token')) {
                                config.body.set('_token', pingData.csrf_token);
                            }

                            return await originalFetch(resource, config);
                        }
                    }
                } catch (retryErr) {
                    console.error('[PWA] Failed to auto-recover from 419:', retryErr);
                }
            }

            return response;
        };
    })();
</script>
