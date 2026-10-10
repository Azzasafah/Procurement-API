<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk | Procurement & Supply Chain ERP')</title>
    <meta name="description" content="Portal Operasional Logistik, Pengadaan Barang, dan Manajemen Inventaris Enterprise.">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        slate: {
                            50: '#F8FAFC',
                            100: '#F1F5F9',
                            200: '#E2E8F0',
                            300: '#CBD5E1',
                            400: '#94A3B8',
                            500: '#64748B',
                            600: '#475569',
                            700: '#334155',
                            800: '#1E293B',
                            900: '#0F172A',
                            950: '#020617',
                        },
                        emerald: {
                            50: '#ECFDF5',
                            100: '#D1FAE5',
                            600: '#059669',
                            700: '#047857',
                            800: '#065F46',
                            900: '#064E3B',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="/css/erp.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="min-h-full flex flex-col justify-center bg-[#F8FAFC] text-slate-900 font-sans antialiased selection:bg-emerald-600 selection:text-white relative overflow-x-hidden">
    <!-- Ambient Subtly Diffused Mesh Lights (Awwwards-grade Soft Structuralism) -->
    <div class="fixed top-0 left-1/4 w-[500px] h-[500px] bg-emerald-100/40 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-1/4 w-[500px] h-[500px] bg-teal-100/30 rounded-full blur-3xl pointer-events-none -z-10"></div>
    
    <div class="w-full flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-10">
        @yield('content')
    </div>
</body>
</html>
