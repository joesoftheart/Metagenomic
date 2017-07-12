<div id="page-wrapper">
<?php

$user = "joesoftheart";
$project = "SAMPLE-WES-2023";
$path_owncloud = "owncloud/data/joesoftheart/files/SAMPLE-WES-2023/output";
$file_read = array('svg');
$cdir = array();
$result_folder = array();
$result_files = array();
if (is_dir($path_owncloud)) {

    $select_folder = array_diff(scandir($path_owncloud, 1), array('.', '..'));
    $cdir = scandir($path_owncloud);

    foreach ($cdir as $key => $value) {
        if (!in_array($value, array('.', '..'))) {
            if (is_dir($path_owncloud . DIRECTORY_SEPARATOR . $value)) {
                $result_folder[$value] = $value;
            } else {


                $result_files[$value] = $value;
            }


        }
    }
}

$num_folder = count($result_folder);
$num_files = count($result_files);


$count_files = 0;
if ($cdir != null) {
                $file_in_dir = scandir($path_owncloud);

                foreach ($file_in_dir as $key => $value) {
                    $type = explode('.', $value);
                    $type = array_reverse($type);
                    if (in_array($type[0], $file_read)) {
                        $count_files++;
                        echo $value;
                        echo "<br/>";
                    }
    }
}


?>

</div>