<?php
$conn = new mysqli('localhost', 'root', '', 'dynamic');
$query = "SELECT * FROM products";
$result = $conn->query($query);

$options = '<option value="" disabled selected>Select item</option>';
while ($row = $result->fetch_assoc()) {
    $n = $row['name'];
    $p = $row['price'];
    $options .= "<option value=\"$p\">$n</option>";
}

echo $options;
$conn->close();

