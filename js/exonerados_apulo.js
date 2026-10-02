$(function () {
  //-------tabla de exonerados---
  var table = crearTabla("#tablaExonerados", {
    order: [[1, "asc"]],
    columnDefs: [
      { targets: [0, 2], orderable: false },
      { targets: 1, type: "string" },
    ],
    language: {
      zeroRecords:
        '<div class="tabla-vacia"><strong>Sin resultados</strong><span>Esta persona no aparece en el listado de exonerados.</span></div>',
    },
  });

  //---buscador personalizado: filtra mientras se escribe---
  $("#buscadorCustom").on("input", function () {
    table.search(this.value).draw();
  });
});
