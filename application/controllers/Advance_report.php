<?php
defined('BASEPATH') OR exit("No direct script access allowed");


class Advance_report extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();

    }



    public function index()
    {
      
       
        $id_project =  $_REQUEST['projectid'];
 
        $data['rs'] = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));

        $project_analysis = null;
        $project_name = "";
        $project_platform_type = null;

        foreach ($data['rs'] as $result) {
            $project_analysis = $result['project_analysis'];
            $project_path = $result['project_path'];
            $project_name =  $result['project_name'];
            $project_platform_type = $result['project_platform_type'];

        }


        $projects_run_t = $this->mongo_db->get_where('projects_run', array('project_id' => $id_project));
         $data['projects_run_t'] = $projects_run_t;
      
 
         $method = "";
         foreach ($projects_run_t as $r) {         
                $method = $r['method']; 
         }



         $project_path = str_replace("../", "", $project_path);

         $user = $this->session->userdata['logged_in']['username'];
         $project_data = basename($project_path);


         $this->changsvgToppng($user,$project_data,$project_name);
         $this->copyBacth_RunLog($id_project,$user,$project_data,$project_name,$project_platform_type);
     


        if ($project_analysis == "phylotype") {

            //$this->database_text();
            $this->read_file_db_phylotype($project_path, $id_project,$method);
            echo json_encode($project_analysis);

        } else if ($project_analysis == "OTUs") {
            $this->read_file_db_otu($project_path, $id_project,$method);
             echo json_encode($project_analysis);
        }


    }

   public function view_report($id_project){

         $user = $this->session->userdata['logged_in']['username'];
         $data['user'] = $user;

         $projects_t = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
         $data['projects_t'] = $projects_t;


         $projects_run_t = $this->mongo_db->get_where('projects_run', array('project_id' => $id_project));
         $data['projects_run_t'] = $projects_run_t;
      
 
         $method = "";
         foreach ($projects_run_t as $r) {         
                $method = $r['method']; 
         }

  
         # check graph NMDS and PCoA
         if($method == "NMDS"){

             $data['thetayc1'] = "NMD_thetayc.png";
             $data['thetayc2'] = "NMDS_BiplotwithMetadata_thetayc.png";
             $data['thetayc3'] = "NMDS_BiplotwithOTU_thetayc.png";

         }else if($method == "PCoA"){
           
             $data['thetayc1'] = "PCoA_thetayc.png";
             $data['thetayc2'] = "PCoA_BiplotwithMetadata_thetayc.png";
             $data['thetayc3'] = "PCoA_BiplotwithOTU_thetayc.png";
         }
       
        # check run picrust 
        $project_data = "";
        $project_name = "";
        foreach ($projects_t as $r) {
                $project_data = basename($r['project_path']);
                $project_name = $r['project_name'];
         }



        $data_read_filealign_class = $this->read_filealign_class($user,$project_name);
          $data['alignment_name'] = trim($data_read_filealign_class[0]);
          $data['classifier_name'] = trim($data_read_filealign_class[1]);


        $read_option72 = file_get_contents('owncloud/data/'.$user.'/files/'.$project_data.'/option72.txt');
        list($option_use,$sample1,$sample2) = explode("\n",$read_option72);
        list($option,$use) = explode(":", $option_use);
        list($sa1,$sam1) = explode(":", $sample1);
        list($sa2,$sam2) = explode(":", $sample2);

        $data['use'] = $use;
        $data['sam1'] = $sam1;
        $data['sam2'] = $sam2;
        
        $data['tablelog'] = "data_report_mothur/".$user."/".$project_name."/table_log.txt";

        $data['dataphylum'] = $this->phylumn_count($user,$project_data,$project_name);
       
       $this->load->library('myfpdf');
       $this->load->library('mytcpdf');
       $this->load->view('advance_report', $data);

    }



    public function read_filealign_class($user,$project_name){
        
        // $alignment = "";
        // $classifier = "";

        $path = "owncloud/data/$user/files/$project_name/input/align_class.txt";
        $read = fopen($path,"r") or die ("Unable to open file");
        $data_array = array();
         
             while(($line = fgets($read)) !== false){
              
                  list($name,$value) = explode(":", $line);
                  array_push($data_array, $value);
              
             }
        fclose($read);

        return $data_array;
      
    }




    public function changsvgToppng($user,$project_data,$project_name){

        $sharedsobs_svg = FCPATH."data_report_mothur/$user/$project_name/beta_diversity_analysis/sharedsobs.svg";  

        $path_dir = FCPATH."owncloud/data/$user/files/$project_data/output/sharedsobs.svg";
        if(file_exists($path_dir)){
               copy($path_dir,$sharedsobs_svg);
        }

     

    # create folder download Graph .SVG
    $folder_download1 = "data_report_mothur/$user/$project_name/Download/alpha_diversity_analysis/";
        if (!file_exists($folder_download1)){
            mkdir($folder_download1, 0777, true);
        }

     $folder_download2 = "data_report_mothur/$user/$project_name/Download/beta_diversity_analysis/";
        if (!file_exists($folder_download2)){
            mkdir($folder_download2, 0777, true);
        }

    $folder_download3 = "data_report_mothur/$user/$project_name/Download/optional_output/";
        if (!file_exists($folder_download3)){
            mkdir($folder_download3, 0777, true);
        }

    $folder_download4 = "data_report_mothur/$user/$project_name/Download/taxonomy_classification/";
        if (!file_exists($folder_download4)){
            mkdir($folder_download4, 0777, true);
        }


            $path1 = "data_report_mothur/$user/$project_name/alpha_diversity_analysis/";
            $path_dir = $path1;
            if (is_dir($path_dir)) {
                if($read = opendir($path_dir)){
                    while (($img_svg = readdir($read)) !== false) {
                        $allowed =  array('svg');
                        $ext = pathinfo($img_svg, PATHINFO_EXTENSION);
                        if(in_array($ext,$allowed)){

                            $name_png = pathinfo($img_svg, PATHINFO_FILENAME);
                             $path_png = $path1.$name_png.".png";
                             $path_svg = $path1.$img_svg;

                             $cmd = 'inkscape -z  '.$path_svg.' -e '.$path_png;
                             exec($cmd); 
                             $path_downlod_svg1 = $folder_download1.$img_svg;
                             copy($path_svg,$path_downlod_svg1);
                             unlink($path_svg); 
                         }
                    }
                   closedir($read);
                }
            } 

            $path2 = "data_report_mothur/$user/$project_name/beta_diversity_analysis/";
            $path_dir = $path2;
            if (is_dir($path_dir)) {
                if($read = opendir($path_dir)){
                    while (($img_svg = readdir($read)) !== false) {
                        $allowed =  array('svg');
                        $ext = pathinfo($img_svg, PATHINFO_EXTENSION);
                        if(in_array($ext,$allowed)){

                            $name_png = pathinfo($img_svg, PATHINFO_FILENAME);
                            $path_png = $path2.$name_png.".png";
                            $path_svg = $path2.$img_svg; 

                             $cmd = 'inkscape -z '.$path_svg.' -e  '.$path_png;
                             exec($cmd); 
                             $path_downlod_svg2 = $folder_download2.$img_svg;
                             copy($path_svg,$path_downlod_svg2);
                             unlink($path_svg); 
                         }
                    }
                   closedir($read);
                }
            } 


            $path3 = "data_report_mothur/$user/$project_name/optional_output/";
            $path_dir = $path3;
            if (is_dir($path_dir)) {
                if($read = opendir($path_dir)){
                    while (($img_svg = readdir($read)) !== false) {
                        $allowed =  array('svg');
                        $ext = pathinfo($img_svg, PATHINFO_EXTENSION);
                        if(in_array($ext,$allowed)){

                            $name_png = pathinfo($img_svg, PATHINFO_FILENAME);
                            $path_png = $path3.$name_png.".png";
                            $path_svg = $path3.$img_svg;

                             $cmd = 'inkscape -z '.$path_svg.' -e  '.$path_png;
                             exec($cmd); 
                             $path_downlod_svg3 = $folder_download3.$img_svg;
                             copy($path_svg,$path_downlod_svg3);
                             unlink($path_svg); 
                         }
                    }
                   closedir($read);
                }
            } 


            $path4 = "data_report_mothur/$user/$project_name/taxonomy_classification/";
            $path_dir = $path4;
            if (is_dir($path_dir)) {
                if($read = opendir($path_dir)){
                    while (($img_svg = readdir($read)) !== false) {
                        $allowed =  array('svg');
                        $ext = pathinfo($img_svg, PATHINFO_EXTENSION);
                        if(in_array($ext,$allowed)){

                            $name_png = pathinfo($img_svg, PATHINFO_FILENAME);
                            $path_png = $path4.$name_png.".png";
                            $path_svg = $path4.$img_svg; 

                             $cmd = 'inkscape -z '.$path_svg.' -e  '.$path_png;
                             exec($cmd); 
                             $path_downlod_svg4 = $folder_download4.$img_svg;
                             copy($path_svg,$path_downlod_svg4);
                             unlink($path_svg); 
                         }
                    }
                   closedir($read);
                }
            } 



     }


    public function copyBacth_RunLog($id_project,$user,$project,$project_name,$project_platform_type){

        # copy file_phylum_count.txt to data_report_mothur
        $path_phylum_count = "owncloud/data/$user/files/$project/output/file_phylum_count.txt";
        if(file_exists($path_phylum_count)){
                $phylum_count = "data_report_mothur/$user/$project_name/file_phylum_count.txt";
                copy($path_phylum_count,$phylum_count);
        }

       

       if($project_platform_type == "proton_without"){

             $this->tablelog_fasta($user,$project_name);  

       }else{

         # copy log sungride makesummary to Log_report
         $MakeSummary_file = null;
         $path_summary = "owncloud/data/$user/files/$project/log/";
         $file_log = FCPATH.$path_summary;
            if(is_dir($file_log)) {
                if($read = opendir($file_log)){
                       while (($summary = readdir($read)) !== false) {

                         # makesummary
                         if(stripos($summary,'makesummary') !== false){
                             
                             $MakeSummary_file = $summary;
                           
                         }
                      }
                    closedir($read);
                }
            }
         
        $this->table_log_create($id_project,$user,$project,$MakeSummary_file,$project_name);
       }
       
    }


    # fasta input
    public function tablelog_fasta($user,$project_name){
              
         $log_data = array();

         $log_data[0][0] =  "Samples name";
         $log_data[0][1] =  "No. of reads in rawdata";
         $log_data[0][2] =  "No. of reads after screen.seqs*";
         $log_data[0][3] =  "No. of reads after removing chimera";
         $log_data[0][4] =  "No. of cleaned reads for phylotype/OTUs step";

         $count = 1;
         $file_log = "owncloud/data/$user/files/$project_name/output/stability.contigs.count.summary"; 

         $read_log = fopen($file_log,"r") or die ("Unable to open file");
         while(($line_log = fgets($read_log)) !== false){
               
             $data_log = explode("\t", $line_log);
             $log_data[$count][1] =  trim($data_log[0]);
             $log_data[$count][2] =  trim($data_log[1]);
             $count++;  
        }
        fclose($read_log);


           #colum 3
        $count1 = 1;
        $path = FCPATH."Log_report/$user/$project_name/stability.trim.contigs.good.good.count.summary";
        $read = fopen($path,"r") or die ("Unable to open file");
        while(($line = fgets($read)) !== false){
               
             $data = explode("\t", $line);
             $log_data[$count1][3] =  trim($data[1]);
             $count1++;  
        }
        fclose($read);


         #colum 4
        $count2 = 1;
        $path = FCPATH."Log_report/$user/$project_name/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count.summary";
        $read = fopen($path,"r") or die ("Unable to open file");
        while(($line = fgets($read)) !== false){
               
             $data = explode("\t", $line);
             $log_data[$count2][4] =  trim($data[1]);
             $count2++;  
        }
        fclose($read);


        #colum 5
        $count3 = 1;
        $path = FCPATH."Log_report/$user/$project_name/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count.summary";
        $read = fopen($path,"r") or die ("Unable to open file");
        while(($line = fgets($read)) !== false){
               
             $data = explode("\t", $line);
             $log_data[$count3][5] =  trim($data[1]);
             $count3++;  
        }
        fclose($read);

        $table_data = array();
        foreach ($log_data as $variable) {
            $colum = 0;
            foreach ($variable as  $value) {
               if($colum <= 3){
                    $log = $value."\t";
                    array_push($table_data,$log);
               }else{
                    $log = $value."\n";
                    array_push($table_data,$log);
               }
               $colum++;
              
              
            } 
        }
       
         
        $path_log = "data_report_mothur/$user/$project_name/table_log.txt";
        $file_log = FCPATH.$path_log;
        file_put_contents($file_log,$table_data); 

    }



    public function table_log_create($id_project,$user,$project,$makesummary,$project_name){


         $projects_data = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
         foreach ($projects_data as $r) {
                  $numsample = $r['project_num_sam'];
         }

         $sample =  $numsample/2;


        $log_data = array();

        # colum Header
        $log_data[0][0] =  "Samples name";
        $log_data[0][1] =  "No. of reads in rawdata after make contigs";
        $log_data[0][2] =  "No. of reads after screen.seqs*";
        $log_data[0][3] =  "No. of reads after removing chimera";
        $log_data[0][4] =  "No. of cleaned reads for phylotype/OTUs step";


        #colum 1 && 2
        $count = 1;
        $file_log = FCPATH."Log_report/$user/$project_name/$makesummary"; 
        $cmd = "/bin/grep  -A ".$sample." 'Group count:' ".$file_log;
        exec($cmd,$output);
        foreach ($output as $key => $value) {
            if($key > 0){
                 $data = explode("\t", $value);
                 $log_data[$count][1] =  trim($data[0]);
                 $log_data[$count][2] =  trim($data[1]);
                 $count++;
            }
        }


         #colum 3
        $count1 = 1;
        $path = FCPATH."Log_report/$user/$project_name/stability.trim.contigs.good.good.count.summary";
        $read = fopen($path,"r") or die ("Unable to open file");
        while(($line = fgets($read)) !== false){
               
             $data = explode("\t", $line);
             $log_data[$count1][3] =  trim($data[1]);
             $count1++;  
        }
        fclose($read);


         #colum 4
        $count2 = 1;
        $path = FCPATH."Log_report/$user/$project_name/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count.summary";
        $read = fopen($path,"r") or die ("Unable to open file");
        while(($line = fgets($read)) !== false){
               
             $data = explode("\t", $line);
             $log_data[$count2][4] =  trim($data[1]);
             $count2++;  
        }
        fclose($read);


        #colum 5
        $count3 = 1;
        $path = FCPATH."Log_report/$user/$project_name/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count.summary";
        $read = fopen($path,"r") or die ("Unable to open file");
        while(($line = fgets($read)) !== false){
               
             $data = explode("\t", $line);
             $log_data[$count3][5] =  trim($data[1]);
             $count3++;  
        }
        fclose($read);

        $table_data = array();
        foreach ($log_data as $variable) {
            $colum = 0;
            foreach ($variable as  $value) {
               if($colum <= 3){
                    $log = $value."\t";
                    array_push($table_data,$log);
               }else{
                    $log = $value."\n";
                    array_push($table_data,$log);
               }
               $colum++;
              
              
            } 
        }
       
         
        $path_log = "data_report_mothur/$user/$project_name/table_log.txt";
        $file_log = FCPATH.$path_log;
        file_put_contents($file_log,$table_data); 

    }

   

    public function phylumn_count($user,$project_data,$project_name){
        
        # read file convert to array
        $data_keep = array();
        $path = FCPATH."data_report_mothur/$user/$project_name/file_phylum_count.txt";
        $read = fopen($path,"r") or die ("Unable to open file");
        
        $data_lenght = 0;
        $line_count = 0;
        while(($line = fgets($read)) !== false){
            $data_arr = explode("\t",$line);
            $data_lenght = sizeof($data_arr);
            foreach ($data_arr as $key => $value) {
                     $data_keep[$line_count][$key] = $value; 
            }
            $line_count++;  
        }
        fclose($read);

        # search phylum max values
        $data_set = array();
        $indx =  $data_lenght-1;
        for($i = 1;$i <= $indx;$i++){
            
            $var_push = array();
            $line_count = 0;
            foreach ($data_keep as $key => $value) {
             #print_r($value);
               if($line_count > 0){
                   array_push($var_push,trim($value[$i]));
                }
                $line_count++;
            }

             $var_max = max($var_push);
             $var_max_index =  array_search($var_max, $var_push);
             $var_max_index = $var_max_index+1;

             // echo $data_keep[0][$i]." "
             //        .$data_keep[$var_max_index][0]." "
             //        .$var_max."<br/>";
            $data_set[$data_keep[$var_max_index][0]][] = $data_keep[0][$i];
           
         }

         # keep count value
          
         $arr_num = array();
         foreach ($data_set as $key => $value) {
             array_push($arr_num,count($value));
          }

        # array keep value
        $phylum_most = array();
        $sample_most = array();

        $phylum_while = array();
        $sample_while = array();

        
        # loop keep data most && while
        $loop_index = 0;
        for($k=0;$k < count($data_set);$k++){
          

          $arr_num_max = max($arr_num);
          $arr_num_max_index = array_search($arr_num_max,$arr_num);
          unset($arr_num[$arr_num_max_index]);
         
            # check index max
            $line_arr_num_max = 0;

            foreach ($data_set as $key => $value) {

                # check index max
                if($arr_num_max_index == $line_arr_num_max){

                    #  phylum_most
                    if($loop_index == 0){
                        array_push($phylum_most,$key);
                        for($i=0;$i < count($value);$i++){

                           array_push($sample_most,$value[$i]);
                        }
                    }
                    
                    # phylum_while
                    else{

                        array_push($phylum_while,$key);
                        for($i=0;$i < count($value);$i++){

                           array_push($sample_while,$value[$i]);
                        }
                    }
                    $loop_index++;   
                }
                $line_arr_num_max++;
            }

         }

        # output data to report

        #echo $phylum_most[0];
        #echo "<br>";

        $sample1_phylum = "";
        # loop $sample_most
        for($i=0;$i< count($sample_most);$i++){
            if($i == (count($sample_most)-1) ){
                $sample1_phylum  .= " and ".$sample_most[$i];
            }else if($i == (count($sample_most)-2) ){
                $sample1_phylum  .= $sample_most[$i];
            }
            else{
                $sample1_phylum  .= $sample_most[$i]." , ";
            }   
        }

        #echo $sample1_phylum; 
        #echo "<br>";


        $phylum_while2 = "";
        # loop $phylum_while
        if(count($phylum_while) == 1){
             $phylum_while2 .= $phylum_while[0];
        }else{
            for($i=0;$i< count($phylum_while);$i++){
                if($i == (count($phylum_while)-1) ){
                     $phylum_while2 .=  " and ".$phylum_while[$i];
                }else if($i == (count($phylum_while)-2) ){
                     $phylum_while2 .= $phylum_while[$i];
                }
                else{
                    $phylum_while2 .= $phylum_while[$i]." , ";
                }   
            }

        }
        // echo $phylum_while2; 
        //echo "<br>";

        $sample2_phylum = "";
        # loop $sample_while
        if(count($sample_while) == 1){
             $sample2_phylum .= $sample_while[0];
        }else{
            for($i=0;$i< count($sample_while);$i++){
                 if($i == (count($sample_while)-1) ){
                     $sample2_phylum .= " and ".$sample_while[$i];
                }else if($i == (count($sample_while)-2) ){
                    $sample2_phylum .= $sample_while[$i];
                }
                else{
                     $sample2_phylum .= $sample_while[$i]." , ";
                }   
            }
        }
        // echo $sample2_phylum;

        $data_phylum_sample2 = "";
        if(count($phylum_while) != 0){
             $data_phylum_sample2 = ",while for dataset taxonomy of ".$sample2_phylum." the most common phylum found was ".$phylum_while2." respectively";
        }
        #echo $data_phylum_sample2;
        return array($phylum_most[0],$sample1_phylum,$data_phylum_sample2);
    }
  


    // public function database_text(){

    //   $path = FCPATH."owncloud/data/aumza/files/otusrun/log/";
    //   $log_make = $path."aumza_makesummary.o778";
    //   $log_classify = $path."aumza_classifly_removelineage_summary.o784";
    //   $log_phylotype = $path."aumza_classify_count.o788";

      
    //   #Log makesummary
    //   $file = file_get_contents($log_make);
    //   $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum|Mean).*\$/m";
        
    //     if(preg_match_all($pattern, $file, $matches)) {
    //             $val = implode("\n", $matches[0]);
    //             $sum = explode("\n", $val);
    //             $index = 0;
    //         foreach ($sum as $key => $value) {

    //              if ($index == 7) {
    //                     $avg = preg_split('/\s+/', $value);
    //                     echo "count_seqs : " .$avg[6] . '<br>';
    //             // file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "count_seqs:" . $avg[6] . "\n", FILE_APPEND);
    //              }
    //             if ($index == 8) {
    //                     $sum_seqs = preg_split('/\s+/', $value);
    //                     echo "avg_length : ".$sum_seqs[3]."<br>";
    //                  // file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "avg_length:" . $sum_seqs[3] . "\n", FILE_APPEND);
    //             }
    //              $index++;
    //         }
    //      }


    //    #Log classify
    //    $file = file_get_contents($log_classify);
    //    $pattern = "/^.*(Start|Minimum|2.5%-tile|25%-tile|Median|75%-tile|97.5%-tile|Maximum|Mean|total).*\$/m";
    //     if(preg_match_all($pattern, $file, $matches)) {
    //            $val = implode("\n", $matches[0]);
    //            $sum = explode("\n", $val);
    //            $index = 0;
    //            foreach ($sum as $key => $value){
    //                if ($index == 8) {
    //                      $avg = preg_split('/\s+/', $value);
    //                       echo   "num_seqs : " . $avg[2] ."<br>";
    //                  // file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "num_seqs:" . $avg[2] . "\n", FILE_APPEND);
    //                }
    //                if ($index == 9){
    //                      $sum_seqs = preg_split('/\s+/', $value);
    //                      echo "avg_reads : " . $sum_seqs[4] . "<br>";
    //                  // file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "avg_reads:" . $sum_seqs[4] . "\n", FILE_APPEND);
    //                 }
    //              $index++;
    //            }
    //     }


    //  #Log_phylotype
    //   $file = file_get_contents($log_phylotype);
    //   $searchfor = 'contains';
    //   $pattern = preg_quote($searchfor, '/');

    //   $pattern = "/^.*$pattern.*\$/m";
    //     if (preg_match_all($pattern, $file, $matches)) {
    //         $i = 0;
    //         $t = array();
    //         foreach ($matches[0] as $ma){
    //             if ($ma != null) {
    //                 $size = explode(" ", $ma);
    //                 $to = explode(".", $size[2]);
    //                 $t[$i] = $to[0];
    //                 $i++;
    //             }
    //         }

    //         $size = min($t);
    //         echo "lib_size : " . $size . "<br>";
    //        // file_put_contents("owncloud/data/$user/files/$project/output/database.txt", "lib_size:" . $size . "\n", FILE_APPEND);
    //     } 


    // }



   public function read_file_db_phylotype($path_owncloud, $id_project ,$method){


        // $path_file_phylotype = array("final.tx.summary", "file_after_reverse.csv", "file_phylum_count.txt", "final.tx.2.subsample.shared",
        //     "final.tx.thetayc.2.lt.ave.nmds.axes", "final.tx.groups.summary", "final.tx.groups.rarefaction");

        $path_owncloud_phylotype = $path_owncloud . "/output/";

        # final.tx.groups.ave-std.summary
        $chao_shanon = file_get_contents($path_owncloud_phylotype . "final.tx.groups.ave-std.summary");
        $row = explode("\n", $chao_shanon);
        array_shift($row);
        $arr_out = array();
        $arr_chao = array();
        $arr_shanon = array();
        $index = 0;
        $table_alpha = array();
        foreach ($row as $value => $data){
            if($data != null) {
                $row_data = preg_split("/\s+/", $data);
                if ($row_data[0] == 2 and $row_data[2] == "ave") {
                    $arr_out[$index] = $row_data[5];

                    $arr_chao[$row_data[1]] = $row_data[9];
                    $arr_shanon[$row_data[1]] = $row_data[12];

                    $table_alpha[$index] = $row_data[1] . ":" . $row_data[4] . ":" . $row_data[5] . ":" . $row_data[9] . ":" . $row_data[12];
                    $index++;
                }
            }
        }

        $min_otu = min($arr_out);
        $max_otu = max($arr_out);
        $min_chao = min($arr_chao);
        $max_chao = max($arr_chao);
        $min_shanon = min($arr_shanon);
        $max_shanon = max($arr_shanon);
        $t_range_otu = min($arr_out) . "-" . max($arr_out);

        $samplename_min_chao =  array_search($min_chao,$arr_chao);
        $samplename_max_chao =  array_search($max_chao,$arr_chao);
        $samplename_min_shanon = array_search($min_shanon,$arr_shanon);
        $samplename_max_shanon = array_search($max_shanon,$arr_shanon);


        # final.tx.groups.rarefaction
        $rarefaction = file_get_contents($path_owncloud_phylotype . "final.tx.groups.rarefaction");
        $rare_index = array();
        $name_sample = array();
        $save_value = array();     
        $row = explode("\n", $rarefaction);
        $check_index = preg_split('/\s+/', $row[0]);
        for ($i = 0; $i < count($check_index); $i++) {
            $save_index = explode('-', $check_index[$i]);
            if ($save_index[0] == "2") {
                array_push($rare_index, $i);
                array_push($name_sample, $save_index[1]);
            }
        }
        array_shift($row);
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data_rare = preg_split('/\s+/', $data);
                for ($i = 0; $i < count($rare_index); $i++) {
                    if (is_numeric($row_data_rare[$rare_index[$i]])) {
                        if (isset($save_value[$i])) {
                        } else {
                            $save_value[$i] = "0";
                        }
                        if ($save_value[$i] < $row_data_rare[$rare_index[$i]]) {
                            $save_value[$i] = $row_data_rare[$rare_index[$i]];
                        } else {

                        }
                    } else {
                        if ($save_value[$i] == null) {
                            $save_value[$i] = "0";
                        } else {

                        }
                    }
                }
            }
        }
        $index_hi = null;
        $num_hi = 0;
        for ($i = 0; $i < count($save_value); $i++) {
            if ($num_hi < $save_value[$i]) {
                $num_hi = $save_value[$i];
                $index_hi = $i;
            }
        }
        $sample_height = $name_sample[$index_hi];
        $index_low = null;
        $num_low = 0;
        for ($i = 0; $i < count($save_value); $i++) {
            if ($i == 0) {
                $num_low = $save_value[$i];
                $index_low = $i;
            } else {
                if ($num_low > $save_value[$i]) {
                    $num_low = $save_value[$i];
                    $index_low = $i;

                }
            }
        }
        $sample_low = $name_sample[$index_low];




        # database.txt
        $read_logs = file_get_contents($path_owncloud_phylotype . "database.txt");
        $row = explode("\n", $read_logs);
        $index = 0;
        $count_seqs = null;
        $avg_lenght = null;
        $num_seqs = null;
        $avg_reads = null;
        $lib_size = null;
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/:/", $data);
                    if ("count_seqs" == $row_data[0]) {
                        $count_seqs = $row_data[1];
                    } else if ("avg_length" == $row_data[0]) {
                        $avg_lenght = $row_data[1];
                    } else if ("num_seqs" == $row_data[0]) {
                        $num_seqs = $row_data[1];
                    } else if ("avg_reads" == $row_data[0]) {
                        $avg_reads = $row_data[1];
                    } else if ("lib_size" == $row_data[0]) {
                        $lib_size = $row_data[1];
                    }
                $index++;
            }
        }
        // $count_seqs = "160000";
        // $avg_lenght = "263.05";
        // $num_seqs = "525";
        // $avg_reads = "43599";
        // $lib_size = "10349";
    


