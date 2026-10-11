<?php
$fruits = array("Avocado", "Blueberry", "Cherry");

$fruits[] = "Durian";
$fruits[] = "Elderberry";
$fruits[] = "Fig";
$fruits[] = "Grape";
$fruits[] = "Honeydew";

echo "fruits = (\"" . implode('", "', $fruits) . "\")<br>";
echo "Nilai dengan indeks tertinggi: " . end($fruits) . "<br><br>";

unset($fruits[1]);
$fruits = array_values($fruits);

echo "Data Blueberry dihapus.<br>";
echo "fruits = (\"" . implode('", "', $fruits) . "\")<br>";
echo "Nilai dengan indeks tertinggi: " . end($fruits);
?>
