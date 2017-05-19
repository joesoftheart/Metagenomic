<?php
$user = $argv[1];
$id = $argv[2];
$project = $argv[3];
$path = $argv[4];
include('setting_sge.php');
putenv("SGE_ROOT=$SGE_ROOT");
putenv("PATH=$PATH");


if ($user != null && $project != null  && $path != null && $id != null){
   // run($user,$id,$project,$path);

    run($user,$id,$project,$path);
    }


// Run Program
function run($user,$id,$project,$path){
    check_file($user,$id,$project,$path);
}




// Check file 
 function check_file($user,$id, $project,$path){
     echo "\n";
     echo "Run check_file :";
    $path_stability = "owncloud/data/$user/files/$project/data/input/stability.files";
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
    $make = "make.file(inputdir=$path,outputdir=$path)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $make);
    $cmd = "qsub  -N  '$jobname' -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch";
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
    $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=8 ,inputdir=$path,outputdir=$path)
screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,inputdir=$path,outputdir=$path)
summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=$path,outputdir=$path)
unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=$path,outputdir=$path)
count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=$path,outputdir=$path)
summary.seqs(count=stability.trim.contigs.good.count_table,inputdir=$path,outputdir=$path)";
    file_put_contents('../owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";
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
    $cmd ="make.contigs(file=stability.files,processors=8,inputdir=$path,outputdir=$path)
summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path,outputdir=$path)
screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,inputdir=$path,outputdir=$path)
summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=$path,outputdir=$path)
unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=$path,outputdir=$path)
count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=$path,outputdir=$path)
summary.seqs(count=stability.trim.contigs.good.count_table,inputdir=$path,outputdir=$path)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
    $cmd ="align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=silva.v4.fasta, processors=8,inputdir=$path,outputdir=$path)
summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table,inputdir=$path,outputdir=$path)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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

    $file = file_get_contents($file_name);
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
    $cmd ="screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=$start, end=$end, maxambig=8, maxhomop=8, maxlength=260, processors=8,inputdir=$path,outputdir=$path)
summary.seqs(fasta=current, count=current,inputdir=$path,outputdir=$path)
filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8,inputdir=$path,outputdir=$path)
unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table,inputdir=$path,outputdir=$path)
pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=2,inputdir=$path,outputdir=$path)
chimera.vsearch(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8,inputdir=$path,outputdir=$path)
remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.accnos,inputdir=$path,outputdir=$path)
summary.seqs(fasta=current, count=current,inputdir=$path,outputdir=$path)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
    $cmd ="classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=80, processors=8,inputdir=$path,outputdir=$path)
remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified,inputdir=$path,outputdir=$path)
summary.seqs(fasta=current, count=current,inputdir=$path,outputdir=$path)
summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=$path,outputdir=$path)
system(cp owncloud/data/$user/files/$project/data/input/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta owncloud/data/joesoftheart/files/SAMPLE-WES1053/data/input/final.fasta)
system(cp owncloud/data/$user/files/$project/data/input/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table owncloud/data/joesoftheart/files/SAMPLE-WES1053/data/input/final.count_table)
system(cp owncloud/data/$user/files/$project/data/input/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy owncloud/data/joesoftheart/files/SAMPLE-WES1053/data/input/final.taxonomy)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
    $jobname = $user."_".$id."_phylotype_count";
    $cmd ="phylotype(taxonomy=final.taxonomy,inputdir=$path,outputdir=$path)
make.shared(list=final.tx.list, count=final.count_table, label=1-2-3-4-5-6,inputdir=$path,outputdir=$path)
classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, label=1-2-3-4-5-6,inputdir=$path,outputdir=$path)
#classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, basis=sequence, output=simple, label=2,inputdir=$path,outputdir=$path)
count.groups(shared=final.tx.shared,inputdir=$path,outputdir=$path)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
             sub_sample_sammary($user,$id, $project,$path);
             break;
         }
     }
}

