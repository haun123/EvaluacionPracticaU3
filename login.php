<?php
session_start();
require_once "conexion.php";

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';

    echo "<h3>DEBUG</h3>";

    echo "Email ingresado: $email<br>";
    echo "Clave ingresada: $clave<br>";

    if (!empty($email) && !empty($clave)) {

        $db = new Database();
        $pdo = $db->conectar();

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<pre>";
        echo "Resultado de la consulta:<br>";
        var_dump($usuario);
        echo "</pre>";

        if ($usuario) {
            echo "<br>Hash guardado en BD: " . $usuario['clave'] . "<br>";

            if (password_verify($clave, $usuario['clave'])) {
                echo "<b>password_verify = TRUE ✔</b><br>";
                echo "Se redirigiría a panel.php";
                exit;
            } else {
                echo "<b>password_verify = FALSE ❌</b><br>";
                echo "La contraseña NO coincide.<br>";
            }

        } else {
            echo "<b>No se encontró el usuario con ese email ❌</b><br>";
        }

    } else {
        echo "Campos vacíos";
    }

    exit; // Detenemos ejecución para ver el debug
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>

<h1>Login</h1>

<?php if($mensaje != ""): ?>
    <p><?= $mensaje ?></p>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Correo" required>
    <input type="password" name="clave" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
</form>

</body>
</html>