//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 1-2<<<<<<<<<<<<<<<<<br>";
//        echo "Bigsam_rare :". $sample_height.'<br>';


        # file_phylum_count.txt
        $phylumn_count = file_get_contents($path_owncloud_phylotype . "file_phylum_count.txt");
        $sample_big_rare = $sample_height;
        $name_sample_phylumn = array();
        $value_phylumn = [];
        $save_value_phylumn = array();
        $total_num = null;
        $row = explode("\n", $phylumn_count);
        $check_index = preg_split('/\s+/', $row[0]);
        array_shift($row);
        $half_sam = count($check_index) / 2;
        for ($i = 0; $i < count($check_index); $i++) {
            $name_sample_phylumn[$i] = $check_index[$i];
        }
        $index = 0;
        foreach ($row as $value => $data) {
            $row_data_phy = preg_split('/\s+/', $data);
            if ($data != null) {
                $total_num = null;
                for ($i = 0; $i < count($name_sample_phylumn); $i++) {
                    $value_phylumn[$index][$i] = $row_data_phy[$i];
                    $total_num += $row_data_phy[$i];
                }
                $save_value_phylumn[$index] = $total_num;
            }
            $index++;
        }

        $max = 0;
        $k = 0;
        $more = 0;
        $name_phylumn = null;
        foreach ($save_value_phylumn as $key => $value) {

            if ($value >= $max) {
                $max = max($save_value_phylumn);
                $k = $key;
            }
        }
        for ($n = 0; $n < count($name_sample_phylumn); $n++) {
            if ($n == 0) {
                $name_phylumn = $value_phylumn[$k][$n];
                $sample_big_phy = $name_sample_phylumn[$n];
            }
            if ($value_phylumn[$k][$n] > $more) {
                $more = $value_phylumn[$k][$n];
                $sample_big_phy = $name_sample_phylumn[$n];
            }
        }



