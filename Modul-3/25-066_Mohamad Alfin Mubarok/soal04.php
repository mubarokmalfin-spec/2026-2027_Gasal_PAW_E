<?php
// 4.1 Mengakses array asosiatif $height dengan perulangan
$height = array("Andy"=>"176", "Barry"=>"165", "Charlie"=>"170");

$height["David"] = "180";
$height["Ethan"] = "172";
$height["Frank"] = "168";
$height["George"] = "175";
$height["Harry"] = "182";

echo "height = (";
$keys = array_keys($height);
$last_key = end($keys);
foreach ($height as $nama => $tinggi) {
    echo '"' . $nama . '"=>"' . $tinggi . '"';
    if ($nama !== $last_key) {
        echo ", ";
    }
}
echo ")<br><br>";

foreach ($height as $nama => $tinggi) {
    echo "$nama is $tinggi cm tall.<br>";
}

echo "<hr>";

// 4.2 Membuat array $weight dan mengaksesnya dengan perulangan FOR
$weight = array("Andy"=>"70", "Barry"=>"65", "Charlie"=>"75");

echo "weight = (";
$keys_w = array_keys($weight);
$last_key_w = end($keys_w);
foreach ($weight as $nama => $berat) {
    echo '"' . $nama . '"=>"' . $berat . '"';
    if ($nama !== $last_key_w) {
        echo ", ";
    }
}
echo ")<br><br>";

$keys_weight = array_keys($weight);
$arrlength = count($weight);
for ($x = 0; $x < $arrlength; $x++) {
    $nama = $keys_weight[$x];
    $berat = $weight[$nama];
    echo "$nama is $berat kg.<br>";
}
?>
