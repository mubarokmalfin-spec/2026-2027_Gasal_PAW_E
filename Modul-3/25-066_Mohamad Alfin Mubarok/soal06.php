<?php
// 1. array_push()
$arr1 = array("A");
echo "Array awal: (\"A\")<br>";
array_push($arr1, "B");
echo "Hasil array_push: " . implode(' ', $arr1) . "<br><br>";

// 2. array_merge()
echo "Array awal: (\"A\", \"B\") digabung dengan (\"C\")<br>";
$arr2 = array_merge(array("A", "B"), array("C"));
echo "Hasil array_merge: " . implode(' ', $arr2) . "<br><br>";

// 3. array_values()
$arr3 = array("x" => 1, "y" => 2);
echo "Array awal: (\"x\" => 1, \"y\" => 2)<br>";
$res3 = array_values($arr3);
echo "Hasil array_values: " . implode(' ', $res3) . "<br><br>";

// 4. array_search()
$arr4 = array("A", "B", "C");
echo "Mencari \"B\" pada array: (\"A\", \"B\", \"C\")<br>";
$pos = array_search("B", $arr4);
echo "Hasil array_search: " . $pos . "<br><br>";

// 5. array_filter()
$arr5 = array(0, 1, false, 2, "", 3, "array");
echo "Array awal: (0, 1, false, 2, \"\", 3, \"array\")<br>";
$res5 = array_filter($arr5);
echo "Hasil array_filter: " . implode(' ', $res5) . "<br><br>";

// 6. sort() & rsort()
$arr6 = array(3, 1, 2);
echo "Array awal: (3, 1, 2)<br>";
sort($arr6);
echo "Hasil sort: " . implode(' ', $arr6) . "<br>";
rsort($arr6);
echo "Hasil rsort: " . implode(' ', $arr6) . "<br><br>";

// 7. asort(), ksort(), arsort(), krsort()
$assoc = array("Peter" => 35, "Ben" => 37, "Joe" => 43);
echo "Array awal: (\"Peter\"=>35, \"Ben\"=>37, \"Joe\"=>43)<br>";

$a = $assoc; asort($a);
echo "Hasil asort: ";
foreach($a as $k => $v) { echo "$k => $v, "; }
echo "<br>";

$k = $assoc; ksort($k);
echo "Hasil ksort: ";
foreach($k as $key => $val) { echo "$key => $val, "; }
echo "<br>";

$ar = $assoc; arsort($ar);
echo "Hasil arsort: ";
foreach($ar as $key => $val) { echo "$key => $val, "; }
echo "<br>";

$kr = $assoc; krsort($kr);
echo "Hasil krsort: ";
foreach($kr as $key => $val) { echo "$key => $val, "; }
?>
