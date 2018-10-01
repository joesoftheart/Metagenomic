
<?php

include('setting_sge.php');
putenv("SGE_ROOT=$SGE_ROOT");
putenv("PATH=$PATH");


$user = $argv[1];
$project = $argv[2];
$path_in = $argv[3];
$path_out = $argv[4];
$GLOBALS['size'] = $argv[5];
$GLOBALS['path_log'] = $argv[6];

if ($user != "" && $project != "" && $path_in != "" && $path_out != "" && $argv[5] != "" && $argv[6] != "") {
    echo "Check Parameter Success" . "\n";
    sub_sample($user, $project, $path_in, $path_out);

} else {
    echo "user : " . $user . "\n";
    echo "project : " . $project . "\n";
    echo "path_in : " . $path_in . "\n";
    echo "path_out : " . $path_out . "\n";
    echo "size : " . $GLOBALS['size'] . "\n";
    echo "path_log : " . $GLOBALS['path_log'];
}


function sub_sample($user, $project, $path_in, $path_out)
{

    echo "Run sub_sample_phylotype " . "\n";
    $jobname = $user . "_sub_sample_phylotype";

    $make = "sub.sample(shared=final.tx.shared,size=" . $GLOBALS['size'] . ",inputdir=$path_in,outputdir=$path_out)";

    file_put_contents($path_in . '/advance.batch', $make);
    $log = $GLOBALS['path_log'];
    $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";

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

            remove_logfile_mothur($path_out);
            break;

        }
    }
}


function remove_logfile_mothur($path_out)
{

    $path_dir = $path_out;
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

}


?>