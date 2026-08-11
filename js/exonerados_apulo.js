$(document).ready(function () {
  var table = $("#tablaExonerados").DataTable({
    ordering: true,
    pagingType: "simple",
    order: [[1, "asc"]],
    columnDefs: [
      { targets: [0, 2], orderable: false },
      { targets: 1, type: "string" }
    ],
    language: {
      lengthMenu: "Mostrar _MENU_ registros",
      search: "Buscar:",
      processing: "Procesando...",
      zeroRecords: `
                    <div class="flex flex-col items-center justify-center py-10">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">search_off</span>
                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">No se encontraron resultados</p>
                    </div>`,
      emptyTable: `
                    <div class="flex flex-col items-center justify-center py-10">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">folder_off</span>
                        <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">No hay datos disponibles</p>
                    </div>`,
      info: "Mostrando del _START_ al _END_ de _TOTAL_ registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      infoEmpty: "Mostrando 0 registros",
      paginate: {
        next: '<span class="material-symbols-outlined text-[18px] leading-none">chevron_right</span>',
        previous:
          '<span class="material-symbols-outlined text-[18px] leading-none">chevron_left</span>',
      },
    },
    dom: 'rt<"flex flex-col md:flex-row items-center justify-between px-10 py-8 gap-4"ip>',
    pageLength: 5,
  });

  $("#buscadorCustom").on("keyup", function () {
    table.search(this.value).draw();
  });
});
