<?php
require_once "conexion.php";

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $rol = $_POST['rol'] ?? 'empleado';

    if (!empty($nombre) && !empty($email) && !empty($clave)) {
        $db = new Database();
        $pdo = $db->conectar();

        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->fetch()) {
            $mensaje = "El email ya está registrado";
        } else {
            // Generar hash de la contraseña
            $hash = password_hash($clave, PASSWORD_DEFAULT);

            // Insertar usuario
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, clave, rol) VALUES (:nombre, :email, :clave, :rol)");
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":clave", $hash);
            $stmt->bindParam(":rol", $rol);

            if ($stmt->execute()) {
                $mensaje = "Usuario registrado correctamente";
            } else {
                $mensaje = "Error al registrar el usuario";
            }
        }
    } else {
        $mensaje = "Por favor completa todos los campos";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
</head>
<body>

<h1>Registrar Usuario</h1>

<?php if($mensaje != ""): ?>
    <p><?= $mensaje ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre completo" required>
    <input type="email" name="email" placeholder="Correo" required>
    <input type="password" name="clave" placeholder="Contraseña" required>
    <select name="rol">
        <option value="empleado">Empleado</option>
        <option value="admin">Administrador</option>
    </select>
    <button type="submit">Registrar</button>
</form>

</body>
</html>
