<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: panel.php");
    exit;
}

$id = $_GET['id'];

$db = new Database();
$pdo = $db->conectar();

$stmt = $pdo->prepare("DELETE FROM medicamentos WHERE id = :id");
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

header("Location: panel.php");
exit;
