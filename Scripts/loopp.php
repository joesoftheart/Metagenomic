#!/usr/bin/php
<?php
$myfile = fopen("Scripts/newfile.txt", "w") or die("Unable to open file!");

for ($i=0;$i<10000;$i++){

    $txt = "John Doe\n";
    fwrite($myfile, $txt);

}

fclose($myfile);
?>