//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 3<<<<<<<<<<<<<<<<<br>";

        #file_after_reverse.csv 
        $genus = file_get_contents($path_owncloud_phylotype . "file_after_reverse.csv");
        $row = explode("\n", $genus);
        $row2 = explode("\n", $genus);
        $row_name = explode("\n", $genus);
        array_shift($row);
        array_shift($row2);
        $count_genus = array();
        $count_genus2 = array();
        $num = 0;
        $key_sam = null;
        $genus_pack = array();
        $max_for_sam = array();
        $index_genus = 0;
        $name_sample_num_ven = array();
        
       
        foreach ($row as $value => $data) {
            if ($data != null) {
                $split = preg_split('/,/', $data);
                if ($count_genus == null) {
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] = $split[$j];
                        $count_genus2[$j] = $split[$j];
                    }
                } else {
                    for ($j = 1; $j < count($split); $j++) {
                        $count_genus[$j] += $split[$j];
                        $count_genus2[$j] = $split[$j];
                    }
                }
                $num = 0;
                $key_index = null;

                for ($k = 1; $k < count($count_genus2); $k++) {
                    if ($num < $count_genus2[$k]) {
                        $num = $count_genus2[$k];
                        $key_index = $k;
                    }
                }

                $total = 0;
                foreach ($row2 as $value_e => $data_a) {
                    if ($data_a != null) {
                        $split1 = preg_split('/,/', $data_a);
                        $total += $split1[$key_index];
                    }
                }

                $splitn = preg_split('/,/', $row_name[0]);
                $genus_name = $splitn[$key_index];

                $maxximun = max($count_genus2);
                $percent = $maxximun * 100 / $total;


                $genus_pack[$index_genus] = $split[0] . ":" . $maxximun . ":" . $key_index . ":" . $total . ":" . round($percent, 2) . "%" . ":" . $genus_name;

                $index_genus++;
            }
        }
        $abun_genus = $genus_pack;


        #sharedsobs.svg
        $svg_file = file_get_contents($path_owncloud_phylotype . "sharedsobs.svg");
        $find_string = '<text';
        $position = strpos($svg_file, $find_string);

        $svg_file_new = substr($svg_file, $position);
        $searchfor = 'The number of species in group';
        $searchfor2 = 'The total richness of all';
        $pattern = preg_quote($searchfor, '/');
        $pattern = "/^.*$pattern.*\$/m";
        if (preg_match_all($pattern, $svg_file, $matches)) {
            $index_ven = 0;
            foreach ($matches[0] as $value => $data_otu) {
                $name_sam_otu = preg_split('/\s+/', $data_otu);
                $repalce_str = str_replace('</text>', '', $name_sam_otu[13]);
                $name_sample_num_ven[$index_ven] = $name_sam_otu[11] . " : " . $repalce_str;
                $index_ven++;
            }
        }else {
            echo "No matches found";
        }



        #sharedsobs.sharedotus
        $svg_file2 = file_get_contents($path_owncloud_phylotype . "sharedsobs.sharedotus");
        $row_otu = explode("\n", $svg_file2);
        foreach ($row_otu as $value => $data) {
            if ($data != null) {
                $split_row_otu = preg_split('/\s+/', $data);
                $num_outs = $split_row_otu[1];
            }
        }
        $num_otu = $num_outs;