// Sub samplr sammary
 function sub_sample_sammary($user,$id, $project,$path){
     echo "\n";
     echo "Run sub_sample_summary :";
    $jobname = $user."_".$id."_sub_sample_sammary";
    $cmd ="sub.sample(shared=final.tx.shared, size=5000,inputdir=$path,outputdir=$path)
collect.single(shared=final.tx.shared, calc=chao, freq=100,inputdir=$path,outputdir=$path)
rarefaction.single(shared=final.tx.shared, calc=sobs, freq=100, processors=8,inputdir=$path,outputdir=$path)
summary.single(shared=final.tx.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon, subsample=5000,inputdir=$path,outputdir=$path)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
             plot_graph($user,$id, $project,$path);
             break;
         }
     }
}

// Last funtion plot graph
 function plot_graph($user,$id, $project,$path){
     echo "\n";
     echo "Run plot_graph :";
    $jobname = $user."_".$id."_plot_graph";
    $cmd ="heatmap.bin(shared=final.tx.2.subsample.shared, scale=log2, numotu=10,inputdir=$path,outputdir=$path)
dist.shared(shared=final.tx.shared, calc=thetayc-jclass-lennon-morisitahorn-braycurtis, subsample=5000,inputdir=$path,outputdir=$path)
heatmap.sim(phylip=final.tx.thetayc.2.lt.ave.dist,inputdir=$path,outputdir=$path) #No need
heatmap.sim(phylip=final.tx.jclass.2.lt.ave.dist,inputdir=$path,outputdir=$path) #No need
summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=S1_S1-S2_S3-S3_S5-S4_S7, all=T,inputdir=$path,outputdir=$path)
venn(shared=final.tx.2.subsample.shared, groups=S1_S1-S2_S3-S3_S5-S4_S7,inputdir=$path,outputdir=$path)
tree.shared(phylip=final.tx.thetayc.2.lt.ave.dist,inputdir=$path,outputdir=$path)
tree.shared(phylip=final.tx.morisitahorn.2.lt.ave.dist,inputdir=$path,outputdir=$path)
tree.shared(phylip=final.tx.jclass.2.lt.ave.dist,inputdir=$path,outputdir=$path)
tree.shared(phylip=final.tx.braycurtis.2.lt.ave.dist,inputdir=$path,outputdir=$path)
tree.shared(phylip=final.tx.lennon.2.lt.ave.dist,inputdir=$path,outputdir=$path)
parsimony(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design,  groups=all,inputdir=$path,outputdir=$path) #No need
unifrac.weighted(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design, random=T,inputdir=$path,outputdir=$path) #No need
unifrac.unweighted(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design, random=T, groups=all,inputdir=$path,outputdir=$path) #No need
pcoa(phylip=final.tx.morisitahorn.2.lt.ave.dist,inputdir=$path,outputdir=$path)
pcoa(phylip=final.tx.thetayc.2.lt.ave.dist,inputdir=$path,outputdir=$path)
pcoa(phylip=final.tx.jclass.2.lt.ave.dist,inputdir=$path,outputdir=$path)
nmds(phylip=final.tx.morisitahorn.2.lt.ave.dist, mindim=3, maxdim=3,inputdir=$path,outputdir=$path)
nmds(phylip=final.tx.thetayc.2.lt.ave.dist, mindim=2, maxdim=2,inputdir=$path,outputdir=$path)
nmds(phylip=final.tx.jclass.2.lt.ave.dist, mindim=3, maxdim=3,inputdir=$path,outputdir=$path)
amova(phylip=final.tx.thetayc.2.lt.ave.dist, design=soil.design,inputdir=$path,outputdir=$path) #No need
homova(phylip=final.tx.thetayc.2.lt.ave.dist, design=soil.design,inputdir=$path,outputdir=$path)
corr.axes(axes=final.tx.thetayc.2.lt.ave.nmds.axes, shared=final.tx.2.subsample.shared, method=spearman, numaxes=2, label=2,inputdir=$path,outputdir=$path)
corr.axes(axes=final.tx.thetayc.2.lt.ave.nmds.axes, metadata=soilpro.metadata, method=pearson, numaxes=2, label=2,inputdir=$path,outputdir=$path)";
    file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);
    $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur ../owncloud/data/$user/files/$project/data/input/run.batch ";
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
             echo "<-- Finish plot graph -->";
             break;
         }
     }
}

?>


