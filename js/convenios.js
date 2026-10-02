/*
 * CONSULTA PÚBLICA DE CONVENIOS
 */
$(function () {
  // ---------------------------------------------------------------------------
  // -------------------------CONFIGURACIÓN DE LA TABLA-------------------------
  // ---------------------------------------------------------------------------
  const tabla = crearTabla("#tablaConvenios", {
    order: [[0, "asc"]],
    columnDefs: [
      { targets: [6, 7], visible: false }, //----exoneracion y promocion ocultas
      { targets: 8, orderable: false },
    ],
    language: {
      info: "Mostrando <strong>_START_–_END_</strong> de <strong>_TOTAL_</strong> convenios",
      zeroRecords:
        '<div class="tabla-vacia"><strong>Sin resultados</strong><span>No hay convenios que coincidan con la búsqueda o el filtro.</span></div>',
    },
  });

  //-------buscador personalizado
  $("#buscadorCustom").on("input", function () {
    tabla.search(this.value).draw();
  });

  // ---------------------------------------------------------------------------
  // ---Filtros (Todos / Vigentes / No vigentes / Exoneración / Promoción)------
  // ---------------------------------------------------------------------------
  $(".tab").on("click", function () {
    const boton = $(this);

    //---------------------------------cambia el estilo del botón activo
    $(".tab").removeClass("activa").attr("aria-pressed", "false");
    boton.addClass("activa").attr("aria-pressed", "true");

    //-----limpia los filtros
    tabla.column(2).search("");
    tabla.column(6).search("");
    tabla.column(7).search("");

    //------aplica el filtro correspondiente al botón 
    if (boton.data("tipo") === "vigencia") {
      tabla.column(2).search("^" + boton.data("val") + "$", true, false);
    } else if (boton.data("tipo") === "exprom") {
      tabla.column(boton.data("col")).search("^SI$", true, false);
    }
    tabla.draw();
  });

  // ---------------------------------------------------------------------------
  // -------------------------DETALLE DEL CONVENIO------------------------------
  // ---------------------------------------------------------------------------
  const modal = document.getElementById("modalDetalle");
  const refGuardada = localStorage.getItem("modalDetalleRef");
  if (refGuardada) {
    //----accede a los datos de todas las filas
    const boton = $(tabla.rows().nodes())
      .find(".btn-detalle")
      .filter(function () {
        return $(this).data("convenio").ref === refGuardada;
      });
    if (boton.length) mostrarDetalle(boton.data("convenio"));
  }

  //----trae los datos del convenio del botón y los muestra en el modal para ver el detalle del convenio
  $(document).on("click", ".btn-detalle", function () {
    const datos = $(this).data("convenio");
    localStorage.setItem("modalDetalleRef", datos.ref);
    mostrarDetalle(datos);
  });

  //------------cerrar el modal de detalle del convenio
  $(".btn-cerrar-modal").on("click", function () {
    modal.close();
  });
  $(modal).on("click", function (e) {
    if (e.target === modal) modal.close();
  });
  modal.addEventListener("close", function () {
    localStorage.removeItem("modalDetalleRef");
  });

  //----------muestra el detalle del convenio en el modal
  function mostrarDetalle(d) {
    $("#m-ref").text(d.ref);
    $("#m-inst").text(d.inst);
    $("#m-sus").text(d.sus);
    $("#m-ven").text(d.ven);
    $("#m-plazo").text(d.plazo);

    //----------vigente o no vigente: cambia el texto y el color del círculo
    const vigente = d.vig === "SI";
    $("#m-estado")
      .text(vigente ? "Vigente" : "No Vigente")
      .toggleClass("estado-vigente", vigente)
      .toggleClass("estado-vencido", !vigente);

    mostrarSeccion("#padre-desc", "#m-desc", d.desc);
    mostrarSeccion("#padre-ex", "#m-ex", d.ex);
    mostrarSeccion("#padre-prom", "#m-prom", d.prom);
    mostrarSeccion("#padre-com", "#m-com", d.com);
    alternarColores();

    if (!modal.open) modal.showModal();
  }

  //----------muestra u oculta una sección del detalle según si hay texto
  function mostrarSeccion(idSeccion, idTexto, texto) {
    const hayTexto = texto && String(texto).trim() !== "";
    if (hayTexto) $(idTexto).html(texto);
    $(idSeccion).toggle(Boolean(hayTexto));
  }

  //----------colores para los circulos de las secciones del dettalle del convenio
  function alternarColores() {
    $(".detalle-seccion")
      .filter(function () {
        return this.style.display !== "none";
      })
      .each(function (i) {
        $(this)
          .find(".detalle-circulo")
          .toggleClass("circulo-azul", i % 2 === 0)
          .toggleClass("circulo-verde", i % 2 === 1);
      });
  }
});
