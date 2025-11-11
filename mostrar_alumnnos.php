<?php
$conexion = mysqli_connect("localhost", "root", "123456789", "conalep");
if (!$conexion) { 
    die("Error de conexión: " . mysqli_connect_error()); 
}

$sql = "SELECT * FROM registro ORDER BY pin DESC";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) > 0) {
    echo "<table>
            <tr>
              <th>Pin</th>
              <th>Nombre</th>
              <th>Apellidos</th>
              <th>Acciones</th>
            </tr>";
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $json = htmlspecialchars(json_encode($fila), ENT_QUOTES, 'UTF-8');
        echo "<tr>
                <td>{$fila['pin']}</td>
                <td>{$fila['nombre_alumno']}</td>
                <td>{$fila['apellido_paterno']} {$fila['apellido_materno']}</td>
                <td>
                    <button class='btn btn-editar' data-nombre='{$json}'>✏️ Editar</button>
                    <button class='btn btn-eliminar' data-pin='{$fila['pin']}'>🗑️ Eliminar</button>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>No hay alumnos registrados.</p>";
}

mysqli_close($conexion);
?>
