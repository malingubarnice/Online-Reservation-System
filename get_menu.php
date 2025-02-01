<?php
$conn = new mysqli("localhost", "root", "", "coppers_ivy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM menu");
$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}
echo json_encode($menu);
$conn->close();
?>
