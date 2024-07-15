<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brightway";
$message = '';
$messageType = '';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    $stmt = $conn->prepare("UPDATE admin SET $field = ? WHERE id = ?");
    $stmt->bind_param('si', $value, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Update successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed']);
    }

    $stmt->close();
}
?>
