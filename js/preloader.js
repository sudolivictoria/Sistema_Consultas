window.addEventListener("load", function () {
  const preloader = document.getElementById("preloader");
  preloader.classList.add("opacity-0");
  document.body.classList.remove("overflow-hidden");
  document.body.style.overflow = "auto";
  setTimeout(() => {
    preloader.style.display = "none";
  }, 1000);
});
