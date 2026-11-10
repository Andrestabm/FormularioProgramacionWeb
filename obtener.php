<?php

header("Content-Type: application/json; charset=UTF-8");

mysqli_report(
    MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT
);

try {

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

    $sqlBase = "
        SELECT ID, nombre, apellido, documento, fechaNacimiento
        FROM personal
    ";

    // Si existe una búsqueda, aplicar los filtros.
    if ($buscar !== "") {

        $criterio = "%" . $buscar . "%";

        if ($campo === "nombre") {

            $sql = $sqlBase . "
                WHERE nombre LIKE ?
                   OR apellido LIKE ?
                ORDER BY $columnaOrden $direccion
            ";

            $consulta = $conexion->prepare($sql);

            $consulta->bind_param(
                "ss",
                $criterio,
                $criterio
            );

        } elseif ($campo === "documento") {

            $sql = $sqlBase . "
                WHERE documento LIKE ?
                ORDER BY $columnaOrden $direccion
            ";

            $consulta = $conexion->prepare($sql);

            $consulta->bind_param(
                "s",
                $criterio
            );

        } else {

            $sql = $sqlBase . "
                WHERE nombre LIKE ?
                   OR apellido LIKE ?
                   OR documento LIKE ?
                ORDER BY $columnaOrden $direccion
            ";

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

        // Si no hay búsqueda, consultar todos los registros.
        $sql = $sqlBase . "
            ORDER BY $columnaOrden $direccion
        ";

        $resultado = $conexion->query($sql);
    }

    // Guardar los registros en un arreglo.
    $registros = [];

    while ($fila = $resultado->fetch_assoc()) {
        $registros[] = $fila;
    }

    // Devolver los datos como JSON.
    echo json_encode(
        $registros,
        JSON_UNESCAPED_UNICODE
    );

    $conexion->close();

} catch (Throwable $error) {

    http_response_code(500);

    echo json_encode(
        [
            "error" => "No fue posible consultar los registros."
        ],
        JSON_UNESCAPED_UNICODE
    );

}

?>