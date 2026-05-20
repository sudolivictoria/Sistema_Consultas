<?php include '../conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Exonerados - Apulo</title>

    <!----Librerias y estilos------>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="../css/exonerados_apulo.css" rel="stylesheet" />

    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary-azul": "#000829",
                        "primary-verde": "#002e21",
                        "accent-azul": "#007380",
                        "accent-verde": "#a7f3d0",
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

<body class="font-body antialiased">

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

    <main class="relative z-10 pt-16 pb-24 px-4 md:px-12 max-w-6xl mx-auto">
        <div class="mb-6 flex items-center justify-between gap-4 w-full">
            <div class="text-left">
                <span class="inline-block bg-accent-azul/10 text-primary-azul px-5 py-1.5 rounded-full text-sm font-black tracking-widest uppercase mb-4 shadow-sm border border-primary-azul/10">
                    ISTU • APULO
                </span>
                <h1 class="text-4xl md:text-5xl text-[#05013B] font-black tracking-tight leading-tight">Consulta de <span class="text-accent-verde-intenso">Exonerados</span></h1>
            </div>

            <a href="../index.php" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-slate-600 bg-primary-verde text-white border border-slate-200 shadow-sm transition-all duration-300 hover:text-primary-verde hover:border-primary-verde/30 hover:bg-slate-50 hover:-translate-x-1 group">
                <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:-translate-x-0.5">arrow_back</span>
                Inicio
            </a>

        </div>

        <!--barra de busqueda-->
        <div class="relative group mb-8">
            <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-primary-azul/30 group-focus-within:text-primary-verde transition-colors text-3xl">search</span>
            </div>

            <!---------Buscador Personalizado--------->
            <input id="buscadorCustom"
                class="w-full glass-card rounded-2xl py-4 pl-14 pr-6 text-base text-slate-900 placeholder:text-slate-400 outline-none border border-slate-200 focus:ring-4 focus:ring-accent-azul/20 transition-all duration-300 focus:border-primary-verde shadow-sm font-bold"
                placeholder="Escribe un nombre o número de DUI..." type="text" autocomplete="off" />
        </div>

        <div class="glass-card rounded-3xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto custom-scrollbar">
                <table id="tablaExonerados" class="w-full text-left border-collapse table-fixed">
                    <thead class="bg-accent-verde-intenso/10">
                        <tr class="text-primary-verde text-lg uppercase tracking-[0.25em] font-black">
                            <th class="w-[20%] px-10 py-6 border border-slate-200/50">DUI</th>
                            <th class="w-[50%] px-10 py-6 border border-slate-200/50">Nombre Completo</th>
                            <th class="w-[30%] px-10 py-6 border border-slate-200/50">Comunidad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/40">
                        <?php
                        $sql = "SELECT dui, nombre, comunidad FROM exonerados_apulo";
                        $resultado = $conexion->query($sql);

                        if ($resultado && $resultado->num_rows > 0) {
                            while ($fila = $resultado->fetch_assoc()) {
                                echo '<tr class="hover:bg-accent-azul/5 transition-colors duration-200">';

                                //-------------------DUI-------------------
                                echo '<td class="px-8 py-7 border border-slate-200/50">';
                                echo '<span class="font-extrabold text-primary-azul text-lg tracking-tight block truncate">' . htmlspecialchars($fila['dui']) . '</span>';
                                echo '</td>';

                                //-------------------NOMBRE-------------------
                                echo '<td class="px-8 py-7 border border-slate-200/50">';
                                echo '<span class="font-extrabold text-primary-azul text-lg tracking-tight block truncate" title="' . htmlspecialchars($fila['nombre']) . '">' . htmlspecialchars($fila['nombre']) . '</span>';
                                echo '</td>';

                                //-------------------COMUNIDAD-------------------
                                echo '<td class="px-8 py-7 border border-slate-200/50">';
                                echo '<span class="font-extrabold text-primary-azul text-lg tracking-tight block truncate" title="' . htmlspecialchars($fila['comunidad']) . '">' . (!empty($fila['comunidad']) ? htmlspecialchars($fila['comunidad']) : 'No registrada') . '</span>';
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
    <script src="../js/exonerados_apulo.js"></script>
</body>

</html>