//       echo ">>>>>>>>>>>>>>>>>>>>>End Page 4 <<<<<<<<<<<<<<<<<br>";
         if($method == "NMDS"){

                #final.tx.thetayc.2.lt.ave.nmds.axes
                $near_data = file_get_contents($path_owncloud_phylotype . "final.tx.thetayc.2.lt.ave.nmds.axes");
        }else{

             #final.tx.thetayc.2.lt.ave.pcoa.axes
             $near_data = file_get_contents($path_owncloud_phylotype . "final.tx.thetayc.2.lt.ave.pcoa.axes");
        }
        

      
        $axes1 = array();
        $axes2 = array();
        $row = explode("\n", $near_data);
        array_shift($row);
        $index = 0;
        foreach ($row as $value => $data) {
            if ($data != null) {
                $split = preg_split('/\s+/', $data);
                $axes1[$index] = $split[1];
                $axes2[$index] = $split[2];
                $index++;
            }
        }
        $sam1 = null;
        $sam2 = null;
        $save_near_county = 0;
        for ($i = 0; $i < count($axes2); $i++) {
            for ($j = 0; $j < count($axes2); $j++) {
                if ($i != $j) {
                    if ($save_near_county == null) {
                        $save_near_county = sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2));
                        $sam1 = $i;
                        $sam2 = $j;
                    } else if ($save_near_county > sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2))) {
                        $save_near_county = sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2));

                        $sam1 = $i;
                        $sam2 = $j;
                    } 

                }
            }
        }
        $near_sam = "";
        $index = 0;
        foreach ($row as $value => $data) {
            if ($data != null) {
                $split = preg_split('/\s+/', $data);
                if ($index == $sam1) {
                    $near_sam1 = $split[0];
                }
                if ($index == $sam2) {
                    $near_sam2 = $split[0];
                }
                $index++;
            }
        }
        $near_sam = $near_sam1 . ":" . $near_sam2;



        #final.tx.summary
        $table_stat = file_get_contents($path_owncloud_phylotype . "final.tx.summary");
        $row = explode("\n", $table_stat);
        array_shift($row);
        $arr_sam1 = array();
        $arr_sam2 = array();
        $arr_lennon = array();
        $arr_jclass = array();
        $arr_morisitahorn = array();
        $arr_sorabund = array();
        $arr_theten = array();
        $arr_thetayc = array();
        $arr_braycurtis = array();
        $index = 0;
        $table_stat = array();
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);
                if ($row_data[0] == 2) {
                    $arr_sam1[$index] = $row_data[1];
                    $arr_sam2[$index] = $row_data[2];
                    $arr_lennon[$index] = $row_data[3];
                    $arr_jclass[$index] = $row_data[4];
                    $arr_morisitahorn[$index] = $row_data[5];
                    $arr_sorabund[$index] = $row_data[6];
                    $arr_theten[$index] = $row_data[7];
                    $arr_thetayc[$index] = $row_data[8];
                    $arr_braycurtis[$index] = $row_data[9];
                    $table_stat[$index] = $row_data[1] . ":" . $row_data[2] . ":" . $row_data[3] . ":" . $row_data[4] . ":" . $row_data[5] . ":" . $row_data[6] . ":" . $row_data[7] . ":" . $row_data[8] . ":" . $row_data[9];
                    $index++;
                }
            }
        }



        #final.tx.thetayc.2.lt.ave.amova
        $amova = file_get_contents($path_owncloud_phylotype . "final.tx.thetayc.2.lt.ave.amova");
        $row = explode("\n", $amova);
        $index = 0;
        $amova = array();
        $name_vs_sam = array();
        $p_value = array();
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);

                if ($index == 0) {
                    $name_vs_sam = array_push($name_vs_sam, $row_data[0]);

                } else if ($index % 7 == 0) {
                    if ($name_vs_sam == null) {
                        $name_vs_sam = $row_data[0];
                    } else {
                        $name_vs_sam = $name_vs_sam . ":" . $row_data[0];
                    }   
                }
                if($row_data[0] == "p-value:") {
                    if ($p_value == null) {
                        $p_value = $row_data[1];
                    } else {
                        $p_value = $p_value . ":" . $row_data[1];
                    }
                   
                }

            }
            $index++;
        }



        #final.tx.thetayc.2.lt.ave.homova
        $homova = file_get_contents($path_owncloud_phylotype . "final.tx.thetayc.2.lt.ave.homova");
        $row = explode("\n", $homova);
        $index = 0;
        $name_vs_sam_homo = null;
        $p_value_homo = null;
        $split_homova = preg_split('/\s+/', $row[1]);
        $name_vs_sam_homo = $split_homova[0];
        $p_value_homo = $split_homova[2];


