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
        previous: '<span class="material-symbols-outlined text-[18px] leading-none">chevron_left</span>',
      },
    },
    dom: 'rt<"flex flex-row items-center justify-between px-4 py-3 border-t border-slate-200 bg-slate-50 text-xs gap-4"ip>',
  });

  //---buscador personalizado---
  $("#buscadorCustom").on("keyup", function () {
    table.search(this.value).draw();
  });

  //----filtro de vigencia----------
  $(".filtro-vigencia").on("click", function () {
    var valorFiltro = $(this).data("filter");
    
    $(".filtro-vigencia")
      .removeClass("bg-primary-azul text-white shadow-sm")
      .addClass("text-slate-900");
      
    $(this)
      .addClass("bg-primary-azul text-white shadow-sm")
      .removeClass("text-slate-900");

    if (valorFiltro === "TODOS") {
      table.column(2).search("").draw();
    } else {
      table
        .column(2)
        .search("^" + valorFiltro + "$", true, false)
        .draw();
    }
  });

  //------modal detalle convenio------
  $(document).on("click", ".btn-detalle", function () {
    const button = $(this);

    //----data attributes-----
    $("#m-ref").text(button.data("ref"));
    $("#m-inst").text(button.data("inst"));
    $("#m-sus").text(button.data("sus"));
    $("#m-ven").text(button.data("ven"));
    $("#m-plazo").text(button.data("plazo"));
    $("#m-desc").text(
      button.data("desc") ? button.data("desc") : "No se ha registrado una descripción detallada."
    );

    //---comentarios vacios-----
    if (button.data("com") && button.data("com").trim() !== "") {
      $("#m-com")
        .text(button.data("com"))
        .removeClass("not-italic text-slate-400");
    } else {
      $("#m-com")
        .text("SIN COMENTARIOS O ACLARACIONES ADICIONALES.")
        .addClass("not-italic text-slate-400");
    }

    //-----vigencia con estilos adicionales-----
    const vigencia = button.data("vig");
    if (vigencia === "SI") {
      $("#m-vig").html(
        '<span class="inline-block px-2.5 py-0.5 text-xs font-black rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">VIGENTE</span>'
      );
    } else {
      $("#m-vig").html(
        '<span class="inline-block px-2.5 py-0.5 text-xs font-black rounded-md bg-rose-50 text-rose-700 border border-rose-200">NO VIGENTE</span>'
      );
    }

    //----show modal-----
    $("#modalDetalle").removeClass("hidden").addClass("flex");
    $("body").addClass("overflow-hidden"); //---evitar scroll del fondo al abrir el modal
  });

  //----close modal-----
  function cerrarModal() {
    $("#modalDetalle").addClass("hidden").removeClass("flex");
  }

  //---close modal buttons---
  $(document).on("click", "#btnCerrarX, #btnCerrarFooter, #cerrarModalFondo", function () {
    cerrarModal();
  });

  //----close modal with Escape key---
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      cerrarModal();
    }
  });
});