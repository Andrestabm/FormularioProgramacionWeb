<?php

include("conexion.php");

$buscar = trim($_GET["buscar"] ?? "");
$campo = trim($_GET["campo"] ?? "todos");
$ordenar = trim($_GET["ordenar"] ?? "nombre");
$direccion = strtoupper(trim($_GET["direccion"] ?? "ASC"));

$camposPermitidos = [
    "todos",
    "nombre",
    "documento"
];

$ordenPermitido = [
    "nombre" => "nombre",
    "apellido" => "apellido",
    "documento" => "documento",
    "fechaNacimiento" => "fechaNacimiento",
    "ID" => "ID"
];

if (!in_array($campo, $camposPermitidos, true)) {
    $campo = "todos";
}

if (!array_key_exists($ordenar, $ordenPermitido)) {
    $ordenar = "nombre";
}

if (!in_array($direccion, ["ASC", "DESC"], true)) {
    $direccion = "ASC";
}

$columnaOrden = $ordenPermitido[$ordenar];

if ($buscar !== "") {

    $criterio = "%" . $buscar . "%";

    if ($campo === "nombre") {

        $sql = "SELECT ID, nombre, apellido, documento, fechaNacimiento
                FROM personal
                WHERE nombre LIKE ? OR apellido LIKE ?
                ORDER BY $columnaOrden $direccion";

        $consulta = $conexion->prepare($sql);

        $consulta->bind_param(
            "ss",
            $criterio,
            $criterio
        );

    } elseif ($campo === "documento") {

        $sql = "SELECT ID, nombre, apellido, documento, fechaNacimiento
                FROM personal
                WHERE documento LIKE ?
                ORDER BY $columnaOrden $direccion";

        $consulta = $conexion->prepare($sql);

        $consulta->bind_param(
            "s",
            $criterio
        );

    } else {

        $sql = "SELECT ID, nombre, apellido, documento, fechaNacimiento
                FROM personal
                WHERE nombre LIKE ?
                   OR apellido LIKE ?
                   OR documento LIKE ?
                ORDER BY $columnaOrden $direccion";

        $consulta = $conexion->prepare($sql);

        $consulta->bind_param(
            "sss",
            $criterio,
            $criterio,
            $criterio
        );

    }

    $consulta->execute();

    $resultado = $consulta->get_result();

} else {

    $sql = "SELECT ID, nombre, apellido, documento, fechaNacimiento
            FROM personal
            ORDER BY $columnaOrden $direccion";

    $resultado = $conexion->query($sql);

}

?>