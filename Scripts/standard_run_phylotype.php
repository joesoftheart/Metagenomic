<?php
// *** argument input
$user = $argv[1];
$id = $argv[2];
$project = $argv[3];
$path = $argv[4];

//setting cmd sungrid engine
include('setting_sge.php');
putenv("SGE_ROOT=$SGE_ROOT");
putenv("PATH=$PATH");


// check value params
if ($user != null && $project != null  && $path != null && $id != null){
    plot_graph_r_heatmap($user,$id,$project,$path);
    }


// Run Program
function run($user,$id,$project,$path){
    check_file($user,$id,$project,$path);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "quality"."\n", FILE_APPEND);
}


// Check file 
 function check_file($user,$id, $project,$path){
     echo "\n";
     echo "Run check_file :";
    $path_stability = "../owncloud/data/$user/files/$project/output/stability.files";
    $path_file = $path_stability;
    if(file_exists($path_file)) {
        echo "go to check file oligo ->";
        check_oligos($user,$id, $project,$path);
    }
    else {
        echo "go to run make file ->";
        run_makefile($user,$id, $project,$path);
    }
 }


// check file oligos
 function check_oligos($user,$id, $project,$path){
     echo "\n";
     echo "Run check_oligos :";
    $total_oligo = 0;
    $path_dir = $path;
    if (is_dir($path_dir)) {
        if ($read = opendir($path_dir)){
            while (($file_oligo = readdir($read)) !== false) {
                $allowed =  array('oligo');
                $ext = pathinfo($file_oligo, PATHINFO_EXTENSION);
                if(in_array($ext,$allowed)) {
                    $total_oligo +=1;
                    echo "go to make_contigs_olios ->";
                    make_contigs_oligos($file_oligo,$user,$id,$project,$path);
                }
            }
            closedir($read);
        }
    }
    if($total_oligo == 0){
        echo "go to makecontig_summary -> ";
        make_contigs_summary($user,$id, $project,$path);
    }
}


// Make file
 function run_makefile($user,$id, $project,$path){
     echo "\n";
     echo "Run run_makefile :";
    $jobname = $user."_".$id."_run_makefile";
    $make = "make.file(inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $make);
    $cmd = "qsub  -N   '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch";
    exec($cmd);
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
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if($check_run == false){
             echo "check file again ->";
             check_file($user,$id, $project,$path);
             break;
         }
     }
}


// Make contig oligos
 function make_contigs_oligos($file_oligo,$user,$id,$project,$path){

     echo "\n";
     echo "Run make_contigs_oligos :";
    $jobname = $user."_".$id."_oligo";
    $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=8 ,inputdir=$path/input/,outputdir=$path/output/)
            summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/input/run.batch ";
     exec($cmd);
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
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if($check_run == false){
             echo "go to align_seqs ->";
             replace_group($user, $id, $project, $path);
             break;
         }
     }
}


// make.contigs && summary.seqs
 function make_contigs_summary($user,$id,$project,$path){
     echo "\n";
     echo "Run make_contigs_summary";
    $jobname = $user."_".$id."_make_contigs_summary";
    $cmd ="make.contigs(file=stability.files,processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if($check_run == false){
             echo "go to align_seqs ->";
             replace_group($user, $id, $project, $path);
             break;
         }
     }
}


function replace_group($user,$id, $project,$path){
    echo "\n";
    echo "Run Replace_group :";
    $file_path_test = $path."/output/stability.contigs.groups.txt";
    $file_path = $path."/output/stability.contigs.groups";
    $data_w = array();
if ($file = fopen($file_path, "r")) {
    $i = 0;
    while(!feof($file)) {
        $line = fgets($file);
        $out = explode("\t", $line);

        if ($out[0] == "" or $out[1] == ""){
            echo "out[0] =====" . "null";
            echo "\n";
            echo "out[1] =====" . "null";
        }else {
            $out[1] =  str_replace("-", "_", $out[1]);
            $data = $out[0] . "\t" . $out[1];
            array_push($data_w, $data);
        }
    }
    fclose($file);
        if (file_exists($file_path)){
            file_put_contents($file_path, "");
            foreach ($data_w as $value){
                file_put_contents($file_path, $value ,FILE_APPEND);
            }

        }else{
            foreach ($data_w as $value){
                file_put_contents($file_path, $value ,FILE_APPEND);
            }
        }

    }

    echo "go to screen_seqs ->";
    screen_seqs($user,$id, $project,$path);
}


