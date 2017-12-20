<?php
$path_file_original = $argv[1];
//read the entire string
$str=implode("",file($path_file_original));
$fp=fopen($path_file_original,'w');
//replace something in the file
$str=str_replace('.0','',$str);
//now, save the file
fwrite($fp,$str,strlen($str));
?>


