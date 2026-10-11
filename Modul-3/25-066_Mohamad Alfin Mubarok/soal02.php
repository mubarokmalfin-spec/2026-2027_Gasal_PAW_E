<?php
// 2.1
$fruits = array("Avocado", "Blueberry", "Cherry");

for ($i = 1; $i <= 5; $i++) {
    $fruits[] = "Buah Tambahan " . $i;
}

$arrlength = count($fruits);
echo "Panjang array saat ini: " . $arrlength . "<br><br>";

for ($x = 0; $x < $arrlength; $x++) {
    echo $fruits[$x] . "<br>";
}

echo "<br>";
echo "<br>";
echo "<br>";

// 2.2
$vegies = array("Carrot", "Broccoli", "Spinach");
$veglength = count($vegies);

for ($x = 0; $x < $veglength; $x++) {
    echo $vegies[$x] . "<br>";
}
?>
