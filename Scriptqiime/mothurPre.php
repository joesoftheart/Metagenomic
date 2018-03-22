
<?php 
        include('setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");

         $user = $argv[1];
         $project = $argv[2];
         $path_in = $argv[3];
         $path_out = $argv[4];
         $GLOBALS['path_log'] = $argv[5];
         $GLOBALS['maximum_ambiguous'] = $argv[6];
         $GLOBALS['maximum_homopolymer'] = $argv[7];
         $GLOBALS['minimum_reads_length'] = $argv[8];
         $GLOBALS['maximum_reads_length'] = $argv[9];
         $GLOBALS['alignment'] = $argv[10];
         $GLOBALS['diffs'] = $argv[11];
         $GLOBALS['reference'] = $argv[12];
         $GLOBALS['taxonomy'] = $argv[13];
         $GLOBALS['cutoff'] = $argv[14];
         $GLOBALS['taxon'] = $argv[15]; 
         $platform_sam = $argv[16];
         $platform_type = $argv[17];

         $GLOBALS['taxon'] = str_replace(',', ';',$GLOBALS['taxon']);     

        if($user != "" && $project != "" && $argv[3] != "" && $argv[4] != "" && $argv[5] != "" && $argv[6] != "" && $argv[7] != "" && $argv[8] != "" && $argv[9] != "" && $argv[10] != "" && $argv[11] != "" && $argv[12] != "" && $argv[13] != ""  && $argv[14] != ""  && $argv[15] != "" && $argv[16] != "" && $argv[17] != ""){

              echo "Check Parameter Success"."\n";
              find_fastq_fasta($platform_sam,$platform_type,$user,$project,$path_in,$path_out);

          }else {

            echo "user : " . $user . "\n";
            echo "project : " . $project . "\n";
            echo "ambiguous : " . $GLOBALS['maximum_ambiguous'] . "\n";
            echo "homopolymer : " . $GLOBALS['maximum_homopolymer'] . "\n";
            echo "minimum_reads : " . $GLOBALS['minimum_reads_length'] . "\n";
            echo "maximum_reads : " . $GLOBALS['maximum_reads_length'] . "\n";
            echo "alignment : " . $GLOBALS['alignment'] . "\n";
            echo "diffs : " . $GLOBALS['diffs'] . "\n";
            echo "path_in : " . $path_in . "\n";
            echo "path_out : " . $path_out . "\n";
            echo "path_log : " . $GLOBALS['path_log'] . "\n";
         }


    # Input ==> $user , $project
    function find_oligos($path_in){

        $path = $path_in;
        $array_name = array();
        $path_dir = $path;
        if (is_dir($path_dir)){
            if ($read = opendir($path_dir)) {
                while (($file = readdir($read)) !== false) {
                    $allowed = array("fastq","fasta","tax","align","batch","files");
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    if (!in_array($ext,$allowed)) {
                        if(($file != "99_otu_map.txt")&&($file != ".")&&($file != "..")){
                            array_push($array_name, $file);
                        }
                    }
                }
            closedir($read);
            }
        }

        foreach ($array_name as $key => $value){
            $file = $path.$value; 
            $check = false;
            $myfile = fopen($file,'r') or die ("Unable to open file");
            while(($lines = fgets($myfile)) !== false){
                if(preg_match("/primer|barcode/", $lines)){
                $type = explode("\t",$lines);

                  if((sizeof($type) == 3) && 
                     ($type[0] == "primer") && 
                     (trim($type[2]) == "NONE")){
                         $check = true;
                  }elseif((sizeof($type) == 4) &&
                          ($type[0] == "barcode") &&
                          ($type[2] ==  "NONE")){
                          $check = true;   
                  }else{
                      $check = false;
                      break;
                  }
                }
            }
            fclose($myfile);
            if($check == true){  return $value; }
            else{  return "empty"; }
        }


    }


    # Input ==> $platform_sam , $platform_type , $user , $project
    function find_fastq_fasta($platform_sam,$platform_type,$user, $project, $path_in, $path_out){

        $path = $path_in;
        $file_fastq = glob($path."/*.fastq");
        $file_fasta = glob($path."/*.fasta");
        $ref_fasta = array("gg_13_8_99.fasta",
                           "silva.v4.fasta",
                           "silva.bacteria.fasta",
                           "silva.v123.fasta",
                           "silva.v34.fasta",
                           "silva.v345.fasta",
                           "silva.v45.fasta",
                           "trainset16_022016.rdp.fasta");

        if($platform_sam == "miseq"){

            $file_oligo = find_oligos($path_in);
            if($platform_type == "miseq_without_barcodes"){

                  $check = "not";
                  check_file($user,$project,$path_in,$path_out,$file_oligo,$check);

            }elseif ($platform_type == "miseq_contain_primer") {

                if($file_oligo != "empty"){
                   $check = "oligos";
                   check_file($user,$project,$path_in,$path_out,$file_oligo,$check);

                }else{ 
                    echo "Unable-open-fileoligos";
                    break;
                }  
                 
            }elseif($platform_type == "miseq_barcodes_primers"){
                $k_fastq = null;
                foreach ($file_fastq as $value) {
                $name = end((explode('/',$value)));
                preg_match('/R(\w)/',$name,$results);
                    if($results){
                      list($R,$R_index) = $results;
                      $k_fastq .= $R.":".$name.":";
                    }
                }
                list($R1,$R1fastq,$R2,$R2fastq) = explode(":", $k_fastq);
                # Get fastq => $R1name $R2name
                if($file_oligo != "empty"){
                    makecontigs_barcode_primer($user, $project, $path_in, $path_out,$R1fastq,$R2fastq,$file_oligo);
                }else{ 
                    echo "Unable-open-fileoligos";
                    break;
                }  
            }


        }elseif ($platform_sam == "proton") {

            $file_oligo = find_oligos($path_in);
            if($platform_type == "proton_barcodes_primers"){
                $name_fastq = null;
                foreach($file_fastq as $value){
                      $name_fastq = end((explode('/',$value)));
                }
                if($file_oligo != "empty"){
                    list($fasta_get) = explode("fastq", $name_fastq);
                    $fasta_get = $fasta_get."fasta";
                    fastq_IonProton_primer($user,$project,$path_in,$path_out,$name_fastq,$fasta_get,$file_oligo);
                }else{ 
                    echo "Unable-open-fileoligos";
                    break;
                }
            }elseif($platform_type == "proton_barcodes_fasta"){
                $get_fasta = null;
                foreach ($file_fasta as $key => $value){
                $name_fasta = end((explode('/', $value)));
                    if(!in_array($name_fasta,$ref_fasta)){
                        $get_fasta = $name_fasta;
                    }   
                }
                if($file_oligo != "empty"){
                    fasttaOnly_IonProton_primer($user,$project,$path_in,$path_out,$get_fasta,$file_oligo);
                }else{ 
                    echo "Unable-open-fileoligos";
                    break;
                }
            }  
        }         
    }


    # Input ==> log 
    function tail($name_log){
         $path = $GLOBALS['path_log'];
         $file = $path.$name_log;
         exec("/usr/bin/tail ".$file." -n 4 " , $output);
         list($line0,$line1,$line2,$line3) = $output;
             if(preg_match("/Detected 1 /", $line0)){
                 return "OK";
             }else{
                return "NO";   
             }     
    }

    # Input ==> log 
    function tail2($name_log){
         $path = $GLOBALS['path_log'];
         $file = $path.$name_log;
         exec("/usr/bin/tail ".$file." -n 1 " , $output);
         list($line0) = $output;
             if(preg_match("/mothur > quit()/", $line0)){
                 return "OK";
             }else{
                return "NO";   
             }     
    }

  

        # input 2 file fastq 
        # Paired-end fastq file contain barcodes and primers
        function makecontigs_barcode_primer($user, $project, $path_in, $path_out,$R1fastq,$R2fastq,$file_oligo){
              
              list($namefastq_R1) = explode(".fastq", $R1fastq);
              # cutadapt
                $file_cutadapt = $namefastq_R1.".trim.contigs.fasta";
              # moveTO_stability_summary
                $file_group = $namefastq_R1.".contigs.groups";

              echo "makecontigs_barcode_primer" . "\n";
              $jobname = $user . "_makecontigs_barcode_primer";


              $make ="make.contigs(ffastq=$R1fastq,rfastq=$R2fastq, oligos=$file_oligo, checkorient=T, processors=8 ,inputdir=$path_in,outputdir=$path_out)";

              file_put_contents($path_in . 'advance.batch', $make);
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
                        $id = trim($id_job);
                        $name_log = $jobname.".o".$id;
                        $check = tail($name_log);

                        if($check == "OK"){
                           convert_sequences($user, $project, $path_in, $path_out,$file_oligo,$file_cutadapt,$file_group); 
                           $loop = false;  
                        }else{
                            echo "Stop Run(makecontigs-barcode-primer)";
                            break; 
                        }  
                       
                    }
                }
         }



         # input file fastq
         # Fastq file contain barcodes and primers
         function fastq_IonProton_primer($user, $project, $path_in, $path_out,$name_fastq,$fasta_get,$file_oligo){

              list($fastq_first) = explode(".fastq", $name_fastq);
              # cutadapt
                $file_cutadapt = $fastq_first.".trim.fasta";
              # moveTO_stability_summary
                $file_group = $fastq_first.".groups";

            echo "fastq-IonProton_primer"."\n";
            $jobname = $user."_fastq-IonProton_primer";

            $make ="fastq.info(fastq=$name_fastq,inputdir=$path_in,outputdir=$path_out)
                 trim.seqs(fasta=$fasta_get, oligos=$file_oligo,qfile=metagenomeNepenthes.qual, qaverage=25 ,inputdir=$path_in,outputdir=$path_out)";


            file_put_contents($path_in . 'advance.batch', $make);
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

                        $id = trim($id_job);
                        $name_log = $jobname.".o".$id;
                        $check = tail($name_log);

                        if($check == "OK"){
                           convert_sequences($user, $project, $path_in, $path_out,$file_oligo,$file_cutadapt,$file_group);
                           $loop = false;  
                        }else{
                            echo "Stop Run(fastq_IonProton_primer)";
                            break; 
                        }

                    }
                }
         }

       
         # input file fasta
         # Only fasta file contain barcodes and primers (no quality file)
        function fasttaOnly_IonProton_primer($user, $project, $path_in, $path_out,$get_fasta,$file_oligo){

    
            list($fasta_first) = explode(".fasta", $get_fasta);
            # cutadapt
                $file_cutadapt = $fasta_first.".trim.fasta";
            # moveTO_stability_summary
                $file_group = $fasta_first.".groups";


            echo "fasttaOnly-IonProton_primer"."\n";
            $jobname = $user."_fasttaOnly-IonProton_primer";

            $make ="trim.seqs(fasta=$get_fasta,oligos=$file_oligo,inputdir=$path_in,outputdir=$path_out)";

            file_put_contents($path_in . 'advance.batch', $make);
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

                        $id = trim($id_job);
                        $name_log = $jobname.".o".$id;
                        $check = tail2($name_log);

                        if($check == "OK"){
                             convert_sequences($user, $project, $path_in, $path_out,$file_oligo,$file_cutadapt,$file_group);
                             $loop = false;
                        }else{
                            
                            echo "Stop Run(fasttaOnly_IonProton_primer)";
                            break;
                        }
                       
                    }
                }
         }


        function convert_sequences($user, $project, $path_in, $path_out,$file_oligo,$file_cutadapt,$file_group){
        
             echo "convert_sequences"."\n";
             $path = $path_in.$file_oligo;
             $file = file_get_contents($path);

             $cutadapt = array();

                $pattern = "/^.*(primer).*\$/m";
                if (preg_match_all($pattern, $file, $matches)) {

                    $val = implode("\n", $matches[0]);
                    $sum = explode("\n", $val);

                    foreach ($sum as $key => $value){
                         
                    $primer = explode("\t", $value);
                    $con_primer = shell_exec("/usr/bin/python Scripts/revcomDNAseq.py ". $primer[1]);

                    array_push($cutadapt," -a ".trim($con_primer));     
                    }
                }
               
             
              $cmd_cutadapt = implode("",$cutadapt);
              cutadapt($user, $project, $path_in, $path_out,$cmd_cutadapt,$file_cutadapt,$file_group);
       
        }

        function cutadapt($user, $project, $path_in, $path_out,$cmd_cutadapt,$file_cutadapt,$file_group){
           

            echo "cutadapt" . "\n";
            $jobname = $user . "_cutadapt";
            $log = $GLOBALS['path_log'];

            $file_primer = $cmd_cutadapt;

            $out_stability = $path_out."stability.trim.contigs.fasta";

            $in_trim_contigs_fasta = $path_out.$file_cutadapt;

         $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y /usr/bin/cutadapt $file_primer -o $out_stability $in_trim_contigs_fasta";

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
            while ($loop){
                   $check_run = exec("qstat -j $id_job");
                    if($check_run == false){
                         moveTO_stability_summary($user, $project, $path_in, $path_out,$file_group);
                      break;  
                    }
                }   
          

        }


    # move file.contigs.groups to stability.contigs.groups && Run summary.seqs
    function moveTO_stability_summary($user, $project, $path_in, $path_out,$file_group){
               
               $file_in = $path_out.$file_group;
               $file_out = $path_out."stability.contigs.groups";
              
               echo "moveTO_stability_summary" . "\n";
                $jobname = $user . "_moveTO_stability_summary";
                $make = "system(cp " .$file_in." ".$file_out. ",outputdir=$path_out)
                          summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path_out,outputdir=$path_out)";

                file_put_contents($path_in . 'advance.batch', $make);
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
                        //remove_logfile_mothur($path_out);
                        sleep(60);
                        replace_group($user, $project, $path_in, $path_out);
                        break;

                    }
                }

        }



        function check_file($user, $project, $path_in, $path_out,$file_oligo,$check){

                echo "check_file " . "\n";
                $path_file = $path_in . "stability.files";
                #stability.files ==> check oligos
                if (file_exists($path_file)) {
                     
                     if($check == "oligos"){
                       moveName_makeContigs($file_oligo, $user,$project,$path_in,$path_out);

                     }elseif ($check == "not") {
                         makecontig_summary($user, $project, $path_in, $path_out);
                     }  
                } 

                #create stability.files
                else {
                  run_makefile($user,$project,$path_in,$path_out,$file_oligo,$check);
                }
        }


