document.addEventListener("DOMContentLoaded", function () {
 
    const patronTexto = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
    const mensajeGlobal = document.getElementById("mensajeGlobal");
    let temporizadorMensaje = null;
 
    function fechaHoy() {
        const ahora = new Date();
        const mes = String(ahora.getMonth() + 1).padStart(2, "0");
        const dia = String(ahora.getDate()).padStart(2, "0");
 
        return ahora.getFullYear() + "-" + mes + "-" + dia;
    }

    function mostrarMensajeGlobal(texto, tipo) {
        if (!mensajeGlobal) {
            alert(texto);
            return;
        }
 
        clearTimeout(temporizadorMensaje);
 
        mensajeGlobal.textContent = texto;
        mensajeGlobal.classList.toggle("mensaje-exito", tipo === "exito");
        mensajeGlobal.classList.add("mostrar");
 
        temporizadorMensaje = setTimeout(
            ocultarMensajeGlobal,
            tipo === "exito" ? 2500 : 4000
        );
    }
 
    function ocultarMensajeGlobal() {
        if (mensajeGlobal) {
            clearTimeout(temporizadorMensaje);
            mensajeGlobal.classList.remove("mostrar");
        }
    }
 
    // ------------------------------------------------------------
    // TABLA DINÁMICA: fetch + JSON + DOM
    // ------------------------------------------------------------
 
    const tabla = document.getElementById("tabla-registros");
    const resumen = document.getElementById("resumen-resultados");
 
    function mostrarFilaInformativa(texto) {
        const fila = document.createElement("tr");
        const celda = document.createElement("td");
 
        celda.colSpan = 6;
        celda.className = "sin-resultados";
        celda.textContent = texto;
 
        fila.appendChild(celda);
        tabla.replaceChildren(fila);
    }
 
    function construirFila(registro) {
        const fila = document.createElement("tr");
 
        ["ID", "nombre", "apellido", "documento", "fechaNacimiento"]
            .forEach(function (columna) {
                const celda = document.createElement("td");
 
                // textContent evita interpretar los datos como HTML.
                celda.textContent =
                    registro[columna] == null ? "" : registro[columna];
 
                fila.appendChild(celda);
            });
 
        const celdaAcciones = document.createElement("td");
 
        const enlaceEditar = document.createElement("a");
        enlaceEditar.href = "editar.php?id=" + encodeURIComponent(registro.ID);
        enlaceEditar.className = "boton-editar";
        enlaceEditar.textContent = "Editar";
        celdaAcciones.appendChild(enlaceEditar);
 
        const formularioEliminar = document.createElement("form");
        formularioEliminar.action = "eliminar.php";
        formularioEliminar.method = "POST";
        formularioEliminar.className = "form-eliminar";
        formularioEliminar.style.display = "inline";
 
        const idOculto = document.createElement("input");
        idOculto.type = "hidden";
        idOculto.name = "id";
        idOculto.value = registro.ID;
        formularioEliminar.appendChild(idOculto);
 
        const botonEliminar = document.createElement("button");
        botonEliminar.type = "submit";
        botonEliminar.className = "boton-eliminar";
        botonEliminar.textContent = "Eliminar";
        formularioEliminar.appendChild(botonEliminar);
 
        celdaAcciones.appendChild(formularioEliminar);
        fila.appendChild(celdaAcciones);
 
        return fila;
    }
 
    // Solicita los registros a obtener.php (respetando búsqueda y orden
    // de la URL) y reconstruye el <tbody>.
    function cargarRegistros() {
        if (!tabla) {
            return;
        }
 
        fetch("obtener.php" + window.location.search)
            .then(function (respuesta) {
                if (!respuesta.ok) {
                    throw new Error("No fue posible cargar los registros.");
                }
 
                return respuesta.json();
            })
            .then(function (registros) {
                if (!Array.isArray(registros)) {
                    throw new Error(
                        registros.error ||
                        "La respuesta del servidor no es válida."
                    );
                }
 
                if (resumen) {
                    resumen.textContent = registros.length === 1
                        ? "Se encontró 1 registro."
                        : "Se encontraron " + registros.length + " registros.";
                }
 
                if (registros.length === 0) {
                    mostrarFilaInformativa(
                        "No se encontraron registros que coincidan con la búsqueda."
                    );
                    return;
                }
 
                const filas = registros.map(construirFila);
                tabla.replaceChildren(...filas);
            })
            .catch(function (error) {
                console.error("Error al cargar los registros:", error);
 
                mostrarFilaInformativa(
                    "No fue posible cargar los registros. Verifique Apache, MySQL y la conexión con la base de datos."
                );
 
                if (resumen) {
                    resumen.textContent = "Error al cargar los registros.";
                }
            });
    }
 
    // ------------------------------------------------------------
    // CONTADORES DE CARACTERES (evento input)
    // ------------------------------------------------------------
 
    [
        ["nombre", "contadorNombre"],
        ["apellido", "contadorApellido"]
    ].forEach(function (par) {
        const campo = document.getElementById(par[0]);
        const contador = document.getElementById(par[1]);
 
        if (!campo || !contador) {
            return;
        }
 
        function actualizarContador() {
            contador.textContent =
                campo.value.length + " / " + campo.maxLength + " caracteres";
        }
 
        campo.addEventListener("input", actualizarContador);
        actualizarContador();
    });
 
    // ------------------------------------------------------------
    // VALIDACIÓN DE LOS FORMULARIOS DE REGISTRO Y EDICIÓN
    // ------------------------------------------------------------
 
    document.querySelectorAll(".form-validacion").forEach(function (formulario) {
        const nombre = formulario.querySelector('[name="nombre"]');
        const apellido = formulario.querySelector('[name="apellido"]');
        const documento = formulario.querySelector('[name="documento"]');
        const fecha = formulario.querySelector('[name="fechaNacimiento"]');
        const mensaje = formulario.querySelector(".alert");
 
        // Devuelve el primer error encontrado y marca el campo con
        // is-invalid; devuelve "" si todo es correcto.
        function validar() {
            const reglas = [
                {
                    campo: nombre,
                    ok: function (v) { return v !== ""; },
                    texto: "El nombre es obligatorio."
                },
                {
                    campo: nombre,
                    ok: function (v) { return patronTexto.test(v); },
                    texto: "El nombre solamente puede contener letras y espacios."
                },
                {
                    campo: apellido,
                    ok: function (v) { return v !== ""; },
                    texto: "El apellido es obligatorio."
                },
                {
                    campo: apellido,
                    ok: function (v) { return patronTexto.test(v); },
                    texto: "El apellido solamente puede contener letras y espacios."
                },
                {
                    campo: documento,
                    ok: function (v) { return v !== ""; },
                    texto: "El documento es obligatorio."
                },
                {
                    campo: documento,
                    ok: function (v) { return /^\d+$/.test(v); },
                    texto: "El documento debe contener solamente números."
                },
                {
                    campo: fecha,
                    ok: function (v) { return v !== ""; },
                    texto: "La fecha de nacimiento es obligatoria."
                },
                {
                    campo: fecha,
                    ok: function (v) { return v <= fechaHoy(); },
                    texto: "La fecha de nacimiento no puede ser futura."
                }
            ];
 
            for (const regla of reglas) {
                if (!regla.campo) {
                    continue;
                }
 
                if (!regla.ok(regla.campo.value.trim())) {
                    regla.campo.classList.add("is-invalid");
                    return regla.texto;
                }
            }
 
            return "";
        }
 
        formulario.addEventListener("submit", function (evento) {
            // Limpiar errores anteriores.
            [nombre, apellido, documento, fecha].forEach(function (campo) {
                if (campo) {
                    campo.classList.remove("is-invalid");
                }
            });
 
            if (mensaje) {
                mensaje.textContent = "";
                mensaje.classList.add("d-none");
            }
 
            const error = validar();
 
            if (error) {
                evento.preventDefault();
 
                if (mensaje) {
                    mensaje.textContent = error;
                    mensaje.classList.remove("d-none");
                } else {
                    alert(error);
                }
 
                return;
            }
 
            // El formulario de edición se envía de forma tradicional
            // (POST a actualizar.php). El de registro se envía con fetch.
            if (formulario.id !== "formulario") {
                return;
            }
 
            evento.preventDefault();
            ocultarMensajeGlobal();
 
            fetch("procesar.php", {
                method: "POST",
                body: new FormData(formulario)
            })
                .then(function (respuesta) {
                    return respuesta.json();
                })
                .then(function (resultado) {
                    if (!resultado.success) {
                        mostrarMensajeGlobal(resultado.mensaje, "error");
                        return;
                    }

                    formulario.reset();
 
                    formulario
                        .querySelectorAll("input")
                        .forEach(function (campo) {
                            campo.dispatchEvent(new Event("input"));
                        });
 
                    mostrarMensajeGlobal(resultado.mensaje, "exito");
                    cargarRegistros();
                })
                .catch(function () {
                    mostrarMensajeGlobal(
                        "No fue posible conectar con el servidor.",
                        "error"
                    );
                });
        });
 
        // Quitar el estado de error mientras el usuario corrige.
        formulario.querySelectorAll("input").forEach(function (campo) {
            campo.addEventListener("input", function () {
                campo.classList.remove("is-invalid");
 
                if (mensaje) {
                    mensaje.classList.add("d-none");
                }
            });
        });
    });
 
    // ------------------------------------------------------------
    // CONFIRMACIÓN ANTES DE ELIMINAR
    // Se usa delegación de eventos porque los formularios de la tabla
    // se crean dinámicamente, después de cargar la página.
    // ------------------------------------------------------------
 
    document.addEventListener("submit", function (evento) {
        if (evento.target.matches(".form-eliminar")) {
            const confirmar = confirm(
                "¿Está seguro de que desea eliminar este registro?"
            );
 
            if (!confirmar) {
                evento.preventDefault();
            }
        }
    });
 
    // Carga inicial de la tabla (solo existe en index.php).
    cargarRegistros();
});
