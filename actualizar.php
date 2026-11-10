<?php

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Método de solicitud no permitido");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$documento = trim($_POST["documento"] ?? "");
$fechaNacimiento = trim($_POST["fechaNacimiento"] ?? "");

if ($id === false || $id === null || $id < 1) {
    die("Error: identificador no válido");
}

if (
    $nombre === "" ||
    $apellido === "" ||
    $documento === "" ||
    $fechaNacimiento === ""
) {
    die("Error: todos los campos son obligatorios");
}

if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre)) {
    die("Error: el nombre solamente puede contener letras");
}

if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $apellido)) {
    die("Error: el apellido solamente puede contener letras");
}

if (!ctype_digit($documento)) {
    die("Error: el documento debe contener solamente números");
}

$fecha = DateTime::createFromFormat(
    "Y-m-d",
    $fechaNacimiento
);

if (
    !$fecha ||
    $fecha->format("Y-m-d") !== $fechaNacimiento
) {
    die("Error: la fecha de nacimiento no es válida");
}

if ($fechaNacimiento > date("Y-m-d")) {
    die("Error: la fecha de nacimiento no puede ser futura");
}

// Verificar si otro registro ya utiliza ese documento.
$consulta = $conexion->prepare(
    "SELECT ID
     FROM personal
     WHERE documento = ?
       AND ID <> ?"
);

$consulta->bind_param("si", $documento, $id);
$consulta->execute();
$consulta->store_result();

if ($consulta->num_rows > 0) {

    $consulta->close();

    die("Error: el documento ya pertenece a otro registro");

}

$consulta->close();

// Actualizar la información.
$actualizar = $conexion->prepare(
    "UPDATE personal
     SET nombre = ?,
         apellido = ?,
         documento = ?,
         fechaNacimiento = ?
     WHERE ID = ?"
);

$actualizar->bind_param(
    "ssssi",
    $nombre,
    $apellido,
    $documento,
    $fechaNacimiento,
    $id
);

if ($actualizar->execute()) {

    $actualizar->close();
    $conexion->close();

    header("Location: index.php");
    exit();

} else {

    echo "Error al actualizar: " . $conexion->error;

}

?>