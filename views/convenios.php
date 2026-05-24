<?php include '../conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Convenios - ISTU</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="../css/convenios.css" rel="stylesheet" />

    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary-azul": "#000e4d",
                        "primary-verde": "#002e21",
                        "accent-azul": "#007380",
                        "accent-verde-intenso": "#047857"
                    },
                    fontFamily: {
                        "body": ["Inter", "sans-serif"]
                    }
                }
            }
        }
    </script>
</head>

<body class="font-body antialiased text-slate-800">
    <!---Preloader-->
    <div id="preloader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white transition-opacity duration-1000">
        <div class="flex flex-col items-center">
            <div class="h-16 w-16 animate-spin rounded-full border-4 border-slate-200 border-t-primary-azul"></div>
            <p class="mt-4 text-sm font-black uppercase tracking-widest text-accent-azul animate-pulse">
                Cargando sistema...
            </p>
        </div>
    </div>

    <main class="relative z-10 pt-16 pb-24 px-4 md:px-8 max-w-7xl mx-auto">

        <div class="mb-4 flex items-center justify-between gap-4 w-full">
            <div class="text-left">
                <h1 class="text-4xl text-primary-azul font-black tracking-tight">
                    Consulta de <span class="text-accent-verde-intenso">Convenios</span>
                </h1>
            </div>
            <a href="../index.php" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-slate-600 bg-primary-verde text-white border border-slate-200 shadow-sm transition-all duration-300 hover:text-primary-verde hover:border-primary-verde/30 hover:bg-slate-50 hover:-translate-x-1 group">
                <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:-translate-x-0.5">arrow_back</span>
                Inicio
            </a>
        </div>

        <p class="font-body-lg text-body-md md:text-body-lg text-on-surface-variant w-full opacity-50 pt-1 mb-6">
            Para visualizar la información completa, haga clic en el ícono de visualización ubicado en la columna Detalle.
        </p>

        <!--buscador-->
        <div class="flex flex-row gap-3 mb-4 w-full">
            <div class="relative group flex-1">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-slate-400 text-xl">search</span>
                </div>
                <input id="buscadorCustom" class="w-full glass-card rounded-xl py-3 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 outline-none border border-slate-200 focus:ring-4 focus:ring-accent-azul/20 transition-all duration-300 focus:border-primary-verde shadow-sm font-bold" placeholder="Buscar institución o convenio..." type="text" autocomplete="off" />
            </div>

            <!--filtros de vigencia-->
            <div class="glass-card p-1 rounded-xl flex items-center gap-1 border border-slate-200 shadow-sm shrink-0 bg-white">
                <button data-filter="TODOS" class="filtro-vigencia px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200 bg-primary-azul text-white shadow-sm">
                    Todos
                </button>
                <button data-filter="SI" class="filtro-vigencia px-4 py-2 rounded-lg text-xs font-bold text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition-all duration-200">
                    Vigentes
                </button>
                <button data-filter="NO" class="filtro-vigencia px-4 py-2 rounded-lg text-xs font-bold text-slate-600 hover:text-rose-700 hover:bg-rose-50 transition-all duration-200">
                    No Vigentes
                </button>
            </div>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden shadow-lg w-full bg-white">
            <div class="w-full overflow-x-auto">
                <table id="tablaConvenios" class="w-full text-left border-collapse table-layout-fixed">
                    <thead>
                        <tr class="bg-accent-verde-intenso/10 text-primary-verde text-[13.5px] uppercase tracking-wider font-extrabold border-b border-slate-200">
                            <th class="px-3 py-3.5 w-[7%] border-r border-slate-200/50 text-center">Ref.</th>
                            <th class="px-4 py-3.5 w-[35%] border-r border-slate-200/50 text-left">Institución</th>
                            <th class="px-2 py-3.5 w-[9%] border-r border-slate-200/50 text-center">Vigencia</th>
                            <th class="px-3 py-3.5 w-[12%] border-r border-slate-200/50 text-center">Suscripción</th>
                            <th class="px-3 py-3.5 w-[12%] border-r border-slate-200/50 text-center">Vencimiento</th>
                            <th class="px-3 py-3.5 w-[17%] border-r border-slate-200/50 text-center">Plazo</th>
                            <th class="px-4 py-3.5 w-[9%] text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/70 text-[13px] divide-y divide-slate-100">
                        <?php
                        $sql = "SELECT referencia, institucion, vigencia, descripcion, suscripcion, plazo, vencimiento, comentario FROM convenios";
                        $resultado = $conexion->query($sql);

                        if ($resultado && $resultado->num_rows > 0) {
                            while ($fila = $resultado->fetch_assoc()) {
                                echo '<tr class="hover:bg-accent-azul/5 transition-colors duration-300 group text-[13.5px]">';

                                //--referencia
                                echo '<td class="px-3 py-3 text-primary-azul text-center leading-tight border-r border-b border-slate-200/50 font-extrabold bg-slate-50/40">' . htmlspecialchars($fila['referencia']) . '</td>';

                                //--institucion
                                echo '<td class="px-4 py-3 text-left leading-tight border-r border-b border-slate-200/50 font-extrabold group-hover:text-primary-azul transition-colors">' . htmlspecialchars($fila['institucion']) . '</td>';

                                //--vigencia 
                                $vigencia = trim(strtoupper($fila['vigencia']));
                                $clase_badge = ($vigencia === 'SI') ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-rose-50 text-rose-700 border-rose-200/60';
                                echo '<td class="px-2 py-3 text-center border-r border-b border-slate-200/50">';
                                echo '<span class="inline-block px-2 py-0.5 text-[12px] font-extrabold rounded-md border ' . $clase_badge . '">' . $vigencia . '</span>';
                                echo '</td>';

                                //--suscripcion
                                $fecha_suscripcion = (!empty($fila['suscripcion']) && $fila['suscripcion'] !== '0000-00-00') ? (new DateTime($fila['suscripcion']))->format('d-m-Y') : '—';
                                echo '<td class="px-3 py-3 text-center border-r border-b border-slate-200/50 font-extrabold">' . $fecha_suscripcion . '</td>';

                                //--vencimiento 
                                $fecha_vencimiento = (!empty($fila['vencimiento']) && $fila['vencimiento'] !== '0000-00-00') ? (new DateTime($fila['vencimiento']))->format('d-m-Y') : '—';
                                echo '<td class="px-3 py-3 text-center border-r border-b border-slate-200/50 font-extrabold">' . $fecha_vencimiento . '</td>';

                                //--plazo
                                $plazo = !empty($fila['plazo']) ? htmlspecialchars($fila['plazo']) : '—';
                                echo '<td class="px-3 py-3 text-center border-r border-b border-slate-200/50 font-extrabold">' . $plazo . '</td>';

                                //--acción (data-attributes para el modal)
                                echo '<td class="px-4 py-3 text-center border-b border-slate-200/50">';
                                echo '  <button type="button" 
                                            class="btn-detalle inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-accent-azul hover:bg-primary-azul transition-all duration-300 shadow-sm focus:outline-none"
                                            data-ref="' . htmlspecialchars($fila['referencia']) . '"
                                            data-inst="' . htmlspecialchars($fila['institucion']) . '"
                                            data-vig="' . $vigencia . '"
                                            data-sus="' . $fecha_suscripcion . '"
                                            data-ven="' . $fecha_vencimiento . '"
                                            data-plazo="' . $plazo . '"
                                            data-desc="' . htmlspecialchars($fila['descripcion']) . '"
                                            data-com="' . htmlspecialchars($fila['comentario']) . '">';
                                echo '      <span class="material-symbols-outlined !text-base">visibility</span>';
                                echo '  </button>';
                                echo '</td>';

                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Aesthetic Accents -->
    <div class="fixed top-0 left-0 w-full h-1.5 bg-accent-verde-intenso z-50"></div>
    <div class="fixed bottom-0 left-0 w-full h-1.5 bg-primary-azul z-50"></div>

    <!-- Modal de Detalle -->
    <div id="modalDetalle" class="fixed inset-0 z-[10000] hidden flex items-center justify-center px-4 overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300" id="cerrarModalFondo"></div>

        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-95 duration-300 max-h-[90vh] flex flex-col z-10">

            <div class="bg-gradient-to-r from-[#0c003f] to-[#047857] px-6 py-4 text-white flex items-center justify-between shadow-md shrink-0">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-300 text-3xl">description</span>
                    <div>
                        <span id="m-ref" class="font-black text-white px-1.5 py-1 rounded text-lg tracking-tight leading-tight uppercase pb-1">---</span>
                    </div>
                </div>
                <button id="btnCerrarX" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 p-1.5 rounded-full transition-all focus:outline-none">
                    <span class="material-symbols-outlined !text-xl block">close</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5 text-slate-700 text-sm">

                <!--institución u organismo-->
                <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl">
                    <span class="text-[12px] font-black uppercase tracking-wider text-accent-azul block mb-1">Institución u Organismo</span>
                    <p id="m-inst" class="text-base font-black text-primary-azul leading-snug">---</p>
                </div>

                <!--vigencia, suscripción, vencimiento, plazo-->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="border border-slate-100 p-3 rounded-xl bg-slate-50/50">
                        <span class="text-[12px] font-black uppercase text-accent-azul block mb-1">Estado</span>
                        <div id="m-vig">---</div>
                    </div>
                    <div class="border border-slate-100 p-3 rounded-xl bg-slate-50/50">
                        <span class="text-[12px] font-black uppercase text-accent-azul block mb-1">Suscripción</span>
                        <p id="m-sus" class="font-black text-slate-800">---</p>
                    </div>
                    <div class="border border-slate-100 p-3 rounded-xl bg-slate-50/50">
                        <span class="text-[12px] font-black uppercase text-accent-azul block mb-1">Vencimiento</span>
                        <p id="m-ven" class="font-black text-slate-800">---</p>
                    </div>
                    <div class="border border-slate-100 p-3 rounded-xl bg-slate-50/50">
                        <span class="text-[12px] font-black uppercase text-accent-azul block mb-1">Plazo</span>
                        <p id="m-plazo" class="font-black text-slate-800">---</p>
                    </div>
                </div>

                <!--descripción del convenio y comentarios-->
                <div class="space-y-1.5">
                    <span class="text-[12px] font-black uppercase tracking-wider text-accent-azul block">Descripción del Convenio u Objeto</span>
                    <div id="m-desc" class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl leading-relaxed font-black text-slate-800 break-words text-[16px] max-h-40 overflow-y-auto whitespace-pre-line">
                        ---
                    </div>
                </div>

                <div class="space-y-1.5">
                    <span class="text-[12px] font-black uppercase tracking-wider text-accent-azul block">Comentarios y Aclaraciones Adicionales</span>
                    <div id="m-com" class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl leading-relaxed font-black text-slate-800 break-words text-[16px] whitespace-pre-line">
                        ---
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end shrink-0">
                <button id="btnCerrarFooter" class="px-5 py-2 text-sm font-bold bg-primary-verde hover:bg-accent-verde-intenso/70 text-white rounded-xl transition-all shadow-sm focus:outline-none">
                    Cerrar Ventana
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="../js/preloader.js"></script>
    <script src="../js/convenios.js"></script>
</body>
</html>