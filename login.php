<?php
session_start();
require_once "conexion.php";

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (!empty($email) && !empty($clave)) {
        $db = new Database();
        $pdo = $db->conectar();

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($clave, $usuario['clave'])) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            header("Location: panel.php");
            exit;
        } else {
            $mensaje = "Email o contraseña incorrectos";
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
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center vh-100">

    <div class="container ">
        
        <div class="bg-primary text-center p-4 rounded-3" style="max-width: 80%; margin-left: auto; margin-right: auto;">
            
            <h1 class="text-white mb-4">Login</h1>

            <?php if($mensaje != ""): ?>
                <p><?= $mensaje ?></p>
            <?php endif; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Correo" class="form-control mb-4" required>
                <input type="password" name="clave" placeholder="Contraseña" class="form-control mb-4" required>
                <button type="submit" class="btn bg-white text-primary mb-4">Ingresar</button>
            </form>
        </div>
    </div>
</body>
</html>
