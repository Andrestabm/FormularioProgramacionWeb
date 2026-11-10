<?php

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Método de solicitud no permitido");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    die("Error: identificador no válido");
}

$eliminar = $conexion->prepare(
    "DELETE FROM personal WHERE ID = ?"
);

$eliminar->bind_param("i", $id);
$eliminar->execute();

if ($eliminar->affected_rows > 0) {

    $eliminar->close();
    $conexion->close();

    header("Location: index.php");
    exit();

} else {

    echo "El registro no existe o ya fue eliminado";

}

?>