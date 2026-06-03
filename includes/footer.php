<?php
// PMDCRM/includes/footer.php
?>
    <!-- Reusable Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <!-- Global JavaScript Helpers -->
    <script>
        // escapeHtml helper to prevent XSS in JS Templates
        function escapeHtml(string) {
            if (string === null || string === undefined) return '';
            return String(string)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Global Toast Notification Utility
        function showToast(message, type = 'success', duration = 3000) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-3 p-4 rounded-xl shadow-lg border text-sm font-medium transition-all duration-300 transform translate-y-2 opacity-0 pointer-events-auto max-w-sm `;
            
            if (type === 'success') {
                toast.className += 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300';
                toast.innerHTML = `<span class="text-emerald-500 text-lg">✓</span> <span class="flex-1">${escapeHtml(message)}</span>`;
            } else if (type === 'error') {
                toast.className += 'bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300';
                toast.innerHTML = `<span class="text-rose-500 text-lg">⚠️</span> <span class="flex-1">${escapeHtml(message)}</span>`;
            } else {
                toast.className += 'bg-blue-50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300';
                toast.innerHTML = `<span class="text-blue-500 text-lg">ℹ️</span> <span class="flex-1">${escapeHtml(message)}</span>`;
            }
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);
            
            // Remove after duration
            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        // Override native window.alert to automatically use our premium Toast component
        window.alert = function(message) {
            // Determine type by content hint (very simple heuristic)
            let type = 'info';
            const lower = message.toLowerCase();
            if (lower.includes('sucesso') || lower.includes('salvo') || lower.includes('atualizado') || lower.includes('criado')) {
                type = 'success';
            } else if (lower.includes('erro') || lower.includes('falha') || lower.includes('inválido') || lower.includes('negado') || lower.includes('obrigatório')) {
                type = 'error';
            }
            showToast(message, type);
        };

        // Intercept native fetch and XMLHttpRequest to automatically inject CSRF token header
        (function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) return;

            // Fetch API interceptor
            const originalFetch = window.fetch;
            window.fetch = async function(...args) {
                let [resource, config] = args;
                if (!config) config = {};
                
                // If it is a modifying method, attach header
                const method = (config.method || 'GET').toUpperCase();
                if (['POST', 'PUT', 'DELETE'].includes(method)) {
                    if (!config.headers) {
                        config.headers = {};
                    }
                    if (config.headers instanceof Headers) {
                        if (!config.headers.has('X-CSRF-Token')) {
                            config.headers.append('X-CSRF-Token', csrfToken);
                        }
                    } else if (Array.isArray(config.headers)) {
                        // Array of pairs
                        if (!config.headers.some(h => h[0].toLowerCase() === 'x-csrf-token')) {
                            config.headers.push(['X-CSRF-Token', csrfToken]);
                        }
                    } else {
                        // Object
                        if (!config.headers['X-CSRF-Token'] && !config.headers['x-csrf-token']) {
                            config.headers['X-CSRF-Token'] = csrfToken;
                        }
                    }
                }
                return originalFetch(resource, config);
            };

            // XMLHttpRequest interceptor
            const originalOpen = XMLHttpRequest.prototype.open;
            XMLHttpRequest.prototype.open = function(method, url, ...rest) {
                this._method = method.toUpperCase();
                return originalOpen.call(this, method, url, ...rest);
            };

            const originalSend = XMLHttpRequest.prototype.send;
            XMLHttpRequest.prototype.send = function(body) {
                if (['POST', 'PUT', 'DELETE'].includes(this._method)) {
                    this.setRequestHeader('X-CSRF-Token', csrfToken);
                }
                return originalSend.call(this, body);
            };
        })();

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('service-worker.js')
                    .then(reg => console.log('Service Worker registrado com sucesso:', reg.scope))
                    .catch(err => console.error('Erro ao registrar Service Worker:', err));
            });
        }
    </script>
</body>
</html>
