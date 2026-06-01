$(function () {
  //---configuracion datatable---
  var table = $("#tablaConvenios").DataTable({
    ordering: true,
    info: true,
    pageLength: 5,
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
      zeroRecords: `
        <div class="flex flex-col items-center justify-center py-12">
            <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">search_off</span>
            <p class="text-slate-500 font-bold text-sm">No se encontraron convenios con ese criterio</p>
        </div>`,
      emptyTable: `
        <div class="flex flex-col items-center justify-center py-12">
            <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">folder_off</span>
            <p class="text-slate-500 font-bold text-sm">No hay registros de convenios disponibles</p>
        </div>`,
      info: "Mostrando _START_ a _END_ de _TOTAL_ convenios",
      infoFiltered: "(filtrado)",
      infoEmpty: "0 registros",
      paginate: {
        next: '<span class="material-symbols-outlined text-[18px] leading-none">chevron_right</span>',
        previous:
          '<span class="material-symbols-outlined text-[18px] leading-none">chevron_left</span>',
      },
    },
    dom: 'rt<"flex flex-row items-center justify-between px-4 py-3 border-t border-slate-200 bg-slate-50 text-xs gap-4"ip>',
  });

  //---buscador personalizado---
  $("#buscadorCustom").on("keyup", function () {
    table.search(this.value).draw();
  });

  $; //---- Filtros Globales (Vigencia, Exoneración, Promoción) ----------
  $(".btn-filtro-global").on("click", function () {
    var tipo = $(this).data("tipo"); // 'todos', 'vigencia', o 'exprom'

    // 1. Limpiar colores de TODOS los botones
    $(".btn-filtro-global")
      .removeClass("bg-primary-azul text-white shadow-sm")
      .addClass("text-slate-600");

    // 2. Pintar solo el botón clickeado
    $(this)
      .addClass("bg-primary-azul text-white shadow-sm")
      .removeClass("text-slate-600");

    // 3. Limpiar SIEMPRE las 3 columnas de filtro en DataTables por precaución
    table.column(2).search(""); // Columna Vigencia
    table.column(6).search(""); // Columna Exoneración
    table.column(7).search(""); // Columna Promoción

    // 4. Aplicar el filtro correspondiente según el botón
    if (tipo === "vigencia") {
      var valor = $(this).data("val");
      table.column(2).search("^" + valor + "$", true, false);
    } else if (tipo === "exprom") {
      var columna = $(this).data("col");
      table.column(columna).search("^SI$", true, false);
    }
    // Si el tipo es "todos", no hacemos nada extra porque ya limpiamos arriba.

    // 5. Redibujar la tabla con los nuevos datos
    table.draw();
  });

  //------modal detalle convenio------
  $(document).on("click", ".btn-detalle", function () {
    const button = $(this);

    //----data attributes-----
    $("#m-ref").text(button.data("ref"));


     //---descripcion-----
    const descText = button.data("desc");
    if (descText && descText.trim() !== "") {
        $("#m-desc").html(descText); // Usamos .html() por si decides meter formato luego
    } else {
        $("#m-desc").html('<span class="inline-flex items-center gap-1.5 px-1 py-1 text-slate-500 font-bold text-xs"><span class="material-symbols-outlined !text-sm">block</span> No cuenta con descripción</span>');
    }


    //---exoneracion-----
    const exText = button.data("ex");
    if (exText && exText.trim() !== "") {
        $("#m-ex").html(exText); // Usamos .html() por si decides meter formato luego
    } else {
        $("#m-ex").html('<span class="inline-flex items-center gap-1.5 px-1 py-1 text-slate-500 font-bold text-xs"><span class="material-symbols-outlined !text-sm">block</span> No cuenta con exoneración</span>');
    }

    //---promocion-----
    const promText = button.data("prom");
    if (promText && promText.trim() !== "") {
        $("#m-prom").html(promText);
    } else {
        $("#m-prom").html('<span class="inline-flex items-center gap-1.5 px-1 py-1 text-slate-500 font-bold text-xs"><span class="material-symbols-outlined !text-sm">block</span> No cuenta con promoción</span>');
    }

    //---commetarios-----
    const comText = button.data("com");
    if (comText && comText.trim() !== "") {
        $("#m-com").text(comText);
    } else {
         $("#m-com").html('<span class="inline-flex items-center gap-1.5 px-1 py-1 text-slate-500 font-bold text-xs"><span class="material-symbols-outlined !text-sm">block</span> No cuenta con comentarios</span>');
    }

    //----show modal-----
    $("#modalDetalle").removeClass("hidden").addClass("flex");
    $("body").addClass("overflow-hidden"); //---evitar scroll del fondo al abrir el modal
  });

  //----close modal-----
  function cerrarModal() {
    $("#modalDetalle").addClass("hidden").removeClass("flex");
    $("body").removeClass("overflow-hidden");
  }

  //---close modal buttons---
  $(document).on(
    "click",
    "#btnCerrarX, #btnCerrarFooter, #cerrarModalFondo",
    function () {
      cerrarModal();
    },
  );

  //----close modal with Escape key---
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      cerrarModal();
    }
  });
});
