<?php 
defined('BASEPATH') OR exit("No direct script access allowed");

Class Qiime_report extends CI_Controller{

	public function __construct(){

		parent::__construct();
        $this->load->library('zip');

	}


    public function check_dirzip(){

        $id_project = $_REQUEST['current'];

        $user = "NULL";
        $folder = "NULL";
        $step_run = "NULL";

        #Query data status-process
        $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
         foreach ($array_status as $r) {             
                
                $step_run = $r['step_run'];
                $user = $r['user'];
                $folder = $r['project'];
         }
         $path_img = FCPATH."data_report_qiime/$user/$folder/Download/";  

            if($step_run == "2"){
                if(file_exists($path_img)){
                  echo json_encode("TRUE");  
                }
            }else{
             echo json_encode("Null");
            }
   }

    public function down_zip(){

         $id_project = $this->uri->segment(3);
         #Query data status-process
         $array_status = $this->mongo_db->get_where('status_process',array('project_id' => $id_project));
         foreach ($array_status as $r) {             
              $user = $r['user'];
              $folder = $r['project'];
         }

         $this->zip->read_dir("data_report_qiime/".$user."/".$folder."/Download/",FALSE);
          $this->zip->download('visualization.zip');
   } 


    public function graph_qiime_full($id_project){

        $user = $this->session->userdata['logged_in']['username'];
        $projects_name = null;

        $read = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        foreach ($read as $key => $value) {
            $projects_name = $value['project_name'];
        }
         
        $data['path_index'] = "data_report_qiime/$user/$projects_name/cdotu/index.html";

        $this->load->view('qime_full_graph',$data);

    }


    public function graph_qiime($username,$id_project){

          $user = $this->session->userdata['logged_in']['username'];

        $projects_name = null;
        $read = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        foreach ($read as $key => $value) {
            $projects_name = $value['project_name'];
        }

         
        $file_min = "data_report_qiime/".$user."/".$projects_name."/file_report/min.txt";
        $data_filemin = file_get_contents($file_min);
        list($min,$level_kegg) = explode("\t", $data_filemin);

        $data['alpha_diversity'] = $this->read_alpha_diversity($user,$projects_name,$min);

        $data['jaccard'] = $this->table2($user,$projects_name);
        $data['moris'] = $this->table3($user,$projects_name);

        $data['user'] = $user;
        $data['project'] = $projects_name;

        $this->load->view('header');
        $this->load->view('graph_qiime',$data);
        $this->load->view('footer');

    }

	 public function index($id_project){

        $user = $this->session->userdata['logged_in']['username'];
        $projects_name = null;
        $date_time = null;
        $project_type = null;

        $read = $this->mongo_db->get_where('projects', array('_id' => new \MongoId($id_project)));
        foreach ($read as $key => $value) {
            $projects_name = $value['project_name'];
            $date_time = $value['project_date_time'];
            $project_type = $value['project_type'];
            
        }

        $file_table_log = "data_report_qiime/$user/$projects_name/file_report/table_log.txt";
        if(!file_exists($file_table_log)){
            $this->detail_table_log($user,$projects_name);
        }

        $file_min = "data_report_qiime/$user/$projects_name/file_report/min.txt";
        $data_filemin = file_get_contents($file_min);
        list($min,$level_kegg) = explode("\t", $data_filemin);
        $level = str_replace("L", "", $level_kegg);

        $day_time = explode(" ", $date_time);
        $day = $day_time[0];
        $time = $day_time[1];

        $data['user'] = $user;
        $data['project_name'] = $projects_name;
        $data['project_type'] = $project_type;
       
        $data['day'] = $day;
        $data['time'] = $time;

        $data['alpha_diversity'] = $this->read_alpha_diversity($user,$projects_name,$min);
        $data['jaccard'] = $this->table2($user,$projects_name);
        $data['moris'] = $this->table3($user,$projects_name);

        $sequnec_average = $this->writeText($user,$projects_name);
        $data['sequences1'] = $sequnec_average[0];
        $data['average1'] = intval($sequnec_average[1]);

        $seq_average_length = $this->seq_average_length($user,$projects_name);
        $data['average2'] =  $seq_average_length[0];
        $data['seq1'] =  intval($seq_average_length[1]);
        
        $min_max = $this->table_otu($user,$projects_name,$min);
        $data['otu_min'] =  intval($min_max[0]);
        $data['otu_max'] =  intval($min_max[1]);

       
        $data['library_size'] = $min;

        $chao = $this->table_otu_chao($user,$projects_name,$min);
        $data['chao_max'] = $chao[0];
        $data['chao_min'] = $chao[1];
        
        $shannon = $this->table_otu_shannon($user,$projects_name,$min);
        $data['shannon_max'] = $shannon[0];
        $data['shannon_min'] = $shannon[1];

        $observed = $this->observed($user,$projects_name);
        $data['observed_max'] = $observed[0];
        $data['observed_min'] = $observed[1];

        $phylum1 = $this->table_l2($user,$projects_name,$min);
        $data['phylum1'] = $phylum1[0];
        $data['phylum1_sam'] = $phylum1[1];
    
        $phylum2 =  $this->table_l22($user,$projects_name,$min);
        $data['phylum2'] = $phylum2[0];
        $data['phylum2_sam1'] = $phylum2[1];
        $data['phylum2_sam2'] = $phylum2[2];

        $genus =  $this->table_l6($user,$projects_name,$min);
        $data['genus_sam1'] = $genus[0];
        $data['genus_name1'] = $genus[1];
        $data['genus_num1'] = $genus[2];
        $data['genus_sam2'] = $genus[3];
        $data['genus_name2'] = $genus[4];
        $data['genus_num2'] = $genus[5];

        $horn = $this->horn($user,$projects_name);
        $data['horn1'] = $horn;
       
        
        # check file adonis_results.txt
        $file_adonis_result = "data_report_qiime/$user/$projects_name/file_report/adonis_results.txt";
        $data['adonis_r2'] = "off";
        $data['adonis_pr'] = "off";
        if(file_exists($file_adonis_result)){

            $adonis = $this->adonis($user,$projects_name);
            $data['adonis_r2'] = trim($adonis[0]);
            $data['adonis_pr'] = trim($adonis[1]);
        }


        #check file anosim_results.txt
        $file_anosim_result = "data_report_qiime/$user/$projects_name/file_report/anosim_results.txt";
        $data['anosim_test'] = "off";
        $data['anosim_p'] = "off";
        if(file_exists($file_anosim_result)){

             $anosim = $this->anosim($user,$projects_name);
             $data['anosim_test'] = trim($anosim[0]);
             $data['anosim_p'] = number_format(trim($anosim[1]),4); 
        }
       

        #check file permanova_results.txt
        $file_permanova_result = "data_report_qiime/$user/$projects_name/file_report/permanova_results.txt";
        $data['permanova_test'] = "off";
        $data['permanova_p'] = "off";
        if(file_exists($file_permanova_result)){

            $permanova = $this->permanova($user,$projects_name);
            $data['permanova_test'] = trim($permanova[0]);
            $data['permanova_p'] = number_format(trim($permanova[1]),4);
        }

       
        #check file myResultPathway.tsv

        $file_myResult = "data_report_qiime/$user/$projects_name/file_report/myResultsPathway".$level_kegg.".tsv";
        $data['my_result'] = "off";
        $data['level_kegg'] = "off";
        if(file_exists($file_myResult)){

            $data['my_result'] = $this->my_result($user,$projects_name,$level_kegg);
            $data['level_kegg'] = $level;
        }
      


        $data['tablelog'] = "data_report_qiime/$user/$projects_name/file_report/table_log.txt";


	 	$this->load->library('myfpdf');
        $this->load->library('mytcpdf');
        $this->load->view('qiime_report',$data);
	 }


	 public function read_alpha_diversity($user,$projects_name,$min){

        $data_out = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/alpha_diversity_from_table_even".$min.".txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
         
	 		while(($line = fgets($read)) !== false){

     			$data = explode("\t", $line);
     	
     			if($count == 0){
     				 $output =  array("SampleID",$data[1],$data[2],$data[4],$data[5]);
     			      array_push($data_out,$output);
     				
                     $count++;
     			}
     			else{

     				 if(fmod($data[1], 1) !== 0.00){
     				 	 $data[1] = number_format($data[1],4);
     				 }
     				 if(fmod($data[2], 1) !== 0.00){
     				 	 $data[2] = number_format($data[2],4);
     				 }
     				 if(fmod($data[4], 1) !== 0.00){
     				 	 $data[4] = number_format($data[4],4);
     				 }
     				 if(fmod($data[5], 1) !== 0.00){
     				 	 $data[5] = number_format($data[5],4);
     				 }

     				 $output =  array($data[0], $data[1] ,$data[2],$data[4],$data[5]);
     				 array_push($data_out,$output);
     			}  
       		 }
        fclose($read);
        return $data_out;
        
         // for($i=0;$i < count($data_out); $i++){
         // 	 for($j = 0 ; $j < count($data_out[$i]); $j++){
         // 	 	echo $data_out[$i][$j]."&nbsp";
         // 	 }
         // 	 echo "<br>";
         // }
        
	 }

	 public function array_push_assoc($array,$key,$value){

	 	$array[$key] = $value;
	 	return $array;
	 }


	 public function table2($user,$projects_name){

        $data_out = array();
        $data_index = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/abund_jaccard_dm.txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 
	 		while(($line = fgets($read)) !== false){
               
     		     $data = explode("\t",$line);
     		     for($i =0 ;$i < count($data);$i ++){
                    #echo $data[$i]."&nbsp";
                    $data[$i] = trim($data[$i]);
                    if((is_numeric($data[$i])) && (fmod($data[$i],1) !== 0.00)){

                       $data[$i] = number_format($data[$i],4);
                    }

                    $data_index = $this->array_push_assoc($data_index,$i,$data[$i]);
     		     }
     		     array_push($data_out,$data_index);
     		     foreach ($data_index as $key => $value) {
     		     	unset($data_index[$key]);
     		     }
       		 }
        fclose($read);

         // for($i=0;$i < count($data_out); $i++){
         // 	 for($j = 0 ; $j < count($data_out[$i]); $j++){
     			 // 	 echo $data_out[$i][$j]."&nbsp";
         // 	 }
         // 	 echo "<br>";
         // }
        return $data_out;
	 }


	 public function table3($user,$projects_name){

	 	$data_index = "";
        $data_out = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/morisita_horn_dm.txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 
	 		while(($line = fgets($read)) !== false){
                
     		     $data = explode("\t",$line);
     		     for($i =0 ;$i < count($data);$i ++){
                    #echo $data[$i]."&nbsp";
                    $data[$i] = trim($data[$i]);
                    if((is_numeric($data[$i])) && (fmod($data[$i],1) !== 0.00)){

                       $data[$i] = number_format($data[$i],4);
                    }

                    $data_index = $this->array_push_assoc($data_index,$i,$data[$i]);
     		     }
     		     array_push($data_out,$data_index);
     		     foreach ($data_index as $key => $value) {
     		     	unset($data_index[$key]);
     		     }
     		     
       		 }
        fclose($read);
        return $data_out;

	 }


    ###  Read text insert Report  ###

	 public function writeText($user,$projects_name){

       $path = "owncloud/data/$user/files/$projects_name/output/stitched_reads/";
       $cmd = "Scriptqiime2/average_read.sh $path";
       exec($cmd,$output);
       $sequences = null;
       $average = null;
       foreach ($output as $key => $value) {
       	  $data = explode(" ", $value);
       	  $sequences = $data[6];
       	  $average = $data[14];

       }
       #echo "sequences : ".$sequences."<br> average : ".$average;
       return array($sequences,$average);

	 }


	 public function seq_average_length($user,$projects_name){

	 	# fasta_files OR newfasta_files
	 	  $path = "owncloud/data/$user/files/$projects_name/output/fasta_files/Processeddata/combined_seqs.fna";

	 	  $cmd = "perl Scriptqiime2/seq_average_length.pl $path";
          exec($cmd,$output);
          $sequences = null;
          $average = null;
          foreach ($output as $key => $value) {
          	if($key == "4"){
               $average = $value;
          	}
          	if($key == "6"){
               $sequences = $value;
          	}
          }
          //echo "sequences : ".$sequences."<br> average : ".$average;
          $sequences_all = explode("=",$sequences);
          $average_all = explode("=", $average);

          return array($sequences_all[1],$average_all[1]);
	 }

	


    public function detail_table_log($user,$projects_name){



        $log_data = array();

        # colum Header
        $log_data[0][0] =  "Samples name";
        $log_data[0][1] =  "No. of reads in rawdata after assembly";
        $log_data[0][2] =  "No. of reads after filter quality";
        $log_data[0][3] =  "No. of reads after removing chimera";
        $log_data[0][4] =  "No. of cleaned reads for OTUs analysis";


        # column 1
        $sample_name = "data_report_qiime/$user/$projects_name/file_report/sampleName.txt";
        $read = fopen($sample_name,"r") or die ("Unable to open file");
        $count = 1;
            while(($line = fgets($read)) !== false){
                 $val_name = explode("\t", trim($line));
                 foreach ($val_name as $key => $value){

                         $log_data[$count][0] =  trim($value);
                         
                   $count++;    
                 }
              
             }
        fclose($read);


        # column 2 && column 3
        $read_filter = "data_report_qiime/$user/$projects_name/file_report/read_filter.log";
        $read2 = fopen($read_filter,"r") or die ("Unable to open file");
        $count2 = 0;
            while(($line2 = fgets($read2)) !== false){

               if($count2 >= 1){

                   $value = explode("\t", trim($line2));
                   $log_data[$count2][1] =  trim($value[1]);
                   $log_data[$count2][2] =  trim($value[6]);
               }
               
               $count2++;
             }
        fclose($read2);
   
         
     
        # column 4
        $chimeras_summary = "data_report_qiime/$user/$projects_name/file_report/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_summary.txt";

         $read3 = fopen($chimeras_summary,"r") or die ("Unable to open file");
         $count3 = 0;
         
            while(($line3 = fgets($read3)) !== false){
                if($count3 >= 15){

                    $val3 = explode(":", trim($line3));
                    $index_val = $this->searchForId($val3[0],$log_data);
                    $log_data[$index_val][3] =  floor($val3[1]); 

                 }
               
               $count3++;
             }
        fclose($read3);
   
        

        # column 5
         $filtered_summary = "data_report_qiime/$user/$projects_name/file_report/otu_table_mc2_w_tax_no_pynast_failures_no_chimeras_frequency_filtered_summary.txt";

         $read4 = fopen($filtered_summary,"r") or die ("Unable to open file");
         $count4 = 0;

            while(($line4 = fgets($read4)) !== false){
                if($count4 >= 15){
                    $val4 = explode(":", trim($line4));
                    $index_val = $this->searchForId($val4[0],$log_data);
                    $log_data[$index_val][4] =  floor($val4[1]); 


                }
                $count4++;
            }
         fclose($read4);


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

        $path_log = "data_report_qiime/$user/$projects_name/file_report/table_log.txt";
        $file_log = FCPATH.$path_log;
        file_put_contents($file_log,$table_data); 

    }
    
    # search array 2 dimension
    public function searchForId($id,$array){
        foreach ($array as $key => $val) {
            foreach ($val as $key2 => $value2) {
               if($value2 == $id){
                  return $key;
               }
           }
        }
    }


    public function table_otu($user,$projects_name,$min){

        $data_out = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/alpha_diversity_from_table_even".$min.".txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
         
	 		while(($line = fgets($read)) !== false){

     			$data = explode("\t", $line);
     			if($count > 0){
                  array_push($data_out, $data[1]);
     			}
     	       $count++;
       		 }
        fclose($read);
         //echo "Min : ".min($data_out)."<br>";
         //echo "Max : ".max($data_out);
        return array(min($data_out),max($data_out));
	 }
     

     public function table_otu_chao($user,$projects_name,$min){

        $data_out = array();
        $data_num = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/alpha_diversity_from_table_even".$min.".txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
         
	 		while(($line = fgets($read)) !== false){

     			$data = explode("\t", $line);
     			if($count > 0){

                  array_push($data_out,$data[0]);
                  array_push($data_num,$data[2]);
     			}
     	       $count++;
       		 }
        fclose($read);
       
         $min = min($data_num);
         $max = max($data_num);

         $index_min = array_search($min, $data_num);
         $index_max = array_search($max, $data_num);

         // echo "Min : ".$min." ==> ".$data_out[$index_min]."<br>";
         // echo "Max : ".$max." ==> ".$data_out[$index_max]."<br>";

         return array($data_out[$index_max],$data_out[$index_min]);

	 }


	  public function table_otu_shannon($user,$projects_name,$min){

        $data_out = array();
        $data_num = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/alpha_diversity_from_table_even".$min.".txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
         
	 		while(($line = fgets($read)) !== false){

     			$data = explode("\t", $line);
     			if($count > 0){

                  array_push($data_out,$data[0]);
                  array_push($data_num,$data[4]);
     			}
     	       $count++;
       		 }
        fclose($read);
       
         $min = min($data_num);
         $max = max($data_num);

         $index_min = array_search($min, $data_num);
         $index_max = array_search($max, $data_num);

         // echo "Min : ".$min." ==> ".$data_out[$index_min]."<br>";
         // echo "Max : ".$max." ==> ".$data_out[$index_max]."<br>";

          return array($data_out[$index_max],$data_out[$index_min]);

	 }


	  public function observed($user,$projects_name){

        $data_out = array();
     
	 	$path = "data_report_qiime/$user/$projects_name/file_report/observed.txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
         
	 		while(($line = fgets($read)) !== false){

     			$data = explode("\t", $line);
     			array_push($data_out,$data);
     			
       		 }
        fclose($read);
         
          $last_index = count($data_out)-1;
          array_splice($data_out[$last_index],0,1);
          $data = $data_out[$last_index];

          //echo "Max : ".max($data)."<br>";
          //echo "Min : ".min($data)."<br>";
          return array(max($data),min($data));

	 }


	 public function  table_l6($user,$projects_name,$min){

	 	$data_out = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/table_mc".$min."_sorted_L6.txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
         
	 		while(($line = fgets($read)) !== false){

     			$data = explode("\t", $line);
     			array_push($data_out,$data);
     			
       		 }
        fclose($read);
        //print_r($data_out);
        $max_all = 0;
        $max = 0 ;
        $data_max = array();
        //echo "<table border='1'>";
        for($i=0;$i < count($data_out);$i++){
        	
             //echo "<tr>";
             //echo "<td>".$i."</td>";

        	for($j= 1; $j < count($data_out[$i]);$j++){

        		//echo "<td>".$data_out[$i][$j]."</td>";
        		$val = trim($data_out[$i][$j]);
        		if(is_numeric($val)){
                    if($val > $max){
                         $max = $val;
                    }
        		}
        	}
        	array_push($data_max,$max);
        	//echo "<td>".$max."</td>";
        	//echo "</tr>";
        	$max = $max_all;
        }

        //echo "</table>";
	 	// foreach ($data_max as $key => $value) {
	 	// 	echo $key.":".$value."<br>";
	 	// }

        # Max one
	 	$max_one = max($data_max);
	 	$max_one_row = array_search($max_one, $data_max);
	 	$max_one_col = array_search($max_one,$data_out[$max_one_row]);


        $genus_one = explode(";",$data_out[$max_one_row][0]);
        $array_genus_one = array();
        foreach ($genus_one as $key => $value) {
        	//echo $value."<br>";
        	$g_one = explode("__", $value);
        	if($g_one[1] != null){
        		// echo $key.": ".$g_one[0]."  ".$g_one[1]."<br>";
        		array_push($array_genus_one,$key);
        	}
        }

        $last_genus_one = count($array_genus_one)-1;

        //echo "Sample  : ".$data_out[1][$max_one_col]."<br>";
	 	//echo "Genus   : ".$data_out[$max_one_row][0]."<br>";
	 	//echo "Max one : ".number_format($max_one,4)."<br>";
       // echo "Genus last : ".$genus_one[$last_genus_one]."<br>";
	 	# End Max one

         //echo "<br> ********** <br>";

        # Max two
	 	unset($data_max[$max_one_row]);
	 	$max_two = max($data_max);
	 	$max_two_row = array_search($max_two, $data_max);
	 	$max_two_col = array_search($max_two,$data_out[$max_two_row]);

        $genus_two = explode(";",$data_out[$max_two_row][0]);
        $array_genus_two = array();
        foreach ($genus_two as $key => $value) {
        	$g_two = explode("__", $value);
        	if($g_two[1] != null){
        		
        		array_push($array_genus_two,$key);
        	}
        }

        $last_genus_two = count($array_genus_two)-1;

        //echo "Sample  : ".$data_out[1][$max_two_col]."<br>";
	 	//echo "Genus   : ".$data_out[$max_two_row][0]."<br>";
	 	//echo "Max two : ".number_format($max_two,4)."<br>";
	 	//echo "Genus last : ".$genus_two[$last_genus_two]."<br>";
	 	 # End Max two

        return array($data_out[1][$max_one_col],$genus_one[$last_genus_one],number_format($max_one,4),$data_out[1][$max_two_col],$genus_two[$last_genus_two],number_format($max_two,4));
	 }

	 public function table_l2($user,$projects_name,$min){

	 	$data_out = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/table_mc".$min."_sorted_L2.txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
	 		while(($line = fgets($read)) !== false){
     			$data = explode("\t", $line);
     			array_push($data_out,$data);
     			
       		 }
        fclose($read);
        $max_all = 0;
        $max = 0 ;
        $data_max = array();
        for($i=0;$i < count($data_out);$i++){
        	for($j= 1; $j < count($data_out[$i]);$j++){
        		$val = trim($data_out[$i][$j]);
        		if(is_numeric($val)){
                    if($val > $max){
                         $max = $val;
                    }
        		}
        	}
        	array_push($data_max,$max);
        	$max = $max_all;
        }

        # Max 
	 	$max = max($data_max);
	 	$max_row = array_search($max, $data_max);
	 	$max_col = array_search($max,$data_out[$max_row]);

	 	$phylum = explode(";",$data_out[$max_row][0]);
        $array_phylum = array();
        foreach ($phylum as $key => $value) {
        	$phy_split = explode("__", $value);
        	if($phy_split[1] != null){
        		array_push($array_phylum,$key);
        	}
        }
        $phylum_index = count($array_phylum)-1;
        //echo "Max  : ".$max."<br>";
	 	// echo "Sample  : ".$data_out[1][$max_col]."<br>";
	 	// echo "Phylum   : ".$phylum[$phylum_index]."<br>";

        return array($phylum[$phylum_index],$data_out[1][$max_col]);

	 }

	  public function table_l22($user,$projects_name,$min){

	 	$data_out = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/table_mc".$min."_sorted_L2.txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
	 		while(($line = fgets($read)) !== false){
     			$data = explode("\t", $line);
     			array_push($data_out,$data);
     			
       		 }
        fclose($read);

        $most_average = 0;
        $index_key = null;
        for($i=2;$i < count($data_out);$i++){
        	#split null value
        	array_filter($data_out[$i]);
        	$average = array_sum($data_out[$i])/count($data_out[$i]);
        	if($average > $most_average){
        		$most_average = $average;
        		$index_key = $i;
        	}
        }


        $phylum = explode(";",$data_out[$index_key][0]);
        $array_phylum = array();
        foreach ($phylum as $key => $value) {
        	$phy_split = explode("__", $value);
        	if($phy_split[1] != null){
        		array_push($array_phylum,$key);
        	}
        }
        $phylum_index = count($array_phylum)-1;
	 	//echo "Phylum   : ".$phylum[$phylum_index]."<br>";

        unset($data_out[$index_key][0]);

        $topmax1 = max($data_out[$index_key]);
	 	$top1_col = array_search($topmax1,$data_out[$index_key]);       
        $index_topmax1 = array_search($topmax1, $data_out[$index_key]);
        unset($data_out[$index_key][$index_topmax1]);

        // echo "Sample  : ".$data_out[1][$top1_col]."<br>";
        // echo $topmax1."<br>";
      

        $topmax2 = max($data_out[$index_key]);
        $top2_col = array_search($topmax2,$data_out[$index_key]);
        $index_topmax2 = array_search($topmax2, $data_out[$index_key]);
        
        unset($data_out[$index_key][$index_topmax2]);

        // echo "Sample  : ".$data_out[1][$top2_col]."<br>";
        // echo $topmax2."<br>";


        $topmax3 = max($data_out[$index_key]);
        $top3_col = array_search($topmax3,$data_out[$index_key]);
        $index_topmax3 = array_search($topmax3, $data_out[$index_key]);
        
        unset($data_out[$index_key][$index_topmax3]);

        // echo "Sample  : ".$data_out[1][$top3_col]."<br>";
        // echo $topmax3."<br>";

        return array($phylum[$phylum_index],$data_out[1][$top2_col],$data_out[1][$top3_col]);

	 }

	public function horn($user,$projects_name){

	 	$data_out = array();
	 	$path = "data_report_qiime/$user/$projects_name/file_report/morisita_horn_pc.txt";
	 	$read = fopen($path,"r") or die ("Unable to open file");
	 	$count = 0;
	 		while(($line = fgets($read)) !== false){
     			$data = explode("\t", $line);
     			array_push($data_out,$data);
     			
       		 }
        fclose($read);

         $array_sample = array();
         $array_x = array();
         $array_y = array();

         for($i=9;$i < count($data_out); $i++){

         	 $nloop = (count($data_out[$i])-3);
           
             if($nloop >= 2){
                for($j = 2 ; $j < 3; $j++){
                   
                    array_push($array_sample,$data_out[$i][0]);
                    array_push($array_x,$data_out[$i][1]);
                    array_push($array_y,$data_out[$i][2]);
                }
             }
         }

        // for($i = 0;$i < count($array_sample);$i++){
        //      for($j =1 ;$j < count($array_sample)-$i;$j++){
        //      echo $i." ".($j+$i)."(".$array_sample[$i].") - (".$array_sample[$j+$i].")<br>";

        //      }
        //      echo "<br>";
        // }

       
         //echo "index (x)"."<br>";
         $array_x_index = array();
         for($i = 0;$i < count($array_x);$i++){
             for($j =1 ;$j < count($array_x)-$i;$j++){
             	if(($array_x[$i] < 0 ) && ($array_x[$j+$i] < 0)){
                    $num = $array_x[$i]-$array_x[$j+$i];
             		//echo $i." ".($j+$i)." (".$array_x[$i].") - (".$array_x[$j+$i].") => ".($num)."<br>";
             		$num_index = ($i."\t".($j+$i)."\t".$num);
             		array_push($array_x_index,$num_index);
             	}else if(($array_x[$i] > 0 ) && ($array_x[$j+$i] > 0)){
                    $num = $array_x[$i]-$array_x[$j+$i];
             	    //echo $i." ".($j+$i)." (".$array_x[$i].") - (".$array_x[$j+$i].") => ".($num)."<br>";
             	    $num_index = ($i."\t".($j+$i)."\t".$num);
             		array_push($array_x_index,$num_index);
             	}else{
             		//echo $i." ".($j+$i)." (".$array_x[$i].") - (".$array_x[$j+$i].") => "."minus"."<br>";
             	}
             }
         }

        //  echo "<br>";
        //  foreach ($array_x_index as $key => $value) {
        //  	echo $value."<br>";
        //  }

        // echo "<br>";

        // echo "index (y)"."<br>";
        $array_y_index = array();
        for($i = 0;$i < count($array_y);$i++){
            for($j =1 ;$j < count($array_y)-$i;$j++){
             	if(($array_y[$i] < 0 ) && ($array_y[$j+$i] < 0)){
             		$num = $array_y[$i]-$array_y[$j+$i];
             	    //echo $i." ".($j+$i)."(".$array_y[$i].") - (".$array_y[$j+$i].") => ".($num)."<br>";
             	    $num_index = ($i."\t".($j+$i)."\t".$num);
             	    array_push($array_y_index, $num_index);
             	}else  if(($array_y[$i] > 0 ) && ($array_y[$j+$i] > 0)){
             	    $num = $array_y[$i]-$array_y[$j+$i];
             	    //echo $i." ".($j+$i)."(".$array_y[$i].") - (".$array_y[$j+$i].") => ".($num)."<br>";
             	    $num_index = ($i."\t".($j+$i)."\t".$num);
             	    array_push($array_y_index, $num_index);
            	}else{
             	     //echo $i." ".($j+$i)."(".$array_y[$i].") - (".$array_y[$j+$i].") => "."minus"."<br>";
            	}
             }
         }

         // echo "<br>";
         // foreach ($array_y_index as $key => $value) {
         // 	echo $value."<br>";
         // }

         // echo "<br>";

         $array_index_sample = array();
         $array_min_x = array();
         $array_min_y = array();

         for($i=0;$i < count($array_x_index);$i++){
         	$data_x = explode("\t", $array_x_index[$i]);

            for($j = 0;$j < count($array_y_index);$j++){
            	$data_y = explode("\t", $array_y_index[$j]);

            	if(($data_x[0] == $data_y[0]) && ($data_x[1] == $data_y[1])){
            		//echo abs($data_x[0])." ".abs($data_x[1])." : ".abs($data_x[2])."  *****  ".abs($data_y[0])." ".abs($data_y[1])." : ".abs($data_y[2])."<br>";
                    
                    $num_index = $data_x[0].":".$data_x[1];
                    $num_x = trim(abs($data_x[2]));
            		$num_y = trim(abs($data_y[2]));

            		array_push($array_index_sample,$num_index);
            		array_push($array_min_x,$num_x);
            		array_push($array_min_y,$num_y);

            	}
            }
         }


         $min_x = min($array_min_x);
         $min_y = min($array_min_y);

         $key_sample_x = array_search($min_x, $array_min_x);
         $key_sample_y = array_search($min_y, $array_min_y);

         //echo "<br>".$min_x."<br>".$min_y."<br>";

         $data_x = "";
         $data_y = "";
         $data_send = null;

         $key_1x = $array_index_sample[$key_sample_x];
         $key_2y =  $array_index_sample[$key_sample_y];

         if($key_1x == $key_2y){

         	$data_answer = explode(":",$key_1x);
         	
             $data_x = $array_sample[$data_answer[0]];
             $data_y = $array_sample[$data_answer[1]];
             //echo "1: (".$data_x." and ".$data_y.")";
             $data_send = $data_x ." and ".$data_y;
         }
          else{

         	$data_answer = explode(":",$key_1x);
 
             $data_x1 = $array_sample[$data_answer[0]];
             $data_y1 = $array_sample[$data_answer[1]];
             //echo "1: (".$data_x1." and ".$data_y1.")"."<br/>";

            $data_answer = explode(":",$key_2y);
            
             $data_x2 = $array_sample[$data_answer[0]];
             $data_y2 = $array_sample[$data_answer[1]];
             //echo "2: (".$data_x2." and ".$data_y2.")";

             $data_send = $data_x1." and ".$data_y1.' , '.$data_x2." and ".$data_y2;
          }

         return $data_send;
         //echo $data_send;

    }


    public function adonis($user,$projects_name){

        $data_adonis = "";
        $path = "data_report_qiime/$user/$projects_name/file_report/adonis_results.txt";
        $read = fopen($path,"r") or die ("Unable to open file");
        $count = 0;
         
            while(($line = fgets($read)) !== false){

                if($count == 10){
                    $data_adonis = $line;
                }
                $count++;
        
             }
         fclose($read);
      
         $data = explode(" ", $data_adonis);
         // echo "R2 : ".$data[8]."<br/>";
         // echo "Pr(>F) : ".$data[9];

         return array($data[8],$data[9]);
        
     }

     public function anosim($user,$projects_name){

        $data_test_statistic = "";
        $data_pvalue = "";

        $path = "data_report_qiime/$user/$projects_name/file_report/anosim_results.txt";
        $read = fopen($path,"r") or die ("Unable to open file");
        $count = 0;
         
            while(($line = fgets($read)) !== false){
                  if($count == 4){
                     $data = explode("\t", $line);
                     $var = floatval($data[1]);
                     $data_test_statistic = number_format($var,4);
                  }else if($count == 5){
                      $data = explode("\t", $line);
                      $data_pvalue = $data[1];
                  }
                  $count++;
        
             }
        fclose($read);
        
        // echo "test_statistic : ".$data_test_statistic."<br/>";
        // echo "pvalue : ".$data_pvalue;

        return array($data_test_statistic,$data_pvalue);
     }

    public function permanova($user,$projects_name){

        
        $data_test_statistic = "";
        $data_pvalue = "";

        $path = "data_report_qiime/$user/$projects_name/file_report/permanova_results.txt";
        $read = fopen($path,"r") or die ("Unable to open file");
        $count = 0;
         
            while(($line = fgets($read)) !== false){
                if($count == 4){
                     $data = explode("\t", $line);
                     $var = floatval($data[1]);
                     $data_test_statistic = number_format($var,4);
                  }else if($count == 5){
                      $data = explode("\t", $line);
                      $data_pvalue = $data[1];
                  }
                  $count++;
        
             }
        fclose($read);
        // echo "test_statistic : ".$data_test_statistic."<br/>";
        // echo "pvalue : ".$data_pvalue;

        return array($data_test_statistic,$data_pvalue);
 
     }

     public function my_result($user,$projects_name,$level_kegg){

        $data_adonis = "";
        $path = "data_report_qiime/$user/$projects_name/file_report/myResultsPathway".$level_kegg.".tsv";
        $read = fopen($path,"r") or die ("Unable to open file");
        $count = 0;
         
            while(($line = fgets($read)) !== false){
               #echo $count." :==>: ".$line."<br>";
               $count++;
             }
        fclose($read);
        #echo "last line : ".$count;
        $found = $count-1;
        return $found;
     }


 
}



?>