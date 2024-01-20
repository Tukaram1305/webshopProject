<?php
// Zmiana statusu zamowienia przez administratora
require_once "connect.php";
$conn = @new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$ordId = $_POST['ordID'];
$ordStatus = $_POST['status'];
$sql = sprintf("UPDATE orders SET status=%s WHERE id=%d",$ordStatus,$ordId);
$result = $conn->query($sql);
if ($result) echo "OK!";
else echo "Błąd!";

$conn->close();
?>