//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 5 <<<<<<<<<<<<<<<<<br>";
//        echo "In db";
//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 6 <<<<<<<<<<<<<<<<<br>";
//        echo "In db";
//        echo ">>>>>>>>>>>>>>>>>>>>>End Page 7 <<<<<<<<<<<<<<<<<br>";



        $data = array(
            "avg_read_pre" => "",
            "num_seqs" => "",
            "t_range_otu" => $t_range_otu,
            "lib_size" => $lib_size,
            "min_otu" => $min_otu,
            "max_otu" => $max_otu,
            "max_chao" => $max_chao,
            "min_chao" => $min_chao,
            "max_shanon" => $max_shanon,
            "min_shanon" => $min_shanon,
            "samplename_min_chao" => $samplename_min_chao,
            "samplename_max_chao" => $samplename_max_chao,
            "samplename_min_shanon" => $samplename_min_shanon,
            "samplename_max_shanon" => $samplename_max_shanon,
            "sample_hi" => $sample_height,
            "sample_low" => $sample_low,
            "sample_big_rare" => $sample_big_rare,
            "name_phylumn" => $name_phylumn,
            "common_sample_phylumn" => $name_sample_phylumn,
            "sample_big_phy" => $sample_big_phy,
            "abun_genus" => $abun_genus,
            "name_sample_num_ven" => $name_sample_num_ven,
            "num_otu" => $num_otu,
            "near_sam" => $near_sam,
            "table_alpha" => $table_alpha,
            "table_stat" => $table_stat,
            "name_vs_sam" => $name_vs_sam,
            "p_value" => $p_value,
            "name_vs_sam_homo" => $name_vs_sam_homo,
            "p_value_homo" => $p_value_homo,
            "count_seqs" => $count_seqs,
            "avg_lenght" => $avg_lenght,
            "num_seqs2" => $num_seqs,
            "avg_reads" => $avg_reads
        );

      // $this->mongo_db->insert('projects_run', $data);
      $this->mongo_db->where(array('project_id' => $id_project))->set($data)->update('projects_run');

        // echo "<br>"."Show DATA"."<br>";
        // foreach($data as $key => $value) {
        //     if(is_array($value)){
        //           echo $key ." ==> ";
        //           print_r($value);
        //           echo "<br>";
        //     }else{
        //          echo $key ." ==> ". $value."<br>";
        //     }           
        // }

    }


