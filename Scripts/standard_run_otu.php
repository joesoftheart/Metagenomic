<?php

$user = $argv[1];
$id = $argv[2];
$project = $argv[3];
$path = $argv[4];
include('setting_sge.php');
putenv("SGE_ROOT=$SGE_ROOT");
putenv("PATH=$PATH");
//putenv("SGE_ROOT=/opt/sge/");
//putenv("PATH=/opt/sge/bin/lx-amd64/");
//putenv( "SGE_CELL=default");
//putenv("SGE_QMASTER_PORT=6444");
//putenv("SGE_EXECD_PORT=6445");
// check value params
if ($user != null && $project != null && $path != null && $id != null) {
    run($user, $id, $project, $path);
}
// Run Program
function run($user, $id, $project, $path)
{
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', "");
    check_file($user, $id, $project, $path);
}
$progres_f = "owncloud/data/$user/files/$project/output/progress.txt";
// check file
function check_file($user, $id, $project, $path)
{
    echo "\n";
    echo "Run check_file :";
    $path_stability = "../owncloud/data/$user/files/$project/input/stability.files";
    $path_file = $path_stability;
    if (file_exists($path_file)) {
        echo "go to check file oligo ->";
        check_oligos($user, $id, $project, $path);
    } else {
        echo "go to run make file ->";
        run_makefile($user, $id, $project, $path);
    }
}
// check file oligos
function check_oligos($user, $id, $project, $path)
{
    echo "\n";
    echo "Run check_oligos :";
    $total_oligo = 0;
    $path_dir = $path . "/input/";
    if (is_dir($path_dir)) {
        if ($read = opendir($path_dir)) {
            while (($file_oligo = readdir($read)) !== false) {
                $allowed = array('oligo');
                $ext = pathinfo($file_oligo, PATHINFO_EXTENSION);
                if (in_array($ext, $allowed)) {
                    $total_oligo += 1;
                    echo "go to make_contigs_olios ->";
                    make_contigs_oligos($file_oligo, $user, $id, $project, $path);
                }
            }
            closedir($read);
        }
    }
    if ($total_oligo == 0) {
        echo "go to makecontig_summary -> ";
        make_contigs_summary($user, $id, $project, $path);
    }
}
// make file
function run_makefile($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "quality" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run run_makefile :";
    $jobname = $user . "_" . $id . "_run_makefile";
    $make = "make.file(inputdir=$path/input/,outputdir=$path/input/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $make);
    $cmd = "qsub  -N   '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "check file again ->";
            check_file($user, $id, $project, $path);
            break;
        }
    }
}
// make contig oligos
function make_contigs_oligos($file_oligo, $user, $id, $project, $path)
{
    echo "\n";
    echo "Run make_contigs_oligos :";
    $path = $path;
    $jobname = $user . "_" . $id . "_oligo";
    $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=8 ,inputdir=$path/input/,outputdir=$path/input/)
            summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to replace_group ->";
            readlogs_make_contigs_oligos($user, $id, $project, $path, $id_job);
            break;
        }
    }
}
// make contigs summary
function make_contigs_summary($user, $id, $project, $path)
{
    echo "\n";
    echo "Run make_contigs_summary";
    $jobname = $user . "_" . $id . "_make_contigs_summary";
    $cmd = "make.contigs(file=stability.files,processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to reads log make contigs summary";
            readlogs_make_contigs_summary($user, $id, $project, $path,$id_job);
            break;
        }
    }
}
// read log to report
function readlogs_make_contigs_oligos($user, $id, $project, $path, $id_job)
{
    echo "\n";
    echo "Run readlogs_make_contigs_oligos :";
    $name = $user . "_" . $id . "_readlogs_make_contigs_oligos.o" . $id_job;
    $file = file_get_contents("Logs_sge/phylotype/" . $name);

    if (file_exists("owncloud/data/$user/files/$project/output/database.txt")) {
        file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "");
    }
    $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum|Mean).*\$/m";
    if (preg_match_all($pattern, $file, $matches)) {
        $val = implode("\n", $matches[0]);
        $sum = explode("\n", $val);
        $index = 0;
        foreach ($sum as $key => $value) {

            if ($index == 7) {
                $avg = preg_split('/\s+/', $value);
                echo $avg[6] . '<br>';
                file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "count_seqs:" . $avg[6] . "\n", FILE_APPEND);
            }

            if ($index == 8) {
                $sum_seqs = preg_split('/\s+/', $value);
                file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "avg_length:" . $sum_seqs[3] . "\n", FILE_APPEND);
            }


            $index++;
        }
    } else {
        echo "No match readlogs_make_contigs_oligos";
    }
    echo "success read logs go to replace_group";
    replace_group($user, $id, $project, $path);


}
// read log to report
function readlogs_make_contigs_summary($user, $id, $project, $path, $id_job)
{
    echo "\n";
    echo "Run readlogs_make_contigs_summary :";
    $name = $user . "_" . $id . "_make_contigs_summary.o" . $id_job;
    $file_name = str_replace(' ', '', $name);
    $file = file_get_contents("Logs_sge/phylotype/" . $file_name);
    file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "");
    $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum|Mean).*\$/m";
    if (preg_match_all($pattern, $file, $matches)) {
        $val = implode("\n", $matches[0]);
        $sum = explode("\n", $val);
        $index = 0;
        foreach ($sum as $key => $value) {
            if ($index == 7) {
                $avg = preg_split('/\s+/', $value);
                echo $avg[6] . '<br>';
                file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "count_seqs:" . $avg[6] . "\n", FILE_APPEND);
            }
            if ($index == 8) {
                $sum_seqs = preg_split('/\s+/', $value);
                file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "avg_length:" . $sum_seqs[3] . "\n", FILE_APPEND);
            }
            $index++;
        }
    } else {
        echo "No match readlogs_make_contigs_summary";
    }
    echo "success read logs  to replace_group";
    replace_group($user, $id, $project, $path);
}
// replace name because - not use
function replace_group($user, $id, $project, $path)
{
    echo "\n";
    echo "Run Replace_group :";
    $file_path = $path . "/output/stability.contigs.groups";
    $data_w = array();
    if ($file = fopen($file_path, "r")) {
        $i = 0;
        while (!feof($file)) {
            $line = fgets($file);
            $out = explode("\t", $line);

            if ($out[0] == "" or $out[1] == "") {
                echo "No replace _ groups";

            } else {
                $out[1] = str_replace("-", "_", $out[1]);
                $data = $out[0] . "\t" . $out[1];
                array_push($data_w, $data);
            }
        }
        fclose($file);
        if (file_exists($file_path)) {
            file_put_contents($file_path, "");
            foreach ($data_w as $value) {
                file_put_contents($file_path, $value, FILE_APPEND);
            }

        } else {
            foreach ($data_w as $value) {
                file_put_contents($file_path, $value, FILE_APPEND);
            }
        }

    }
    echo "go to screen_seqs ->";
    screen_seqs($user, $id, $project, $path);
}
// screen seqs
function screen_seqs($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "quality-finish" . "\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "align-sequence" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run screen_seqs";
    $jobname = $user . "_" . $id . "_screen_seqs";
    $cmd = "screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=$path/input/,outputdir=$path/output/)
unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=$path/input/,outputdir=$path/output/)
count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(count=stability.trim.contigs.good.count_table,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to align_seqs ->";
            align_seqs($user, $id, $project, $path);
            break;
        }
    }
}
// align-seqs
function align_seqs($user, $id, $project, $path)
{
    echo "\n";
    echo "Run align_seqs";
    $jobname = $user . "_" . $id . "_align_seqs";
    $cmd = "align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=silva.v4.fasta, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to read_log_sungrid_start_end->";
            read_log_sungrid_start_end($user, $id, $project, $path, $id_job);
            break;
        }
    }
}
// read start an end
function read_log_sungrid_start_end($user, $id, $project, $path, $id_job)
{
    $path_logs = $path . "/logs/";
    $name = $user . "_" . $id . "_align_seqs.o" . $id_job;
    $file_name = str_replace(' ', '', $name);
    $file = file_get_contents("Logs_sge/otu/" . $file_name);
    //echo var_dump($file);
    $search_for = 'Start';
    $pattern = preg_quote($search_for, '/');
    $start_array = array();
    $end_array = array();
    $start = 0;
    $end = 0;
    $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum).*\$/m";
    if (preg_match_all($pattern, $file, $matches)) {
        $val = implode("\n", $matches[0]);
        $sum = explode("\n", $val);

        foreach ($sum as $key => $value) {
            //echo  $value ."<br/>";
            if ($key >= "1") {
                $va_ex = explode(":", $value);
                $va_ex2 = explode("\t", trim($va_ex[1]));
                array_push($start_array, $va_ex2[0]);
                array_push($end_array, $va_ex2[1]);
            }
        }
    }
    #start
    $count_start = array_count_values($start_array);
    $start_max = max($count_start);
    $start_min = min($count_start);
    #end
    $count_end = array_count_values($end_array);
    $end_max = max($count_end);
    $end_min = min($count_end);
    if (($start_min == $start_max) || ($end_min == $end_max)) {
        foreach ($sum as $key => $value) {
            echo $value . "<br/>";
        }
    } elseif (($start_min != $start_max) && ($end_min != $end_max)) {
        #start
        foreach ($count_start as $key_start => $value_start) {
            if ($start_max == $value_start) {
                $start = $key_start;
            }
        }
        #end
        foreach ($count_end as $key_end => $value_end) {
            if ($end_max == $value_end) {
                $end = $key_end;
            }
        }
        echo "\n";
        echo "Start : " . $start;
        echo " End : " . $end;
        echo "go to avg_seq_before_filter->";
        if ($start != null && $end != null) {
            avg_seq_before_filter($user, $id, $project, $path, $start, $end);
        } else {
            echo "\n";
            echo "Start and End null value";
        }
    }
}
// avg
function avg_seq_before_filter($user, $id, $project, $path, $start, $end)
{
    echo "\n";
    echo " Run avg_seq_before_filter :";
    $jobname = $user . "_" . $id . "_avg_seq_before_filter";
    $cmd = "screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=$start, end=$end, maxambig=8, maxhomop=8, maxlength=260, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=current, count=current,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to screen_remove->";
            screen_remove($user, $id, $project, $path);
            break;
        }
    }
}
// screen remove
function screen_remove($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "align-sequence-finish" . "\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "pre-cluster-chimera" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run screen_remove :";
    $jobname = $user . "_" . $id . "_screen_remove";
    $cmd = "filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8,inputdir=$path/input/,outputdir=$path/output/)
unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table,inputdir=$path/input/,outputdir=$path/output/)
pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=2,inputdir=$path/input/,outputdir=$path/output/)
chimera.vsearch(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8,inputdir=$path/input/,outputdir=$path/output/)
remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.accnos,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=current, count=current,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to classify_system->";
            classify_system($user, $id, $project, $path);
            break;
        }
    }
}
// classify
function classify_system($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "pre-cluster-chimera-finish" . "\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "classify-sequence-remove" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run classify_system :";
    $jobname = $user . "_" . $id . "_classify_system";
    $cmd = "classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=80, processors=8,inputdir=$path/input/,outputdir=$path/output/)
    remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=current, count=current,inputdir=$path/input/,outputdir=$path/output/)
summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=$path/input/,outputdir=$path/output/)
system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta owncloud/data/$user/files/$project/output/final.fasta)
system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table owncloud/data/$user/files/$project/output/final.count_table)
system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy owncloud/data/$user/files/$project/output/final.taxonomy)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to readlogs_classify_system->";
            readlogs_classify_system($user, $id, $project, $path, $id_job);
            break;
        }
    }
}
// read log class to report
function readlogs_classify_system($user, $id, $project, $path, $id_job)
{
    echo "\n";
    echo "Run readlogs_classify_system :";
    $name = $user . "_" . $id . "_classify_system.o" . $id_job;
    $file = file_get_contents("Logs_sge/phylotype/" . $name);
    $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum|Mean|total).*\$/m";
    if (preg_match_all($pattern, $file, $matches)) {
        $val = implode("\n", $matches[0]);
        $sum = explode("\n", $val);
        $index = 0;
        foreach ($sum as $key => $value) {

            if ($index == 8) {
                $avg = preg_split('/\s+/', $value);
                file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "num_seqs:" . $avg[2] . "\n", FILE_APPEND);
            }

            if ($index == 9) {
                $sum_seqs = preg_split('/\s+/', $value);
                file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "avg_reads:" . $sum_seqs[4] . "\n", FILE_APPEND);
            }
            $index++;
        }
    }
    echo "go to phylotype_count->";
    phylotype_count($user, $id, $project, $path);
}
// phylotype count
function phylotype_count($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "classify-sequence-remove-finish" . "\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "classify-otu" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run phylotype_count :";
    $jobname = $user . "_" . $id . "_phylotype_count";
    $cmd = "dist.seqs(fasta=final.fasta, cutoff=0.21, processors=8,inputdir=$path/input/,outputdir=$path/output/)
cluster(column=final.dist, count=final.count_table, method=opti, cutoff=0.03,inputdir=$path/input/,outputdir=$path/output/)
make.shared(list=final.opti_mcc.list, count=final.count_table, label=0.03,inputdir=$path/input/,outputdir=$path/output/)
classify.otu(list=final.opti_mcc.list, count=final.count_table, taxonomy=final.taxonomy, label=0.03,inputdir=$path/input/,outputdir=$path/output/)
classify.otu(list=final.opti_mcc.list, count=final.count_table, taxonomy=final.taxonomy, basis=sequence, output=simple, label=0.03,inputdir=$path/output/,outputdir=$path/output_plot/)
count.groups(shared=final.opti_mcc.shared,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
    $check_qstat = "qstat  -j '$jobname' ";
    exec($check_qstat, $output);
    $id_job = ""; # give job id
    foreach ($output as $key_var => $value) {
        if ($key_var == "1") {
            $data = explode(":", $value);
            $id_job = $data[1];
            $id_job_string = str_replace(' ', '', $id_job);
        }
    }
    $loop = true;
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to read_log_sungrid_phylotype_count->";
            read_log_sungrid_phylotype_count($user, $id, $project, $path, $id_job_string);
            break;
        }
    }
}
// read log lib size to report
function read_log_sungrid_phylotype_count($user, $id, $project, $path, $id_job_string)
{
    echo "\n";
    echo "Run read_log_sungrid_phylotype_count :";
    $name = $user . "_" . $id . "_phylotype_count.o" . $id_job_string;
    $file_name = str_replace(' ', '', $name);
    $searchfor = 'contains';
    $file = file_get_contents("Logs_sge/phylotype/" . $file_name);
    $pattern = preg_quote($searchfor, '/');
// finalise the regular expression, matching the whole line
    $pattern = "/^.*$pattern.*\$/m";
// search, and store all matching occurences in $matches
    if (preg_match_all($pattern, $file, $matches)) {
        $i = 0;
        $t = array();
        foreach ($matches[0] as $ma) {
            if ($ma != null) {
                $size = explode(" ", $ma);
                $to = explode(".", $size[2]);
                $t[$i] = $to[0];
                $i++;
            }
        }
        $size = min($t);
        file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "lib_size:" . $size . "\n", FILE_APPEND);
        echo "Size :" . $size;
        echo "<br>";
        echo "Go to sub_sample_sammary->";
        sub_sample_summary($user, $id, $project, $path, $size);
    } else {
        echo "No matches found";
    }
}
// sub sample and sammary
function sub_sample_summary($user, $id, $project, $path, $size)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "classify-otu-finish" . "\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "alpha-beta-diversity" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run sub_sample_summary :";
    $jobname = $user . "_" . $id . "_sub_sample_sammary";
    $cmd = "sub.sample(shared=final.opti_mcc.shared, size=$size,inputdir=$path/input/,outputdir=$path/output/)
