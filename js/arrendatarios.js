$(function () {
  //---------config tabla arrendatarios---------
  //---la tabla solo existe cuando ya se entró a un parque (antes se muestra "Ver mi parque")
  if ($("#tablaArrendatarios").length) {
    const tabla = crearTabla("#tablaArrendatarios", {
      order: [[0, "asc"]],
      columnDefs: [{ targets: 2, orderable: false }],
      language: {
        info: "Mostrando <strong>_START_–_END_</strong> de <strong>_TOTAL_</strong> arrendatarios",
        zeroRecords:
          '<div class="tabla-vacia"><strong>Sin resultados</strong><span>No hay arrendatarios que coincidan con la búsqueda.</span></div>',
        emptyTable:
          '<div class="tabla-vacia"><strong>Sin arrendatarios</strong><span>Este parque todavía no tiene arrendatarios registrados.</span></div>',
      },
    });

    //------buscador
    $("#buscadorCustom").on("input", function () {
      tabla.search(this.value).draw();
    });
  }

  //-------ventana de las clausulas del contrato
  $("#btnClausulas").on("click", function () {
    document.getElementById("modalClausulas").showModal();
  });

  //-------ventana de la clave del parque ("Ver mi parque")
  const modalParque = document.getElementById("modalParque");
  $("#btnVerParque").on("click", function () {
    modalParque.showModal();
  });
  //---si la clave fue incorrecta, PHP marca la ventana con data-abrir: se abre sola para mostrar el error
  if (modalParque && modalParque.hasAttribute("data-abrir")) {
    modalParque.showModal();
  }

  //-----botón del ojo: muestra u oculta la clave
  $("#verClave").on("click", function () {
    const campo = document.getElementById("clave");
    const oculta = campo.type === "password";
    campo.type = oculta ? "text" : "password";
    this.setAttribute("aria-label", oculta ? "Ocultar clave" : "Mostrar clave");
  });

  //-----close modal: los botones de cerrar y el clic en el fondo oscuro sirven para las dos ventanas
  //-----closest("dialog") = la ventana en la que está el botón
  $(".btn-cerrar-modal").on("click", function () {
    this.closest("dialog").close();
  });
  $("dialog.modal").on("click", function (e) {
    if (e.target === this) this.close();
  });
});
