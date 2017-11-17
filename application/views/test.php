foreach ($row as $value => $data) {
if ($data != null ) {
$split = preg_split('/,/', $data);


if ($count_genus == null) {
for ($j = 0; $j < count($split); $j++) {
$count_genus[$j] =  $split[$j];
$count_genus2[$j] =  $split[$j];
if ($num < $split[$j]){
$num = $split[$j];
$key_index = $j;
$key_sam = $value;
}
}
}else{
for ($j = 1; $j < count($split); $j++) {
$count_genus[$j] +=  $split[$j];
$count_genus2[$j] =  $split[$j];
if ($num < $split[$j]){
$num = $split[$j];
$key_index = $j;
$key_sam = $value;
}
}

}

}

$max_for_sam[$value] = max($count_genus2);
}

echo $key_index.'<br>';
echo $num.'<br>';
$k_genus = 0;
$max_genus = 0;
foreach ($max_for_sam as $key => $value) {
if ($value >= $max_genus) {
$max_genus = max($count_genus);
$k_genus = $key;
}
}
echo $max_genus.'<br>';

$row_default = explode("\n", $genus);
$split_row = preg_split('/,/', $row_default[0]);
echo $split_row[$key_index].'<br>';
echo $num * 100 / $count_genus[$key_index].'<br>';

echo $key_sam.'<br>';