collect.single(shared=final.opti_mcc.shared, calc=chao, freq=100, label=0.03,inputdir=$path/input/,outputdir=$path/output/)
rarefaction.single(shared=final.opti_mcc.shared, calc=sobs, freq=100, label=0.03, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.single(shared=final.opti_mcc.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon-simpson, subsample=$size, label=0.03,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "go to read_name_sample->";
            echo "Total" . $size;
            read_name_sample($user, $id, $project, $path, $size);
            break;
        }
    }
}
// read name sample use plot mathur
function read_name_sample($user, $id, $project, $path, $size)
{
    $group_sample = array();
    $name_sample = null;
    if ($file = fopen('../owncloud/data/' . $user . '/files/' . $project . '/input/stability.files', "r")) {
        $i = 0;
        while (!feof($file)) {
            $line = fgets($file);
            $parts = preg_split('/\s+/', $line);
            echo $parts[0];
            echo "\n";
            if ($parts[0] != null) {
                $group_sample[$i] = $parts[0];
                $i++;
            }
        }
        fclose($file);
        foreach ($group_sample as $value) {
            $sample = str_replace("-", "_", $value);
            if ($name_sample == null) {
                $name_sample = $sample;
            } else {
                $name_sample = $name_sample . "-" . $sample;
            }
        }
        echo "go to plot_graph->";
        plot_graph($user, $id, $project, $path, $size, $name_sample);
    }
}
// plot graph by mothur program
function plot_graph($user, $id, $project, $path, $size, $name_sample)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot_graph" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run plot_graph :";
    $jobname = $user . "_" . $id . "_plot_graph";
    $cmd = "heatmap.bin(shared=final.opti_mcc.0.03.subsample.shared, scale=log2, numotu=50,inputdir=$path/input/,outputdir=$path/output/)
