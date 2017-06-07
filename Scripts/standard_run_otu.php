<?php
$user = $argv[1];
$id = $argv[2];
$project = $argv[3];
$path = $argv[4];
include('setting_sge.php');
putenv("SGE_ROOT=$SGE_ROOT");
putenv("PATH=$PATH");


if ($user != null && $project != null  && $path != null && $id != null){
    phylotype_count($user,$id,$project,$path);


}


// Run Program
function run($user,$id,$project,$path){
    check_file($user,$id,$project,$path);
}




// Check file
function check_file($user,$id, $project,$path){
    echo "\n";
    echo "Run check_file :";
    $path_stability = "../owncloud/data/$user/files/$project/data/output/stability.files";
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
        makecontig_summary($user,$id, $project,$path);
    }

}


// Make file
function run_makefile($user,$id, $project,$path){
    echo "\n";
    echo "Run run_makefile :";
    $jobname = $user."_".$id."_run_makefile";
    $make = "make.file(inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $make);
    $cmd = "qsub  -N   '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch";
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
    $path = $path;
    $jobname = $user."_".$id."_oligo";
    $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=8 ,inputdir=$path/input/,outputdir=$path/output/)
screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=$path/input/,outputdir=$path/output/)
unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=$path/input/,outputdir=$path/output/)
count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(count=stability.trim.contigs.good.count_table,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";
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
            align_seqs($user,$id, $project,$path);
            break;
        }
    }
}


// make.contigs && summary.seqs
function makecontig_summary($user,$id,$project,$path){
    echo "\n";
    echo "Run makecontig_summary";
    $jobname = $user."_".$id."_makecontig_summary";
    $cmd ="make.contigs(file=stability.files,processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path/input/,outputdir=$path/output/)
screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=$path/input/,outputdir=$path/output/)
unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=$path/input/,outputdir=$path/output/)
count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(count=stability.trim.contigs.good.count_table,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
            align_seqs($user,$id, $project,$path);
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
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
            echo "go to read_log_sungrid->";
            read_log_sungrid($user,$id,$project,$path,$id_job);
            break;
        }
    }

}



function read_log_sungrid($user,$id,$project,$path,$id_job){
    $name = $user."_".$id."_align_seqs.o".$id_job;
    $file_name = str_replace(' ', '', $name) ;

    echo $file_name;

    $file = file_get_contents('Logs_sge/'.$file_name);
    //echo var_dump($file);
    $search_for = 'Start';
    $pattern = preg_quote($search_for,'/');

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

        echo   "Start : ".$start ."<br/>". " End : ".$end;
        screen_remove($user,$id, $project,$path,$start,$end);
    }

}

// Screen remove
function screen_remove($user,$id, $project,$path,$start,$end){
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
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
    echo "\n";
    echo "Run classify_system";
    $jobname = $user."_".$id."_classify_system";
    $cmd ="classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=80, processors=8,inputdir=$path/input/,outputdir=$path/output/)
remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified,inputdir=$path/input/,outputdir=$path/output/)
summary.seqs(fasta=current, count=current,inputdir=$path/input/,outputdir=$path/output/)
summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=$path/input/,outputdir=$path/output/)
system(cp owncloud/data/$user/files/$project/data/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta owncloud/data/joesoftheart/files/SAMPLE-WES1053/data/input/final.fasta)
system(cp owncloud/data/$user/files/$project/data/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table owncloud/data/joesoftheart/files/SAMPLE-WES1053/data/input/final.count_table)
system(cp owncloud/data/$user/files/$project/data/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy owncloud/data/joesoftheart/files/SAMPLE-WES1053/data/input/final.taxonomy)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
    echo "\n";
    echo "Run phylotype_count :";
    $path_cus = "owncloud/data/$user/files/$project/data/output/";
    $jobname = $user."_".$id."_phylotype_count";
    $cmd ="dist.seqs(fasta=final.fasta, cutoff=0.21, processors=8,inputdir=$path/input/,outputdir=$path/output/)
