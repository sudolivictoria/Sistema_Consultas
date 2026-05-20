<!DOCTYPE html>

<html class="light" lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Sistema de Consultas - Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Hanken+Grotesk:wght@600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="css/index.css" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#000829",
                        "secondary": "#047857",
                        "on-primary": "#ffffff",
                        "on-secondary": "#0c003f",
                        "accent-azul": "#007380",
                        "surface": "#f9f9fc",
                        "on-surface": "#1a1c1e",
                        "on-surface-variant": "#43474f",
                        "outline-variant": "#c3c6d1",
                        "surface-container": "#f0f2f5"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "1rem",
                        "2xl": "1.5rem"
                    },
                    "spacing": {
                        "base": "8px",
                        "grid-gap": "24px",
                        "margin-desktop": "64px",
                        "gutter": "24px",
                        "margin-mobile": "16px"
                    },
                    "fontFamily": {
                        "headline-lg": ["Manrope"],
                        "body-lg": ["Manrope"],
                        "body-md": ["Manrope"],
                        "headline-lg-mobile": ["Manrope"],
                        "label-bold": ["Hanken Grotesk"],
                        "button-text": ["Hanken Grotesk"],
                        "headline-xl": ["Manrope"]
                    },
                    "fontSize": {
                        "headline-lg": ["32px", {
                            "lineHeight": "40px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "700"
                        }],
                        "body-lg": ["18px", {
                            "lineHeight": "28px",
                            "fontWeight": "400"
                        }],
                        "body-md": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "400"
                        }],
                        "headline-lg-mobile": ["24px", {
                            "lineHeight": "32px",
                            "fontWeight": "700"
                        }],
                        "label-bold": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "700"
                        }],
                        "button-text": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "600"
                        }],
                        "headline-xl": ["56px", {
                            "lineHeight": "64px",
                            "letterSpacing": "-0.03em",
                            "fontWeight": "800"
                        }]
                    }
                },
            },
        }
    </script>
</head>

<body class="bg-pattern min-h-screen flex flex-col items-center justify-start p-margin-mobile md:p-margin-desktop overflow-x-hidden">

    <div id="preloader"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-white transition-opacity duration-1000">
        <div class="flex flex-col items-center">
            <div class="h-16 w-16 animate-spin rounded-full border-4 border-primary-azul border-t-primary"></div>

            <p class="mt-4 text-sm font-black uppercase tracking-widest text-accent-azul animate-pulse">
                Cargando sistema...
            </p>
        </div>
    </div>

    <main class="w-full max-w-6xl mx-auto flex flex-col items-center">
        <!--Header Title-->
        <header class="mb-16 text-center">
            <h1 class="font-headline-xl text-[40px] md:text-headline-xl hero-title mb-4 tracking-tight font-extrabold">
                Sistema de Consultas ISTU
            </h1>
            <div class="h-1.5 w-24 bg-secondary mx-auto mb-6 rounded-full"></div>
            <p class="font-body-lg text-body-md md:text-body-lg text-on-surface-variant max-w-xl mx-auto opacity-80">
                Consulta y visualización de información institucional.
            </p>
        </header>
        <!-- Refined Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full">
            <!--Card Template-->
            <a href="views/exonerados_apulo.php" class="group relative flex flex-col items-center p-10 min-h-[240px] bg-white rounded-2xl card-shadow border border-slate-200 transition-all duration-300 hover:border-secondary/50 hover:shadow-xl hover:-translate-y-2 overflow-hidden">
                <!--Corners-->
                <div class="card-corner-accent -top-10 -left-10"></div>
                <div class="card-corner-accent -bottom-10 -right-10"></div>
                <div class="mb-6 relative z-10">
                    <span class="material-symbols-outlined !text-5xl text-secondary icon-glow transition-transform duration-300 group-hover:scale-110" data-icon="park">park</span>
                </div>
                <div class="text-center relative z-10">
                    <h2 class="font-headline-2xl text-2xl text-on-secondary mb-2 font-extrabold">EXONERADOS APULO</h2>
                </div>
            </a>
            <a href="views/convenios.php" class="group relative flex flex-col items-center p-10 min-h-[240px] bg-white rounded-2xl card-shadow border border-slate-200 transition-all duration-300 hover:border-secondary/50 hover:shadow-xl hover:-translate-y-2 overflow-hidden">
                <div class="card-corner-accent -top-10 -left-10"></div>
                <div class="card-corner-accent -bottom-10 -right-10"></div>
                <div class="mb-6 relative z-10">
                    <span class="material-symbols-outlined !text-5xl text-secondary icon-glow transition-transform duration-300 group-hover:scale-110" data-icon="park">park</span>
                </div>
                <div class="text-center relative z-10">
                    <h2 class="font-headline-2xl text-2xl text-on-secondary mb-2 font-extrabold">CONVENIOS</h2>
                </div>
            </a>
        </div>
    </main>
    <!-- Aesthetic Accents -->
    <div class="fixed top-0 left-0 w-full h-1.5 bg-secondary z-50"></div>
    <div class="fixed bottom-0 left-0 w-full h-1.5 bg-primary z-50"></div>
    <script src="js/preloader.js"></script>
</body>

</html>