dist.shared(shared=final.opti_mcc.shared, calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, subsample=$size, processors=8,inputdir=$path/input/,outputdir=$path/output/)
heatmap.sim(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
heatmap.sim(phylip=final.opti_mcc.jclass.0.03.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=$name_sample, all=T,inputdir=$path/input/,outputdir=$path/output/)
venn(shared=final.opti_mcc.0.03.subsample.shared, groups=$name_sample,inputdir=$path/input/,outputdir=$path/output/)
venn(shared=final.opti_mcc.0.03.subsample.shared, groups=$name_sample,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, processors=8,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.opti_mcc.morisitahorn.0.03.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.opti_mcc.jclass.0.03.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.opti_mcc.braycurtis.0.03.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.opti_mcc.lennon.0.03.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
pcoa(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
nmds(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, mindim=3, maxdim=3,inputdir=$path/input/,outputdir=$path/output/)
amova(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, design=soil.design,inputdir=$path/input/,outputdir=$path/output/)
homova(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, design=soil.design,inputdir=$path/input/,outputdir=$path/output/)
corr.axes(axes=final.opti_mcc.thetayc.0.03.lt.ave.pcoa.axes, shared=final.opti_mcc.0.03.subsample.shared, method=spearman, numaxes=3, label=0.03,inputdir=$path/input/,outputdir=$path/output/)
system(mv final.opti_mcc.0.03.subsample.spearman.corr.axes final.opti_mcc.0.03.subsample.spearman.corr.axesThetayc3D,inputdir=$path/input/,outputdir=$path/output/)
corr.axes(axes=final.opti_mcc.thetayc.0.03.lt.ave.pcoa.axes, metadata=soilpro.metadata, method=pearson, numaxes=3, label=0.03,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to create_file_input_heatmap ->";
            create_file_input_heatmap($user, $id, $project, $path);
            break;
        }
    }


}
// create file input heatmap
function create_file_input_heatmap($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "alpha-beta-diversity-finish" . "\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot-graph" . "\n", FILE_APPEND);

    echo "\n";
    echo "Run create_file_input_heatmap :";
    $jobname = $user . "_" . $id . "_create_file_input_heatmap";
    $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f R_Script/create_input_heatmap_otu.php $user $project";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to create_file_input_abun ->";
            create_file_input_abun($user, $id, $project, $path);
            break;
        }
    }

}
// create_file_input_abun
function create_file_input_abun($user, $id, $project, $path)
{
    echo "\n";
    echo "Run create_file_input_abun :";
    $jobname = $user . "_" . $id . "_create_file_input_abun";
    $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f R_Script/create_input_abundance_otu.php $user $project";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to create_input_alphash ->";
            create_input_alphash($user, $id, $project, $path);
            break;
        }
    }

}
//create_input_alphash
function create_input_alphash($user, $id, $project, $path)
{
    echo "\n";
    echo "Run create_input_alphash :";
    $jobname = $user . "_" . $id . "_create_input_alphash";
    $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f R_Script/create_input_alphash_otu.php $user $project";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to plot_graph_r_heartmap ->";
            create_input_biplot($user, $id, $project, $path);
            break;
        }
    }

}
//create_input_biplot
function create_input_biplot($user, $id, $project, $path)
{
    echo "\n";
    echo "Run create_input_biplot :";
    $jobname = $user . "_" . $id . "_create_input_biplot";
    $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f R_Script/create_input_biplot_otu.php $user $project";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to plot_graph_r_heartmap ->";
            plot_graph_r_heatmap($user, $id, $project, $path);
            break;
        }
    }

}
// plot_graph_r_heatmap
function plot_graph_r_heatmap($user, $id, $project, $path)
{
    echo "\n";
    echo "Run plot_graph_r_heatmap :";
    $path_input_csv = "owncloud/data/$user/files/$project/output/file_after_reverse.csv";
    $path_to_save = "owncloud/data/$user/files/$project/output/heatmap.png";
    $jobname = $user . "_" . $id . "_plot_graph_r_heartmap";
    $cmd = "qsub -N $jobname -o Logs_sge/otu/ -e Logs_sge/otu/  -cwd -b y /usr/bin/Rscript R_Script/Heatmap_graph.R $path_input_csv $path_to_save";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to plot_graph_r_NMD ->";
            plot_graph_r_NMD($user, $id, $project, $path);
            break;
        }
    }

}
// plot_graph_r_NMD
function plot_graph_r_NMD($user, $id, $project, $path)
{
    echo "\n";
    echo "Run plot_graph_r_NMD :";
    $path_input_axes = "owncloud/data/$user/files/$project/output/final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes";
    $path_to_save = "owncloud/data/$user/files/$project/output/NMD.png";
    $jobname = $user . "_" . $id . "plot_graph_r_NMD";
    $cmd = "qsub -N $jobname -o Logs_sge/otu/ -e Logs_sge/otu/  -cwd -b y /usr/bin/Rscript  R_Script/NMD_graph.R $path_input_axes $path_to_save";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to plot_graph_r_Rare->";
            plot_graph_r_Rare($user, $id, $project, $path);
            break;
        }
    }

}
// plot_graph_r_Rare
function plot_graph_r_Rare($user, $id, $project, $path)
{
    echo "\n";
    echo "Run plot_graph_r_Rare :";
    $path_input_rarefaction = "owncloud/data/$user/files/$project/output/final.opti_mcc.groups.rarefaction";
    $path_to_save = "owncloud/data/$user/files/$project/output/Rare.png";
    $jobname = $user . "_" . $id . "plot_graph_r_Rare";
    $cmd = "qsub -N $jobname -o Logs_sge/otu/ -e Logs_sge/otu/  -cwd -b y /usr/bin/Rscript  R_Script/Rarefaction_graph_otu.R $path_input_rarefaction $path_to_save";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to plot_graph_r_Abun ->";
            plot_graph_r_Abun($user, $id, $project, $path);
            break;
        }
    }

}
// plot_graph_r_Abun
function plot_graph_r_Abun($user, $id, $project, $path)
{
    echo "\n";
    echo "Run plot_graph_r_Abun :";
    $path_input_phylumex = "owncloud/data/$user/files/$project/output/file_phylum_count.txt";
    $path_to_save = "owncloud/data/$user/files/$project/output/Abun.png";
    $jobname = $user . "_" . $id . "plot_graph_r_Abun";
    $cmd = "qsub -N $jobname -o Logs_sge/otu/ -e Logs_sge/otu/  -cwd -b y /usr/bin/Rscript  R_Script/Abundance_bar_graph.R $path_input_phylumex $path_to_save";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to plot_graph_r_Alphash ->";
            plot_graph_r_Alphash($user, $id, $project, $path);
            break;
        }
    }

}
// plot_graph_r_Alphash
function plot_graph_r_Alphash($user, $id, $project, $path)
{
    echo "\n";
    echo "Run plot_graph_r_Alphash :";
    $path_input_chao_shannon = "owncloud/data/$user/files/$project/output/file_after_chao.txt";
    $path_to_save = "owncloud/data/$user/files/$project/output/Alpha.png";
    $jobname = $user . "_" . $id . "plot_graph_r_Alphash";
    $cmd = "qsub -N $jobname -o Logs_sge/otu/ -e Logs_sge/otu/  -cwd -b y /usr/bin/Rscript  R_Script/Alpha_chaoshannon_graph.R $path_input_chao_shannon $path_to_save";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            plot_graph_r_Biplot($user, $id, $project, $path);
            break;
        }
    }

}
// plot_graph_r_Biplot
function plot_graph_r_Biplot($user, $id, $project, $path)
{

    echo "\n";
    echo "Run plot_graph_r_Biplot :";
    $path_input_biplot_nmds = "owncloud/data/$user/files/$project/output/final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes";
    $path_output_biplot_withBiplotwithOTU = "owncloud/data/$user/files/$project/output/NewNMDS_withBiplotwithOTU.png";
    $path_input_biplot = "owncloud/data/$user/files/$project/output/output_bioplot.txt";
    $path_output_biplot_withBiplotwithMetadata = "owncloud/data/$user/files/$project/output/NewNMDS_withBiplotwithMetadata.png";
    $path_input_soilpro = "owncloud/data/$user/files/$project/output/soilpro.pearson.corr.axes";
    $jobname = $user . "_" . $id . "_plot_graph_r_Biplot";
    $cmd = "qsub -N $jobname -o Logs_sge/otu/ -e Logs_sge/otu/  -cwd -b y /usr/bin/Rscript  R_Script/Biplot_graph_otu.R $path_input_biplot_nmds $path_output_biplot_withBiplotwithOTU $path_input_biplot $path_output_biplot_withBiplotwithMetadata $path_input_soilpro";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to change name ->";
            plot_graph_r_Tree($user, $id, $project, $path);
            break;
        }
    }

}
// plot_graph_r_Tree
function plot_graph_r_Tree($user, $id, $project, $path)
{

    echo "\n";
    echo "Run plot_graph_r_Tree :";
    $path_input_tree = "owncloud/data/$user/files/$project/output/final.opti_mcc.morisitahorn.0.03.lt.ave.tre";
    $path_output_tree = "owncloud/data/$user/files/$project/output/Tree.png";

    $jobname = $user . "_" . $id . "_plot_graph_r_Tree";
    $cmd = "qsub -N $jobname -o Logs_sge/otu/ -e Logs_sge/otu/  -cwd -b y /usr/bin/Rscript  R_Script/Tree_graph.R $path_input_tree $path_output_tree";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to change name ->";
            make_biom($user, $id, $project, $path);
            break;
        }
    }

}
// make_biom file
function make_biom($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot-graph-finish" . "\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "make-biom" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run make_biom :";
    $jobname = $user . "_" . $id . "_make_biom";
    $cmd = "make.biom(shared=final.opti.mcc.shared, label=0.03,constaxonomy=final.opti_mcc.0.03.cons.taxonomy, reftaxonomy=gg_13_8_99.gg.tax, picrust=99_otu_map.txt,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Finish make biom ->";
            convert_biom($user, $id, $project, $path);
            break;
        }
    }
}
// convert_biom because error
function convert_biom($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "make-biom-finish" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run convert_biom :";
    $jobname = $user . "_" . $id . "_convert_biom";
    $path_input = "owncloud/data/$user/files/$project/output/final.tx.1.biom";
    $path_output_biom = "owncloud/data/$user/files/$project/output/normalized_otus.1.biom";
    $path_output_txt = "owncloud/data/$user/files/$project/output/final.tx.1.txt";
    $cmd = "qsub -N '$jobname' -o Logs_sge/otu/ -e Logs_sge/otu/ -cwd -b y picrust-1.1.1/scripts/convert_biom $path_input $path_output_biom $path_output_txt";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Finish convert_biom go to phylotype_picrust->";
            phylotype_picrust($user, $id, $project, $path);
            break;
        }
    }
}
// phylotype_picrust1
function phylotype_picrust($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "picrust" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run phylotype_picrust :";
    $path_input = "owncloud/data/$user/files/$project/output/final.tx.1.biom";
    $path_output_biom = "owncloud/data/$user/files/$project/output/final.biom";
    $jobname = $user . "_" . $id . "_phylotype_picrust";
    $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y picrust/scripts/qsubMoPhylo5andpicrust_norm $path_input $path_output_biom ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Finish phylotype_picrust go to phylotype_picrust2->";
            phylotype_picrust2($user, $id, $project, $path);
            break;
        }
    }
}
// phylotype_picrust2
function phylotype_picrust2($user, $id, $project, $path)
{
    echo "\n";
    echo "Run phylotype_picrust2 :";
    $path_input = "owncloud/data/$user/files/$project/output/normalized_otus.1.biom";
    $path_output_biom = "owncloud/data/$user/files/$project/output/metagenome_predictions.1.biom";
    $jobname = $user . "_" . $id . "_phylotype_picrust2";
    $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y picrust-1.1.1/scripts/qsubMoPhylo5andpicrust $path_input $path_output_biom ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Finish phylotype_picrust2 go to phylotype_picrust3->";
            phylotype_picrust3($user, $id, $project, $path);
            break;
        }
    }
}
// phylotype_picrust3
function phylotype_picrust3($user, $id, $project, $path)
{
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "picrust" . "\n", FILE_APPEND);
    echo "\n";
    echo "Run phylotype_picrust3 :";
    $path_input = "owncloud/data/$user/files/$project/output/metagenome_predictions.1.biom";
    $path_output_biom = "owncloud/data/$user/files/$project/output/predicted_metagenomes.1.L2.biom";
    $jobname = $user . "_" . $id . "_phylotype_picrust3";
    $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y picrust-1.1.1/scripts/qsubMoPhylo5andpicrust1 $path_input $path_output_biom ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Finish phylotype_picrust3 go to Biom to stamp->";
            biom_to_stamp($user, $id, $project, $path);
            break;
        }
    }
}
// make file biom_to_stamp
function biom_to_stamp($user, $id, $project, $path)
{
    echo "\n";
    echo "Run biom_to_stamp :";
    $path_input = "owncloud/data/$user/files/$project/output/predicted_metagenomes.1.L2.biom";
    $path_output_biom = "owncloud/data/$user/files/$project/output/pathways1L1.spf";
    $jobname = $user . "_" . $id . "_biom_to_stamp";
    $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y picrust-1.1.1/scripts/qsubBiomtoStamp $path_input $path_output_biom ";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Finish biom_to_stamp go to remove_float->";
            remove_float($user, $id, $project, $path);
            break;
        }
    }
}
// remove_float because not use float
function remove_float($user, $id, $project, $path)
{
    echo "\n";
    echo "Run remove_float :";
    $path_input = "owncloud/data/$user/files/$project/output/pathways1L1.spf";
    $jobname = $user . "_" . $id . "_biom_to_stamp";
    $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y /usr/bin/php -f R_Script/replace_string.php $path_input";
    exec($cmd);
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Finish remove float->";
            read_name_sample_to_plotgraph($user, $id, $project, $path);
            break;
        }
    }
}
// read name sample to plot graph
function read_name_sample_to_plotgraph($user, $id, $project, $path)
{
    echo "\n";
    echo "Run read_name_sample :";
    $group_sample = array();
    $name_sample = null;
    if ($file = fopen('../owncloud/data/' . $user . '/files/' . $project . '/input/stability.files', "r")) {
        $i = 0;
        while (!feof($file)) {
            $line = fgets($file);
            $parts = preg_split('/\s+/', $line);

            if ($parts[0] != null) {
                $group_sample[$i] = $parts[0];
                $i++;

            }
        }
        fclose($file);
        $k =1;
        foreach ($group_sample as $value) {
            $k++;
            $sample = str_replace("-", "_", $value);
            if ($name_sample == null) {
                $name_sample = $sample;
            } else {
                $name_sample = $name_sample . ":" . $sample;
            }
        }
        echo $name_sample;
        if ($k < 5 ){
            echo " less 4 sam Go to run stamp -> ";
            stamp($user, $id, $project, $path,$name_sample);
        }else{
            echo "more 4 sam Go ro change name";
            change_name($user, $id, $project, $path);
        }

    }
}
// run make file tsv use to plot graph stat
function stamp($user, $id, $project, $path,$name_sample)

