$(function () {
  var table = $("#tablaConvenios").DataTable({
    ordering: true,
    info: true,
    pageLength: 5,
    pagingType: "simple",
    language: {
      processing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      search: "Buscar:",
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

  // ---buscador personalizado---
  $("#buscadorCustom").on("keyup", function () {
    table.search(this.value).draw();
  });

  // ---- Filtros Globales (Vigencia, Exoneración, Promoción) ----------
  $(".btn-filtro-global").on("click", function () {
    var tipo = $(this).data("tipo");

    // ----limpiar colores de TODOS los botones----
    $(".btn-filtro-global")
      .removeClass("bg-primary-azul text-white shadow-sm")
      .addClass("text-slate-600");

    // ---pintar solo el botón clickeado----
    $(this)
      .addClass("bg-primary-azul text-white shadow-sm")
      .removeClass("text-slate-600");

    // ---limpiar las 3 columnas de filtro en DataTables por precaución----
    table.column(2).search(""); // ----Vigencia
    table.column(6).search(""); // ---Columna Exoneración
    table.column(7).search(""); // ----Columna Promoción

    // 4. Aplicar el filtro correspondiente según el botón
    if (tipo === "vigencia") {
      var valor = $(this).data("val");
      table.column(2).search("^" + valor + "$", true, false);
    } else if (tipo === "exprom") {
      var columna = $(this).data("col");
      table.column(columna).search("^SI$", true, false);
    }
    // ----redibujar la tabla con los nuevos datos
    table.draw();
  });

  // ==========================================================================
  // -----------------verificar si el modal esta abierto--------------------
  // ==========================================================================
  const estadoModal = localStorage.getItem("modalDetalleEstado");
  if (estadoModal === "abierto") {
    const ref = localStorage.getItem("modal_ref");
    const desc = localStorage.getItem("modal_desc");
    const ex = localStorage.getItem("modal_ex");
    const prom = localStorage.getItem("modal_prom");
    const com = localStorage.getItem("modal_com");

    $("#m-ref").text(ref || "---");
    gestionarBloque("#padre-desc", "#m-desc", desc);
    gestionarBloque("#padre-ex", "#m-ex", ex);
    gestionarBloque("#padre-prom", "#m-prom", prom);
    gestionarBloque("#padre-com", "#m-com", com);

    //-------------forzar apertura del modal retenido----------------------------
    $("#modalDetalle").removeClass("hidden").addClass("flex");
    $("body").addClass("overflow-hidden");
  }

  // ----------------------------modal detalle convenio----------------------------------
  $(document).on("click", ".btn-detalle", function () {
    const button = $(this);

    //************recolectar datos*****************/
    const refText = button.data("ref") || "---";
    const descText = button.data("desc") || "";
    const exText = button.data("ex") || "";
    const promText = button.data("prom") || "";
    const comText = button.data("com") || "";

    // ==========================================================================
    // ----------guardar datos actuales en la memoria del navegador--------------
    // ==========================================================================
    localStorage.setItem("modalDetalleEstado", "abierto");
    localStorage.setItem("modal_ref", refText);
    localStorage.setItem("modal_desc", descText);
    localStorage.setItem("modal_ex", exText);
    localStorage.setItem("modal_prom", promText);
    localStorage.setItem("modal_com", comText);

    //----Inyectar datos en la vista usando la función optimizada-----
    $("#m-ref").text(refText);
    gestionarBloque("#padre-desc", "#m-desc", descText);
    gestionarBloque("#padre-ex", "#m-ex", exText);
    gestionarBloque("#padre-prom", "#m-prom", promText);
    gestionarBloque("#padre-com", "#m-com", comText);

    // ----show modal-----
    $("#modalDetalle").removeClass("hidden").addClass("flex");
    $("body").addClass("overflow-hidden"); // ---evitar scroll del fondo
  });
});

//----------FUNCION PARA CONVERTIR TEXTO EN MAYUSCULA A "Sentence case"------------
function toSentenceCase(str) {
  if (!str) return "";
  let resultado = str
    .toLowerCase()
    .replace(/(^\s*\w|[.!?]\s*\w)/g, (c) => c.toUpperCase());

  const siglas = ["ISTU", "MITUR", "MOP", "SV", "ISSS", "ONU"];
  siglas.forEach((sigla) => {
    const regex = new RegExp(`\\b${sigla}\\b`, "gi");
    resultado = resultado.replace(regex, sigla);
  });

  return resultado;
}

//----------FUNCION PARA MOSTRAR U OCULTAR UN BLOQUE------------
function gestionarBloque(idPadre, idHijo, texto) {
  if (texto && texto.trim() !== "") {
    $(idHijo).html(toSentenceCase(texto));
    $(idPadre).show();
  } else {
    $(idPadre).hide();
  }
}

// ----close modal-----
function cerrarModal() {
  $("#modalDetalle").addClass("hidden").removeClass("flex");
  $("body").removeClass("overflow-hidden");

  // ==========================================================================
  // ------------------limpiar memoria-----------------------------------------
  // ==========================================================================
  localStorage.removeItem("modalDetalleEstado");
  localStorage.removeItem("modal_ref");
  localStorage.removeItem("modal_desc");
  localStorage.removeItem("modal_ex");
  localStorage.removeItem("modal_prom");
  localStorage.removeItem("modal_com");
}

// ---close modal buttons---
$(document).on(
  "click",
  "#btnCerrarX, #btnCerrarFooter, #cerrarModalFondo",
  function () {
    cerrarModal();
  },
);

// ----close modal with Escape key---
$(document).on("keydown", function (e) {
  if (e.key === "Escape") {
    cerrarModal();
  }
});