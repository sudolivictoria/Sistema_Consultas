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
                        "primary-azul": "#000829",
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
    <div id="preloader"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-white transition-opacity duration-1000">
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

        <div class="flex flex-row gap-3 mb-4 w-full">
            <!---------Buscador--------->
            <div class="relative group flex-1">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-slate-400 text-xl">search</span>
                </div>
                <input id="buscadorCustom"
                    class="w-full glass-card rounded-xl py-3 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 outline-none border border-slate-200 focus:ring-4 focus:ring-accent-azul/20 transition-all duration-300 focus:border-primary-verde shadow-sm font-bold"
                    placeholder="Buscar institución o convenio..." type="text" autocomplete="off" />
            </div>

            <!---------Filtros de Vigencia--------->
            <div class="glass-card p-1 rounded-xl flex items-center gap-1 border border-slate-200 shadow-sm shrink-0">
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

        <div class="glass-card rounded-2xl overflow-hidden shadow-lg w-full">
            <div class="w-full">
                <table id="tablaConvenios" class="w-full text-left border-collapse table-layout-fixed shadow-sm rounded-2xl overflow-hidden border border-slate-200/60">
                    <thead>
                        <tr class="bg-accent-verde-intenso/10 text-primary-verde text-[13px] uppercase tracking-wider font-extrabold border-b border-slate-200">
                            <th class="px-3 py-3.5 w-[7%] border-r border-slate-200/50text-center">Ref.</th>
                            <th class="px-4 py-3.5 w-[18%] border-r border-slate-200/50 text-left">Institución</th>
                            <th class="px-2 py-3.5 w-[4%] border-r border-slate-200/50 text-center">Vigencia</th>
                            <th class="px-4 py-3.5 w-[24%] border-r border-slate-200/50 text-left">Descripción</th>
                            <th class="px-3 py-3.5 w-[5%] border-r border-slate-200/50 text-center">Suscripción</th>
                            <th class="px-3 py-3.5 w-[5%] border-r border-slate-200/50 text-center">Vencimiento</th>
                            <th class="px-3 py-3.5 w-[14%] border-r border-slate-200/50 text-center">Plazo</th>
                            <th class="px-4 py-3.5 w-[23%] text-left">Comentario</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/70 text-[13px] divide-y divide-slate-100">
                        <?php
                        $sql = "SELECT referencia, institucion, vigencia, descripcion, suscripcion, plazo, vencimiento, comentario FROM convenios";
                        $resultado = $conexion->query($sql);

                        if ($resultado && $resultado->num_rows > 0) {
                            while ($fila = $resultado->fetch_assoc()) {
                                echo '<tr class="hover:bg-accent-azul/5 transition-colors duration-150 group text-[12.5px]">';

                                //--referencia
                                echo '<td class="px-3 py-3 text-primary-azul text-center leading-tight border-r border-b border-slate-200/50 font-extrabold bg-slate-50/40">' . htmlspecialchars($fila['referencia']) . '</td>';

                                //--institucion
                                echo '<td class="px-4 py-3 text-left leading-tight border-r border-b border-slate-200/50 font-bold group-hover:text-primary-azul transition-colors">' . htmlspecialchars($fila['institucion']) . '</td>';

                                //--vigencia 
                                $vigencia = trim(strtoupper($fila['vigencia']));
                                $clase_badge = ($vigencia === 'SI') ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-red-100 text-red-900 border-red-200';
                                echo '<td class="px-2 py-3 text-center border-r border-b border-slate-200/50">';
                                echo '<span class="inline-block px-2 py-0.5 text-[12px] font-extrabold rounded-md border ' . $clase_badge . '">' . $vigencia . '</span>';
                                echo '</td>';

                                //--descripcion
                                echo '<td class="px-4 py-3 text-left leading-normal break-words border-r border-b border-slate-200/50 font-bold">' . htmlspecialchars($fila['descripcion']) . '</td>';

                                //--suscripcion
                                $fecha_suscripcion = (!empty($fila['suscripcion']) && $fila['suscripcion'] !== '0000-00-00')
                                    ? (new DateTime($fila['suscripcion']))->format('d-m-Y')
                                    : '';
                                echo '<td class="px-3 py-3 text-center border-r border-b border-slate-200/50 whitespace-nowrap font-bold">' . $fecha_suscripcion . '</td>';

                                //--vencimiento 
                                $fecha_vencimiento = (!empty($fila['vencimiento']) && $fila['vencimiento'] !== '0000-00-00')
                                    ? (new DateTime($fila['vencimiento']))->format('d-m-Y')
                                    : '';
                                echo '<td class="px-3 py-3 text-center border-r border-b border-slate-200/50 whitespace-nowrap font-bold">' . $fecha_vencimiento . '</td>';

                                //--plazo
                                echo '<td class="px-3 py-3 text-center border-r border-b border-slate-200/50 font-bold">' . htmlspecialchars($fila['plazo']) . '</td>';

                                //--comentario
                                echo '<td class="px-4 py-3 text-left border-b border-slate-200/50 leading-normal break-words font-bold">';
                                echo (!empty($fila['comentario'])) ? htmlspecialchars($fila['comentario']) : '<span class="not-italic">— — —</span>';
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="../js/preloader.js"></script>
    <script src="../js/convenios.js"></script>
</body>

</html>