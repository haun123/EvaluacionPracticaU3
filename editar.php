<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->conectar();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: panel.php");
    exit;
}

// Obtener datos del medicamento
$stmt = $pdo->prepare("SELECT * FROM medicamentos WHERE id = :id");
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();
$medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicamento) {
    header("Location: panel.php");
    exit;
}

// Manejar el POST para actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $precio = $_POST['precio'] ?? 0;
    $proveedor_id = $_POST['proveedor_id'] ?? 0;

    $update = $pdo->prepare("UPDATE medicamentos 
                             SET nombre = :nombre, categoria = :categoria, cantidad = :cantidad, precio = :precio, proveedor_id = :proveedor_id
                             WHERE id = :id");
    $update->execute([
        ':nombre' => $nombre,
        ':categoria' => $categoria,
        ':cantidad' => $cantidad,
        ':precio' => $precio,
        ':proveedor_id' => $proveedor_id,
        ':id' => $id
    ]);

    header("Location: panel.php");
    exit;
}

// Obtener proveedores para el select
$prov_stmt = $pdo->query("SELECT * FROM proveedores");
$proveedores = $prov_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Medicamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4 text-info text-center">Editar Medicamento</h1>

    <form method="POST">
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($medicamento['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Categoría</label>
            <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($medicamento['categoria']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Cantidad</label>
            <input type="number" name="cantidad" class="form-control" value="<?= $medicamento['cantidad'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control" value="<?= $medicamento['precio'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Proveedor</label>
            <select name="proveedor_id" class="form-select" required>
                <?php foreach ($proveedores as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $medicamento['proveedor_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-info"><b>Actualizar</b></button>
        <a href="panel.php" class="btn btn-secondary"><b>Cancelar</b></a>
    </form>
</div>

</body>
</html>
