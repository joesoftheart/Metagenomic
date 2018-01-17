
<?php 
        include('setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");


         $user = $argv[1];
         $project = $argv[2];
         $GLOBALS['maximum_ambiguous'] = $argv[3];
         $GLOBALS['maximum_homopolymer'] = $argv[4];
         $GLOBALS['minimum_reads_length'] = $argv[5];
         $GLOBALS['maximum_reads_length'] = $argv[6];
         $GLOBALS['alignment'] = $argv[7];
         $GLOBALS['diffs'] = $argv[8];
         $GLOBALS['reference'] = $argv[9];
         $GLOBALS['taxonomy'] = $argv[10];
         $GLOBALS['cutoff'] = $argv[11];
         $GLOBALS['taxon'] = $argv[12];      
         $path_in = $argv[13];
         $path_out = $argv[14];
         $GLOBALS['path_log'] = $argv[15];

         $GLOBALS['taxon'] = str_replace(',', ';',$GLOBALS['taxon']);

         

          $lable = explode('_',$GLOBALS['reference']);
           # db gg
          if($lable[0] == "gg"){
             $GLOBALS['lable'] = "1-2-3-4-5-6";
             $GLOBALS['lable_get_taxon'] = "2"; 
          }
          # db silva, RDP
          else{
             $GLOBALS['lable'] = "1-2-3-4-5";
             $GLOBALS['lable_get_taxon'] = "1"; 
          }


        if($user != "" && $project != "" && $argv[3] != "" && $argv[4] != "" && $argv[5] != "" && $argv[6] != "" && $argv[7] != "" && $argv[8] != "" && $argv[9] != "" && $argv[10] != "" && $argv[11] != "" && $argv[12] != "" && $argv[13] != "" && $argv[14] != "" && $argv[15] != ""){
            echo "Check Parameter Success"."\n";
           // check_file($user,$project,$path_in,$path_out);
            
          }else {

            echo "user : " . $user . "\n";
            echo "project : " . $project . "\n";
            echo "ambiguous : " . $GLOBALS['maximum_ambiguous'] . "\n";
            echo "homopolymer : " . $GLOBALS['maximum_homopolymer'] . "\n";
            echo "minimum_reads : " . $GLOBALS['minimum_reads_length'] . "\n";
            echo "maximum_reads : " . $GLOBALS['maximum_reads_length'] . "\n";
            echo "alignment : " . $GLOBALS['alignment'] . "\n";
            echo "diffs : " . $GLOBALS['diffs'] . "\n";
            echo "reference : " . $GLOBALS['reference'] . "\n";
            echo "taxonomy : " . $GLOBALS['taxonomy'] . "\n";
            echo "cutoff : " . $GLOBALS['cutoff'] . "\n";
            echo "taxon : " . $GLOBALS['taxon'] . "\n";
            echo "path_in : " . $path_in . "\n";
            echo "path_out : " . $path_out . "\n";
            echo "path_log : " . $GLOBALS['path_log'] . "\n";
         }


            function check_file($user, $project, $path_in, $path_out)
            {

                echo "check_file " . "\n";

                $path_file = $path_in . "stability.files";

                # stability.files ==> check oligos
                if (file_exists($path_file)) {

                    check_oligos($user, $project, $path_in, $path_out);

                } # create stability.files
                else {

                    run_makefile($user, $project, $path_in, $path_out);

                }
            }


            function check_oligos($user, $project, $path_in, $path_out)
            {

                echo "check_file_oligos " . "\n";
                $total_oligo = 0;

                $path_dir = $path_in;
                if (is_dir($path_dir)) {
                    if ($read = opendir($path_dir)) {
                        while (($file_oligo = readdir($read)) !== false) {

                            $allowed = array('oligo');
                            $ext = pathinfo($file_oligo, PATHINFO_EXTENSION);

                            if (in_array($ext, $allowed)) {

                                $total_oligo += 1;
                                makecontigs_oligos_summary($file_oligo, $user, $project, $path_in, $path_out);
                            }
                        }

                        closedir($read);
                    }
                }

                if ($total_oligo == 0) {
                    makecontig_summary($user, $project, $path_in, $path_out);
                }

            }


# make.file  stability.files
            function run_makefile($user, $project, $path_in, $path_out)
            {

                echo "run_makefile" . "\n";
                $jobname = $user . "_makefile";

                #make.file
                $make = "make.file(inputdir=$path_in,outputdir=$path_in)";

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

                        remove_logfile_mothur($path_in);
                        check_file($user, $project, $path_in, $path_out);
                        break;
                    }
                }
            }


# make.contigs oligos remove primer && summary.seqs
    function makecontigs_oligos_summary($file_oligo, $user, $project, $path_in, $path_out)
    {

                echo "makecontigs_oligos_summary" . "\n";
                $jobname = $user . "_oligo";

                $make = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=4 ,inputdir=$path_in,outputdir=$path_out)
                    summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path_in,outputdir=$path_out)";

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

                    $check_run = exec("qstat -j '$id_job' ");

                    if ($check_run == false) {

                        sleep(60);
                        replace_group($user, $project, $path_in, $path_out);
                        break;

                    }
                }
            }


