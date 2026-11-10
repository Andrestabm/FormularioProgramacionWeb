<?php

$buscar = trim($_GET["buscar"] ?? "");
$campo = trim($_GET["campo"] ?? "todos");
$ordenar = trim($_GET["ordenar"] ?? "nombre");
$direccion = strtoupper(trim($_GET["direccion"] ?? "ASC"));

if (!in_array($campo, ["todos", "nombre", "documento"], true)) {
    $campo = "todos";
}

if (!in_array(
    $ordenar,
    ["nombre", "apellido", "documento", "fechaNacimiento", "ID"],
    true
)) {
    $ordenar = "nombre";
}

if (!in_array($direccion, ["ASC", "DESC"], true)) {
    $direccion = "ASC";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Formulario</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="style.css?v=5">
</head>

<body>

<div id="mensajeGlobal" role="alert" aria-live="polite"></div>

<div class="container">

    <div class="card formulario-card">

        <div class="card-body">

            <h2 class="titulo">Formulario</h2>

            <hr>

            <!-- FORMULARIO DE REGISTRO -->
            <form
                action="procesar.php"
                method="POST"
                id="formulario"
                class="form-validacion"
                novalidate>

                <div
                    id="mensajeError"
                    class="alert alert-danger d-none"
                    role="alert">
                </div>

                <fieldset>

                    <legend>Información personal</legend>

                    <div class="mb-3">

                        <label for="nombre" class="form-label">
                            Nombre
                        </label>

                        <input
                            class="form-control"
                            type="text"
                            id="nombre"
                            name="nombre"
                            placeholder="Ingrese su nombre"
                            required
                            maxlength="50">

                        <div class="invalid-feedback">
                            Ingrese un nombre válido, usando solamente letras y espacios.
                        </div>

                        <small
                            id="contadorNombre"
                            class="text-muted contador">
                            0 / 50 caracteres
                        </small>

                    </div>

                    <div class="mb-3">

                        <label for="apellido" class="form-label">
                            Apellido
                        </label>

                        <input
                            class="form-control"
                            type="text"
                            id="apellido"
                            name="apellido"
                            placeholder="Ingrese su apellido"
                            required
                            maxlength="50">

                        <div class="invalid-feedback">
                            Ingrese un apellido válido, usando solamente letras y espacios.
                        </div>

                        <small
                            id="contadorApellido"
                            class="text-muted contador">
                            0 / 50 caracteres
                        </small>

                    </div>

                    <div class="mb-3">

                        <label for="documento" class="form-label">
                            Documento
                        </label>

                        <input
                            class="form-control"
                            type="text"
                            inputmode="numeric"
                            id="documento"
                            name="documento"
                            placeholder="Ingrese su documento"
                            required>

                        <div class="invalid-feedback">
                            El documento debe contener solamente números.
                        </div>

                    </div>

                    <div class="mb-3">

                        <label for="fechaNacimiento" class="form-label">
                            Fecha de nacimiento
                        </label>

                        <input
                            class="form-control"
                            type="date"
                            id="fechaNacimiento"
                            name="fechaNacimiento"
                            required>

                        <div class="invalid-feedback">
                            Seleccione una fecha de nacimiento válida.
                        </div>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100">
                        Registrar
                    </button>

                </fieldset>

            </form>

            <hr>

            <h3 class="text-center mb-3">
                Registros existentes
            </h3>

            <!-- FORMULARIO DE BÚSQUEDA Y ORDENAMIENTO -->
            <form
                action="index.php"
                method="GET"
                class="busqueda-form">

                <div class="busqueda-campo">

                    <label for="buscar" class="form-label">
                        Buscar registros
                    </label>

                    <input
                        type="text"
                        id="buscar"
                        name="buscar"
                        class="form-control"
                        placeholder="Escribe un nombre o documento..."
                        value="<?php echo htmlspecialchars(
                            $buscar,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>">

                </div>

                <div class="busqueda-campo">

                    <label for="campo" class="form-label">
                        Buscar en
                    </label>

                    <select
                        id="campo"
                        name="campo"
                        class="form-select">

                        <option
                            value="todos"
                            <?php echo $campo === "todos" ? "selected" : ""; ?>>
                            Todos los campos
                        </option>

                        <option
                            value="nombre"
                            <?php echo $campo === "nombre" ? "selected" : ""; ?>>
                            Nombre y apellido
                        </option>

                        <option
                            value="documento"
                            <?php echo $campo === "documento" ? "selected" : ""; ?>>
                            Documento
                        </option>

                    </select>

                </div>

                <div class="busqueda-campo">

                    <label for="ordenar" class="form-label">
                        Ordenar por
                    </label>

                    <select
                        id="ordenar"
                        name="ordenar"
                        class="form-select">

                        <option
                            value="nombre"
                            <?php echo $ordenar === "nombre" ? "selected" : ""; ?>>
                            Nombre
                        </option>

                        <option
                            value="apellido"
                            <?php echo $ordenar === "apellido" ? "selected" : ""; ?>>
                            Apellido
                        </option>

                        <option
                            value="documento"
                            <?php echo $ordenar === "documento" ? "selected" : ""; ?>>
                            Documento
                        </option>

                        <option
                            value="fechaNacimiento"
                            <?php echo $ordenar === "fechaNacimiento" ? "selected" : ""; ?>>
                            Fecha de nacimiento
                        </option>

                        <option
                            value="ID"
                            <?php echo $ordenar === "ID" ? "selected" : ""; ?>>
                            ID
                        </option>

                    </select>

                </div>

                <div class="busqueda-campo">

                    <label for="direccion" class="form-label">
                        Orden
                    </label>

                    <select
                        id="direccion"
                        name="direccion"
                        class="form-select">

                        <option
                            value="ASC"
                            <?php echo $direccion === "ASC" ? "selected" : ""; ?>>
                            Ascendente (A-Z)
                        </option>

                        <option
                            value="DESC"
                            <?php echo $direccion === "DESC" ? "selected" : ""; ?>>
                            Descendente (Z-A)
                        </option>

                    </select>

                </div>

                <div class="busqueda-acciones">

                    <button
                        type="submit"
                        class="boton-buscar">
                        Buscar
                    </button>

                    <a
                        href="index.php"
                        class="boton-limpiar">
                        Limpiar
                    </a>

                </div>

            </form>

            <!-- RESUMEN DE RESULTADOS -->
            <p
                class="resumen-resultados"
                id="resumen-resultados"
                aria-live="polite">
                Cargando registros...
            </p>

            <!-- TABLA QUE CONSTRUIRÁ JAVASCRIPT -->
            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Documento</th>
                            <th>Fecha Nacimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody id="tabla-registros">

                        <tr>
                            <td colspan="6" class="sin-resultados">
                                Cargando registros...
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<!-- ARCHIVO JAVASCRIPT EXTERNO -->
<script src="js/app.js?v=3"></script>

</body>
</html>