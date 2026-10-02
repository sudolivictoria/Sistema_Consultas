/*
 * PRELOADER: la pantalla de "Cargando…" de todas las páginas.
 * "load" se dispara cuando la página terminó de cargar TODO 
 */
window.addEventListener("load", function () {
  const preloader = document.getElementById("preloader");
  if (!preloader) return;

  preloader.classList.add("oculto"); 
  setTimeout(function () {
    preloader.remove();
  }, 400);
});
