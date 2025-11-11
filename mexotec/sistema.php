<?php
// ====================== CONEXIÓN ======================
$servername = "localhost";
$username = "root";
$password = "123456789";
$dbname = "mexotec";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("❌ Error al conectar con la base de datos: " . $conn->connect_error);
}

// ====================== CREAR TABLAS SI NO EXISTEN ======================
$conn->query("CREATE TABLE IF NOT EXISTS alumnos (
  id_alumno INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  apellido_paterno VARCHAR(100),
  apellido_materno VARCHAR(100),
  grado VARCHAR(10),
  grupo VARCHAR(10),
  carrera VARCHAR(100),
  turno VARCHAR(20),
  correo_electronico VARCHAR(100),
  telefono VARCHAR(20),
  estatus VARCHAR(20),
  pin CHAR(4)
)");

$conn->query("CREATE TABLE IF NOT EXISTS asistencias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_alumno INT,
  fecha DATE,
  hora TIME,
  estado ENUM('Asistencia','Falta'),
  FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno)
)");

// ====================== PROCESAR ASISTENCIA ======================
$mensaje = "";
if (isset($_POST['pin'])) {
    $pin = $_POST['pin'];
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    $sql = "SELECT * FROM alumnos WHERE pin='$pin' LIMIT 1";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $alumno = $resultado->fetch_assoc();
        $id_alumno = $alumno['id_alumno'];

        // Verificar si ya marcó hoy
        $verifica = $conn->query("SELECT * FROM asistencias WHERE id_alumno='$id_alumno' AND fecha='$fecha'");
        if ($verifica->num_rows > 0) {
            $mensaje = "⚠️ Ya registraste tu asistencia hoy, {$alumno['nombre']}.";
        } else {
            $conn->query("INSERT INTO asistencias (id_alumno, fecha, hora, estado) VALUES ('$id_alumno', '$fecha', '$hora', 'Asistencia')");
            $mensaje = "✅ Asistencia registrada correctamente, {$alumno['nombre']}!";
        }
    } else {
        // Si el PIN no existe, registrar falta
        $mensaje = "❌ PIN no encontrado. Se registra falta automática.";
    }
}

// ====================== CONSULTA DE ASISTENCIAS ======================
$asistencias = $conn->query("SELECT a.nombre, a.apellido_paterno, a.apellido_materno, s.fecha, s.hora, s.estado 
FROM asistencias s
JOIN alumnos a ON s.id_alumno = a.id_alumno
ORDER BY s.fecha DESC, s.hora DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Control de Asistencia - MEXOTEC</title>
<style>
body {
  font-family: "Segoe UI", sans-serif;
  background: url('fondo.jpg') no-repeat center center fixed;
  background-size: cover;
  margin: 0;
  color: #333;
}

.container {
  background: rgba(255,255,255,0.92);
  max-width: 700px;
  margin: 50px auto;
  padding: 30px 40px;
  border-radius: 15px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}

.logo {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 100px;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.3);
}

h1 {
  text-align: center;
  color: #00a300ff;
}

form {
  text-align: center;
  margin-top: 20px;
}

input[type="password"] {
  font-size: 20px;
  letter-spacing: 4px;
  text-align: center;
  width: 200px;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #aaa;
}

button {
  margin-top: 15px;
  padding: 10px 20px;
  background-color: #00d712ff;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  background-color: #005fa3;
}

.mensaje {
  text-align: center;
  font-weight: bold;
  margin: 15px;
  color: #0078d7;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 25px;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: center;
}

th {
  background-color: #0078d7;
  color: white;
}

.estado-Asistencia {
  background-color: #c3f9c3;
}

.estado-Falta {
  background-color: #f9c3c3;
}
</style>
</head>
<body>

<img src="logo.png" class="logo" alt="Logo MEXOTEC">

<div class="container">
  <h1>Registro de Asistencia</h1>

  <?php if ($mensaje): ?>
    <div class="mensaje"><?= $mensaje ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="pin">Introduce tu PIN (4 dígitos):</label><br>
    <input type="password" name="pin" id="pin" maxlength="4" required><br>
    <button type="submit">Registrar</button>
  </form>

  <h2 style="text-align:center;margin-top:30px;">Historial de Asistencia</h2>
  <table>
    <tr>
      <th>Nombre</th>
      <th>Apellido Paterno</th>
      <th>Apellido Materno</th>
      <th>Fecha</th>
      <th>Hora</th>
      <th>Estado</th>
    </tr>
    <?php if ($asistencias->num_rows > 0): ?>
      <?php while($fila = $asistencias->fetch_assoc()): ?>
        <tr class="estado-<?= $fila['estado'] ?>">
          <td><?= $fila['nombre'] ?></td>
          <td><?= $fila['apellido_paterno'] ?></td>
          <td><?= $fila['apellido_materno'] ?></td>
          <td><?= $fila['fecha'] ?></td>
          <td><?= $fila['hora'] ?></td>
          <td><?= $fila['estado'] ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6">No hay registros todavía.</td></tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
