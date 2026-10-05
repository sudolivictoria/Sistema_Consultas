/*
 * ============================================================================
 * CONFIGURACIÓN COMÚN DE LAS TABLAS (DataTables)
 * ============================================================================
 * const tabla = crearTabla("#miTabla", { pageLength: 10 });
 * El segundo parámetro cambia solo lo que esa tabla necesita distinto;
 * $.extend(true, ...) mezcla esas opciones con las de aquí.
 */
function crearTabla(selector, opciones) {
  //----icono() arma el SVG con el trazo que se le pase (mismo estilo que los íconos de PHP)
  const icono = (trazo) =>
    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + trazo + "</svg>";
  const chevronIzq = icono('<path d="M15 18l-6-6 6-6"></path>');
  const chevronDer = icono('<path d="M9 18l6-6-6-6"></path>');
  const dobleIzq = icono('<path d="M11 18l-6-6 6-6"></path><path d="M18 18l-6-6 6-6"></path>');   //---« ir al inicio
  const dobleDer = icono('<path d="M6 18l6-6-6-6"></path><path d="M13 18l6-6-6-6"></path>');    //---» ir al final

  const base = {
    autoWidth: false,
    pagingType: "full", //---"full" = botones « primera, ‹ anterior, › siguiente, » última (sin números)
    pageLength: 5,
    dom: 'r<"tabla-scroll"t><"tabla-pie"ip>',
    language: {
      info: "Mostrando <strong>_START_–_END_</strong> de <strong>_TOTAL_</strong> registros",
      infoEmpty: "Sin registros",
      infoFiltered: "",
      zeroRecords: '<div class="tabla-vacia"><strong>Sin resultados</strong><span>No hay registros que coincidan con la búsqueda.</span></div>',
      emptyTable: '<div class="tabla-vacia"><strong>Sin registros</strong><span>Todavía no hay información para mostrar.</span></div>',
      paginate: { first: dobleIzq, previous: chevronIzq, next: chevronDer, last: dobleDer },
      aria: { paginate: { first: "Primera página", previous: "Página anterior", next: "Página siguiente", last: "Última página" } },
    },
  };

  return $(selector).DataTable($.extend(true, base, opciones));
}