# make.file  stability.files
        function run_makefile($user,$project,$path_in,$path_out,$file_oligo,$check){

                echo "run_makefile" . "\n";
                $jobname = $user . "_makefile";

                #make.file
                $make = "make.file(inputdir=$path_in,outputdir=$path_in)";

                file_put_contents($path_in . 'advance.batch', $make);
                $log = $GLOBALS['path_log'];
                $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur1_39_5/mothur $path_in/advance.batch";

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
                     check_file($user,$project,$path_in,$path_out,$file_oligo,$check);
                     break;
                    }
                }
        }


    function moveName_makeContigs($file_oligo, $user,$project,$path_in,$path_out){

             echo "moveName_makeContigs" . "\n";
             $jobname = $user . "_moveName_makeContigs";

             $make = "system(mv ".$path_in."stability.files  ".$path_in."raw.files)
                      make.contigs(file=raw.files, processors=8 ,inputdir=$path_in,outputdir=$path_out)";

             file_put_contents($path_in . 'advance.batch', $make);
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

                        sleep(120);
                        $id = trim($id_job);
                        $name_log = $jobname.".o".$id;
                        $check = tail($name_log);

                        if($check == "OK"){
                           convert_primer($user,$project,$path_in,$path_out,$file_oligo);
                           $loop = false;  
                        }else{
                            echo "Stop Run(moveName_makeContigs)";
                            break; 
                        }

                    }
                }
     }

        function convert_primer($user,$project,$path_in,$path_out,$file_oligo){
        
             echo "convert_primer"."\n";
             $path = $path_in.$file_oligo;
             $file = file_get_contents($path);

             $cutadapt_g = array();
             $cutadapt_a = array();

                $pattern = "/^.*(primer).*\$/m";
                if (preg_match_all($pattern, $file, $matches)) {

                    $val = implode("\n", $matches[0]);
                    $sum = explode("\n", $val);

                    foreach ($sum as $key => $value){
                         
                    $primer = explode("\t", $value);
                    $con_primer = shell_exec("/usr/bin/python Scripts/revcomDNAseq.py ". $primer[1]);
                    
                     array_push($cutadapt_g," -g ".trim($primer[1]));
                     array_push($cutadapt_a," -a ".trim($con_primer));    

                    }
                }
               
             
              $cmd_cutadapt_g = implode("",$cutadapt_g);
              $cmd_cutadapt_a = implode("",$cutadapt_a);
              // echo $cmd_cutadapt_g."\n".$cmd_cutadapt_a."\n";

              cutadapt_primer1($user,$project,$path_in,$path_out,$cmd_cutadapt_g,$cmd_cutadapt_a);
       
        }

     function cutadapt_primer1($user,$project,$path_in,$path_out,$cmd_cutadapt_g,$cmd_cutadapt_a){
           

            echo "cutadapt_primer1" . "\n";
            $jobname = $user . "_cutadapt_primer1";
            $log = $GLOBALS['path_log'];

            $output1 = $path_out."out_raw1.fasta";
            $raw_fasta = $path_out."raw.trim.contigs.fasta";

           
            $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y /usr/bin/cutadapt $cmd_cutadapt_g -e 0.15 -o $output1 $raw_fasta";

             // /usr/bin/cutadapt $1 -o $2 $3 
             // /usr/bin/cutadapt $4 -o stability.trim.contigs.fasta $2 

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
            while ($loop){
                   $check_run = exec("qstat -j $id_job");
                    if($check_run == false){
                      cutadapt_primer2($user,$project,$path_in,$path_out,$cmd_cutadapt_a);
                      break;  
                    }
                }   

        }

    function cutadapt_primer2($user,$project,$path_in,$path_out,$cmd_cutadapt_a){
           

            echo "cutadapt_primer2" . "\n";
            
            $file_group = "raw.contigs.groups";

            $jobname = $user . "_cutadapt_primer2";
            $log = $GLOBALS['path_log'];

            $output1 = $path_out."out_raw1.fasta";
            $stability_fasta = $path_out."stability.trim.contigs.fasta";
            
            $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y /usr/bin/cutadapt $cmd_cutadapt_a -e 0.15 -o $stability_fasta $output1";

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
            while ($loop){
                   $check_run = exec("qstat -j $id_job");
                    if($check_run == false){

                      moveTO_stability_summary($user, $project, $path_in, $path_out,$file_group);
                    
                      break;  
                    }
            }   
    }


