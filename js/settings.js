function toggleSettings() {
    const drawer = document.getElementById('settingsDrawer');
    const overlay = document.getElementById('settingsDrawerOverlay');

    if (drawer.classList.contains('translate-x-full')) {
        // Open
        drawer.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
    } else {
        // Close
        drawer.classList.add('translate-x-full');
        overlay.classList.add('opacity-0');
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }
}

function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
}

// Theme Check is now handled instantly in head by theme-loader.js

// Theme Color FOUC Fix: Apply immediately if cached
(function () {
    const cachedColor = localStorage.getItem('theme_color');
    if (cachedColor) {
        // We need to define applyThemeColor or inline the logic here because 
        // the function definition might be below. However, function declarations are hoisted.
        // But to be safe and fast, we can just run the logic.
        // Actually, applyThemeColor is defined below, so hoisting works.
        try { applyThemeColor(cachedColor); } catch (e) { }
    }
})();

// Load Settings on Init
document.addEventListener('DOMContentLoaded', loadSettings);

async function loadSettings() {
    try {
        const res = await fetch('api/settings.php');
        const data = await res.json();

        if (data.success && data.data) {
            const s = data.data;

            // Meta
            if (s.meta_page_id) document.getElementById('metaPageId').value = s.meta_page_id;
            if (s.meta_verify_token) document.getElementById('metaVerifyToken').value = s.meta_verify_token;
            if (s.meta_page_access_token) document.getElementById('metaAccessToken').value = s.meta_page_access_token;

            // Customization
            if (s.theme_color) {
                document.getElementById('themeColorInput').value = s.theme_color;
                applyThemeColor(s.theme_color);
                localStorage.setItem('theme_color', s.theme_color);
            }
            if (s.whatsapp_default_msg) document.getElementById('whatsappMsgInput').value = s.whatsapp_default_msg;
            if (s.webhook_token && document.getElementById('metaWebhookUrl')) {
                const basePath = window.location.href.substring(0, window.location.href.lastIndexOf('/'));
                const webhookUrl = basePath + '/api/webhook.php?token=' + s.webhook_token;
                document.getElementById('metaWebhookUrl').value = webhookUrl;
            }
            if (s.company_logo) {
                document.getElementById('logoPreview').src = s.company_logo;
                document.getElementById('logoPreview').classList.remove('hidden');
                document.getElementById('logoPlaceholder').classList.add('hidden');
            }
        }
    } catch (e) { console.error("Error loading settings", e); }
}

function previewLogo(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('logoPreview').src = e.target.result;
            document.getElementById('logoPreview').classList.remove('hidden');
            document.getElementById('logoPlaceholder').classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}

async function saveSettings() {
    const btn = document.querySelector('button[onclick="saveSettings()"]');
    const originalText = btn.innerText;
    btn.innerHTML = '<span class="animate-spin text-white">⟳</span>';
    btn.disabled = true;

    try {
        const formData = new FormData();

        // File
        const fileInput = document.getElementById('logoInput');
        if (fileInput.files[0]) {
            formData.append('company_logo_file', fileInput.files[0]);
        }

        // Text Settings
        const settings = {
            whatsapp_default_msg: document.getElementById('whatsappMsgInput').value,
            theme_color: document.getElementById('themeColorInput').value,
            meta_page_id: document.getElementById('metaPageId').value,
            meta_verify_token: document.getElementById('metaVerifyToken').value,
            meta_page_access_token: document.getElementById('metaAccessToken').value
        };
        formData.append('settings', JSON.stringify(settings));

        const res = await fetch('api/settings.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            // alert("Configurações salvas com sucesso!");

            // Update Cache
            if (settings.theme_color) localStorage.setItem('theme_color', settings.theme_color);

            btn.innerHTML = 'Salvo!';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                if (fileInput.files[0] || settings.theme_color) location.reload();
            }, 1000);
        } else {
            alert("Erro: " + (data.error || "Desconhecido"));
            btn.innerText = originalText;
            btn.disabled = false;
        }

    } catch (e) {
        console.error(e);
        alert("Erro ao salvar.");
    } finally {
        // Reset all specific buttons if needed
        document.querySelectorAll('button[onclick="saveSettings()"]').forEach(b => {
            b.innerText = b.getAttribute('data-original-text') || (b.innerText === 'Salvo!' ? "Salvar Meta Config" : "Salvar Configurações"); // Fallback
            b.disabled = false;
        });
        btn.innerText = originalText;
        btn.disabled = false;
    }
}

function generateVerifyToken() {
    const token = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    document.getElementById('metaVerifyToken').value = token;
}

function copyToClipboard(id) {
    const el = document.getElementById(id);
    el.select();
    el.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(el.value).then(() => {
        // Optional feedback
        const originalBg = el.style.backgroundColor;
        el.style.backgroundColor = '#dcfce7'; // green-100
        setTimeout(() => el.style.backgroundColor = originalBg, 200);
    });
}

// Webhook initial population is now handled inside loadSettings() to wait for the token to arrive
document.addEventListener('DOMContentLoaded', () => {

    // Drag & Drop Logo
    const dropZone = document.getElementById('logoDropZone');
    const fileInput = document.getElementById('logoInput');

    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-[#00BF24]', 'bg-green-50', 'dark:bg-green-900/10');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-[#00BF24]', 'bg-green-50', 'dark:bg-green-900/10');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            previewLogo({ target: fileInput });
        }
    }

    // Theme Color Live Preview
    const colorInput = document.getElementById('themeColorInput');
    if (colorInput) {
        colorInput.addEventListener('input', (e) => {
            applyThemeColor(e.target.value);
        });
    }
});


function applyThemeColor(color) {
    // Inject dynamic style to override hardcoded #00BF24
    let style = document.getElementById('dynamic-theme-style');
    if (!style) {
        style = document.createElement('style');
        style.id = 'dynamic-theme-style';
        document.head.appendChild(style);
    }

    // Create shades (basic hex manipulation logic or simple override)
    // For simplicity, we assume the user picks the main color.
    // We override specific classes used in the project.

    style.innerHTML = `
        .bg-\\[\\#00BF24\\], .bg-\\[\\#00bf24\\] { background-color: ${color} !important; }
        .text-\\[\\#00BF24\\], .text-\\[\\#00bf24\\] { color: ${color} !important; }
        .border-\\[\\#00BF24\\], .border-\\[\\#00bf24\\] { border-color: ${color} !important; }
        .focus\\:ring-\\[\\#00BF24\\]:focus { --tw-ring-color: ${color} !important; }
        .focus\\:border-\\[\\#00BF24\\]:focus { border-color: ${color} !important; }
        .hover\\:bg-\\[\\#00BF24\\]:hover { background-color: ${color} !important; filter: brightness(0.9); }
        .hover\\:text-\\[\\#00BF24\\]:hover { color: ${color} !important; }
        .hover\\:border-\\[\\#00BF24\\]:hover { border-color: ${color} !important; }
        .group:hover .group-hover\\:text-\\[\\#00BF24\\] { color: ${color} !important; }
        /* Badge backgrounds */
        .bg-green-100 { background-color: ${color}20 !important; } /* 20% opacity */
        .text-green-700 { color: ${color} !important; }
    `;
}


