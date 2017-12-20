<?php

include('setting_sge.php');
putenv("SGE_ROOT=$SGE_ROOT");
putenv("PATH=$PATH");


$path = $argv[1];
$path_log = $argv[2];
$user = $argv[3];


if ($path != "" && $path_log != "" && $user != "") {

    make_sra($path, $path_log, $user);

} else {

    echo "Error Command !!!";

}


function make_sra($path, $path_log, $user)
{

    echo "make_sra" . "\n";

    $jobname = $user . "_make_sra";

    $make = "make.sra(file=stability.files, project=test.project, mimark=stability.tsv ,inputdir=$path,outputdir=$path)";

    file_put_contents($path . '/sra.batch', $make);

    $cmd = "qsub  -N '$jobname' -o $path_log  -cwd -j y -b y Mothur/mothur $path/sra.batch";


    shell_exec($cmd);

    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);

    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {

        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }

    $loop = true;
    while ($loop) {

        $check_run = exec("qstat -j $id_job ");

        if ($check_run == false) {

            remove_logfile_mothur($path);
            break;
        }
    }
}


function remove_logfile_mothur($path)
{

    $path_dir = $path;
    if (is_dir($path_dir)) {
        if ($read = opendir($path_dir)) {
            while (($logfile = readdir($read)) !== false) {

                $allowed = array('logfile');
                $ext = pathinfo($logfile, PATHINFO_EXTENSION);

                if (in_array($ext, $allowed)) {

                    unlink($path_dir . $logfile);
                }
            }

            closedir($read);
        }
    }
    echo "remove_logfile_mothur" . "\n";


}


?>