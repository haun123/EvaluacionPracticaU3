<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->conectar();

// Filtro por categoría (GET)
$categoriaFiltro = $_GET['categoria'] ?? '';

if ($categoriaFiltro != '') {
    $stmt = $pdo->prepare("SELECT m.*, p.nombre AS proveedor FROM medicamentos m 
                           LEFT JOIN proveedores p ON m.proveedor_id = p.id
                           WHERE m.categoria = :cat");
    $stmt->bindParam(":cat", $categoriaFiltro);
    $stmt->execute();
} else {
    $stmt = $pdo->query("SELECT m.*, p.nombre AS proveedor FROM medicamentos m 
                         LEFT JOIN proveedores p ON m.proveedor_id = p.id");
}

$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Eliminar medicamento
if (isset($_GET['eliminar'])) {
    $idEliminar = (int)$_GET['eliminar'];
    $stmtDel = $pdo->prepare("DELETE FROM medicamentos WHERE id = :id");
    $stmtDel->bindParam(":id", $idEliminar);
    $stmtDel->execute();
    header("Location: panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panel de Inventario</title>
</head>
<body>

<h1>Bienvenido <?= $_SESSION['nombre'] ?></h1>
<a href="registro.php">Agregar medicamento</a>
<a href="login.php?logout=true">Cerrar sesión</a>

<h2>Medicamentos</h2>

<form method="GET">
    <label>Filtrar por categoría:</label>
    <input type="text" name="categoria">
    <button type="submit">Filtrar</button>
</form>

<table border="1">
    <tr>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Proveedor</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($medicamentos as $m): ?>
    <tr>
        <td><?= $m['nombre'] ?></td>
        <td><?= $m['categoria'] ?></td>
        <td><?= $m['cantidad'] ?></td>
        <td><?= $m['precio'] ?></td>
        <td><?= $m['proveedor'] ?></td>
        <td>
            <a href="panel.php?eliminar=<?= $m['id'] ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
