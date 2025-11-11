<?php
$conexion = mysqli_connect("localhost", "root", "123456789", "conalep");

if (!$conexion) {
    die("❌ Error: " . mysqli_connect_error());
}

$nombre = $_POST['nombre'];
$ap_paterno = $_POST['apellido_paterno'];
$ap_materno = $_POST['apellido_materno'];
$pin = $_POST['pin'];

$ruta = '';
if (!empty($_FILES['imagen']['name'])) {
    $imagen = $_FILES['imagen']['name'];
    $ruta = "fotos/" . basename($imagen);
    if (!file_exists("fotos")) mkdir("fotos", 0777, true);
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
}

$sql = "INSERT INTO registro (nombre, apellido_paterno, apellido_materno, pin)
        VALUES ('$nombre','$ap_paterno','$ap_materno','$pin')";

echo mysqli_query($conexion, $sql)
    ? "✅ Alumno registrado correctamente"
    : "❌ Error: " . mysqli_error($conexion);

mysqli_close($conexion);
?>