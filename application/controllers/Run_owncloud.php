<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Run_owncloud extends CI_Controller
{


    public function __construct()
    {

        parent::__construct();
        $this->load->helper(array('url', 'path'));

        // include(APPPATH.'../setting_sge.php');
        // putenv("SGE_ROOT=$SGE_ROOT");
        // putenv("PATH=$PATH");
    }



    public function find_fasta(){

        $ref_fasta = array("gg_13_8_99.fasta",
                           "silva.v4.fasta",
                           "silva.bacteria.fasta",
                           "silva.v123.fasta",
                           "silva.v34.fasta",
                           "silva.v345.fasta",
                           "silva.v45.fasta",
                           "trainset16_022016.rdp.fasta");
        $path = "owncloud/data/aumza/files/fastaChimera/input/";
        #find (.fasta) or (.fst)
        $file_fastq = glob($path."*.{fasta,fst}", GLOB_BRACE); 
        $name_list = array();
        $namefasta_list = array();
        foreach ($file_fastq as $key => $file) {
            $name_fasta = basename($file);
            if(!in_array($name_fasta,$ref_fasta)){
                         list($file_name,$file_extension) = explode(".",$name_fasta);
                         array_push($name_list,$file_name);
                         array_push($namefasta_list,$name_fasta);
            }
        } 
        
        $str_name_list = null;
        $count_line = null;
        $count_name_list = count($name_list);
        foreach ($namefasta_list as $val) {
            $count_line++;
            if($count_line == $count_name_list){
                 $str_name_list .= $val; 
            }else{
                 $str_name_list .= $val."-";
            }
        }

      
        $group_name_list = null;
        $count_line2 = null;
        foreach ($name_list as $val_name) {

            $data_split = preg_split("/(_|-)/", $val_name);
            $count_line2++;
            if($count_line2 == $count_name_list){
                 $group_name_list .= $data_split[0]; 
            }else{
                 $group_name_list .= $data_split[0]."-";
            }  
        }

         echo $str_name_list;
         echo "<br><br>";
         echo $group_name_list;
    

    }
    
    public function gunzip(){

        $file_gun = FCPATH."table_even2029.biom.gz";
        shell_exec('gunzip '.$file_gun);
    }

    public function minotu_table(){

         $otu_talbe = FCPATH."otu_table_high_conf_summary.txt";
         $file = file_get_contents($otu_talbe);
         $search_for = 'Min';
         $pattern = preg_quote($search_for, '/');

            $pattern = "/^.*(Min).*\$/m";

                if (preg_match_all($pattern, $file, $matches)) {
                  
                     $value = $matches[0][0];
                     list($mane_min,$val_min) = explode(':', $value);
                     list($int,$double) = explode(".", $val_min);
                     echo str_replace(",","",$int);
                }
    }

    public function change_name(){
   
    $dir = FCPATH."owncloud/data/aumza/files/testsys/output/";
    $file_read = array('svg','sharedotus');
    $scan_result = scandir($dir);

    foreach ($scan_result as $key => $value) {

        if (!in_array($value, array('.', '..'))) {

                $type = explode('.', $value);
                $type = array_reverse($type);
      
                if (in_array($type[0], $file_read)) {

                    $file_name = preg_split("/[.]/", $value);
                  
                    if (in_array("svg", $file_name)) {

                        //rename($dir."/".$value,$dir."sharedsobs.svg");
                        echo $value."<br>";
                  
                    }
                    if (in_array("sharedotus", $file_name)) {

                        //rename($dir . "/" . $value,$dir."sharedsobs.sharedotus");
                        echo $value."<br>";
                    }
                
                }
            
        }
    }
}


     public function python(){

       echo shell_exec('python -c "import matplotlib; print(matplotlib.matplotlib_fname())"');
      
    }


    public function treport(){

        
        $this->load->library('myfpdf');
        $this->load->library('mytcpdf');
        $this->load->view('advance_report');
    }

    # Input ==> log makecontigs_barcode_primer.o1909
    public function tail(){
         $path = FCPATH."owncloud/data/aumza/files/testprimer/log";
         $file = $path."/aumza_fasttaOnly-IonProton_primer.o1903";
         exec("tail -n 1 ".$file , $output);
         list($line0) = $output;
        
             if(preg_match("/mothur > quit()/", $line0)){
                echo  "Match <br>";
             }else{
                echo  "No Match <br>";
             }
     
    }

   # Input ==> $user , $project
    public  function find_oligos(){

        $path = FCPATH."owncloud/data/aumza/files/testprimer/input/";
        $array_name = array();
        $path_dir = $path;
        if (is_dir($path_dir)){
            if ($read = opendir($path_dir)) {
                while (($file = readdir($read)) !== false) {
                    $allowed = array("fastq","fasta","tax","align","batch");
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
            if($check == true){
               return $value;
            }else{
                return "empty";
            }
        }
    }

    # Input ==> $platform_sam , $platform_type , $user , $project
    public function find_fastq_fasta(){

        $platform_sam = "miseq";
        $platform_type = "miseq_barcodes_primers";
        $path = FCPATH."owncloud/data/aumza/files/testprimer/input";

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
            $file_oligo = $this->find_oligos();
            if($platform_type == "miseq_without_barcodes"){


            }elseif ($platform_type == "miseq_contain_primer"){
                
                
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

                 list($R1,$R1name,$R2,$R2name) = explode(":", $k_fastq);
                 echo $R1name."<br>".$R2name."<br>".$file_oligo."<br>";
                 list($firstname) = explode(".fastq", $R1name);
                 echo $firstname;
                
            }

        }elseif ($platform_sam == "proton") {
            $file_oligo = $this->find_oligos();
            if($platform_type == "proton_barcodes_primers"){
                foreach($file_fastq as $value){
                      $name = end((explode('/',$value)));
                      list($fastq_get) = explode("fastq", $name);
                      $fastq_get = $fastq_get."fasta";
                      echo $fastq_get."<br>";
                }
            }elseif($platform_type == "proton_barcodes_fasta"){
                foreach ($file_fasta as $key => $value){
                $name_fasta = end((explode('/', $value)));
                    if(!in_array($name_fasta,$ref_fasta)){
                         echo $name_fasta."<br/>";
                    }   
                }
            }  
        }
            
        
    }

    public function keep_primer(){
        
        $path = FCPATH."Scripts/facialskin1modi.oligos";
        $file = file_get_contents($path);

        $cutadapt = array("cutadapt");

                $pattern = "/^.*(primer).*\$/m";
                if (preg_match_all($pattern, $file, $matches)) {

                    $val = implode("\n", $matches[0]);
                    $sum = explode("\n", $val);

                    foreach ($sum as $key => $value){
                         
                         $primer = explode("\t", $value);
                         $con_primer = shell_exec("python Scripts/revcomDNAseq.py ". $primer[1]);
                        
                         array_push($cutadapt,"-a  ".$con_primer);     
                    }
                }

        $cmd_cutadapt = implode(" ",$cutadapt);
        echo $cmd_cutadapt;

       
    }



    public function testread(){
       $path = FCPATH."owncloud/data/aumza/files/testrun/output/myResultsPathwayL2.tsv";

           echo "<table border='1'>";
           if(file_exists($path)){
               
                $myfile = fopen($path,'r') or die ("Unable to open file");
              
                   $row = 0;
                    while(($lines = fgets($myfile)) !== false){
                        $line = explode("\t", $lines);
                       if($row == 0){
                           echo "<tr>"; 
                           echo "<td>".$line[0]."</td>".
                                "<td>".$line[1]. "</td>".
                                "<td>".str_replace('1',$line[2] ,$line[6] ). "</td>".
                                "<td>".str_replace('2',$line[3] ,$line[7] ). "</td>".
                                "<td>".$line[9]. "</td>";
                           echo "</tr>";
                       }else{
                          echo "<tr>"; 
                          echo "<td>".$line[0]."</td>".
                               "<td>".$line[1]. "</td>".
                               "<td>".number_format(floatval($line[6]),3,'.',''). "</td>".
                               "<td>".number_format(floatval($line[7]),3,'.',''). "</td>".
                               "<td>".sprintf("%.3e",$line[9]). "</td>";
                          echo "</tr>";
                       }

                    $row++;

                    }
                    
                    
                fclose($myfile);  
          
           } 

           echo "</table>";

    }



    public function ex_string(){

        //$lable = explode('_', "gg_13_8_99.fasta");
        // echo $lable[0];

        $out = shell_exec('ls -la /usr/bin/python');
        print_r($out);

        $img_source = 'images/check.png';
        $img_code = base64_encode(file_get_contents($img_source));

        $src = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;

        echo '<img src="', $src, '"/>';


        // $this->load->view('test');


    }


    public function replace_group()
    {

        $file = "owncloud/data/admin/files/mothurphylotype/data/output/stability.contigs.groups";

        $data_w = array();

        $lines = file($file);

        foreach ($lines as $line) {

            $out = explode("\t", $line);

            $out[1] = str_replace("-", "_", $out[1]);

            $data = $out[0] . "\t" . $out[1];
            array_push($data_w, $data);

        }

        file_put_contents($file, $data_w);

    }


    public function show_log_last_line()
    {
        $file = FCPATH . "aumza-test_run-phylotype-advance3.o2457";
        $count = 0;
        $myfile = fopen($file, 'r') or die ("Unable to open file");
        while (($lines = fgets($myfile)) !== false) {
            if ($lines != "\n") {
                $count++;
                echo $count . " " . $lines . "<br/>";
            }

        }
        fclose($myfile);
        $line = file($file);

        echo $line[$count - 1];

    }


    public function fasta_read()
    {
        $file = FCPATH . "img/trainset16_022016.rdp.fasta";
        $check = "";
        $myfile = fopen($file, 'r') or die ("Unable to open file");
        while (($lines = fgets($myfile)) !== false) {

            $check = substr($lines, 0, 1);
            echo $check;
            break;
        }
        fclose($myfile);

        if ($check == '>') {
            echo "Fasta";
        } else {
            echo "No fasta";
        }

    }


    public function test()
    {

        $user = 'admin';
        $project = 'mothur_phylotype';
        $id_project = '5936621381b81380138b4567';
        $classifly = 'gg';

        $count = $this->mongo_db->where(array('id_project' => $id_project))->count('advance_classifly');

        if ($count == 0) {

            $data = array('user' => $user,
                'project_name' => $project,
                'id_project' => $id_project,
                'classifly' => $classifly);

            # insert data project
            $this->mongo_db->insert('advance_classifly', $data);

        } else {

            # update classifly
            $this->mongo_db->where(array('id_project' => $id_project))->set('classifly', $classifly)->update('advance_classifly');
        }


        $array_project = $this->mongo_db->get_where('advance_classifly', array('id_project' => $id_project));
        foreach ($array_project as $r) {

            $id = $r['_id'];
            $project = $r['project_name'];
            $classifly = $r['classifly'];
            echo "ID : " . $id . "<br/>" . "Project : " . $project . "<br/>" . "classifly : " . $classifly;
        }


        # Delete data project
        //$this->mongo_db->where(array("_id" => new MongoId('5938ca7081b8133f138b4568')))->delete('advance_classifly');


    }


    public function read_count()
    {

        $file = FCPATH . "owncloud/data/admin/files/data_mothur/data/output/final.opti_mcc.count.summary";
        $count = array();
        $data_read_count = array();
        $myfile = fopen($file, 'r') or die ("Unable to open file");
        while (($lines = fgets($myfile)) !== false) {

            $var = explode("\t", $lines);
            array_push($data_read_count, $var[0] . " : " . $var[1] . "<br/>");
            array_push($count, $var[1]);

        }
        fclose($myfile);
        $count_less = min($count);

        array_push($data_read_count, $count_less);

        for ($i = 0; $i < sizeof($data_read_count); $i++) {
            echo $data_read_count[$i] . "<br/>";

        }
        echo "final : " . end($data_read_count);

    }


    public function check_run()
    {

        $id_string = $_REQUEST['id_job'];
        $id_job = (int)$id_string;
        $check_run = exec("qstat -j $id_job");

        if ($check_run == false) {
            $message = "run queue complete";
            echo json_encode($message);
        } else {
            $message = "run queue " . $id_job;
            echo json_encode($message);
        }


    }


    public function read_log_sungrid()
    {

        $file = file_get_contents(FCPATH . 'admin_align_summary.o76');
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

            echo "Start : " . $start . "<br/>" . " End : " . $end;
        }

    }


    public function remove_logfile_mothur()
    {

        $project = "data_mothur";
        $user = "admin";

        $path_dir = FCPATH . "owncloud/data/$user/files/$project/output/";
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


    public function check_file($user, $project)
    {

        $path = "owncloud/data/$user/files/$project/data/input/stability.files";

        //$file_list = "fileList.paired.file"; 
        //$stability = "stability.files";

        $path_file = FCPATH . "$path";

        # stability.files ==> check oligos
        if (file_exists($path_file)) {

            $this->check_oligos($user, $project);
        } # create stability.files
        else {

            $out_var = $this->run_makefile($user, $project);

            if ($out_var == "0") {

                //rename($path.$file_list,$path.$stability);
                echo "Run makefile complete" . "<br/>";
                $this->check_file($user, $project);

            }

        }

    }


    public function check_oligos($user, $project)
    {

        $total_oligo = 0;

        $path_dir = FCPATH . "owncloud/data/$user/files/$project/data/input/";
        if (is_dir($path_dir)) {
            if ($read = opendir($path_dir)) {
                while (($file_oligo = readdir($read)) !== false) {

                    $allowed = array('oligo');
                    $ext = pathinfo($file_oligo, PATHINFO_EXTENSION);

                    if (in_array($ext, $allowed)) {

                        $total_oligo += 1;
                        echo "have ==> filename: " . $file_oligo . " is type oligos" . "<br/>";
                        $this->makecontigs_oligos_summary($file_oligo, $user, $project);
                    }

                }

                closedir($read);
            }
        }

        if ($total_oligo == 0) {

            $this->makecontig_summary($user, $project);

        }

    }


    # make.file  stability.files
    public function run_makefile($user, $project)
    {

        $jobname = $user . "_makefile";

        #make.file
        $make = "make.file(inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/data/input)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $make);


        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                return "0";
            }
        }

    }


    # make.contigs oligos remove primer && summary.seqs
    public function makecontigs_oligos_summary($file_oligo, $user, $project)
    {

        $jobname = $user . "_oligo";

        $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=4 ,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);


        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run makecontigs_oligos_summary complete";

            }
        }

    }


    # make.contigs && summary.seqs
    public function makecontig_summary($user, $project)
    {

        $jobname = $user . "_makesummary";

        $cmd = "make.contigs(file=stability.files,processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
             summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);

        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run makecontigs_summary complete" . "<br/>";

            }
        }


    }

    ///////////////////////////////////////////////////////////////////////////////////////  


    # screen.seqs && summary.seqs
    # input maximum ambiguous  , minimum reads length , maximum reads length  

    public function screen_summary($user, $project)
    {

        $jobname = $user . "_screen_summary";

        $cmd = "screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run screen_summary complete" . "<br/>";

            }
        }
    }

    #  unique.seqs && count.seqs && summary.seqs

    public function unique_count_summary($user, $project)
    {

        $jobname = $user . "_unique_count_summary";

        $cmd = " unique.seqs(fasta=stability.trim.contigs.good.fasta,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                     count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                     summary.seqs(count=stability.trim.contigs.good.count_table ,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run unique_count_summary complete" . "<br/>";

            }
        }
    }



    # align.seqs && summary.seqs
    # select alignment step
    public function align_summary($user, $project)
    {
        $jobname = $user . "_align_summary";

        $cmd = "align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=silva.v4.fasta, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                  summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run align_summary complete" . "<br/>";

            }
        }


    }




    #Start    #End 

    # screen.seqs = stat , end && summary.seqs
    #input maximum ambiguous , maximum homopolymer , maximum reads length
    public function screen_summary_2($user, $project)
    {
        $jobname = $user . "_screen_summary_2";

        $cmd = "screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=8, end=9582, maxambig=8, maxhomop=8, maxlength=260, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                  summary.seqs(fasta=current, count=current,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run screen_summary_2 complete" . "<br/>";

            }
        }

    }



    # filter.seqs && unique.seqs && pre.cluster && chimera.vsearch && remove.seqs && summary.seqs
    # input diffs => pre.cluster
    public function filter_unique_cluster_vsearch_remove_summary($user, $project)
    {
        $jobname = $user . "_filter_unique_cluster_vsearch_remove_summary";

        $cmd = "filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=2,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    chimera.vsearch(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.accnos,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    summary.seqs(fasta=current, count=current,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run filter_unique_cluster_vsearch_remove_summary complete" . "<br/>";

            }
        }


    }



    # Prepare in taxonmy   

    # classifly.seqs && remove.lineage && summary.seqs
    # input reference , taxonomy , cutoff
    public function classifly_removelineage_summary($user, $project)
    {

        $jobname = $user . "_classifly_removelineage_summary";
        $cmd = "classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=80, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                  remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                  summary.seqs(fasta=current, count=current,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run classifly_removelineage_summary complete" . "<br/>";

            }
        }

    }


    #  && summary.tax
    # input taxon 
    public function summary_tax($user, $project)
    {

        $jobname = $user . "_summary_tax";
        $cmd = "summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";


        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run summary_tax complete" . "<br/>";

            }
        }


    }



    # system_cp 

    // path file false stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta final.fasta
    public function system_cp($user, $project)
    {

        $jobname = $user . "_system_cp";
        $cmd = "system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta owncloud/data/$user/files/$project/output/final.fasta)
                    system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table owncloud/data/$user/files/$project/output/final.count_table)
                    system(cp owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy owncloud/data/$user/files/$project/output/final.taxonomy)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run system_cp complete" . "<br/>";

            }
        }


    }

    /////////////////////////////////////////////////////////////////////////////


    # Prepare phylotype analysis 

    # phylotype && make.shared && classify.out 

    public function phylotype_makeshared_classifyout($user, $project)
    {

        $jobname = $user . "_phylotype_makeshared_classifyout";

        $cmd = "phylotype(taxonomy=final.taxonomy,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                     make.shared(list=final.tx.list, count=final.count_table, label=1-2-3-4-5-6,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                     classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, label=1-2-3-4-5-6,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run phylotype_makeshared_classifyout complete" . "<br/>";

            }
        }
    }




    #classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, basis=sequence, output=simple, label=2) #get taxon


    # count.groups 
    public function count_gruops_shared($user, $project)
    {

        $jobname = $user . "_count_gruops_shared";

        $cmd = "count.groups(shared=final.tx.shared,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run count_gruops_shared complete" . "<br/>";

            }
        }
    }

    # sub.sample 
    #input size
    public function sub_smple($user, $project)
    {

        $jobname = $user . "_sub_smple";
        $cmd = "sub.sample(shared=final.tx.shared, size=5000,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run sub_smple complete" . "<br/>";

            }
        }

    }

    # Alpha and beta analysis based phylotype analysis

    # collect.single  && rarefaction.single && summary.single 

    public function collect_rarefaction_summary($user, $project)
    {

        $jobname = $user . "_collect_rarefaction_summary";

        $cmd = "collect.single(shared=final.tx.shared, calc=chao, freq=100,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    rarefaction.single(shared=final.tx.shared, calc=sobs, freq=100, processors=8,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                    summary.single(shared=final.tx.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon, subsample=5000,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run collect_rarefaction_summary complete" . "<br/>";

            }
        }

    }


    # dist.shared && summary.shared
    public function dist_shared($user, $project)
    {

        $jobname = $user . "_dist_shared";

        $cmd = "dist.shared(shared=final.tx.shared, calc=thetayc-jclass-lennon-morisitahorn-braycurtis, subsample=5000,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)
                      summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=soils1_1-soils2_1-soils3_1-soils4_1, all=T,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run dist_shared complete" . "<br/>";

            }
        }

    }


    # venn 
    public function venn($user, $project)
    {

        $jobname = $user . "_venn";

        $cmd = "venn(shared=final.tx.2.subsample.shared, groups=soils1_1-soils2_1-soils3_1-soils4_1,inputdir=owncloud/data/$user/files/$project/data/input,outputdir=owncloud/data/$user/files/$project/output)";

        file_put_contents('owncloud/data/' . $user . '/files/' . $project . '/data/input/run.batch', $cmd);
        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if ($check_run == false) {
                $loop = false;
                echo "Run venn complete" . "<br/>";

            }
        }


    }


}


?>