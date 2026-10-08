/*
 * ------------PANEL ADMINISTRATIVO----------------
 */
$(function () {
  // ---------------------------------------------------------------------------
  // ---------------------------ADMIN TABLE-------------------------------------
  // ---------------------------------------------------------------------------
  $(".tabla-admin").each(function () {
    const tabla = crearTabla(this, {
      order: [],
      pageLength: 5, 
      columnDefs: [{ orderable: false, targets: -1 }], //----columna acciones
      language: { info: "Página <strong>_PAGE_</strong> de _PAGES_" },
    });

    //------------buscador
    $("#buscarTabla").on("input", function () {
      tabla.search(this.value).draw();
    });
  });

  // ---------------------------------------------------------------------------
  // --------------------------VENTANA PARA ELIMINAR UN REGISTRO----------------
  // ---------------------------------------------------------------------------
  const dialogo = document.getElementById("dialogoEliminar");
  let formularioPendiente = null;

  $(document).on("submit", ".form-eliminar", function (e) {
    e.preventDefault(); //------no envía el formulario todavía, espera a que se confirme
    formularioPendiente = this;
    $("#tituloEliminar").text($(this).data("titulo") || "¿Eliminar este registro?");
    $("#nombreEliminar").text($(this).data("nombre"));
    dialogo.showModal();
  });

  $("#confirmarEliminar").on("click", function () {
    //---------.submit() nativo envía el formulario SIN volver a disparar el evento de arriba
    formularioPendiente.submit();
  });

  $("#cancelarEliminar").on("click", function () {
    dialogo.close();
  });

  // ---------------------------------------------------------------------------
  // -------------------NOTIFICACION TOAST-------------------------------------
  // ---------------------------------------------------------------------------
  const toast = $("#toast");
  if (toast.length) {
    const ocultar = function () {
      toast.addClass("oculto");
    };
    setTimeout(ocultar, 3500);
    toast.find(".toast-cerrar").on("click", ocultar);
  }

  // ---------------------------------------------------------------------------
  // -----------------------CERRAR EL PANEL (drawer)----------------------------
  // ---------------------------------------------------------------------------
  const cerrarDrawer = document.getElementById("cerrarDrawer");
  if (cerrarDrawer) {
    $(document).on("keydown", function (e) {
      if (e.key === "Escape" && !dialogo.open) {
        window.location = cerrarDrawer.href;
      }
    });
  }

  // ---------------------------------------------------------------------------
  // --------------------los cuadros de texto se ajustan a su contenido---------
  // ---------------------------------------------------------------------------
  //-------Se ajusta la altura al contenido (scrollHeight) al abrir y cada vez que se escribe.
  function ajustarAltura(textarea) {
    textarea.style.height = "auto"; 
    textarea.style.height = textarea.scrollHeight + 2 + "px"; 
  }
  $("textarea.entrada")
    .each(function () {
      ajustarAltura(this);
    })
    .on("input", function () {
      ajustarAltura(this);
    });

  // ---------------------------------------------------------------------------
  // ------vencimiento indefinido si el campo es disabled (checkbox)------------
  // ---------------------------------------------------------------------------
  $("#indefinido")
    .on("change", function () {
      $("#vencimiento").prop("disabled", this.checked);
      if (this.checked) $("#vencimiento").val("");
    })
    .trigger("change"); 

  // ---------------------------------------------------------------------------
  // --------------------------ETIQUETAS (tags)---------------------------------
  // ---------------------------------------------------------------------------
  //----se ve como etiquetas, pero lo que se envía es un <input type="hidden"> con el texto separado por comas
  $(".etiquetas").each(function () {
    const caja = $(this);
    const oculto = $("#" + caja.data("campo")); //----el input hidden
    const entrada = caja.find("input"); //----donde se escribe

    //----sincroniza el input hidden con las etiquetas visibles
    function sincronizar() {
      const lista = caja
        .find(".etiqueta-item")
        .map(function () {
          //----attr() y no data(): data() convierte "true", "null" o "1e3" en otros tipos; attr() siempre da el texto tal cual
          return $(this).attr("data-valor");
        })
        .get();
      oculto.val(lista.join(", "));
    }

    //----- agrega una etiqueta visible y sincroniza el input hidden
    function agregar(texto) {
      texto = texto.replace(/,/g, " ").trim(); //----quita comas y espacios al principio y al final
      if (!texto) return;
      const boton = $('<button type="button" class="etiqueta-quitar">')
        .attr("aria-label", "Quitar " + texto)
        .html('<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6L6 18"></path><path d="M6 6l12 12"></path></svg>');
      $('<span class="etiqueta-item">').attr("data-valor", texto).text(texto).append(boton).insertBefore(entrada);
      sincronizar();
    }

    //----------Enter o coma = agregar
    entrada.on("keydown", function (e) {
      if (e.key === "Enter" || e.key === ",") {
        e.preventDefault(); 
        agregar(this.value);
        this.value = "";
      } else if (e.key === "Backspace" && this.value === "") {
        caja.find(".etiqueta-item").last().remove();
        sincronizar();
      }
    });

    //----------clic en la X de una etiqueta = quitar
    caja.on("click", ".etiqueta-quitar", function () {
      $(this).parent().remove();
      sincronizar();
    });
    caja.closest("form").on("submit", function () {
      agregar(entrada.val());
      entrada.val("");
    });
  });

  // ---------------------------------------------------------------------------
  // -----------------SELECT CON BUSCADOR (ej. el parque del arrendatario)------
  // ---------------------------------------------------------------------------
  //----Al <select data-buscador> se le pone encima un campo de texto: al escribir se filtran las opciones.
  //----El <select> queda oculto pero sigue en el formulario: es lo que se envía a PHP.
  //----normalizar(): minúsculas y sin tildes, así "agua fria" encuentra "Agua Fría"
  const normalizar = (texto) => texto.normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase();

  $("select[data-buscador]").each(function (n) {
    const select = $(this);
    const idLista = "combo-lista-" + n;
    const caja = $('<div class="combo">');
    const entrada = $('<input type="text" class="entrada combo-entrada" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false">')
      .attr({ "aria-controls": idLista, placeholder: select.find('option[value=""]').text() });
    const lista = $('<ul class="combo-lista" role="listbox" hidden>').attr("id", idLista);

    //----una <li> por cada opción del select (menos la vacía "Seleccione…")
    select.find("option").each(function () {
      if (this.value === "") return;
      $('<li role="option">').text(this.text).attr("data-valor", this.value).appendTo(lista);
    });
    const vacio = $('<li class="combo-vacio">Sin resultados</li>').appendTo(lista);

    //----el <label for="parque_id"> ahora apunta al campo de texto
    entrada.attr("id", select.attr("id") + "_buscar");
    $('label[for="' + select.attr("id") + '"]').attr("for", entrada.attr("id"));

    //----sin "required" en el select oculto: el navegador no puede mostrar su aviso. PHP valida igual.
    select.prop("required", false).addClass("combo-oculto").after(caja);
    caja.append(entrada, lista);

    //----texto de la opción elegida en el select ("" si no hay)
    const textoElegido = () => (select.val() ? select.find("option:selected").text() : "");
    entrada.val(textoElegido());

    //----muestra solo las opciones que contienen lo escrito
    function filtrar(texto) {
      const buscado = normalizar(texto);
      let hay = false;
      lista.children("[data-valor]").each(function () {
        const coincide = normalizar($(this).text()).includes(buscado);
        $(this).toggle(coincide).removeClass("activa");
        if (coincide) hay = true;
      });
      vacio.toggle(!hay);
    }
    function abrir() {
      lista.prop("hidden", false);
      entrada.attr("aria-expanded", "true");
    }
    function cerrar() {
      lista.prop("hidden", true);
      entrada.attr("aria-expanded", "false");
    }
    function elegir(li) {
      select.val(li.attr("data-valor"));
      entrada.val(li.text());
      lista.children().attr("aria-selected", "false");
      li.attr("aria-selected", "true");
      cerrar();
    }
    //----flechas: mueve la opción marcada entre las visibles
    function mover(paso) {
      const visibles = lista.children("[data-valor]:visible");
      let i = visibles.index(visibles.filter(".activa")) + paso;
      i = Math.max(0, Math.min(visibles.length - 1, i));
      visibles.removeClass("activa").eq(i).addClass("activa")[0]?.scrollIntoView({ block: "nearest" });
    }

    lista.children('[data-valor="' + select.val() + '"]').attr("aria-selected", "true");

    //----al entrar al campo: se muestra la lista completa y se selecciona el texto (para escribir encima)
    entrada.on("focus click", function () {
      filtrar("");
      abrir();
      this.select();
    });
    entrada.on("input", function () {
      filtrar(this.value);
      abrir();
    });
    entrada.on("keydown", function (e) {
      if (e.key === "ArrowDown" || e.key === "ArrowUp") {
        e.preventDefault();
        abrir();
        mover(e.key === "ArrowDown" ? 1 : -1);
      } else if (e.key === "Enter" && !lista.prop("hidden")) {
        e.preventDefault(); //----Enter elige la opción, no envía el formulario
        //----la marcada con las flechas, o la primera visible si solo se escribió
        let li = lista.children(".activa:visible").first();
        if (!li.length) li = lista.children("[data-valor]:visible").first();
        if (li.length) elegir(li); //----si no hay ninguna ("Sin resultados"), no hace nada
      } else if (e.key === "Escape" && !lista.prop("hidden")) {
        e.stopPropagation(); //----Escape cierra la lista, no el panel lateral
        entrada.val(textoElegido());
        cerrar();
      }
    });

    //----mousedown + preventDefault: el clic no le quita el foco al campo antes de elegir
    lista.on("mousedown", "[data-valor]", function (e) {
      e.preventDefault();
      elegir($(this));
    });

    //----al salir del campo: si lo escrito no es una opción, vuelve a la que estaba elegida
    entrada.on("blur", function () {
      entrada.val(textoElegido());
      cerrar();
    });
  });

  // ---------------------------------------------------------------------------
  // --------------------------SUBIR ARCHIVO PDF---------------------------------
  // ---------------------------------------------------------------------------
  //----El <input type="file"> está oculto; el recuadro que se ve es su <label>.
  const inputArchivo = document.getElementById("pdf");
  if (inputArchivo) {
    const recuadro = $(".selector-archivo");

    //------se muestra el nombre del archivo
    $(inputArchivo).on("change", function () {
      const archivo = this.files[0];
      $("#nombreArchivo").text(archivo ? archivo.name : "Seleccione un archivo PDF");
    });

    //------se puede arrastrar el archivo al recuadro
    recuadro
      .on("dragover", function (e) {
        e.preventDefault(); 
        recuadro.addClass("arrastrando");
      })
      .on("dragleave", function () {
        recuadro.removeClass("arrastrando");
      })
      .on("drop", function (e) {
        e.preventDefault();
        recuadro.removeClass("arrastrando");
        inputArchivo.files = e.originalEvent.dataTransfer.files; //-----pone el archivo arrastrado en el input
        $(inputArchivo).trigger("change");
      });
  }
});
