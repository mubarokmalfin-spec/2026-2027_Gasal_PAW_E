<?php
// 3.1
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
echo ")<br>";

echo "Nilai dengan indeks terakhir: " . end($height) . "<br><br>";


unset($height["Barry"]);


echo "height = (";
$keys = array_keys($height);
$last_key = end($keys);
foreach ($height as $nama => $tinggi) {
    echo '"' . $nama . '"=>"' . $tinggi . '"';
    if ($nama !== $last_key) {
        echo ", ";
    }
}
echo ")<br>";


echo "Nilai dengan indeks terakhir setelah dihapus: " . end($height) . "<br>";


//3.2
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
echo ")<br>";

echo "Data kedua: " . $weight["Barry"];
?>