public function read_file_db_otu($path_owncloud, $id_project,$method){

       
        // $path_file_otu = array("final.opti_mcc.summary", "file_after_reverse.csv", "file_phylum_count.txt", "final.opti_mcc.0.03.subsample.shared",
        //     "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes", "final.opti_mcc.groups.summary", "final.opti_mcc.groups.rarefaction");

        $path_owncloud_otu = $path_owncloud . "/output/";


        #final.opti_mcc.groups.summary
        $chao_shanon = file_get_contents($path_owncloud_otu . "final.opti_mcc.groups.summary");
        $row = explode("\n", $chao_shanon);
        array_shift($row);
        $arr_out = array();
        $arr_chao = array();
        $arr_shanon = array();
        $table_alpha = array();
        $index = 0;
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);
                if ($row_data[0] == 0.03) {

                    $arr_out[$index] = $row_data[5];
                    $arr_chao[$row_data[1]] = $row_data[9];
                    $arr_shanon[$row_data[1]] = $row_data[12];
                    $table_alpha[$index] = $row_data[1] . ":" . $row_data[4] . ":" . $row_data[5] . ":" . $row_data[9] . ":" . $row_data[12];
                    $index++;
                }
            }
        }

        $min_otu = min($arr_out);
        $max_otu = max($arr_out);
        $min_chao = min($arr_chao);
        $max_chao = max($arr_chao);
        $min_shanon = min($arr_shanon);
        $max_shanon = max($arr_shanon);
        $t_range_otu = min($arr_out) . "-" . max($arr_out);

        $samplename_min_chao =  array_search($min_chao,$arr_chao);
        $samplename_max_chao =  array_search($max_chao,$arr_chao);
        $samplename_min_shanon = array_search($min_shanon,$arr_shanon);
        $samplename_max_shanon = array_search($max_shanon,$arr_shanon);



        #final.opti_mcc.groups.rarefaction
        $rarefaction = file_get_contents($path_owncloud_otu ."final.opti_mcc.groups.rarefaction");
        $rare_index = array();
        $name_sample = array();
        $save_value = array();
        $row = explode("\n", $rarefaction);
        $check_index = preg_split('/\s+/', $row[0]);
            for($i = 0; $i < count($check_index); $i++) {
                $save_index = explode('-', $check_index[$i]);
                if ($save_index[0] == "0.03") {
                    array_push($rare_index, $i);
                    array_push($name_sample, $save_index[1]);
                }
            }

            array_shift($row);
            foreach ($row as $value => $data){
                if ($data != null){
                    $row_data_rare = preg_split('/\s+/', $data);
                    for ($i = 0; $i < count($rare_index); $i++) {
                        if (is_numeric($row_data_rare[$rare_index[$i]])) {
                             if (isset($save_value[$i])) {
                            } else {
                                $save_value[$i] = "0";
                            }
                            if ($save_value[$i] < $row_data_rare[$rare_index[$i]]) {
                                $save_value[$i] = $row_data_rare[$rare_index[$i]];
                            } else {

                            }
                        } else {
                             if ($save_value[$i] == null) {
                                 $save_value[$i] = "0";
                             } else {

                             }
                        }
                    }
                }
            }

            $index_hi = null;
            $num_hi = 0;
            for ($i = 0; $i < count($save_value); $i++) {
                    if ($num_hi < $save_value[$i]) {
                            $num_hi = $save_value[$i];
                            $index_hi = $i;
                    }
            }
            $sample_height = $name_sample[$index_hi];


            $index_low = null;
            $num_low = 0;
            for ($i = 0; $i < count($save_value); $i++) {
                    if ($i == 0) {
                        $num_low = $save_value[$i];
                        $index_low = $i;
                    } else {
                        if ($num_low > $save_value[$i]) {
                                $num_low = $save_value[$i];
                                $index_low = $i;
                        }
                    }
            }
            $sample_low = $name_sample[$index_low];



            $read_logs = file_get_contents($path_owncloud_otu . "database.txt");
            $row = explode("\n", $read_logs);
            $index = 0;
            $count_seqs = null;
            $avg_lenght = null;
            $num_seqs = null;
            $avg_reads = null;
            $lib_size = null;
            foreach ($row as $value => $data) {
                if ($data != null) {
                    $row_data = preg_split("/:/", $data);

                    if ("count_seqs" == $row_data[0]) {
                        $count_seqs = $row_data[1];
                    } else if ("avg_length" == $row_data[0]) {
                        $avg_lenght = $row_data[1];
                    } else if ("num_seqs" == $row_data[0]) {
                        $num_seqs = $row_data[1];
                    } else if ("avg_reads" == $row_data[0]) {
                        $avg_reads = $row_data[1];
                    } else if ("lib_size" == $row_data[0]) {
                        $lib_size = $row_data[1];
                    }
                     $index++;
                 }
             }


             // $count_seqs = "160000";
             // $avg_lenght = "263.05";
             // $num_seqs = "525";
             // $avg_reads = "53379";
             // $lib_size = "12981";


        // echo ">>>>>>>>>>>>>>>>>>>>>End Page 1-2<<<<<<<<<<<<<<<<<br>";


        #file_phylum_count.txt
        $phylumn_count = file_get_contents($path_owncloud_otu . "file_phylum_count.txt"); 

        $sample_big_rare = $sample_height;
        $name_sample_phylumn = array();
        $value_phylumn = [];
        $save_value_phylumn = array();
        $total_num = null;

        $row = explode("\n", $phylumn_count);
        $check_index = preg_split('/\s+/', $row[0]);
        array_shift($row);
        $half_sam = count($check_index) / 2;

            for ($i = 0; $i < count($check_index); $i++) {
                    $name_sample_phylumn[$i] = $check_index[$i];
            }

            $index = 0;
            foreach ($row as $value => $data) {

                    $row_data_phy = preg_split('/\s+/', $data);
                    if ($data != null) {
                            $total_num = null;
                        for ($i = 0; $i < count($name_sample_phylumn); $i++) {
                            $value_phylumn[$index][$i] = $row_data_phy[$i];
                            $total_num += $row_data_phy[$i];
                        }
                        $save_value_phylumn[$index] = $total_num;  
                    }
                $index++;
            }


            $max = 0;
            $k = 0;
            $more = 0;
            $name_phylumn = null;
            foreach ($save_value_phylumn as $key => $value) {
                if ($value >= $max) {
                        $max = max($save_value_phylumn);
                        $k = $key;
                 }
            }
            for ($n = 0; $n < count($name_sample_phylumn); $n++) {
                    if ($n == 0) {
                        $name_phylumn = $value_phylumn[$k][$n];
                        $sample_big_phy = $name_sample_phylumn[$n];
                    }

                    if ($value_phylumn[$k][$n] > $more) {
                        $more = $value_phylumn[$k][$n];
                        $sample_big_phy = $name_sample_phylumn[$n];
                    }
            }
     

         // echo ">>>>>>>>>>>>>>>>>>>>>End Page 3<<<<<<<<<<<<<<<<<br>";


        #file_after_reverse.csv
        $genus = file_get_contents($path_owncloud_otu . "file_after_reverse.csv");
        $row = explode("\n", $genus);
        $row2 = explode("\n", $genus);
        $row_name = explode("\n", $genus);
        array_shift($row);
        array_shift($row2);
        $count_genus = array();
        $count_genus2 = array();
        $num = 0;
        $key_sam = null;
        $max_for_sam = array();
        $index_genus = 0;
        $name_sample_num_ven = array();
 

        foreach ($row as $value => $data) {
            if ($data != null) {
                $split = preg_split('/,/', $data);
                if ($count_genus == null) {
                    for ($j = 1; $j < count($split); $j++) {
                          $count_genus[$j] = $split[$j];
                          $count_genus2[$j] = $split[$j];
                    }
                } else {
                    for ($j = 1; $j < count($split); $j++) {
                         $count_genus[$j] += $split[$j];
                         $count_genus2[$j] = $split[$j];
                    }
                }
                $num = 0;
                $key_index = null;

                for ($k = 1; $k < count($count_genus2); $k++) {
                    if ($num < $count_genus2[$k]) {
                        $num = $count_genus2[$k];
                        $key_index = $k;
                    }
                }

                $total = 0;
                foreach ($row2 as $value_e => $data_a) {
                    if ($data_a != null) {
                        $split1 = preg_split('/,/', $data_a);
                        $total += $split1[$key_index];
                    }
                }

                $splitn = preg_split('/,/', $row_name[0]);
                $genus_name = $splitn[$key_index];

                $maxximun = max($count_genus2);
                $percent = $maxximun * 100 / $total;

                $genus_pack[$index_genus] = $split[0] . ":" . $maxximun . ":" . $key_index . ":" . $total . ":" . round($percent, 2) . "%" . ":" . $genus_name;

                $index_genus++;
            }
        }



        #sharedsobs.svg
        $abun_genus = $genus_pack;
        $svg_file = file_get_contents($path_owncloud_otu . "sharedsobs.svg");

        $find_string = '<text';
        $position = strpos($svg_file, $find_string);

        $svg_file_new = substr($svg_file, $position);

        $searchfor = 'The number of species in group';
        $searchfor2 = 'The total richness of all';
        $pattern = preg_quote($searchfor, '/');
         // finalise the regular expression, matching the whole line
         $pattern = "/^.*$pattern.*\$/m";
         // search, and store all matching occurences in $matches
             if (preg_match_all($pattern, $svg_file, $matches)) {
                    $index_ven = 0;
                    foreach ($matches[0] as $value => $data_otu) {
                            $name_sam_otu = preg_split('/\s+/', $data_otu);
                            $repalce_str = str_replace('</text>', '', $name_sam_otu[13]);
                            $name_sample_num_ven[$index_ven] = $name_sam_otu[11] . " : " . $repalce_str;
                         $index_ven++;
                    }

             } else {
                    echo "No matches found";
            }


        #sharedsobs.sharedotus
        $svg_file2 = file_get_contents($path_owncloud_otu . "sharedsobs.sharedotus");
        $row_otu = explode("\n", $svg_file2);
        foreach ($row_otu as $value => $data) {
             if ($data != null) {
                    $split_row_otu = preg_split('/\s+/', $data);
                    $num_outs = $split_row_otu[1];
            }
        }
        $num_otu = $num_outs;


     // echo ">>>>>>>>>>>>>>>>>>>>>End Page 4 <<<<<<<<<<<<<<<<<br>";


        if($method == "NMDS"){

             #final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes
            $near_data = file_get_contents($path_owncloud_otu . "final.opti_mcc.thetayc.0.03.lt.ave.nmds.axes");

        }else{

             #final.opti_mcc.thetayc.0.03.lt.ave.pcoa.axes
            $near_data = file_get_contents($path_owncloud_otu . "final.opti_mcc.thetayc.0.03.lt.ave.pcoa.axes");

        }
        


        $axes1 = array();
        $axes2 = array();
        $row = explode("\n", $near_data);
        array_shift($row);
        $index = 0;
            foreach ($row as $value => $data) {
                if ($data != null) {
                        $split = preg_split('/\s+/', $data);
                        $axes1[$index] = $split[1];
                        $axes2[$index] = $split[2];
                        $index++;
                }
            }

            $sam1 = null;
            $sam2 = null;
            $save_near_county = 0;
            for ($i = 0; $i < count($axes2); $i++) {
               for ($j = 0; $j < count($axes2); $j++) {
                    if ($i != $j) {
                       if ($save_near_county == null) {
                             $save_near_county = sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2));
                             $sam1 = $i;
                             $sam2 = $j;
                        } else {
                            if ($save_near_county > sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2))) {
                                $save_near_county = sqrt(pow((($axes1[$i]) - ($axes1[$j])), 2) + pow((($axes2[$i]) - ($axes2[$j])), 2));

                                  $sam1 = $i;
                                  $sam2 = $j;
                            } else {

                            }
                       }
                   }
               }
           }

         $near_sam = "";
         $index = 0;
         foreach ($row as $value => $data) {

            if ($data != null) {
                $split = preg_split('/\s+/', $data);
                if ($index == $sam1) {
                      $near_sam1 = $split[0];
                }
                if ($index == $sam2) {
                     $near_sam2 = $split[0];
                }
                $index++;
            }
         }
        $near_sam = $near_sam1 . ":" . $near_sam2;


        #final.opti_mcc.summary
        $table_stat = file_get_contents($path_owncloud_otu . "final.opti_mcc.summary");
        $row = explode("\n", $table_stat);
        array_shift($row);
        $arr_sam1 = array();
        $arr_sam2 = array();
        $arr_lennon = array();
        $arr_jclass = array();
        $arr_morisitahorn = array();
        $arr_sorabund = array();
        $arr_theten = array();
        $arr_thetayc = array();
        $arr_braycurtis = array();

        $index = 0;
        $table_stat = array();
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);

                if ($row_data[0] == 0.03) {
                    $arr_sam1[$index] = $row_data[1];
                    $arr_sam2[$index] = $row_data[2];
                    $arr_lennon[$index] = $row_data[3];
                    $arr_jclass[$index] = $row_data[4];
                    $arr_morisitahorn[$index] = $row_data[5];
                    $arr_sorabund[$index] = $row_data[6];
                    $arr_theten[$index] = $row_data[7];
                    $arr_thetayc[$index] = $row_data[8];
                    $arr_braycurtis[$index] = $row_data[9];
                    $table_stat[$index] = $row_data[1] . ":" . $row_data[2] . ":" . $row_data[3] . ":" . $row_data[4] . ":" . $row_data[5] . ":" . $row_data[6] . ":" . $row_data[7] . ":" . $row_data[8] . ":" . $row_data[9];
                    $index++;
                }
            }
        }


        #final.opti_mcc.thetayc.0.03.lt.ave.amova
        $amova = file_get_contents($path_owncloud_otu . "final.opti_mcc.thetayc.0.03.lt.ave.amova");
        $row = explode("\n", $amova);
        $index = 0;
        $amova = array();
        $name_vs_sam = array();
        $p_value = array();
        foreach ($row as $value => $data) {
            if ($data != null) {
                $row_data = preg_split("/\s+/", $data);
                if ($index == 0) {

                     $name_vs_sam = array_push($name_vs_sam, $row_data[0]);
                } else if ($index % 7 == 0) {
                    if ($name_vs_sam == null) {
                         $name_vs_sam = $row_data[0];
                    } else {
                         $name_vs_sam = $name_vs_sam . ":" . $row_data[0];
                    }
                }
                if ($row_data[0] == "p-value:") {
                    if ($p_value == null) {
                        $p_value = $row_data[1];
                    } else {
                        $p_value = $p_value . ":" . $row_data[1];
                    } 
                }
            }

            $index++;
        }


        #final.opti_mcc.thetayc.0.03.lt.ave.homova
        $homova = file_get_contents($path_owncloud_otu . "final.opti_mcc.thetayc.0.03.lt.ave.homova");
        $row = explode("\n", $homova);
        $index = 0;
        $name_vs_sam_homo = null;
        $p_value_homo = null;
        $split_homova = preg_split('/\s+/', $row[1]);
        $name_vs_sam_homo = $split_homova[0];
        $p_value_homo = $split_homova[2];


        // echo ">>>>>>>>>>>>>>>>>>>>>End Page 5 <<<<<<<<<<<<<<<<<br>";
        // echo "In db";
        // echo ">>>>>>>>>>>>>>>>>>>>>End Page 6 <<<<<<<<<<<<<<<<<br>";
        // echo "In db";
        // echo ">>>>>>>>>>>>>>>>>>>>>End Page 7 <<<<<<<<<<<<<<<<<br>";

           $data = array(
            "avg_read_pre" => "",
            "num_seqs" => "",
            "t_range_otu" => $t_range_otu,
            "lib_size" => $lib_size,
            "min_otu" => $min_otu,
            "max_otu" => $max_otu,
            "max_chao" => $max_chao,
            "min_chao" => $min_chao,
            "max_shanon" => $max_shanon,
            "min_shanon" => $min_shanon,
            "samplename_min_chao" => $samplename_min_chao,
            "samplename_max_chao" => $samplename_max_chao,
            "samplename_min_shanon" => $samplename_min_shanon,
            "samplename_max_shanon" => $samplename_max_shanon,
            "sample_hi" => $sample_height,
            "sample_low" => $sample_low,
            "sample_big_rare" => $sample_big_rare,
            "name_phylumn" => $name_phylumn,
            "common_sample_phylumn" => $name_sample_phylumn,
            "sample_big_phy" => $sample_big_phy,
            "abun_genus" => $abun_genus,
            "name_sample_num_ven" => $name_sample_num_ven,
            "num_otu" => $num_otu,
            "near_sam" => $near_sam,
            "table_alpha" => $table_alpha,
            "table_stat" => $table_stat,
            "name_vs_sam" => $name_vs_sam,
            "p_value" => $p_value,
            "name_vs_sam_homo" => $name_vs_sam_homo,
            "p_value_homo" => $p_value_homo,
            "count_seqs" => $count_seqs,
            "avg_lenght" => $avg_lenght,
            "num_seqs2" => $num_seqs,
            "avg_reads" => $avg_reads
            );

        $this->mongo_db->where(array('project_id' => $id_project))->set($data)->update('projects_run');  


        // echo "<br>"."Show DATA"."<br>";
        // foreach($data as $key => $value) {
        //     if(is_array($value)){
        //           echo $key ." ==> ";
        //           print_r($value);
        //           echo "<br>";
        //     }else{
        //          echo $key ." ==> ". $value."<br>";
        //     }           
        // }



 }



  


}





