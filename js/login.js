/*
 * LOGIN: mostrar / ocultar la contraseña con el botón del ojo
 */
const botonVer = document.getElementById("verClave");
const campoClave = document.getElementById("clave");

botonVer.addEventListener("click", function () {
  const oculta = campoClave.type === "password";
  campoClave.type = oculta ? "text" : "password";
  botonVer.setAttribute("aria-label", oculta ? "Ocultar contraseña" : "Mostrar contraseña");
});