# make.contigs && summary.seqs
#read log
            function makecontig_summary($user, $project, $path_in, $path_out)
            {

                echo "makecontigs_summary" . "\n";
                $jobname = $user . "_makesummary";

                $make = "make.contigs(file=stability.files,processors=8,inputdir=$path_in,outputdir=$path_out)
                 summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=$path_in,outputdir=$path_out)";

                file_put_contents($path_in . 'advance.batch', $make);
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

                $GLOBALS['log_make'] = trim($id_job);

                $loop = true;
                while ($loop) {

                    $check_run = exec("qstat -j $id_job");

                    if ($check_run == false) {

                        sleep(90);
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

                file_put_contents($path_in . 'advance.batch', $make);
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

                        $loop = false;
                        unique_count_summary($user, $project, $path_in, $path_out);

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

                file_put_contents($path_in . 'advance.batch', $make);
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

                        $loop = false;
                        align_summary($user, $project, $path_in, $path_out);
                   

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

                file_put_contents($path_in . 'advance.batch', $make);
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
                        $loop = false;
                        read_log_sungrid($user, $project, $path_in, $path_out, $log_read);
                       

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

                file_put_contents($path_in . 'advance.batch', $make);
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

                        $loop = false;
                        filter_unique_cluster_vsearch_remove_summary($user, $project, $path_in, $path_out);
                        

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

                file_put_contents($path_in . 'advance.batch', $make);
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
                        $loop = false;
                        classifly_removelineage_summary($user, $project, $path_in, $path_out);
                        
                    }
                }

            }


            //////////////////////////////////////////////