cluster(column=final.dist, count=final.count_table, method=opti, cutoff=0.03,inputdir=$path/input/,outputdir=$path/output/)
dist.seqs(fasta=final.fasta, cutoff=0.21, processors=8,inputdir=$path/input/,outputdir=$path/output/)
cluster(column=final.dist, count=final.count_table, method=opti, cutoff=0.03,inputdir=$path/input/,outputdir=$path/output/)
make.shared(list=final.opti_mcc.list, count=final.count_table, label=0.03,inputdir=$path/input/,outputdir=$path/output/)
classify.otu(list=final.opti_mcc.list, count=final.count_table, taxonomy=final.taxonomy, label=0.03,inputdir=$path/input/,outputdir=$path/output/)
count.groups(shared=final.opti_mcc.shared,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
            echo "go to sub_sample_sammary->";
            //
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
    $file = file_get_contents('Logs_sge/'.$file_name);


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
        sub_sample_sammary($user,$id, $project,$path,$total);
    }
    else{
        echo "No matches found";
    }


}





// Sub samplr sammary
function sub_sample_sammary($user,$id, $project,$path,$total){
    echo "\n";
    echo "Run sub_sample_summary :";
    $jobname = $user."_".$id."_sub_sample_sammary";
    $cmd ="sub.sample(shared=final.opti_mcc.shared, size=$total,inputdir=$path/input/,outputdir=$path/output/)
collect.single(shared=final.opti_mcc.shared, calc=chao, freq=100, label=0.03,inputdir=$path/input/,outputdir=$path/output/)
rarefaction.single(shared=final.opti_mcc.shared, calc=sobs, freq=100, label=0.03, processors=8,inputdir=$path/input/,outputdir=$path/output/)
summary.single(shared=final.opti_mcc.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon-simpson, subsample=$total, label=0.03,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
    $group_sample = array();
    $name_sample = null;
    if ($file = fopen('../owncloud/data/'.$user.'/files/'.$project.'/data/output/stability.files', "r")) {
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
            if ($name_sample == null){
                $name_sample = $value;
            }else {
                $name_sample = $name_sample . "-" . $value;
            }
        }
        plot_graph($user, $id, $project, $path, $total, $name_sample);
    }

}

// Last funtion plot graph
function plot_graph($user,$id, $project,$path,$total,$name_sample)
{
    echo "\n";
    echo "Run plot_graph :";
    $jobname = $user . "_" . $id . "_plot_graph";
    $cmd = "heatmap.bin(shared=final.opti_mcc.0.03.subsample.shared, scale=log2, numotu=50,inputdir=$path/input/,outputdir=$path/output/)
dist.shared(shared=final.opti_mcc.shared, calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, subsample=$total, processors=8,inputdir=$path/input/,outputdir=$path/output/)
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
parsimony(tree=final.opti_mcc.thetayc.0.03.lt.ave.tre, group=soil.design, groups=all,inputdir=$path/input/,outputdir=$path/output/)
unifrac.weighted(tree=final.opti_mcc.thetayc.0.03.lt.ave.tre, group=soil.design, random=T,inputdir=$path/input/,outputdir=$path/output/)
unifrac.unweighted(tree=final.opti_mcc.thetayc.0.03.lt.ave.tre, group=soil.design, random=T, groups=all,inputdir=$path/input/,outputdir=$path/output/)
pcoa(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist,inputdir=$path/input/,outputdir=$path/output/)
nmds(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, mindim=3, maxdim=3,inputdir=$path/input/,outputdir=$path/output/)
amova(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, design=soil.design,inputdir=$path/input/,outputdir=$path/output/)
homova(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, design=soil.design,inputdir=$path/input/,outputdir=$path/output/)
corr.axes(axes=final.opti_mcc.thetayc.0.03.lt.ave.pcoa.axes, shared=final.opti_mcc.0.03.subsample.shared, method=spearman, numaxes=3, label=0.03,inputdir=$path/input/,outputdir=$path/output/)
system(mv final.opti_mcc.0.03.subsample.spearman.corr.axes final.opti_mcc.0.03.subsample.spearman.corr.axesThetayc3D,inputdir=$path/input/,outputdir=$path/output/)
corr.axes(axes=final.opti_mcc.thetayc.0.03.lt.ave.pcoa.axes, metadata=soilpro.metadata, method=pearson, numaxes=3, label=0.03,inputdir=$path/input/,outputdir=$path/output/)";
    file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -o Logs_sge -e Logs_sge -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
            echo "<-- Finish plot graph -->";
            break;
        }
    }



}

?>


