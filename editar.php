<?php

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    die("Método de solicitud no permitido");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    die("Error: identificador no válido");
}

$consulta = $conexion->prepare(
    "SELECT ID, nombre, apellido, documento, fechaNacimiento
     FROM personal
     WHERE ID = ?"
);

$consulta->bind_param("i", $id);
$consulta->execute();

$resultado = $consulta->get_result();

if ($resultado->num_rows === 0) {
    die("Error: el registro no existe");
}

$fila = $resultado->fetch_assoc();

$consulta->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar registro</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="style.css?v=5">
</head>

<body>

<div class="container">

    <div class="card formulario-card">

        <div class="card-body">

            <h2 class="titulo">Editar registro</h2>

            <hr>

            <form
                action="actualizar.php"
                method="POST"
                id="formEditar"
                class="form-validacion"
                novalidate>

                <div
                    class="alert alert-danger d-none"
                    role="alert">
                </div>

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo (int) $fila['ID']; ?>">

                <div class="mb-3">

                    <label for="nombre" class="form-label">
                        Nombre
                    </label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        class="form-control"
                        value="<?php echo htmlspecialchars(
                            $fila['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        maxlength="50"
                        required>

                    <div class="invalid-feedback">
                        Ingrese un nombre válido, usando solamente letras y espacios.
                    </div>

                </div>

                <div class="mb-3">

                    <label for="apellido" class="form-label">
                        Apellido
                    </label>

                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        class="form-control"
                        value="<?php echo htmlspecialchars(
                            $fila['apellido'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        maxlength="50"
                        required>

                    <div class="invalid-feedback">
                        Ingrese un apellido válido, usando solamente letras y espacios.
                    </div>

                </div>

                <div class="mb-3">

                    <label for="documento" class="form-label">
                        Documento
                    </label>

                    <input
                        type="text"
                        id="documento"
                        name="documento"
                        class="form-control"
                        value="<?php echo htmlspecialchars(
                            $fila['documento'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        inputmode="numeric"
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
                        type="date"
                        id="fechaNacimiento"
                        name="fechaNacimiento"
                        class="form-control"
                        value="<?php echo htmlspecialchars(
                            $fila['fechaNacimiento'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        required>

                    <div class="invalid-feedback">
                        Seleccione una fecha de nacimiento válida.
                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100">
                    Actualizar
                </button>

            </form>

            <a
                href="index.php"
                class="btn btn-secondary w-100 mt-2">
                Cancelar
            </a>

        </div>

    </div>

</div>

<script src="js/app.js?v=3"></script>

</body>
</html>