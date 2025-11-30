<?php
session_start();
require_once "conexion.php";

// Verificar que haya sesión activa
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->conectar();

// Obtener todos los medicamentos
$stmt = $pdo->prepare("SELECT m.*, p.nombre AS proveedor_nombre 
                       FROM medicamentos m
                       LEFT JOIN proveedores p ON m.proveedor_id = p.id
                       ORDER BY m.id ASC");
$stmt->execute();
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panel de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Panel de Inventario</h1>

    <a href="registro.php" class="btn btn-success mb-3">Agregar Medicamento</a>

    <table class="table table-striped table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Proveedor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($medicamentos as $m): ?>
            <tr>
                <td><?= $m['id'] ?></td>
                <td><?= $m['nombre'] ?></td>
                <td><?= $m['categoria'] ?></td>
                <td><?= $m['cantidad'] ?></td>
                <td>$<?= number_format($m['precio'],2) ?></td>
                <td><?= $m['proveedor_nombre'] ?></td>
                <td>
                    <a href="editar.php?id=<?= $m['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="eliminar.php?id=<?= $m['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Estás seguro que deseas eliminar este medicamento?');">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
