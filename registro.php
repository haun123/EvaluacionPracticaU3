<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$db = new Database();
$pdo = $db->conectar();

// Obtener lista de proveedores para el <select>
$stmtProv = $pdo->query("SELECT * FROM proveedores");
$proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);

// Procesar el POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $cantidad = $_POST['cantidad'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $proveedor_id = $_POST['proveedor_id'] ?? null;

    if($nombre && $categoria && $cantidad && $precio && $proveedor_id) {
        $stmt = $pdo->prepare("INSERT INTO medicamentos 
            (nombre, categoria, cantidad, precio, proveedor_id) 
            VALUES (:nombre, :categoria, :cantidad, :precio, :prov)");
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":cantidad", $cantidad);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":prov", $proveedor_id);
        $stmt->execute();

        $mensaje = "Medicamento registrado correctamente.";
    } else {
        $mensaje = "Completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrar Medicamento</title>
</head>
<body>

<h1>Registrar Medicamento</h1>

<?php if($mensaje != ""): ?>
    <p><?= $mensaje ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="categoria" placeholder="Categoría" required>
    <input type="number" name="cantidad" placeholder="Cantidad" required>
    <input type="number" step="0.01" name="precio" placeholder="Precio" required>

    <select name="proveedor_id" required>
        <option value="">Seleccione proveedor</option>
        <?php foreach($proveedores as $p): ?>
            <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Registrar</button>
</form>

<a href="panel.php">Volver al panel</a>

</body>
</html>
