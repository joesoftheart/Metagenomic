#!/usr/bin/php
<?php
$user = "joesoftheart";
$project = "SAMPLE-WES1053";


    echo "run make file";
    $output_path = "outputdir=../owncloud/data/$user/files/$project/data/input/";
    $input_path = "inputdir=../owncloud/data/$user/files/$project/data/input/";

    $jobname = $user."_php_run_makefile";


    #make.file
    $make = "make.file($input_path,$output_path)";

    file_put_contents('../owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $make);


    $cmd = "qsub -N '$jobname' -cwd -b y ../Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch";

    shell_exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat,$output);

    $id_job = "" ; # give job id
    foreach ($output as $key_var => $value ) {

        if($key_var == "1"){
            $data = explode(":", $value);
            $id_job = $data[1];
        }
    }
    $loop = true;
//    while ($loop) {
//
//        $check_run = exec("qstat -u apache  '$id_job' ");
//
//        if($check_run == false){
//            $loop = false;
//            return "0";
//        }
//    }


?>


