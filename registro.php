<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$tipoMensaje = "";

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

        $tipoMensaje = "success";
        $mensaje = "Medicamento registrado correctamente ✅";
    } else {
        $tipoMensaje = "danger";
        $mensaje = "Por favor completa todos los campos ❌";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrar Medicamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    
    <?php if($_SERVER['REQUEST_METHOD'] === 'POST' && $mensaje != ""): ?>
        <div class="alert alert-<?= $tipoMensaje ?> d-flex align-items-center" role="alert">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <h2 class="mb-4 text-info text-center">Registrar Medicamento</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" placeholder="Nombre" class="form-control mb-3" required>
        </div>

        <div class="mb-3">
            <label>Categoría</label>
            <input type="text" name="categoria" placeholder="Categoría" class="form-control mb-3" required>
        </div>

        <div class="mb-3">
            <label>Cantidad</label>
            <input type="number" name="cantidad" placeholder="Cantidad" class="form-control mb-3" required>
        </div>

        <div class="mb-3">
            <label>Precio</label>
            <input type="number" step="0.01" name="precio" placeholder="Precio" class="form-control mb-3" required>
        </div>

        <div class="mb-3">
            <label>Proveedor</label>
            <select name="proveedor_id" class="form-select mb-3" required>
                <option value="">Seleccione proveedor</option>
                <?php foreach($proveedores as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn bg-info text-white btn-hover w-46"><b>Registrar</b></button>
            <a href="panel.php" class="btn bg-secondary text-white btn-hovertext-center"><b>Volver al Panel</b></a>
        </div>
    </form>
</div>

</body>

</html>