# Prepare in taxonmy

# classifly.seqs && remove.lineage && summary.seqs
# input reference , taxonomy , cutoff
#read log
        function classifly_removelineage_summary($user, $project, $path_in, $path_out){

                echo "classifly_removelineage_summary" . "\n";
                $jobname = $user . "_classifly_removelineage_summary";
                $make = "classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=" . $GLOBALS['reference'] . ", taxonomy=" . $GLOBALS['taxonomy'] . ", cutoff=" . $GLOBALS['cutoff'] . ", processors=8,inputdir=$path_in,outputdir=$path_out)
                  remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=" . $GLOBALS['taxon'] . ",inputdir=$path_in,outputdir=$path_out)";

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
                
                $GLOBALS['log_classify'] = trim($id_job);

                $loop = true;
                while ($loop) {

                    $check_run = exec("qstat -j $id_job");

                    if ($check_run == false) {
                       $loop = false;
                       system_cp($user, $project, $path_in, $path_out);

                    }
                } 
        }     



             function system_cp($user, $project, $path_in, $path_out){

                echo "system_cp" . "\n";

                $jobname = $user . "_system_cp";
                $make = "system(cp " .$path_out."stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta  ".$path_out."final.fasta ,outputdir=$path_out)
                    system(cp ".$path_out."stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table  ".$path_out."final.count_table ,outputdir=$path_out)";

                file_put_contents($path_in . 'advance.batch', $make);
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
                       
                         $loop = false;
                        replace_NameSame($user, $project, $path_in, $path_out);
                    
                    }
                }


            }


           function replace_NameSame($user, $project, $path_in, $path_out){

                ini_set('memory_limit', '-1');
                $file = $path_out."final.count_table";

                $data_w = array();
                $count = 0;
                $myfile =  fopen($file, 'r') or die ("Unable to open file");
                while (($value = fgets($myfile)) !== false) {
                    if($count == 0){
                        $out1 = str_replace("_", "+", $value);
                        $name = explode("\t", $out1);
                        $out2 = str_replace("+", "_", $name[0]);
                        $name[0] = $out2;
                        $name2 = implode("\t", $name);
                        $header = str_replace("+", "t", $name2);
                        array_push($data_w, $header);
                        $count++;
                    }else{
                        array_push($data_w, $value);
                    }              
                }
                fclose($myfile);
                file_put_contents($file, $data_w);
                deunique_seqs($user,$project,$path_in,$path_out);  
            }



            function deunique_seqs($user,$project,$path_in,$path_out){

                echo "deunique_seqs" . "\n";
                $jobname = $user . "_deunique_seqs";

                $make = "deunique.seqs(fasta=final.fasta , count=final.count_table,inputdir=$path_in,outputdir=$path_out)";

                file_put_contents($path_in . 'advance.batch', $make);
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

                        $loop = false;
                        degap_seqs($user,$project,$path_in,$path_out);
                    }
                }

            }

            function degap_seqs($user,$project,$path_in,$path_out){

                echo "degap_seqs" . "\n";
                $jobname = $user . "_degap_seqs";

                $make = "degap.seqs(fasta=final.redundant.fasta,inputdir=$path_in,outputdir=$path_out)";

                file_put_contents($path_in . 'advance.batch', $make);
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

                         $loop = false;
                         split_groups($user,$project,$path_in,$path_out);
                    }
                }

            }

        function split_groups($user,$project,$path_in,$path_out){

                echo "split_groups" . "\n";
                $jobname = $user ."_split_groups";

                $make = "split.groups(fasta=final.redundant.ng.fasta,group=final.redundant.groups,inputdir=$path_in,outputdir=$path_out)";

                file_put_contents($path_in . 'advance.batch', $make);
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

                          $id = trim($id_job);
                          $log_split =  $jobname.".o".$id;
                          $loop = false;
                          create_file_map($user,$project,$path_in,$path_out,$log_split);
                         
                    }
                }
        }


    function random_sequence($num){
      
         $arr_sequence = array();
         # arvg[1] is number-sequence
         # arvg[2] is length min sequence
         # arvg[3] is length max sequence
         # arvg[4] is number-set-sequence
         $cmd = "/usr/bin/python Scriptqiime/random_sequence.py $num 8 8 1";
         $output = shell_exec($cmd);
      
          $arr = explode(',', $output);
            foreach ($arr as $key => $value) {
                array_push($arr_sequence, $value);
            }

         return $arr_sequence;
    }


     # $otu_talbe = "aumza_split_groups.o2833";
    function create_file_map($user,$project,$path_in,$path_out,$log_split){

        $log = $GLOBALS['path_log'];
        
        $mapcreate = array();

        $header = "#SampleID"."\t"."BarcodeSequence"."\t"."LinkerPrimerSequence"."\t"."ReversePrimer"."\t"."InputFileName"."\t"."Description"."\n";
        array_push($mapcreate,$header);

        $LinkerPrimerSequence = "GGATTAGATACCCTGGTAGTCC";
        $ReversePrimer = "CTCACGRCACGAGCTGACG";
       
        $otu_talbe = $log.$log_split;

        $file = file_get_contents($otu_talbe);
        $search_for = 'Processing group';
        $pattern = preg_quote($search_for, '/');

            $pattern = "/^.*(Processing group).*\$/m";

                if (preg_match_all($pattern, $file, $matches)) {
                  
                     $val = implode("\n", $matches[0]);
                     $sum = explode("\n", $val);
                    
                    $num_sample = count($sum);
                    $sequence = random_sequence($num_sample);

                    foreach ($sum as $key => $value) {
                       
                        list($process,$sample) = explode(':',$value);

                          $s_trim = trim($sample);
                          $n_seq = trim($sequence[$key]); 
                          $infile = "final.redundant.ng.".$s_trim.".fasta";
                          $des = "oricode_".$s_trim;

                          $body = $s_trim."\t".$n_seq."\t".$LinkerPrimerSequence."\t".$ReversePrimer."\t". $infile."\t".$des."\n";

                         array_push($mapcreate,$body);

                    }
                }

          file_put_contents($path_in."Test_Map.txt",$mapcreate);
          on_check_remove($path_in, $path_out); 
     }



        function on_check_remove($path_in, $path_out){

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


        function remove_logfile_mothur($path_out){

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






