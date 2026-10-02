$(function () {
  //---------config tabla arrendatarios---------
  var table = crearTabla("#tablaArrendatarios", {
    order: [[0, "asc"]],
    columnDefs: [{ targets: 2, orderable: false }],
    language: {
      info: "Mostrando <strong>_START_–_END_</strong> de <strong>_TOTAL_</strong> arrendatarios",
      zeroRecords:
        '<div class="tabla-vacia"><strong>Sin resultados</strong><span>No hay arrendatarios que coincidan con la búsqueda.</span></div>',
    },
  });

  //------buscador
  $("#buscadorCustom").on("input", function () {
    table.search(this.value).draw();
  });

  //-------ventana de las clausulas del contrato 
  const modal = document.getElementById("modalClausulas");
  $("#btnClausulas").on("click", function () {
    modal.showModal();
  });
  $(".btn-cerrar-modal").on("click", function () {
    modal.close();
  });
  //-----close modal
  $(modal).on("click", function (e) {
    if (e.target === modal) modal.close();
  });
});
