<?php

header("Content-Type: application/json; charset=UTF-8");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function responder($exito, $mensaje, $codigo = 200)
{
    http_response_code($codigo);

    echo json_encode([
        "success" => $exito,
        "mensaje" => $mensaje
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {
    include("conexion.php");

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        responder(false, "Método de solicitud no permitido.", 405);
    }

    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $documento = trim($_POST["documento"] ?? "");
    $fechaNacimiento = trim($_POST["fechaNacimiento"] ?? "");

    if (
        $nombre === "" ||
        $apellido === "" ||
        $documento === "" ||
        $fechaNacimiento === ""
    ) {
        responder(false, "Todos los campos son obligatorios.", 400);
    }

    $patron = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u";

    if (!preg_match($patron, $nombre)) {
        responder(
            false,
            "El nombre solamente puede contener letras.",
            400
        );
    }

    if (!preg_match($patron, $apellido)) {
        responder(
            false,
            "El apellido solamente puede contener letras.",
            400
        );
    }

    if (!ctype_digit($documento)) {
        responder(
            false,
            "El documento debe contener solamente números.",
            400
        );
    }

    $fecha = DateTime::createFromFormat(
        "!Y-m-d",
        $fechaNacimiento
    );

    if (
        !$fecha ||
        $fecha->format("Y-m-d") !== $fechaNacimiento ||
        $fechaNacimiento > date("Y-m-d")
    ) {
        responder(
            false,
            "La fecha de nacimiento no es válida.",
            400
        );
    }

    // VERIFICAR SI EL DOCUMENTO YA EXISTE
    $consulta = $conexion->prepare(
        "SELECT ID FROM personal WHERE documento = ?"
    );

    $consulta->bind_param("s", $documento);
    $consulta->execute();
    $consulta->store_result();

    if ($consulta->num_rows > 0) {
        $consulta->close();
        $conexion->close();

        responder(
            false,
            "El documento ya está registrado.",
            409
        );
    }

    $consulta->close();

    // INSERTAR EL NUEVO REGISTRO
    $insertar = $conexion->prepare(
        "INSERT INTO personal
        (nombre, apellido, documento, fechaNacimiento)
        VALUES (?, ?, ?, ?)"
    );

    $insertar->bind_param(
        "ssss",
        $nombre,
        $apellido,
        $documento,
        $fechaNacimiento
    );

    $insertar->execute();

    $insertar->close();
    $conexion->close();

    responder(true, "Registro guardado correctamente.");

} catch (Throwable $error) {
    responder(
        false,
        "Ocurrió un error al guardar el registro.",
        500
    );
}

?>