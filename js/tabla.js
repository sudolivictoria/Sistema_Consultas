/*
 * ============================================================================
 * CONFIGURACIÓN COMÚN DE LAS TABLAS (DataTables)
 * ============================================================================
 * const tabla = crearTabla("#miTabla", { pageLength: 10 });
 * El segundo parámetro cambia solo lo que esa tabla necesita distinto;
 * $.extend(true, ...) mezcla esas opciones con las de aquí.
 */
function crearTabla(selector, opciones) {
  const chevronIzq = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"></path></svg>';
  const chevronDer = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"></path></svg>';

  const base = {
    autoWidth: false, 
    pagingType: "simple", 
    pageLength: 5,
    dom: 'r<"tabla-scroll"t><"tabla-pie"ip>',
    language: {
      info: "Mostrando <strong>_START_–_END_</strong> de <strong>_TOTAL_</strong> registros",
      infoEmpty: "Sin registros",
      infoFiltered: "",
      zeroRecords: '<div class="tabla-vacia"><strong>Sin resultados</strong><span>No hay registros que coincidan con la búsqueda.</span></div>',
      emptyTable: '<div class="tabla-vacia"><strong>Sin registros</strong><span>Todavía no hay información para mostrar.</span></div>',
      paginate: { previous: chevronIzq, next: chevronDer },
      aria: { paginate: { previous: "Página anterior", next: "Página siguiente" } },
    },
  };

  return $(selector).DataTable($.extend(true, base, opciones));
}
