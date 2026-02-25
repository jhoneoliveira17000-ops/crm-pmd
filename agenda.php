<?php
require_once 'src/auth.php';
require_once 'src/db.php';
require_login();

$userId = $_SESSION['user_id'];
$isGoogleConnected = false;

// Check if user has connected Google Calendar
$stmt = $pdo->prepare("SELECT id FROM user_integrations WHERE user_id = ? AND provider = 'google'");
$stmt->execute([$userId]);
if ($stmt->fetch()) {
    $isGoogleConnected = true;
}

// Prefill data from Lead Drawer (if any)
$prefillLeadId = $_GET['lead_id'] ?? '';
$prefillLeadName = $_GET['lead_name'] ?? '';
$showModalAuto = ($prefillLeadId != '' && $prefillLeadName != '') ? 'true' : 'false';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
    
    <title>Agenda &bull; PMDCRM</title>
    
    <!-- Theme System Initialization -->
    <script src="js/theme-loader.js"></script>

    <!-- Tailwind CSS (via CDN for build-less setup) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: { 850: '#151e2e' }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
            
            /* Google Calendar Clone Overrides */
            .fc { 
                --fc-page-bg-color: #ffffff; 
                --fc-neutral-bg-color: #f8f9fa;
                --fc-neutral-text-color: #3c4043; 
                --fc-border-color: #dadce0; 
                --fc-button-text-color: #3c4043; 
                --fc-button-bg-color: #ffffff; 
                --fc-button-border-color: #dadce0; 
                --fc-button-hover-bg-color: #f1f3f4; 
                --fc-button-hover-border-color: #dadce0; 
                --fc-button-active-bg-color: #e8eaed; 
                --fc-button-active-border-color: #dadce0; 
                --fc-event-bg-color: #039be5;
                --fc-event-border-color: #039be5; 
                --fc-event-text-color: #ffffff; 
                --fc-today-bg-color: #e8f0fe; 
            }
            .dark .fc { 
                --fc-page-bg-color: #202124; 
                --fc-neutral-bg-color: #202124; 
                --fc-neutral-text-color: #e8eaed; 
                --fc-border-color: #5f6368; 
                --fc-button-text-color: #e8eaed; 
                --fc-button-bg-color: #202124; 
                --fc-button-border-color: #5f6368; 
                --fc-button-hover-bg-color: #303134; 
                --fc-button-hover-border-color: #5f6368; 
                --fc-button-active-bg-color: #434446; 
                --fc-button-active-border-color: #5f6368; 
                --fc-today-bg-color: rgba(138, 180, 248, 0.15); 
            }
            /* Google specific typography and sharp edges */
            .fc-header-toolbar { padding: 8px 16px !important; margin-bottom: 0 !important; border-bottom: 1px solid var(--fc-border-color); }
            .fc-toolbar-title { font-weight: 400 !important; font-size: 1.375rem !important; color: #3c4043 !important; }
            .dark .fc-toolbar-title { color: #e8eaed !important; }
            .fc-button { text-transform: capitalize !important; font-weight: 500 !important; border-radius: 4px !important; padding: 6px 16px !important; box-shadow: none !important; }
            .fc-button-primary { transition: background-color .15s,box-shadow .15s,color .15s !important; }
            .fc-daygrid-event { border-radius: 4px !important; padding: 2px 6px !important; font-size: 0.75rem !important; font-weight: 500 !important; border: none !important; margin: 1px 4px !important; }
            .fc-timegrid-event { border-radius: 4px !important; border: 1px solid #ffffff !important; box-shadow: none !important; }
            .fc-col-header-cell-cushion { padding: 8px 0 !important; font-weight: 500 !important; text-transform: uppercase !important; font-size: 0.6875rem !important; color: #70757a !important; }
            .dark .fc-col-header-cell-cushion { color: #9aa0a6 !important; }
            .fc-daygrid-day-number { font-weight: 500 !important; font-size: 0.75rem !important; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; padding: 0 !important; margin: 4px; }
            .fc-daygrid-day.fc-day-today .fc-daygrid-day-number { background-color: #1a73e8; color: white !important; }
            .dark .fc-daygrid-day.fc-day-today .fc-daygrid-day-number { background-color: #8ab4f8; color: #202124 !important; }
            .fc-theme-standard td, .fc-theme-standard th { border: 1px solid var(--fc-border-color) !important; }
            .fc-scrollgrid { border: none !important; }
            .fc-view-harness { background-color: var(--fc-page-bg-color); }
        }
    </style>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/pt-br.global.min.js'></script>
</head>
<body class="bg-gray-50 dark:bg-[#0f172a] text-slate-900 dark:text-slate-200 h-screen overflow-hidden flex flex-col font-sans transition-colors duration-300 pb-20 md:pb-0 md:pl-64">

    <!-- Navbar -->
    <?php include 'nav.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Google Calendar Style Top Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 py-2 bg-white dark:bg-[#202124] border-b border-[#dadce0] dark:border-[#5f6368] sticky top-0 z-40 shrink-0 h-16">
            <div class="flex items-center gap-4">
                <a href="dashboard" class="md:hidden text-slate-500 hover:text-slate-900 transition p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#1a73e8] dark:text-[#8ab4f8]" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5C3.89 4 3 4.9 3 6v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"></path></svg>
                    <h1 class="text-xl font-normal text-[#3c4043] dark:text-[#e8eaed] tracking-tight">
                        Agenda
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <?php if (!$isGoogleConnected): ?>
                    <a href="api/google_auth.php" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#303134] border border-[#dadce0] dark:border-[#5f6368] hover:bg-[#f1f3f4] dark:hover:bg-[#434446] text-[#3c4043] dark:text-[#e8eaed] font-medium rounded transition-colors text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.9c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg>
                        Conectar Google
                    </a>
                <?php else: ?>
                    <a href="api/google_auth.php" title="Reconectar Conta" class="flex items-center gap-2 p-2 px-3 hover:bg-[#f1f3f4] dark:hover:bg-[#303134] text-[#1a73e8] dark:text-[#8ab4f8] rounded transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        Sincronizado
                    </a>
                <?php endif; ?>
                
                <?php if ($isGoogleConnected): ?>
                <button onclick="openEventModal()" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#303134] hover:bg-[#f8f9fa] dark:hover:bg-[#434446] border border-[#dadce0] dark:border-[#5f6368] text-[#3c4043] dark:text-[#e8eaed] font-medium rounded-full shadow-sm transition-all focus:outline-none focus:ring-1 focus:ring-gray-300">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Criar
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edge to Edge Calendar Container -->
        <div class="flex-1 bg-white dark:bg-[#202124] relative overflow-hidden flex flex-col">
            <?php if (!$isGoogleConnected): ?>
                <!-- Blocker Overlay -->
                <div class="absolute inset-0 z-10 bg-white/90 dark:bg-[#202124]/90 backdrop-blur-sm flex flex-col items-center justify-center">
                    <div class="bg-white dark:bg-[#303134] p-8 rounded-lg shadow-lg max-w-sm w-full text-center border border-[#dadce0] dark:border-[#5f6368]">
                        <div class="mb-4">
                            <svg class="w-12 h-12 text-[#1a73e8] dark:text-[#8ab4f8] mx-auto" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5C3.89 4 3 4.9 3 6v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"></path></svg>
                        </div>
                        <h3 class="text-xl font-normal text-[#3c4043] dark:text-[#e8eaed] mb-2">Google Calendar</h3>
                        <p class="text-[#70757a] dark:text-[#9aa0a6] text-sm mb-6">Conecte sua conta para visualizar os agendamentos nativos do Google em tempo real.</p>
                        <a href="api/google_auth.php" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#1a73e8] hover:bg-[#1b66c9] text-white font-medium rounded transition-colors text-sm w-full">
                            Fazer Login
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <div id="calendar" class="flex-1 w-full h-full"></div>
        </div>

    </div>

    <!-- Event Modal (Create/Edit) -->
    <div id="eventModal" class="fixed inset-0 bg-slate-900/60 z-50 flex justify-center items-center opacity-0 pointer-events-none transition-opacity duration-300 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-[#0f172a] rounded-2xl shadow-2xl w-full max-w-md transform scale-95 transition-transform duration-300 border border-gray-200 dark:border-slate-800 overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-[#1e293b]/50">
                <h3 id="modalTitle" class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Novo Agendamento</h3>
                <button onclick="closeEventModal()" class="text-slate-400 hover:text-rose-500 transition-colors bg-white dark:bg-[#0f172a] hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-full p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form id="eventForm" onsubmit="saveEvent(event)" class="p-6 space-y-5">
                <input type="hidden" id="eventId">
                <input type="hidden" id="eventLeadId" value="<?php echo htmlspecialchars($prefillLeadId); ?>">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Título do Evento</label>
                    <input type="text" id="eventTitle" required placeholder="Ex: Reunião de Apresentação"
                        class="w-full px-4 py-3 bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[var(--theme-color)] focus:border-transparent transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Início</label>
                        <input type="datetime-local" id="eventStart" required
                            class="w-full px-4 py-3 bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-color)] transition-all [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Fim</label>
                        <input type="datetime-local" id="eventEnd" required
                            class="w-full px-4 py-3 bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--theme-color)] transition-all [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                </div>
                
                <div class="flex items-center gap-3 py-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="eventAllDay" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-[var(--theme-color)]"></div>
                        <span class="ml-3 text-sm font-bold text-slate-700 dark:text-slate-300">Dia Inteiro</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Descrição / Notas</label>
                    <textarea id="eventDesc" rows="3" placeholder="Pauta da reunião, links, etc..."
                        class="w-full px-4 py-3 bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[var(--theme-color)] focus:border-transparent transition-all resize-none"></textarea>
                </div>

                <!-- Footer Actions -->
                <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex justify-between items-center gap-3">
                    <button type="button" id="btnDeleteEvent" onclick="deleteEvent()" class="hidden md:flex px-4 py-2.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 font-bold rounded-xl transition-colors text-sm">
                        Excluir
                    </button>
                    <!-- Mobile delete icon fallback -->
                    <button type="button" id="btnDeleteEventMobile" onclick="deleteEvent()" class="md:hidden p-2.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>

                    <div class="flex gap-3 ml-auto">
                        <button type="button" onclick="closeEventModal()" class="px-5 py-2.5 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-xl transition-colors text-sm">
                            Cancelar
                        </button>
                        <button type="submit" id="btnSaveEvent" class="px-5 py-2.5 bg-[var(--theme-color)] hover:bg-opacity-90 text-white font-bold rounded-xl shadow-md transition-transform active:scale-95 text-sm flex items-center gap-2">
                            Salvar no Google
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global State
        let calendar;
        const isConnected = <?php echo $isGoogleConnected ? 'true' : 'false'; ?>;
        const rgbaColor = hexToRgba(getComputedStyle(document.documentElement).getPropertyValue('--theme-color').trim(), 1);

        // Utility: Convert Hex to RGBA for FullCalendar event colors
        function hexToRgba(hex, alpha=1) {
            if(!hex) return `rgba(50, 200, 52, ${alpha})`;
            let c;
            if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
                c= hex.substring(1).split('');
                if(c.length== 3){
                    c= [c[0], c[0], c[1], c[1], c[2], c[2]];
                }
                c= '0x'+c.join('');
                return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+alpha+')';
            }
            return `rgba(50, 200, 52, ${alpha})`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Apply Theme Overrides based on CSS Custom Properties
            const rootStyle = getComputedStyle(document.documentElement);
            const themeColor = rootStyle.getPropertyValue('--theme-color').trim() || '#10b981';
            
            // Extract RGB for alpha backgrounds
            const rgb = hexToRgba(themeColor, 1).match(/\d+/g);
            if(rgb) {
                document.documentElement.style.setProperty('--theme-color-rgb', `${rgb[0]}, ${rgb[1]}, ${rgb[2]}`);
            }

            if (isConnected) {
                initCalendar();
            }

            // Handle Lead Context Prefill if arrived from CRM Pipeline
            const prefillLeadId = "<?php echo addslashes($prefillLeadId); ?>";
            const prefillLeadName = "<?php echo addslashes($prefillLeadName); ?>";
            const shouldAutoShow = <?php echo $showModalAuto; ?>;

            if (shouldAutoShow && isConnected) {
                openEventModal(null, `Reunião - ${prefillLeadName}`);
            } else if (shouldAutoShow && !isConnected) {
                Swal.fire({
                    icon: 'info',
                    title: 'Conecte sua Agenda',
                    text: 'Para agendar uma reunião com este Lead direto no calendário, conecte sua conta do Google primeiro.',
                    confirmButtonText: 'Conectar Agora',
                    confirmButtonColor: themeColor
                }).then((res) => {
                    if (res.isConfirmed) window.location.href = 'api/google_auth.php';
                });
            }

            // Handle "All Day" checkbox toggle logic to hide/show times
            document.getElementById('eventAllDay').addEventListener('change', function(e) {
                const isChecked = e.target.checked;
                const startInput = document.getElementById('eventStart');
                const endInput = document.getElementById('eventEnd');
                
                if (isChecked) {
                    // Extract just the YYYY-MM-DD part and set type to date
                    startInput.type = 'date';
                    endInput.type = 'date';
                    if (startInput.value.includes('T')) startInput.value = startInput.value.split('T')[0];
                    if (endInput.value.includes('T')) endInput.value = endInput.value.split('T')[0];
                } else {
                    startInput.type = 'datetime-local';
                    endInput.type = 'datetime-local';
                    // Re-append default time if it was stripped
                    if (startInput.value && !startInput.value.includes('T')) startInput.value += 'T09:00';
                    if (endInput.value && !endInput.value.includes('T')) endInput.value += 'T10:00';
                }
            });
        });

        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'pt-br',
                initialView: window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek',
                headerToolbar: {
                    left: window.innerWidth < 768 ? 'prev,next' : 'prev,next today',
                    center: 'title',
                    right: window.innerWidth < 768 ? 'timeGridDay,timeGridWeek' : 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    day: 'Dia'
                },
                slotMinTime: '06:00:00', // Start calendar at 6am to save screen space
                slotMaxTime: '23:00:00',
                allDaySlot: true,
                allDayText: 'Dia int.',
                nowIndicator: true,
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                events: function(info, successCallback, failureCallback) {
                    // Fetch events dynamically based on current view range
                    fetch(`api/agenda.php?start=${info.startStr}&end=${info.endStr}`)
                        .then(res => res.json())
                        .then(data => {
                            if(data.error) {
                                if (data.error === 'not_connected') {
                                    window.location.reload(); // Fallback to blocker overlay
                                } else {
                                    console.error("API Error:", data);
                                    Swal.fire('Erro', 'Falha ao carregar eventos da agenda.', 'error');
                                }
                                failureCallback();
                            } else {
                                successCallback(data);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            failureCallback(err);
                        });
                },
                select: function(info) {
                    // Click and drag on empty space to start an event
                    openEventModal(null, '', info.startStr, info.endStr, info.allDay);
                    calendar.unselect();
                },
                eventClick: function(info) {
                    // Click on existing event to edit
                    const ev = info.event;
                    openEventModal(
                        ev.id, 
                        ev.title, 
                        formatDateForInput(ev.start, ev.allDay), 
                        formatDateForInput(ev.end || ev.start, ev.allDay), // Google might not send end if same as start
                        ev.allDay, 
                        ev.extendedProps.description
                    );
                    info.jsEvent.preventDefault(); // Don't follow url
                },
                eventDrop: function(info) {
                    updateEventTime(info.event);
                },
                eventResize: function(info) {
                    updateEventTime(info.event);
                }
            });
            calendar.render();
        }

        // --- Formatters ---
        function formatDateForInput(dateObj, isAllDay) {
            if (!dateObj) return '';
            const pad = (n) => n < 10 ? '0'+n : n;
            const y = dateObj.getFullYear();
            const m = pad(dateObj.getMonth() + 1);
            const d = pad(dateObj.getDate());
            
            if (isAllDay) {
                return `${y}-${m}-${d}`;
            } else {
                const hr = pad(dateObj.getHours());
                const mi = pad(dateObj.getMinutes());
                return `${y}-${m}-${d}T${hr}:${mi}`;
            }
        }

        function buildDateTimeLocalNow(offsetHours = 0) {
            const d = new Date();
            d.setHours(d.getHours() + offsetHours);
            d.setMinutes(0); // Round to hour
            return formatDateForInput(d, false);
        }

        // --- Modals & CRUD ---
        function openEventModal(id = null, title = '', startStr = '', endStr = '', allDay = false, desc = '') {
            const isEditing = id !== null;
            document.getElementById('modalTitle').textContent = isEditing ? 'Editar Agendamento' : 'Novo Agendamento';
            
            document.getElementById('eventId').value = id || '';
            document.getElementById('eventTitle').value = title;
            document.getElementById('eventDesc').value = desc;
            
            const chkAllDay = document.getElementById('eventAllDay');
            chkAllDay.checked = allDay;
            
            const startInput = document.getElementById('eventStart');
            const endInput = document.getElementById('eventEnd');
            
            startInput.type = allDay ? 'date' : 'datetime-local';
            endInput.type = allDay ? 'date' : 'datetime-local';

            startInput.value = startStr || buildDateTimeLocalNow(1); // Default to 1 hour from now
            endInput.value = endStr || buildDateTimeLocalNow(2);

            // Toggle Delete buttons
            const delBtn1 = document.getElementById('btnDeleteEvent');
            const delBtn2 = document.getElementById('btnDeleteEventMobile');
            if (isEditing) {
                delBtn1.classList.remove('hidden'); delBtn1.classList.add('md:flex');
                delBtn2.classList.remove('hidden');
            } else {
                delBtn1.classList.remove('md:flex'); delBtn1.classList.add('hidden');
                delBtn2.classList.add('hidden');
            }

            // Animate Modal In
            const modal = document.getElementById('eventModal');
            modal.classList.remove('pointer-events-none');
            modal.classList.replace('opacity-0', 'opacity-100');
            modal.children[0].classList.replace('scale-95', 'scale-100');
            
            document.getElementById('eventTitle').focus();
        }

        function closeEventModal() {
            const modal = document.getElementById('eventModal');
            modal.classList.replace('opacity-100', 'opacity-0');
            modal.children[0].classList.replace('scale-100', 'scale-95');
            setTimeout(() => {
                modal.classList.add('pointer-events-none');
                document.getElementById('eventForm').reset();
            }, 300);
        }

        async function saveEvent(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSaveEvent');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline max-w-fit" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';
            btn.disabled = true;

            const id = document.getElementById('eventId').value;
            const payload = {
                title: document.getElementById('eventTitle').value,
                description: document.getElementById('eventDesc').value,
                start: document.getElementById('eventStart').value,
                end: document.getElementById('eventEnd').value,
                allDay: document.getElementById('eventAllDay').checked
            };
            
            // Format dates strictly for Google API depending on allDay check
            if(!payload.allDay) {
                // Ensure timezone compatibility by creating full ISO strings assuming local time input
                payload.start = new Date(payload.start).toISOString();
                payload.end = new Date(payload.end).toISOString();
            }

            if (id) payload.id = id;

            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch('api/agenda.php', {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (data.status === 200 || data.status === 204) {
                    // Success!
                    closeEventModal();
                    calendar.refetchEvents(); // Reload from source
                    
                    // Show small toast success
                    Swal.fire({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                        icon: 'success', title: 'Agendamento salvo no Google Calendar!'
                    });
                } else {
                    throw new Error(data.error || "Erro desconhecido na API do Google");
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Erro!', 'Não foi possível salvar o agendamento. Tente novamente.', 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        async function deleteEvent() {
            const id = document.getElementById('eventId').value;
            if(!id) return;

            const rootStyle = getComputedStyle(document.documentElement);
            const themeColor = rootStyle.getPropertyValue('--theme-color').trim() || '#10b981';

            const confirm = await Swal.fire({
                title: 'Desmarcar Agendamento?',
                text: "Isso irá apagar a reunião do seu Google Calendar instantaneamente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48', // rose-600
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sim, Apagar!',
                cancelButtonText: 'Cancelar'
            });

            if (confirm.isConfirmed) {
                try {
                    const response = await fetch(`api/agenda.php?id=${id}`, { method: 'DELETE' });
                    const data = await response.json();
                    
                    if(data.status === 204 || data.status === 200) {
                        closeEventModal();
                        calendar.refetchEvents();
                        Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, icon: 'success', title: 'Apagado com sucesso!' });
                    } else {
                        throw new Error();
                    }
                } catch (error) {
                    Swal.fire('Erro!', 'Não foi possível excluir o evento no Google.', 'error');
                }
            }
        }

        async function updateEventTime(eventObj) {
            // Fired when dragging and dropping an event on the calendar visually
            const payload = {
                id: eventObj.id,
                title: eventObj.title,
                allDay: eventObj.allDay
            };

            // Dragging an event means we trust FullCalendar's date objects
            if(eventObj.allDay) {
                payload.start = formatDateForInput(eventObj.start, true);
                payload.end = formatDateForInput(eventObj.end || eventObj.start, true);
            } else {
                payload.start = eventObj.start.toISOString();
                payload.end = eventObj.end ? eventObj.end.toISOString() : eventObj.start.toISOString();
            }

            try {
                const response = await fetch('api/agenda.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if(data.status !== 200) {
                    throw new Error("API Sync Error");
                }
                // Optional: Show very subtle saving toast
            } catch (error) {
                console.error("Drag Drop Sync Failed", error);
                Swal.fire('Erro de Sincronização', 'A alteração visual não pôde ser salva no Google. Recarregando.', 'error');
                eventObj.revert();
            }
        }
    </script>
</body>
</html>
