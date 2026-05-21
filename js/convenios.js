$(document).ready(function () {
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

  $("#buscadorCustom").on("keyup", function () {
    table.search(this.value).draw();
  });

  $(".filtro-vigencia").on("click", function () {
    var valorFiltro = $(this).data("filter");
    $(".filtro-vigencia")
      .removeClass("bg-primary-azul text-white shadow-sm")
      .addClass("text-slate-600");
    $(this)
      .addClass("bg-primary-azul text-white shadow-sm")
      .removeClass("text-slate-600");

    if (valorFiltro === "TODOS") {
      table.column(2).search("").draw();
    } else {
      table
        .column(2)
        .search("^" + valorFiltro + "$", true, false)
        .draw();
    }
  });
});