# make.contigs && summary.seqs
            function makecontig_summary($user, $project, $path_in, $path_out)
            {

                echo "makecontigs_summary" . "\n";
                $jobname = $user . "_makesummary";

                $make = "make.contigs(file=stability.files,processors=8,inputdir=$path_in,outputdir=$path_out)
                 summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path_in,outputdir=$path_out)";

                file_put_contents($path_in . '/advance.batch', $make);
                $log = $GLOBALS['path_log'];
                $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";

                shell_exec($cmd);
                $check_qstat = "qnable to open Mothur/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.stat  -j '$jobname' ";
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

                        sleep(60);
                        replace_group($user, $project, $path_in, $path_out);
                        break;

                    }
                }
            }


            function replace_group($user, $project, $path_in, $path_out)
            {


                $file = $path_out . "stability.contigs.groups";

                $data_w = array();
                $lines = file($file);

                foreach ($lines as $line) {

                    $out = explode("\t", $line);
                    $out[1] = str_replace("-", "_", $out[1]);

                    $data = $out[0] . "\t" . $out[1];
                    array_push($data_w, $data);
                }

                file_put_contents($file, $data_w);

                screen_summary($user, $project, $path_in, $path_out);
            }


//////////////////////////////////////////////////
# screen.seqs && summary.seqs
#  $maximum_ambiguous
#  $minimum_reads_length
#  $maximum_reads_length

            function screen_summary($user, $project, $path_in, $path_out)
            {

                echo "screen_summary" . "\n";
                $jobname = $user . "_screen_summary";

                $make = "screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=" . $GLOBALS['maximum_ambiguous'] . ", minlength=" . $GLOBALS['minimum_reads_length'] . " , maxlength=" . $GLOBALS['maximum_reads_length'] . ", processors=8,inputdir=$path_in,outputdir=$path_out)
                    summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=$path_in,outputdir=$path_out)";

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

                        unique_count_summary($user, $project, $path_in, $path_out);

                        break;

                    }
                }
            }


#  unique.seqs && count.seqs && summary.seqs

            function unique_count_summary($user, $project, $path_in, $path_out)
            {

                echo "unique_count_summary" . "\n";
                $jobname = $user . "_unique_count_summary";

                $make = " unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=$path_in,outputdir=$path_out)
                     count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=$path_in,outputdir=$path_out)
                     summary.seqs(count=stability.trim.contigs.good.count_table ,inputdir=$path_in,outputdir=$path_out)";

                file_put_contents($path_in . '/advance.batch', $make);
                $log = $GLOBALS['path_log'];
                $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur $path_in/advance.batch";

                shell_exec($cmd);
                $check_qstat = "qstat -j $jobname ";
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


                        align_summary($user, $project, $path_in, $path_out);
                        break;

                    }
                }
            }


//////////////////////////////////////////
# align.seqs && summary.seqs
# input select alignment step

            function align_summary($user, $project, $path_in, $path_out)
            {

                $jobname = $user . "_align_summary";

                $make = "align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=" . $GLOBALS['alignment'] . ", processors=8,inputdir=$path_in,outputdir=$path_out)
                  summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table,inputdir=$path_in,outputdir=$path_out)";

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
                $id_j = trim($id_job);
                $log_read = $log . "/" . $jobname . ".o" . $id_j;
                $loop = true;
                while ($loop) {

                    $check_run = exec("qstat -j  $id_job ");

                    if ($check_run == false) {

                        echo "align_summary" . "\n";
                        //echo $log."\n";
                        read_log_sungrid($user, $project, $path_in, $path_out, $log_read);
                        break;

                    }
                }


            }


            function read_log_sungrid($user, $project, $path_in, $path_out, $log_read)
            {

                $file = file_get_contents($log_read);
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


                    $numItems = count($sum);
                    $i = 0;
                    foreach ($sum as $key => $value) {
                        if (++$i === $numItems) {
                            echo "last index!";
                            echo "Variable start Equal Variable end " . "\n";
                            break;
                        }
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
                    echo "Read_log_sungrid" . "\n";
                    screen_summary_2($user, $project, $path_in, $path_out, $start, $end);
                }

            }


///////////////////////////////////////
# Start  End

# screen.seqs = stat , end && summary.seqs
# input maximum ambiguous , maximum homopolymer , maximum reads length

            function screen_summary_2($user, $project, $path_in, $path_out, $start, $end)
            {

                echo "screen_summary_2" . "\n";
                $jobname = $user . "_screen_summary_2";

                $make = "screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=$start, end=$end, maxambig=" . $GLOBALS['maximum_ambiguous'] . ", maxhomop=" . $GLOBALS['maximum_homopolymer'] . ", maxlength=" . $GLOBALS['maximum_reads_length'] . ", processors=8,inputdir=$path_in,outputdir=$path_out)
                  summary.seqs(fasta=current, count=current,inputdir=$path_in,outputdir=$path_out)";

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

                    $check_run = exec("qstat -j $id_job");

                    if ($check_run == false) {


                        filter_unique_cluster_vsearch_remove_summary($user, $project, $path_in, $path_out);
                        break;

                    }
                }

            }