// make.contigs && summary.seqs
function screen_seqs($user,$id,$project,$path){
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "quality-finish"."\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "align-sequence"."\n", FILE_APPEND);
    echo "\n";
    echo "Run screen_seqs";
    $jobname = $user."_".$id."_screen_seqs";
    $cmd ="screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=$path/input/,outputdir=$path/output/)
unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=$path/input/,outputdir=$path/output/)
count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(count=stability.trim.contigs.good.count_table,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
    while ($loop) {
        $check_run = exec("qstat -j $id_job");
        if($check_run == false){
            echo "go to align_seqs ->";
            align_seqs($user, $id, $project, $path);
            break;
        }
    }
}


// Summary-seqs
 function align_seqs($user,$id,$project,$path){
     echo "\n";
     echo "Run align_seqs";
    $jobname = $user."_".$id."_align_seqs";
    $cmd ="align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=silva.v4.fasta, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
     exec($cmd);
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
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         sleep(120);
         if($check_run == false){
             echo "go to read_log_sungrid->";
             read_log_sungrid($user,$id,$project,$path,$id_job);
             break;
         }
     }
}


function read_log_sungrid($user,$id,$project,$path,$id_job){
    echo "\n";
    echo "Run read_log_sungrid :";
    $name = $user."_".$id."_align_seqs.o".$id_job;
    $file_name = str_replace(' ', '', $name) ;
    $file = file_get_contents("Logs_sge/".$file_name);
    $start_array = array();
    $end_array   = array();

    $start = 0;
    $end =0;
    $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum).*\$/m";
    if(preg_match_all($pattern, $file, $matches)){
        $val = implode("\n", $matches[0]);
        $sum = explode("\n", $val);
        foreach ($sum as $key => $value) {
            //echo  $value ."<br/>";
            if($key >= "1"){
                $va_ex = explode(":", $value);
                $va_ex2 = explode("\t", trim($va_ex[1]));
                array_push($start_array,$va_ex2[0]);
                array_push($end_array,$va_ex2[1]);
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
    if(($start_min == $start_max) || ($end_min == $end_max)){

        foreach ($sum as $key => $value) {
            echo  $value ."<br/>";
        }
    }elseif (($start_min != $start_max) && ($end_min != $end_max) ) {
        #start
        foreach ($count_start as $key_start => $value_start) {
            if($start_max == $value_start){
                $start = $key_start;
            }
        }
        #end
        foreach ($count_end as $key_end => $value_end) {
            if($end_max == $value_end){
                $end = $key_end;
            }
        }
        echo "\n";
        echo "Start : ".$start;
        echo " End : ".$end;
        echo "go to screen_remove->";
        if ($start != null && $end != null){
            screen_remove($user,$id, $project,$path,$start,$end);
        }else{
            echo "\n";
            echo "Start and End null value";
        }

    }

}


// Screen remove
 function screen_remove($user,$id, $project,$path,$start,$end){
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "align-sequence-finish"."\n", FILE_APPEND);
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "pre-cluster-chimera"."\n", FILE_APPEND);
     echo "\n";
     echo "Run screen_remove :";
    $jobname = $user."_".$id."_screen_remove";
    $cmd ="screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=$start, end=$end, maxambig=8, maxhomop=8, maxlength=260, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=current, count=current,inputdir=$path/input/,outputdir=$path/output/)
filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8,inputdir=$path/input/,outputdir=$path/output/)
unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table,inputdir=$path/input/,outputdir=$path/output/)
pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=2,inputdir=$path/input/,outputdir=$path/output/)
chimera.vsearch(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8,inputdir=$path/input/,outputdir=$path/output/)
remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.accnos,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=current, count=current,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
     exec($cmd);
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
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if($check_run == false){
             echo "go to classify_system->";

             classify_system($user,$id, $project,$path);
             break;
         }
     }
}


// Classify_system
 function classify_system($user,$id, $project,$path){
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "pre-cluster-chimera-finish"."\n", FILE_APPEND);
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "classify-sequence-remove"."\n", FILE_APPEND);
     echo "\n";
     echo "Run classify_system :";
    $jobname = $user."_".$id."_classify_system";
    $cmd ="classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=80, processors=8,inputdir=$path/input/,outputdir=$path/output/)
remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=current, count=current,inputdir=$path/input/,outputdir=$path/output/)
summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=$path/input/,outputdir=$path/output/)
system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta owncloud/data/$user/files/$project/output/final.fasta)
system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table owncloud/data/$user/files/$project/output/final.count_table)
system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy owncloud/data/$user/files/$project/output/final.taxonomy)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
     exec($cmd);
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
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if($check_run == false){
             echo "go to phylotype_count->";
             phylotype_count($user,$id, $project,$path);
             break;
         }
     }
}


// Phylotype count
 function phylotype_count($user,$id, $project,$path){
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "classify-sequence-remove-finish"."\n", FILE_APPEND);
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "classify-otu"."\n", FILE_APPEND);
     echo "\n";
     echo "Run phylotype_count :";
    $jobname = $user."_".$id."_phylotype_count";
    $cmd ="phylotype(taxonomy=final.taxonomy,inputdir=$path/input/,outputdir=$path/output/)
make.shared(list=final.tx.list, count=final.count_table, label=1-2-3-4-5-6,inputdir=$path/input/,outputdir=$path/output/)
classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, label=1-2-3-4-5-6,inputdir=$path/input/,outputdir=$path/output/)
classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, basis=sequence, output=simple, label=1,inputdir=$path/output/,outputdir=$path/output_plot/)
count.groups(shared=final.tx.shared,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
    exec($cmd);
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
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if($check_run == false){
             echo "go to read_log_sungrid_phylotype_count->";
             read_log_sungrid_phylotype_count($user, $id, $project, $path, $id_job);
             break;
         }
     }
}


function read_log_sungrid_phylotype_count($user,$id,$project,$path,$id_job){
    echo "\n";
    echo "Run read_log_sungrid_phylotype_count :";
    $name = $user."_".$id."_phylotype_count.o".$id_job;
    $file_name = str_replace(' ', '', $name) ;
    $searchfor = 'contains';
    $file = file_get_contents("Logs_sge/".$file_name);
    $pattern = preg_quote($searchfor, '/');
// finalise the regular expression, matching the whole line
    $pattern = "/^.*$pattern.*\$/m";
// search, and store all matching occurences in $matches
    if(preg_match_all($pattern, $file, $matches)){

        $i = 0;
        $t = array();
        foreach ($matches[0] as $ma ){
            if ($ma != null){
                $tota =  explode(" ",$ma);
                $to = explode(".",$tota[2]);
                $t[$i] = $to[0];
                $i++;
            }
        }
        $total =  min($t);
        echo $total;
        echo "<br>";
        echo "Go to sub_sample_sammary->";
        sub_sample_summary($user,$id, $project,$path,$total);
    }
    else{
        echo "No matches found";
    }
}


// Sub samplr sammary
 function sub_sample_summary($user,$id, $project,$path,$total){
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "classify-otu-finish"."\n", FILE_APPEND);
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "alpha-beta-diversity"."\n", FILE_APPEND);
     echo "\n";
     echo "Run sub_sample_summary :";
    $jobname = $user."_".$id."_sub_sample_summary";
    $cmd ="sub.sample(shared=final.tx.shared, size=$total,inputdir=$path/input/,outputdir=$path/output/)