{
    echo "\n";
    echo "Run stamp:";
    $split_name = preg_split('/:/',$name_sample);
    $name1 = $split_name[0];
    $name2 = $split_name[1];
    $name3 = $split_name[2];
    $name4 = $split_name[3];
    for ($i=0;$i<5;$i++){
        if($i == 0){
            $path_input = "../owncloud/data/$user/files/$project/output/pathways1L1.spf";
            $path_output_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $jobname = $user . "_" . $id . "_stamp";
            $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y stamp/qsubStamp $path_input $path_output_tsv $name1 $name2 ";
            exec($cmd);
        }
        if($i == 1){
            $path_input = "../owncloud/data/$user/files/$project/output/pathways1L1.spf";
            $path_output_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $jobname = $user . "_" . $id . "_stamp";
            $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y stamp/qsubStamp $path_input $path_output_tsv $name1 $name3 ";
            exec($cmd);
        }
        if($i == 2){
            $path_input = "../owncloud/data/$user/files/$project/output/pathways1L1.spf";
            $path_output_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $jobname = $user . "_" . $id . "_stamp";
            $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y stamp/qsubStamp $path_input $path_output_tsv $name1 $name4 ";
            exec($cmd);
        }
        if($i == 3){
            $path_input = "../owncloud/data/$user/files/$project/output/pathways1L1.spf";
            $path_output_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $jobname = $user . "_" . $id . "_stamp";
            $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y stamp/qsubStamp $path_input $path_output_tsv $name2 $name3 ";
            exec($cmd);
        }
        if($i == 4){
            $path_input = "../owncloud/data/$user/files/$project/output/pathways1L1.spf";
            $path_output_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $jobname = $user . "_" . $id . "_stamp";
            $cmd = "qsub -N '$jobname' -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y stamp/qsubStamp $path_input $path_output_tsv $name2 $name4 ";
            exec($cmd);
        }
        if($i == 5){
            $path_input = "../owncloud/data/$user/files/$project/output/pathways1L1.spf";
            $path_output_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $jobname = $user . "_" . $id . "_stamp";
            $cmd = "qsub -N '$jobname'.$i -o Logs_sge/phylotype/ -e Logs_sge/phylotype/ -cwd -b y stamp/qsubStamp $path_input $path_output_tsv $name3 $name4 ";
            exec($cmd);
        }
    }
    $check_qstat = "qstat  -j '$jobname'.'5' ";
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Finish run stamp go to Change name->";
            plot_graph_stat($user, $id, $project, $path);
            break;
        }
    }
}
// stamp plot graph stat
function plot_graph_stat($user, $id, $project, $path){
    echo "\n";
    echo "Run plot graph stat:";
    for($i=0;$i< 5;$i++){
        if($i == 0){
            $path_input_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $path_output_graph_stat = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.png";
            $jobname = $user . "_" . $id . "_plot_graph_r_STAT'$i'";
            $cmd = "qsub -N $jobname -o Logs_sge/phylotype/ -e Logs_sge/phylotype/  -cwd -b y /usr/bin/Rscript  R_Script/barplotwitherrorstampModi.R $path_input_tsv $path_output_graph_stat";
            exec($cmd);
        }
        if($i == 0){
            $path_input_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $path_output_graph_stat = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.png";
            $jobname = $user . "_" . $id . "_plot_graph_r_STAT'$i'";
            $cmd = "qsub -N $jobname -o Logs_sge/phylotype/ -e Logs_sge/phylotype/  -cwd -b y /usr/bin/Rscript  R_Script/barplotwitherrorstampModi.R $path_input_tsv $path_output_graph_stat";
            exec($cmd);
        }
        if($i == 1){
            $path_input_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $path_output_graph_stat = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.png";
            $jobname = $user . "_" . $id . "_plot_graph_r_STAT'$i'";
            $cmd = "qsub -N $jobname -o Logs_sge/phylotype/ -e Logs_sge/phylotype/  -cwd -b y /usr/bin/Rscript  R_Script/barplotwitherrorstampModi.R $path_input_tsv $path_output_graph_stat";
            exec($cmd);
        }
        if($i == 2){
            $path_input_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $path_output_graph_stat = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.png";
            $jobname = $user . "_" . $id . "_plot_graph_r_STAT'$i'";
            $cmd = "qsub -N $jobname -o Logs_sge/phylotype/ -e Logs_sge/phylotype/  -cwd -b y /usr/bin/Rscript  R_Script/barplotwitherrorstampModi.R $path_input_tsv $path_output_graph_stat";
            exec($cmd);
        }
        if($i == 3){
            $path_input_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $path_output_graph_stat = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.png";
            $jobname = $user . "_" . $id . "_plot_graph_r_STAT'$i'";
            $cmd = "qsub -N $jobname -o Logs_sge/phylotype/ -e Logs_sge/phylotype/  -cwd -b y /usr/bin/Rscript  R_Script/barplotwitherrorstampModi.R $path_input_tsv $path_output_graph_stat";
            exec($cmd);
        }
        if($i == 4){
            $path_input_tsv = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.tsv";
            $path_output_graph_stat = "../owncloud/data/$user/files/$project/output/Resultpathways'$i'.png";
            $jobname = $user . "_" . $id . "_plot_graph_r_STAT'$i'";
            $cmd = "qsub -N $jobname -o Logs_sge/phylotype/ -e Logs_sge/phylotype/  -cwd -b y /usr/bin/Rscript  R_Script/barplotwitherrorstampModi.R $path_input_tsv $path_output_graph_stat";
            exec($cmd);
        }
    }
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
        $check_run = exec("qstat -j $id_job");
        if ($check_run == false) {
            echo "Go to plot_graph_r_Rare->";
            change_name($user, $id, $project, $path);
            break;
        }
    }
}
// change_name file graph use show in report
function change_name($user, $id, $project, $path)
{
    echo "\n";
    echo "Run changname :";
    $dir = $path . "/output";
    $file_read = array('svg','sharedotus');
    $dir_ignore = array();
    $scan_result = scandir($dir);
    foreach ($scan_result as $key => $value) {
        if (!in_array($value, array('.', '..'))) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                if (in_array($value, $dir_ignore)) {
                    continue;
                }
            } else {
                $type = explode('.', $value);
                $type = array_reverse($type);
                if (in_array($type[0], $file_read)) {
                    $file_name = preg_split("/[.]/", $value);
                    if (in_array("bin", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "bin.svg");
                        echo $value . " change to  bin.svg";
                    }
                    if (in_array("sharedsobs", $file_name) and in_array("svg", $file_name) ) {
                        rename($dir . "/" . $value, $dir . "/" . "sharedsobs.svg");
                        echo $value . " change to sharedsobs.svg";
                    }
                    if (in_array("sharedotus", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "sharedsobs.sharedotus");
                        echo $value . " change to sharedsobs.sharedotus";
                    }

                    if (in_array("jclass", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "jclass.svg");
                        echo $value . " change to jclass.svg";
                    }
                    if (in_array("thetayc", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "thetayc.svg");
                        echo $value . " change to thetayc.svg";
                    }
                }
            }
        }
    }
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "picrust-finish" . "\n", FILE_APPEND);
    //on_check_remove_database($user, $id, $project, $path);
}
// on_check remove database
function on_check_remove_database($user, $id, $project, $path)
{

    echo "on_check_remove" . "\n";
    $path_dir = $path . "/input/";
    if (is_dir($path_dir)) {
        if ($read = opendir($path_dir)) {
            while (($file = readdir($read)) !== false) {

                $allowed = array('8mer', 'sum', 'train', 'numNonZero', 'prob', 'files');
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if (in_array($ext, $allowed)) {

                    unlink($path_dir . $file);
                }
            }

            closedir($read);
        }
    }

    // on_check_remove_progress($user, $id, $project, $path);

}
// check remove progress
function on_check_remove_progress($user, $id, $project, $path)
{

    echo "on_check_remove" . "\n";
    $path_dir = $path . "/output/";
    if (is_dir($path_dir)) {
        if ($read = opendir($path_dir)) {
            while (($file = readdir($read)) !== false) {

                $allowed = array('txt');
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if (in_array($ext, $allowed)) {

                    unlink($path_dir . $file);
                }
            }

            closedir($read);
        }
    }

    change_name($user, $id, $project, $path);

}
?>


