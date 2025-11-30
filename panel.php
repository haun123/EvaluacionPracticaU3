<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$pdo = $db->conectar();

// Obtener todas las categorías disponibles
$categoria_stmt = $pdo->query("SELECT DISTINCT categoria FROM medicamentos");
$categorias = $categoria_stmt->fetchAll(PDO::FETCH_COLUMN);

// Revisar si se seleccionó una categoría para filtrar
$filtro_categoria = $_GET['categoria'] ?? '';

if ($filtro_categoria) {
    $stmt = $pdo->prepare("SELECT m.*, p.nombre AS proveedor_nombre 
                           FROM medicamentos m
                           LEFT JOIN proveedores p ON m.proveedor_id = p.id
                           WHERE m.categoria = :categoria
                           ORDER BY m.id ASC");
    $stmt->bindParam(":categoria", $filtro_categoria);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT m.*, p.nombre AS proveedor_nombre 
                           FROM medicamentos m
                           LEFT JOIN proveedores p ON m.proveedor_id = p.id
                           ORDER BY m.id ASC");
    $stmt->execute();
}

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
    <h1 class="mb-4 text-info text-center">Panel de Inventario</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="registro.php" class="btn btn-info text-white">Agregar Medicamento</a>

        <!-- Formulario de filtro por categoría -->
        <form method="GET" class="d-flex">
            <select name="categoria" class="form-select me-2">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $cat === $filtro_categoria ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-info text-white">Filtrar</button>
        </form>
    </div>

    <table class="table table-striped table-primary table-bordered text-center align-middle">
        <thead>
            <tr>
                <th class="bg-info text-white">ID</th>
                <th class="bg-info text-white">Nombre</th>
                <th class="bg-info text-white">Categoría</th>
                <th class="bg-info text-white">Cantidad</th>
                <th class="bg-info text-white">Precio</th>
                <th class="bg-info text-white">Proveedor</th>
                <th class="bg-info text-white">Acciones</th>
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