collect.single(shared=final.tx.shared, calc=chao, freq=100,inputdir=$path/input/,outputdir=$path/output/)
rarefaction.single(shared=final.tx.shared, calc=sobs, freq=100, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.single(shared=final.tx.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon, subsample=$total,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
     exec($cmd);
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
     while ($loop) {
         $check_run = exec("qstat -j $id_job");
         if($check_run == false){
             echo "go to plot_graph->";
             read_name_sample($user,$id, $project,$path,$total);
             break;
         }
     }
}

function read_name_sample($user,$id, $project,$path,$total){
    echo "\n";
    echo "Run read_name_sample :";
    $group_sample = array();
    $name_sample = null;
if ($file = fopen('../owncloud/data/'.$user.'/files/'.$project.'/output/stability.files', "r")) {
    $i = 0;
    while(!feof($file)) {
        $line = fgets($file);
        $parts = preg_split('/\s+/', $line);
        echo $parts[0];
        echo "\n";
        if ($parts[0] != null){
            $group_sample[$i] = $parts[0];
            $i++;

        }
    }
    fclose($file);

    foreach ($group_sample as $value){
        $sample =  str_replace("-", "_", $value);
        if ($name_sample == null){
            $name_sample = $sample;
        }else {
            $name_sample = $name_sample . "-" . $sample;
        }
    }
    echo "go to plot_graph->";
    echo "Name sample : ".$name_sample;
    plot_graph($user, $id, $project, $path, $total, $name_sample);
    }
}


// Last funtion plot graph
 function plot_graph($user,$id, $project,$path,$total,$name_sample)
 {
     echo "\n";
     echo "Run plot_graph :";
     $jobname = $user . "_" . $id . "_plot_graph";
     $cmd = "heatmap.bin(shared=final.tx.2.subsample.shared, scale=log2, numotu=10,inputdir=$path/input/,outputdir=$path/output/)
dist.shared(shared=final.tx.shared, calc=thetayc-jclass-lennon-morisitahorn-braycurtis, subsample=$total,inputdir=$path/input/,outputdir=$path/output/)
heatmap.sim(phylip=final.tx.thetayc.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/) #No need
heatmap.sim(phylip=final.tx.jclass.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/) #No need
summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=$name_sample, all=T,inputdir=$path/input/,outputdir=$path/output/)
venn(shared=final.tx.2.subsample.shared, groups=$name_sample,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.tx.thetayc.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.tx.morisitahorn.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.tx.jclass.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.tx.braycurtis.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
tree.shared(phylip=final.tx.lennon.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
parsimony(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design,  groups=all,inputdir=$path/input/,outputdir=$path/output/) #No need
unifrac.weighted(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design, random=T,inputdir=$path/input/,outputdir=$path/output/) #No need
unifrac.unweighted(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design, random=T, groups=all,inputdir=$path/input/,outputdir=$path/output/) #No need
pcoa(phylip=final.tx.morisitahorn.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
pcoa(phylip=final.tx.thetayc.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
pcoa(phylip=final.tx.jclass.2.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
nmds(phylip=final.tx.morisitahorn.2.lt.ave.dist, mindim=3, maxdim=3,inputdir=$path/input/,outputdir=$path/output/)
nmds(phylip=final.tx.thetayc.2.lt.ave.dist, mindim=2, maxdim=2,inputdir=$path/input/,outputdir=$path/output/)
nmds(phylip=final.tx.jclass.2.lt.ave.dist, mindim=3, maxdim=3,inputdir=$path/input/,outputdir=$path/output/)
#amova(phylip=final.tx.thetayc.2.lt.ave.dist, design=soil.design,inputdir=$path/input/,outputdir=$path/output/) #No need
#homova(phylip=final.tx.thetayc.2.lt.ave.dist, design=soil.design,inputdir=$path/input/,outputdir=$path/output/)
corr.axes(axes=final.tx.thetayc.2.lt.ave.nmds.axes, shared=final.tx.2.subsample.shared, method=spearman, numaxes=2, label=2,inputdir=$path/input/,outputdir=$path/output/)
corr.axes(axes=final.tx.thetayc.2.lt.ave.nmds.axes, metadata=soilpro.metadata, method=pearson, numaxes=2, label=2,inputdir=$path/input/,outputdir=$path/output/)";
     file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
     $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
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



 function create_file_input_heatmap($user, $id, $project, $path){
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "alpha-beta-diversity-finish"."\n", FILE_APPEND);
     file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot-graph"."\n", FILE_APPEND);
     echo "\n";
     echo "Run create_file_input_heatmap :";
     $jobname = $user . "_" . $id . "_create_file_input_heatmap";
     $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f R_Script/create_input_heatmap_phylotype.php $user $project";
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


function create_file_input_abun($user, $id, $project, $path){

    echo "\n";
    echo "Run create_file_input_abun :";
    $jobname = $user . "_" . $id . "_create_file_input_abun";
    $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f R_Script/create_input_abundance_phylotype.php $user $project";
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

function create_input_alphash($user, $id, $project, $path){
    echo "\n";
    echo "Run create_input_alphash :";
    $jobname = $user . "_" . $id . "_create_input_alphash";
    $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f R_Script/create_input_alphash_phylotype.php $user $project";
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
            echo "Go to create_input_biplot ->";
            create_input_biplot($user, $id, $project, $path);
            break;
        }
    }

}


function create_input_biplot($user, $id, $project, $path){
    echo "\n";
    echo "Run create_input_biplot :";
    $jobname = $user . "_" . $id . "_create_input_biplot";
    $cmd = "qsub -N $jobname -o Logs_sge -e Logs_sge  -cwd -b y /usr/bin/php -f R_Script/create_input_biplot_phylotype.php $user $project";
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


 function plot_graph_r_heatmap($user, $id, $project, $path){

     echo "\n";
     echo "Run plot_graph_r_heatmap :";
     $path_input_csv = "owncloud/data/$user/files/$project/output/file_after_reverse.csv";
     $path_to_save = "owncloud/data/$user/files/$project/output/heatmap.png";
     $jobname = $user . "_" . $id . "_plot_graph_r_heartmap";
     $cmd = "qsub -N $jobname -o Logs_sge/ -e Logs_sge/  -cwd -b y /usr/bin/Rscript R_Script/heatmapPlottest.R $path_input_csv $path_to_save";
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
            // plot_graph_r_NMD($user, $id, $project, $path);
             break;
         }
     }

 }

function plot_graph_r_NMD($user, $id, $project, $path){

    echo "\n";
    echo "Run plot_graph_r_NMD :";
    $path_input_axes = "owncloud/data/$user/files/$project/output/final.tx.thetayc.2.lt.ave.nmds.axes";
    $path_to_save = "owncloud/data/$user/files/$project/output/NMD.png";
    $jobname = $user . "_" . $id . "plot_graph_r_NMD";
    $cmd = "qsub -N $jobname -o Logs_sge/ -e Logs_sge/  -cwd -b y /usr/bin/Rscript  R_Script/NMDSpcoaplottest.R $path_input_axes $path_to_save";
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

function plot_graph_r_Rare($user, $id, $project, $path){

    echo "\n";
    echo "Run plot_graph_r_Rare :";
    $path_input_rarefaction = "owncloud/data/$user/files/$project/output/final.tx.groups.rarefaction";
    $path_to_save = "owncloud/data/$user/files/$project/output/Rare.png";
    $jobname = $user . "_" . $id . "plot_graph_r_Rare";
    $cmd = "qsub -N $jobname -o Logs_sge/ -e Logs_sge/  -cwd -b y /usr/bin/Rscript  R_Script/RarefactionSoiltest_phtlotype.R $path_input_rarefaction $path_to_save";
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


function plot_graph_r_Abun($user, $id, $project, $path){

    echo "\n";
    echo "Run plot_graph_r_Abun :";
    $path_input_phylumex = "owncloud/data/$user/files/$project/output/file_phylum_count.txt";
    $path_to_save = "owncloud/data/$user/files/$project/output/Abun.png";
    $jobname = $user . "_" . $id . "plot_graph_r_Abun";
    $cmd = "qsub -N $jobname -o Logs_sge/ -e Logs_sge/  -cwd -b y /usr/bin/Rscript  R_Script/Abundancebarplottest.R $path_input_phylumex $path_to_save";
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


function plot_graph_r_Alphash($user, $id, $project, $path){

    echo "\n";
    echo "Run plot_graph_r_Alphash :";
    $path_input_chao_shannon = "owncloud/data/$user/files/$project/output/file_after_chao.txt";
    $path_to_save = "owncloud/data/$user/files/$project/output/Alpha.png";
    $jobname = $user . "_" . $id . "plot_graph_r_Alphash";
    $cmd = "qsub -N $jobname -o Logs_sge/ -e Logs_sge/  -cwd -b y /usr/bin/Rscript  R_Script/AlphachaoshannonSoil.R $path_input_chao_shannon $path_to_save";
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
            echo "Go to plot_graph_r_Biplot ->";
            plot_graph_r_Biplot($user, $id, $project, $path);
            break;
        }
    }

}


function plot_graph_r_Biplot($user, $id, $project, $path){

    echo "\n";
    echo "Run plot_graph_r_Biplot :";
    $path_input_biplot_nmds = "owncloud/data/$user/files/$project/output/final.tx.thetayc.2.lt.ave.nmds.axes";
    $path_output_biplot_withBiplotwithOTU = "owncloud/data/$user/files/$project/output/NewNMDS_withBiplotwithOTU.png";
    $path_input_biplot = "owncloud/data/$user/files/$project/output/output_bioplot.txt";
    $path_output_biplot_withBiplotwithMetadata = "owncloud/data/$user/files/$project/output/NewNMDS_withBiplotwithMetadata.png";
    $path_input_soilpro = "owncloud/data/$user/files/$project/output/soilpro.pearson.corr.axes";
    $jobname = $user . "_" . $id . "_plot_graph_r_Biplot";
    $cmd = "qsub -N $jobname -o Logs_sge/ -e Logs_sge/  -cwd -b y /usr/bin/Rscript  R_Script/ScatterPlotwithbiplot_phylotype.R $path_input_biplot_nmds $path_output_biplot_withBiplotwithOTU $path_input_biplot $path_output_biplot_withBiplotwithMetadata $path_input_soilpro";
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
            echo "Go to plot_graph_r_Tree ->";
            plot_graph_r_Tree($user, $id, $project, $path);
            break;
        }
    }

}

function plot_graph_r_Tree($user, $id, $project, $path){


    echo "\n";
    echo "Run plot_graph_r_Tree :";
    $path_input_tree = "owncloud/data/$user/files/$project/output/final.tx.morisitahorn.2.lt.ave.tre";
    $path_output_tree = "owncloud/data/$user/files/$project/output/Tree.png";

    $jobname = $user . "_" . $id . "_plot_graph_r_Tree";
    $cmd = "qsub -N $jobname -o Logs_sge/ -e Logs_sge/  -cwd -b y /usr/bin/Rscript  R_Script/PlotTreeGraph.R $path_input_tree $path_output_tree";
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


function make_biom($user, $id, $project, $path){
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "plot-graph-finish"."\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "make-biom"."\n", FILE_APPEND);
    echo "\n";
    echo "Run make_biom :";
    $jobname = $user . "_" . $id . "_make_biom";
    $cmd = "make.biom(shared=final.tx.shared, label=1,constaxonomy=final.tx.1.cons.taxonomy, reftaxonomy=gg_13_8_99.gg.tax, picrust=99_otu_map.txt,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/input/run.batch ";
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
function convert_biom($user, $id, $project, $path){
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "make-biom-finish"."\n", FILE_APPEND);
    echo "\n";
    echo "Run convert_biom :";

    $jobname = $user . "_" . $id . "_convert_biom";
    $path_input = "owncloud/data/$user/files/$project/output/final.tx.1.biom";
    $path_output_biom = "owncloud/data/$user/files/$project/output/normalized_otus.1.biom";
    $path_output_txt = "owncloud/data/$user/files/$project/output/final.tx.1.txt";
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y picrust-1.1.1/scripts/convert_biom $path_input $path_output_biom $path_output_txt";
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
            echo "Finish convert_biom ->";
            phylotype_picrust($user, $id, $project, $path);
            break;
        }
    }

}

function phylotype_picrust($user, $id, $project, $path){
//    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "make-biom-finish"."\n", FILE_APPEND);
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "picrust"."\n", FILE_APPEND);
    echo "\n";
    echo "Run phylotype_picrust :";

    $path_input = "owncloud/data/$user/files/$project/output/final.tx.1.biom";
    $path_output_biom = "owncloud/data/$user/files/$project/output/final.biom";
    $jobname = $user . "_" . $id . "_phylotype_picrust";
    $cmd = "qsub -N '$jobname' -o Logs_sge/ -e Logs_sge/ -cwd -b y picrust-1.1.1/scripts/qsubMoPhylo5andpicrust_norm $path_input $path_output_biom ";
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
            echo "Finish phylotype_picrust ->";
            change_name($user, $id, $project, $path);
            break;
        }
    }
}


function change_name($user, $id, $project, $path){

    $dir = $path."/output";
    $file_read = array( 'svg');
    $dir_ignore = array();
    $scan_result = scandir( $dir );

    foreach ( $scan_result as $key => $value ) {

        if (!in_array($value, array('.', '..'))) {

            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {

                if (in_array($value, $dir_ignore)) {
                    continue;
                }


            } else {

                $type = explode('.', $value);
                $type = array_reverse($type);
                if (in_array($type[0], $file_read)) {
                    echo $value;
                    $file_name = preg_split("/[.]/",$value );
                    if (in_array("bin", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "bin.svg");
                        echo "change bin";
                    }
                    if (in_array("sharedsobs", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "sharedsobs.svg");
                        echo "change sharedsobs";
                    }
                    if (in_array("jclass", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "jclass.svg");
                        echo "change jclass";
                    }
                    if (in_array("thetayc", $file_name)) {
                        rename($dir . "/" . $value, $dir . "/" . "thetayc.svg");
                        echo "change thetayc";
                    }
                }
            }
        }
    }
    file_put_contents("owncloud/data/$user/files/$project/output/progress.txt", "picrust-finish"."\n", FILE_APPEND);
}
?>