///////////////////////////////////////
# filter.seqs && unique.seqs && pre.cluster && chimera.vsearch && remove.seqs && summary.seqs

# input diffs => pre.cluster

            function filter_unique_cluster_vsearch_remove_summary($user, $project, $path_in, $path_out)
            {

                echo "filter_unique_cluster_vsearch_remove_summary" . "\n";
                $jobname = $user . "_filter_unique_cluster_vsearch_remove_summary";

                $make = "filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8,inputdir=$path_in,outputdir=$path_out)
                    unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table,inputdir=$path_in,outputdir=$path_out)
                    pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=" . $GLOBALS['diffs'] . ",inputdir=$path_in,outputdir=$path_out)
                    chimera.vsearch(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8,inputdir=$path_in,outputdir=$path_out)
                    remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.accnos,inputdir=$path_in,outputdir=$path_out)
                    summary.seqs(fasta=current, count=current,inputdir=$path_in,outputdir=$path_out)";

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

                        classifly_removelineage_summary($user, $project, $path_in, $path_out);
                        break;
                    }
                }

            }


//////////////////////////////////////////////
# Prepare in taxonmy

# classifly.seqs && remove.lineage && summary.seqs
# input reference , taxonomy , cutoff
            function classifly_removelineage_summary($user, $project, $path_in, $path_out)
            {

                echo "classifly_removelineage_summary" . "\n";
                $jobname = $user . "_classifly_removelineage_summary";
                $make = "classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=" . $GLOBALS['reference'] . ", taxonomy=" . $GLOBALS['taxonomy'] . ", cutoff=" . $GLOBALS['cutoff'] . ", processors=8,inputdir=$path_in,outputdir=$path_out)
                  remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=" . $GLOBALS['taxon'] . ",inputdir=$path_in,outputdir=$path_out)
                  summary.seqs(fasta=current, count=current,inputdir=$path_in,outputdir=$path_out)";

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

                    $check_run = exec("qstat -j $id_job");

                    if ($check_run == false) {

                        summary_tax($user, $project, $path_in, $path_out);
                        break;

                    }
                }

            }


#  && summary.tax

            function summary_tax($user, $project, $path_in, $path_out)
            {

                echo "summary_tax" . "\n";
                $jobname = $user . "_summary_tax";
                $make = "summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=$path_in,outputdir=$path_out)";

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


                        system_cp($user, $project, $path_in, $path_out);
                        break;

                    }
                }

            }


# system_cp

// path file false stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta final.fasta
            function system_cp($user, $project, $path_in, $path_out)
            {

                echo "system_cp" . "\n";
                $jobname = $user . "_system_cp";
                $make = "system(cp " . $path_out . "stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta " . $path_out . "/final.fasta ,outputdir=$path_out)
                    system(cp " . $path_out . "stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table " . $path_out . "/final.count_table ,outputdir=$path_out)
                    system(cp " . $path_out . "stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy " . $path_out . "/final.taxonomy ,outputdir=$path_out)";

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
                        phylotype_make_class_count($user, $project, $path_in, $path_out);

                        break;

                    }
                }


            }

# Phylotype Analysis
#output_plot

            function phylotype_make_class_count($user, $project, $path_in, $path_out)
            {

                echo "phylotype_make_class_count" . "\n";
                $jobname = $user . "_phylotype_make_class_count";

                $make = "phylotype(taxonomy=final.taxonomy,inputdir=$path_in,outputdir=$path_out)
                  make.shared(list=final.tx.list, count=final.count_table, label=" . $GLOBALS['lable'] . ",inputdir=$path_in,outputdir=$path_out)
                  classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, label=" . $GLOBALS['lable'] . ",inputdir=$path_in,outputdir=$path_out)
                  classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, basis=sequence, output=simple, label=" . $GLOBALS['lable_get_taxon'] . ",inputdir=$path_out,outputdir=owncloud/data/$user/files/$project/output_plot/)
                  count.groups(shared=final.tx.shared,inputdir=$path_in,outputdir=$path_out)";

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

                        on_check_remove($path_in, $path_out);

                        break;

                    }
                }

            }


            function on_check_remove($path_in, $path_out)
            {

                echo "on_check_remove" . "\n";
                $path_dir = $path_in;
                if (is_dir($path_dir)) {
                    if ($read = opendir($path_dir)) {
                        while (($file = readdir($read)) !== false) {

                            $allowed = array('8mer', 'sum', 'train', 'numNonZero', 'prob');
                            $ext = pathinfo($file, PATHINFO_EXTENSION);

                            if (in_array($ext, $allowed)) {

                                unlink($path_dir . $file);
                            }
                        }

                        closedir($read);
                    }
                }

                remove_logfile_mothur($path_out);

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
                echo "remove_logfile_mothur" . "\n";


            }
        

?>






