<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->conectar();

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
    <h1 class="mb-4 text-primary">Panel de Inventario</h1>

    <a href="registro.php" class="btn btn-success mb-3">Agregar Medicamento</a>

    <table class="table table-striped table-primary table-bordered text-center align-middle">
        <thead >
            <tr>
                <th class="bg-primary text-white">ID</th>
                <th class="bg-primary text-white">Nombre</th>
                <th class="bg-primary text-white">Categoría</th>
                <th class="bg-primary text-white">Cantidad</th>
                <th class="bg-primary text-white">Precio</th>
                <th class="bg-primary text-white">Proveedor</th>
                <th class="bg-primary text-white">Acciones</th>
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
                    <a href="editar.php?id=<?= $m['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
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
