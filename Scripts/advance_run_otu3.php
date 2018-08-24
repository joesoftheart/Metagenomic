<?php


    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");

          $user = $argv[1];
         $project = $argv[2];
         $path_in = $argv[3];
         $path_out = $argv[4];
         $GLOBALS['path_log'] = $argv[5];

         $GLOBALS['level']= $argv[6];
         $GLOBALS['size_alpha'] = $argv[7];
         $GLOBALS['size_beta'] = $argv[8];
         $GLOBALS['group_sam'] = $argv[9];
         $GLOBALS['group_ven'] =$argv[10];

         $GLOBALS['d_upgma_st'] = $argv[11];
         $GLOBALS['d_upgma_me'] = $argv[12];

         $GLOBALS['d_pcoa_st'] = $argv[13];
         $GLOBALS['d_pcoa_me'] = $argv[14];
         
         $GLOBALS['nmds']  = $argv[15];

         $GLOBALS['d_nmds_st'] = $argv[16];
         $GLOBALS['d_nmds_me'] = $argv[17];
         

         $GLOBALS['file_design'] = $argv[18];
         $GLOBALS['file_metadata'] = $argv[19];

         $GLOBALS['amova'] = $argv[20];
         $GLOBALS['homova'] = $argv[21];
         $GLOBALS['anosim'] = $argv[22];
        
         $GLOBALS['correlation_meta'] = $argv[23];
         $GLOBALS['method_meta'] = $argv[24];
         //$GLOBALS['axes_meta'] = $argv[25];

         $GLOBALS['correlation_otu'] = $argv[26];
         $GLOBALS['method_otu'] = $argv[27];
         //$GLOBALS['axes_otu'] = $argv[28];

         $GLOBALS['label_num'] = $argv[29];

         #PICRUSt and STAMP
         $GLOBALS['kegg'] = $argv[30];
         $GLOBALS['sample_comparison'] = $argv[31];
         $GLOBALS['statistical_test'] = $argv[32];
         $GLOBALS['ci_method'] = $argv[33];
         $GLOBALS['p_value'] = $argv[34];


         
         # Check PCoA & NMDS
         $GLOBALS['func_use'] = $argv[35];
         $GLOBALS['check'] = "";

         if($GLOBALS['func_use'] == "usepcoa"){
             $GLOBALS['check'] = "pcoa";
         }elseif ($GLOBALS['func_use'] == "usenmds") {
             $GLOBALS['check'] = "nmds";
         }

         
         # Keep value method PCoA or nmds
             $GLOBALS['value_method'] = null; 
           
         # Keep value method tree
            $GLOBALS['tree_cal'] = null;


         if($user != "" && $project != "" && $path_in != "" && $path_out != "" && $argv[5] != "" && $argv[6] =! "" && $argv[7] != "" && $argv[8] != "" && $argv[9] != "" && $argv[10] != ""){
             echo "Check Parameter Success"."\n";
             collect_rarefaction_summary($user,$project,$path_in,$path_out);
            


         }else{

              echo "user : ".$user."\n";
              echo "project : ".$project."\n"; 
              echo "path_in : ".$path_in."\n";
              echo "path_out : ".$path_out."\n";
              echo "path_log : ".$GLOBALS['path_log']."\n";
              echo "level : ".$GLOBALS['level']."\n";
             
              echo "size_alpha : ".$GLOBALS['size_alpha']."\n";
              echo "size_beta : ".$GLOBALS['size_beta']."\n";
              echo "group_sam : ".$GLOBALS['group_sam']."\n";
              echo "group_ven : ".$GLOBALS['group_ven']."\n";

              echo "d_upgma_st : ".$GLOBALS['d_upgma_st']."\n";
              echo "d_upgma_me : ".$GLOBALS['d_upgma_me']."\n";

              echo "d_pcoa_st : ".$GLOBALS['d_pcoa_st']."\n";
              echo "d_pcoa_me : ".$GLOBALS['d_pcoa_me']."\n";
         
              echo "nmds : ".$GLOBALS['nmds']."\n";

              echo "d_nmds_st : ".$GLOBALS['d_nmds_st']."\n";
              echo "d_nmds_me : ".$GLOBALS['d_nmds_me']."\n" ;

              echo "file_design : ".$GLOBALS['file_design']."\n";
              echo "file_metadata : ".$GLOBALS['file_metadata']."\n" ;
              echo "ah_mova : ".$GLOBALS['ah_mova']."\n";
              
              echo "correlation_meta : ".$GLOBALS['correlation_meta']."\n";
              echo "method_meta : ".$GLOBALS['method_meta']."\n";
             //echo "axes_meta : ".$GLOBALS['axes_meta']."\n";

              echo "correlation_otu : ".$GLOBALS['correlation_otu']."\n";
              echo "method_otu : ".$GLOBALS['method_otu']."\n";
              //echo "axes_otu : ".$GLOBALS['axes_otu']."\n";
         }

#1
function collect_rarefaction_summary($user, $project, $path_in, $path_out)
{
    echo "collect_rarefaction_summary" . "\n";

    $jobname = $user . "_collect_rarefaction_summary";

    $make = "collect.single(shared=final.opti_mcc.shared, calc=chao, freq=100, label=" . $GLOBALS['level'] . " ,inputdir=$path_in,outputdir=$path_out)
					rarefaction.single(shared=final.opti_mcc.shared, calc=sobs, freq=100, label=" . $GLOBALS['level'] . ", processors=8 ,inputdir=$path_in,outputdir=$path_out)
					summary.single(shared=final.opti_mcc.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon-simpson, subsample=" . $GLOBALS['size_alpha'] . ", label=" . $GLOBALS['level'] . " ,inputdir=$path_in,outputdir=$path_out)";

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

             $loop = false;
            dist_summary_shared($user, $project, $path_in, $path_out);
            //break;


        }
    }
}

#2
function dist_summary_shared($user, $project, $path_in, $path_out)
{

    echo "dist_summary_shared" . "\n";
    $jobname = $user . "_dist_summary_shared";

    $make = "dist.shared(shared=final.opti_mcc.shared, calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, subsample=" . $GLOBALS['size_beta'] . ", processors=8 ,inputdir=$path_in,outputdir=$path_out)
 					  summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=" . $GLOBALS['group_sam'] . ", all=T ,inputdir=$path_in,outputdir=$path_out)";

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

            $loop = false;
            venn($user, $project, $path_in, $path_out);
            //break;


        }
    }
}

#3
function venn($user, $project, $path_in, $path_out)
{

    echo "venn" . "\n";

    $jobname = $user . "_venn";

    $make = " venn(shared=final.opti_mcc." . $GLOBALS['level'] . ".subsample.shared, groups=" . $GLOBALS['group_ven'] . ",inputdir=$path_in,outputdir=$path_out)";

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

            $loop = false;
            tree_shared($user, $project, $path_in, $path_out);
            //break;


        }
    }
}

#4
function tree_shared($user, $project, $path_in, $path_out)
{

    echo "tree_shared" . "\n";
    $jobname = $user . "_tree_shared";

    $make = "";
    # community structure
    if ($GLOBALS['d_upgma_st'] != "0") {
        $d_upgma_st = explode(",", $GLOBALS['d_upgma_st']);
        for ($i = 0; $i < sizeof($d_upgma_st); $i++) {

            $make .= "tree.shared(phylip=final.opti_mcc." . $d_upgma_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist ,inputdir=$path_in,outputdir=$path_out)" . "\n";
            $GLOBALS['tree_cal'] .= $d_upgma_st[$i] . ",";

        }
    }
    # community membership
    if ($GLOBALS['d_upgma_me'] != "0") {
        $d_upgma_me = explode(",", $GLOBALS['d_upgma_me']);
        for ($i = 0; $i < sizeof($d_upgma_me); $i++) {

            $make .= "tree.shared(phylip=final.opti_mcc." . $d_upgma_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist ,inputdir=$path_in,outputdir=$path_out)" . "\n";
            $GLOBALS['tree_cal'] .= $d_upgma_me[$i] . ",";

        }
    }


    if ($make != "") {

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

                if ($GLOBALS['check'] == "pcoa") {

                    $loop = false;
                    pcoa($user, $project, $path_in, $path_out);
                    //break;
                } elseif ($GLOBALS['check'] == "nmds") {

                    $loop = false;
                    nmds($user, $project, $path_in, $path_out);
                    //break;
                }

            }
        }

    } elseif ($make == "") {

        echo "Not command tree_shared !" . "\n";
    }

}

#5
function pcoa($user, $project, $path_in, $path_out)
{

    echo "pcoa" . "\n";
    $jobname = $user . "_pcoa";
    $make = "";
    # community structure
    if ($GLOBALS['d_pcoa_st'] != "0") {
        $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
        for ($i = 0; $i < sizeof($d_pcoa_st); $i++) {

            $make .= "pcoa(phylip=final.opti_mcc." . $d_pcoa_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist ,inputdir=$path_in,outputdir=$path_out)" . "\n";
             
             $GLOBALS['value_method'] .= $d_pcoa_st[$i]." ";
        }
    }
    # community membership
    if ($GLOBALS['d_pcoa_me'] != "0") {
        $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
        for ($i = 0; $i < sizeof($d_pcoa_me); $i++) {

            $make .= "pcoa(phylip=final.opti_mcc." . $d_pcoa_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist ,inputdir=$path_in,outputdir=$path_out)" . "\n";
           
            $GLOBALS['value_method'] .= $d_pcoa_st[$i]." ";
        }
    }

    # run command
    if ($make != "") {

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

                $loop = false;
                amova_homova($user, $project, $path_in, $path_out);
                //break;

            }
        }

    } elseif ($make == "") {

        echo "Not command pcoa !" . "\n";

    }

}

#6
function nmds($user, $project, $path_in, $path_out)
{

    echo "nmds" . "\n";
    $jobname = $user . "_nmds";

    $make = "";
    #community Structure
    if ($GLOBALS['d_nmds_st'] != "0") {
        $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
        for ($i = 0; $i < sizeof($d_nmds_st); $i++) {

            $make .= "nmds(phylip=final.opti_mcc." . $d_nmds_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, mindim=" . $GLOBALS['nmds'] . ", maxdim=" . $GLOBALS['nmds'] . " ,inputdir=$path_in,outputdir=$path_out)" . "\n";
             
              $GLOBALS['value_method'] .= $d_nmds_st[$i]." ";
        }
    }
    # community membership
    if ($GLOBALS['d_nmds_me'] != "0") {
        $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
        for ($i = 0; $i < sizeof($d_nmds_me); $i++) {

            $make .= "nmds(phylip=final.opti_mcc." . $d_nmds_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, mindim=" . $GLOBALS['nmds'] . ", maxdim=" . $GLOBALS['nmds'] . " ,inputdir=$path_in,outputdir=$path_out)" . "\n";

             $GLOBALS['value_method'] .= $d_nmds_st[$i]." ";
        }
    }
    # run command
    if ($make != "") {

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
                
                $loop = false;
                amova_homova($user, $project, $path_in, $path_out);
                //break;

            }
        }

    } elseif ($make == "") {

        echo "Not command nmds !" . "\n";
    

    }


}


# start Options 7.1
#7 use file design
function amova_homova($user, $project, $path_in, $path_out){

    echo "amova_homova_anosim" . "\n";
    $jobname = $user . "_amova_homova_anosim";

    $make = "";

    # Amova
    if ($GLOBALS['amova'] == "amova" && $GLOBALS['file_design'] != "0") {
        # PCoA
        if ($GLOBALS['check'] == "pcoa") {
            # community structure
            if ($GLOBALS['d_pcoa_st'] != "0") {

                $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                for ($i = 0; $i < sizeof($d_pcoa_st); $i++) {

                    $make .= "amova(phylip=final.opti_mcc." . $d_pcoa_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";
                }
            }
            # community membership
            if ($GLOBALS['d_pcoa_me'] != "0") {

                $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                for ($i = 0; $i < sizeof($d_pcoa_me); $i++) {

                    $make .= "amova(phylip=final.opti_mcc." . $d_pcoa_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";

                }
            }
            # NMDS
        } elseif ($GLOBALS['check'] == "nmds") {
            # community structure
            if ($GLOBALS['d_nmds_st'] != "0") {
                $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                for ($i = 0; $i < sizeof($d_nmds_st); $i++) {

                    $make .= "amova(phylip=final.opti_mcc." . $d_nmds_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";
                }
            }
            # community membership
            if ($GLOBALS['d_nmds_me'] != "0") {
                $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                for ($i = 0; $i < sizeof($d_nmds_me); $i++) {

                    $make .= "amova(phylip=final.opti_mcc." . $d_nmds_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";

                }
            }


        }

    } 
    # Homova
    if($GLOBALS['homova'] == "homova" && $GLOBALS['file_design'] != "0") {
        # PCoA
        if ($GLOBALS['check'] == "pcoa") {
            # community structure
            if ($GLOBALS['d_pcoa_st'] != "0") {

                $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                for ($i = 0; $i < sizeof($d_pcoa_st); $i++) {

                    $make .= "homova(phylip=final.opti_mcc." . $d_pcoa_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";

                }
            }
            # community membership
            if ($GLOBALS['d_pcoa_me'] != "0") {

                $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                for ($i = 0; $i < sizeof($d_pcoa_me); $i++) {


                    $make .= "homova(phylip=final.opti_mcc." . $d_pcoa_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";

                }
            }

        }# NMDS
        elseif ($GLOBALS['check'] == "nmds") {
            # community structure
            if ($GLOBALS['d_nmds_st'] != "0") {
                $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                for ($i = 0; $i < sizeof($d_nmds_st); $i++) {

                    $make .= "homova(phylip=final.opti_mcc." . $d_nmds_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";
                }
            }
            # community membership
            if ($GLOBALS['d_nmds_me'] != "0") {
                $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                for ($i = 0; $i < sizeof($d_nmds_me); $i++) {

                    $make .= "homova(phylip=final.opti_mcc." . $d_nmds_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";

                }
            }


        }

    }

    # Anosim
    if ($GLOBALS['anosim'] == "anosim" && $GLOBALS['file_design'] != "0") {
          # PCoA
          if ($GLOBALS['check'] == "pcoa") {
            # community structure
            if ($GLOBALS['d_pcoa_st'] != "0") {

                $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                for ($i = 0; $i < sizeof($d_pcoa_st); $i++) {

                    $make .= "homova(phylip=final.opti_mcc." . $d_pcoa_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";

                }
            }
            # community membership
            if ($GLOBALS['d_pcoa_me'] != "0") {

                $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                for ($i = 0; $i < sizeof($d_pcoa_me); $i++) {


                    $make .= "homova(phylip=final.opti_mcc." . $d_pcoa_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";

                }
            }

        }# NMDS
        elseif ($GLOBALS['check'] == "nmds") {
            # community structure
            if ($GLOBALS['d_nmds_st'] != "0") {
                $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                for ($i = 0; $i < sizeof($d_nmds_st); $i++) {

                    $make .= "homova(phylip=final.opti_mcc." . $d_nmds_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";
                }
            }
            # community membership
            if ($GLOBALS['d_nmds_me'] != "0") {
                $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                for ($i = 0; $i < sizeof($d_nmds_me); $i++) {

                    $make .= "homova(phylip=final.opti_mcc." . $d_nmds_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.dist, design=" . $GLOBALS['file_design'] . ",inputdir=$path_in,outputdir=$path_out)" . "\n";

                }
            }
        }
    }

    # run command
    if ($make != ""){

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

                $loop = false;
                corr_axes($user, $project, $path_in, $path_out);
                //break;
            }
        }
     } elseif ($make == "") {

        echo "Not command amova_homova_anosim !" . "\n";
        parsimony($user, $project, $path_in, $path_out);
        

    }
}

#8 use file metadata
function corr_axes($user, $project, $path_in, $path_out){

    echo "corr_axes" . "\n";
    $jobname = $user . "_corr_axes";
    $make = "";

    # metadata
    if ($GLOBALS['correlation_meta'] == "meta" && $GLOBALS['file_metadata'] != "0") {

        # PCoA
        if ($GLOBALS['check'] == "pcoa") {
            # Community structure
            if ($GLOBALS['d_pcoa_st'] != "0") {
                $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                for ($i = 0; $i < sizeof($d_pcoa_st); $i++) {

                    $make .= "corr.axes(axes=final.opti_mcc." . $d_pcoa_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.pcoa.axes, metadata=" . $GLOBALS['file_metadata'] . ", method=" . $GLOBALS['method_meta'] . ", numaxes=3, label=" . $GLOBALS['level'] . ",inputdir=$path_in,outputdir=$path_out)
                      system(mv " . $path_out . "file." . $GLOBALS['method_meta'] . ".corr.axes " . $path_out . "file." . $GLOBALS['method_meta'] . ".corr.axes_" . $d_pcoa_st[$i] . ")" . "\n";

                
                }
            }
            # Community membership
            if ($GLOBALS['d_pcoa_me'] != "0") {
                $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                for ($i = 0; $i < sizeof($d_pcoa_me); $i++) {

                    $make .= "corr.axes(axes=final.opti_mcc." . $d_pcoa_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.pcoa.axes, metadata=" . $GLOBALS['file_metadata'] . ", method=" . $GLOBALS['method_meta'] . ", numaxes=3, label=" . $GLOBALS['level'] . ",inputdir=$path_in,outputdir=$path_out)
                      system(mv " . $path_out . "file." . $GLOBALS['method_meta'] . ".corr.axes " . $path_out . "file." . $GLOBALS['method_meta'] . ".corr.axes_" . $d_pcoa_me[$i] . ")" . "\n";

                }
            }

        } # NMDS
        elseif ($GLOBALS['check'] == "nmds") {
            # Community structure
            if ($GLOBALS['d_nmds_st'] != "0") {
                $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                for ($i = 0; $i < sizeof($d_nmds_st); $i++) {

                    $make .= "corr.axes(axes=final.opti_mcc." . $d_nmds_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.nmds.axes, metadata=" . $GLOBALS['file_metadata'] . ", method=" . $GLOBALS['method_meta'] . ", numaxes=3, label=" . $GLOBALS['level'] . ",inputdir=$path_in,outputdir=$path_out)
                       system(mv " . $path_out . "file." . $GLOBALS['method_meta'] . ".corr.axes " . $path_out . "file." . $GLOBALS['method_meta'] . ".corr.axes_" . $d_nmds_st[$i] . ")" . "\n";

               
                }
            }
            # Community membership
            if ($GLOBALS['d_nmds_me'] != "0") {
                $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                for ($i = 0; $i < sizeof($d_nmds_me); $i++) {

                    $make .= "corr.axes(axes=final.opti_mcc." . $d_nmds_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.nmds.axes, metadata=" . $GLOBALS['file_metadata'] . ", method=" . $GLOBALS['method_meta'] . ", numaxes=3, label=" . $GLOBALS['level'] . ",inputdir=$path_in,outputdir=$path_out)
                      system(mv " . $path_out . "file." . $GLOBALS['method_meta'] . ".corr.axes " . $path_out . "file." . $GLOBALS['method_meta'] . ".corr.axes_" . $d_nmds_me[$i] . ")" . "\n";

               
                }
            }

        }


    }

    # otu
    if ($GLOBALS['correlation_otu'] == "otu") {

        # PCoA
        if ($GLOBALS['check'] == "pcoa") {
            # Community structure
            if ($GLOBALS['d_pcoa_st'] != "0") {
                $d_pcoa_st = explode(",", $GLOBALS['d_pcoa_st']);
                for ($i = 0; $i < sizeof($d_pcoa_st); $i++) {

                    $make .= "corr.axes(axes=final.opti_mcc." . $d_pcoa_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.pcoa.axes, shared=final.opti_mcc." . $GLOBALS['level'] . ".subsample.shared, method=" . $GLOBALS['method_otu'] . ", numaxes=3, label=" . $GLOBALS['level'] . ",inputdir=$path_in,outputdir=$path_out)
                      system(mv " . $path_out . "final.opti_mcc." . $GLOBALS['level'] . ".subsample." . $GLOBALS['method_otu'] . ".corr.axes " . $path_out . "final.opti_mcc." . $GLOBALS['level'] . ".subsample." . $GLOBALS['method_otu'] . ".corr.axes_" . $d_pcoa_st[$i] . ")" . "\n";

                
                }
            }
            # Community membership
            if ($GLOBALS['d_pcoa_me'] != "0") {
                $d_pcoa_me = explode(",", $GLOBALS['d_pcoa_me']);
                for ($i = 0; $i < sizeof($d_pcoa_me); $i++) {

                   $make .= "corr.axes(axes=final.opti_mcc." . $d_pcoa_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.pcoa.axes, shared=final.opti_mcc." . $GLOBALS['level'] . ".subsample.shared, method=" . $GLOBALS['method_otu'] . ", numaxes=3, label=" . $GLOBALS['level'] . ",inputdir=$path_in,outputdir=$path_out)
                      system(mv " . $path_out . "final.opti_mcc." . $GLOBALS['level'] . ".subsample." . $GLOBALS['method_otu'] . ".corr.axes " . $path_out . "final.opti_mcc." . $GLOBALS['level'] . ".subsample." . $GLOBALS['method_otu'] . ".corr.axes_" . $d_pcoa_me[$i] . ")" . "\n";

                }
            }

        }
        # NMDS
        elseif ($GLOBALS['check'] == "nmds") {
            # Community structure
            if ($GLOBALS['d_nmds_st'] != "0") {
                $d_nmds_st = explode(",", $GLOBALS['d_nmds_st']);
                for ($i = 0; $i < sizeof($d_nmds_st); $i++) {

                    $make .= "corr.axes(axes=final.opti_mcc." . $d_nmds_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.nmds.axes, shared=final.opti_mcc." . $GLOBALS['level'] . ".subsample.shared, method=" . $GLOBALS['method_otu'] . ", numaxes=3, label=" . $GLOBALS['level'] . ",inputdir=$path_in,outputdir=$path_out)
                      system(mv " . $path_out . "final.opti_mcc." . $GLOBALS['level'] . ".subsample." . $GLOBALS['method_otu'] . ".corr.axes " . $path_out . "final.opti_mcc." . $GLOBALS['level'] . ".subsample." . $GLOBALS['method_otu'] . ".corr.axes_" . $d_nmds_st[$i] . ")" . "\n";

                  
                }
            }
            # Community membership
            if ($GLOBALS['d_nmds_me'] != "0") {
                $d_nmds_me = explode(",", $GLOBALS['d_nmds_me']);
                for ($i = 0; $i < sizeof($d_nmds_me); $i++) {

                    $make .= "corr.axes(axes=final.opti_mcc." . $d_nmds_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.nmds.axes, shared=final.opti_mcc." . $GLOBALS['level'] . ".subsample.shared, method=" . $GLOBALS['method_otu'] . ", numaxes=3, label=" . $GLOBALS['level'] . ",inputdir=$path_in,outputdir=$path_out)
                      system(mv " . $path_out . "final.opti_mcc." . $GLOBALS['level'] . ".subsample." . $GLOBALS['method_otu'] . ".corr.axes " . $path_out . "final.opti_mcc." . $GLOBALS['level'] . ".subsample." . $GLOBALS['method_otu'] . ".corr.axes_" . $d_nmds_me[$i] . ")" . "\n";

                    
                }
            }

        }
    }

    # run command
    if ($make != "") {

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
                
                $loop = false;
                parsimony($user, $project, $path_in, $path_out);
                //break;

            }
        }

    } elseif ($make == "") {

        echo "Not command corr_axes !" . "\n";
        parsimony($user, $project, $path_in, $path_out);
     
    }
}


#9 use file design
function parsimony($user, $project, $path_in, $path_out){

    echo "parsimony" . "\n";

    $jobname = $user . "_parsimony";

    $make = "";

    # community structure
    if ($GLOBALS['d_upgma_st'] != "0" && $GLOBALS['file_design'] != "0") {
        $d_upgma_st = explode(",", $GLOBALS['d_upgma_st']);
        for ($i = 0; $i < sizeof($d_upgma_st); $i++) {

            $make .= "parsimony(tree=final.opti_mcc." . $d_upgma_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.tre, group=" . $GLOBALS['file_design'] . ", groups=all ,inputdir=$path_in,outputdir=$path_out)" . "\n";
        }
    }
    # community membership
    if ($GLOBALS['d_upgma_me'] != "0" && $GLOBALS['file_design'] != "0") {
        $d_upgma_me = explode(",", $GLOBALS['d_upgma_me']);
        for ($i = 0; $i < sizeof($d_upgma_me); $i++) {

            $make .= "parsimony(tree=final.opti_mcc." . $d_upgma_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.tre, group=" . $GLOBALS['file_design'] . ", groups=all ,inputdir=$path_in,outputdir=$path_out)" . "\n";

        }
    }

    if ($make != "") {
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

                $loop = false;
                heatmap_bin_sim($user, $project, $path_in, $path_out);
                //break;


            }
        }
    } elseif ($make == "") {

        echo "Not command parsimony !" . "\n";
        heatmap_bin_sim($user, $project, $path_in, $path_out);  
    }
}

#10
# hide output
function heatmap_bin_sim($user, $project, $path_in, $path_out)
{

    echo "heatmap_bin_sim" . "\n";

    $jobname = $user . "_heatmap_bin_sim";

    $path_out_hidden = "owncloud/data/".$user."/files/".$project."/hiddencommand/";

    $make = "heatmap.bin(shared=final.opti_mcc." . $GLOBALS['level'] . ".subsample.shared, scale=log2, numotu=50 ,inputdir=$path_out,outputdir=$path_out_hidden)
                  heatmap.sim(phylip=final.opti_mcc.thetayc." . $GLOBALS['level'] . ".lt.ave.dist ,inputdir=$path_out,outputdir=$path_out_hidden)
                  heatmap.sim(phylip=final.opti_mcc.jclass." . $GLOBALS['level'] . ".lt.ave.dist ,inputdir=$path_out,outputdir=$path_out_hidden)";

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
            
            $loop = false;
            unifrac($user, $project, $path_in, $path_out);
            //break;
        }
    }
}

#11 use file design
# hide output
function unifrac($user, $project, $path_in, $path_out){


    echo "unifrac" . "\n";

    $jobname = $user . "_unifrac";
    $path_out_hidden = "owncloud/data/".$user."/files/".$project."/hiddencommand/";

    $make = "";

    # community structure
    if ($GLOBALS['d_upgma_st'] != "0" && $GLOBALS['file_design'] != "0") {
        $d_upgma_st = explode(",", $GLOBALS['d_upgma_st']);
        for ($i = 0; $i < sizeof($d_upgma_st); $i++) {

            $make .= "unifrac.weighted(tree=final.opti_mcc." . $d_upgma_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.tre, group=" . $GLOBALS['file_design'] . ", random=T ,inputdir=$path_in,outputdir=$path_out)
                      unifrac.unweighted(tree=final.opti_mcc." . $d_upgma_st[$i] . "." . $GLOBALS['level'] . ".lt.ave.tre, group=" . $GLOBALS['file_design'] . ", random=T, groups=all ,inputdir=$path_in,outputdir=$path_out)" . "\n";

        }
    }
    # community membership
    if ($GLOBALS['d_upgma_me'] != "0" && $GLOBALS['file_design'] != "0") {
        $d_upgma_me = explode(",", $GLOBALS['d_upgma_me']);
        for ($i = 0; $i < sizeof($d_upgma_me); $i++) {

            $make .= "unifrac.weighted(tree=final.opti_mcc." . $d_upgma_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.tre, group=" . $GLOBALS['file_design'] . ", random=T ,inputdir=$path_in,outputdir=$path_out)
                      unifrac.unweighted(tree=final.opti_mcc." . $d_upgma_me[$i] . "." . $GLOBALS['level'] . ".lt.ave.tre, group=" . $GLOBALS['file_design'] . ", random=T, groups=all ,inputdir=$path_in,outputdir=$path_out)" . "\n";

        }
    }

    if ($make != "") {
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

                 $loop = false;
                moveoutput_unifrac($path_out,$path_out_hidden);
                remove_logfile_mothur($path_out);
                create_file_input_heatmap($user, $project, $path_in, $path_out);
                //break;


            }
        }
    } elseif ($make == "") {

        echo "Not command unifrac !" . "\n";

        remove_logfile_mothur($path_out);
        create_file_input_heatmap($user, $project, $path_in, $path_out);
     

    }


}

function moveoutput_unifrac($path_out,$path_out_hidden){

        $path_dir =  $path_out;
        if (is_dir($path_dir)) {
            if ($read = opendir($path_dir)){
                  while (($file = readdir($read)) !== false) {
                        
                    $allowed =  array('trewsummary','weighted','uwsummary','unweighted');
                    $ext = pathinfo($file, PATHINFO_EXTENSION);

                    if(in_array($ext,$allowed)) {
                           
                       copy($path_dir.$file,$path_out_hidden.$file);
                       unlink($path_dir.$file);
                      }
                   }
     
                   closedir($read);
            }

        }

  }

# End Options 7.1

# Remove log mothur

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
}


#12
#Graph
function create_file_input_heatmap($user, $project, $path_in, $path_out){

    echo "create_file_input_heatmap" . "\n";
   

    $file_input = "owncloud/data/$user/files/$project/output_plot/final.opti_mcc.0.03.cons.tax.summary";


    $jobname = $user . "_create_file_input_heatmap";
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log  -cwd -j y -b y /usr/bin/php -f R_Script/create_input_heatmap_otu.php $user $project $file_input";

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

             $loop = false;
            create_file_input_abun($user, $project, $path_in, $path_out);
            //break;
        }
    }

}

#13
function create_file_input_abun($user, $project, $path_in, $path_out){

    echo "create_file_input_abun " . "\n";

    $file_input = "owncloud/data/$user/files/$project/output_plot/final.opti_mcc.0.03.cons.tax.summary";


    $jobname = $user . "_create_file_input_abun";
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/php -f R_Script/create_input_abundance_otu.php $user $project $file_input";

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

             $loop = false;
            create_input_alphash($user, $project, $path_in, $path_out);
            //break;
        }
    }

}

#14
function create_input_alphash($user, $project, $path_in, $path_out)
{

    echo "create_input_alphash " . "\n";
    $jobname = $user . "_create_input_alphash";

    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/php -f R_Script/create_input_alphash_otu.php $user $project";

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

            if($GLOBALS['correlation_otu'] != "0"){
                 $loop = false;
                 create_input_biplot($user,$project,$path_in,$path_out);
            }else{
                 $loop = false;
                 plot_graph_r_heatmap($user,$project,$path_in,$path_out);
            }
          
        }
    }

}


#15  Select correlation OTU  ==> 
     # create (( file output_biplot ))  ==>
     # into generate graph  BiplotwithOTU

function create_input_biplot($user, $project, $path_in, $path_out){

    echo "create_input_biplot " . "\n";

    $jobname = $user . "_create_input_biplot";
    $log = $GLOBALS['path_log'];

      $level = $GLOBALS['level'];
      $method_otu = $GLOBALS['method_otu'];
      $calculator = $GLOBALS['value_method'];

      $data_cal = trim($calculator);
      $val_replace = str_replace(" ", ",", $data_cal);



    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/php -f R_Script/create_biplot_otu_advance.php $user $project $level $method_otu $val_replace";

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

             $loop = false;
            plot_graph_r_heatmap($user, $project, $path_in, $path_out);
            //break;
        }
    }

}


#16
function plot_graph_r_heatmap($user, $project, $path_in, $path_out)
{


    echo "plot_graph_r_heatmap " . "\n";

    $path_input_csv = "owncloud/data/$user/files/$project/output/file_after_reverse.csv";
    #$path_to_save = "owncloud/data/$user/files/$project/output/heartmap.png";
    $path_to_save = "data_report_mothur/$user/$project/taxonomy_classification/heartmap.svg";
    $jobname = $user . "_plot_graph_r_heartmap";

    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log  -cwd -j y -b y /usr/bin/Rscript R_Script/Heatmap_graph.R $path_input_csv $path_to_save";

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

             $loop = false;
            plot_graph_r_NMD($user, $project, $path_in, $path_out);
            //break;
        }
    }

}

#17
function plot_graph_r_NMD($user, $project, $path_in, $path_out){

    echo "plot_graph_r_NMD " . "\n";

    # Calculator PCoA or NMDS
      $calculator = $GLOBALS['value_method'];
      $cal = trim($calculator);
    
      $cal_replace = explode(" ", $cal);
      $path_input_axes_name = null;


    # PCoA OR NMDS
    if ($GLOBALS['check'] == "pcoa") {

        for ($i = 0; $i < sizeof($cal_replace); $i++) {

            $path_input_axes_name .= "owncloud/data/$user/files/$project/output/final.opti_mcc." . $cal_replace[$i] . "." . $GLOBALS['level'] . ".lt.ave.pcoa.axes" . "-" . "data_report_mothur/$user/$project/beta_diversity_analysis/PCoA_" . $cal_replace[$i] . ".svg" . " ";

        }

    } else if ($GLOBALS['check'] == "nmds") {

        for ($i = 0; $i < sizeof($cal_replace); $i++) {

            $path_input_axes_name .= "owncloud/data/$user/files/$project/output/final.opti_mcc." . $cal_replace[$i] . "." . $GLOBALS['level'] . ".lt.ave.nmds.axes" . "-" . "data_report_mothur/$user/$project/beta_diversity_analysis/NMD_" . $cal_replace[$i] . ".svg" . " ";

        }
    }

    $path_input_axes_name_trim = trim($path_input_axes_name);
    $path_input_axes = str_replace(" ", ",", $path_input_axes_name_trim);


    $jobname = $user . "_plot_graph_r_NMD";
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/php -f  R_Script/nmds_pcoa_plot_advance.php $path_input_axes";

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

             $loop = false;
            plot_graph_r_Rare($user, $project, $path_in, $path_out);
            //break;
        }
    }

}

#18
function plot_graph_r_Rare($user, $project, $path_in, $path_out)
{


    echo "plot_graph_r_Rare " . "\n";

    $path_input_rarefaction = "owncloud/data/$user/files/$project/output/final.opti_mcc.groups.rarefaction";
    
    $path_to_save = "data_report_mothur/$user/$project/alpha_diversity_analysis/Rare.svg";
    $jobname = $user . "_plot_graph_r_Rare";

    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/Rarefaction_graph_otu.R $path_input_rarefaction $path_to_save";

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

             $loop = false;
            plot_graph_r_Abun($user, $project, $path_in, $path_out);
            //break;
        }
    }

}


#19
function plot_graph_r_Abun($user, $project, $path_in, $path_out){


    echo "plot_graph_r_Abun " . "\n";

    $path_input_phylumex = "owncloud/data/$user/files/$project/output/file_phylum_count.txt";
    $path_to_save = "data_report_mothur/$user/$project/taxonomy_classification/Abun.svg";

       $path = $path_input_phylumex;
        $read = fopen($path,"r") or die ("unable to open file");
        $count_line = 0;
        $count_sample = 0;
        $count_phylum = 0;

        while(($line = fgets($read)) !== false){

            if($count_line == 0){
               $data = explode("\t", $line);
               array_splice($data, 0,1);
               $count_sample = count($data);
            }
        
            $count_line++;
        }
        fclose($read);
        $count_phylum = $count_line-1;

    $jobname = $user . "_plot_graph_r_Abun";
    $log = $GLOBALS['path_log'];
    $cmd = "";

     if($count_sample <= 10 && $count_phylum <= 40){
         
         $ncol = 2;
         $width = 7;
         $height = 6 ;
 
         #AbundancebarplotModibar_new.R
         $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/AbundancebarplotModibar_new.R $path_input_phylumex $path_to_save $ncol $width $height";

      }elseif ($count_sample <= 10 && $count_phylum > 40) {
         
         $ncol = 3;
         $width = 7;
         $height = 6;
         
         #AbundancebarplotModibar_new.R
         $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/AbundancebarplotModibar_new.R $path_input_phylumex $path_to_save $ncol $width $height";

      }elseif($count_sample > 10){
         
         #Abundance_bar_graph.R
         $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/Abundance_bar_graph.R $path_input_phylumex $path_to_save";
      }




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

             $loop = false;
            plot_graph_r_Alphash($user, $project, $path_in, $path_out);
            //break;
        }
    }

}

#20
function plot_graph_r_Alphash($user, $project, $path_in, $path_out)
{


    echo "plot_graph_r_Alphash " . "\n";

    $path_input_chao_shannon = "owncloud/data/$user/files/$project/output/file_after_chao.txt";
    $path_to_save = "data_report_mothur/$user/$project/alpha_diversity_analysis/Alpha.svg";

    $jobname = $user . "_plot_graph_r_Alphash";

    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/Alpha_chaoshannon_graph.R $path_input_chao_shannon $path_to_save";

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

          if(($GLOBALS['correlation_meta'] != "0") || ($GLOBALS['correlation_otu'] != "0")){
               $loop = false;
               plot_graph_r_Biplot($user,$project,$path_in,$path_out);
               //break;
          }else{
               $loop = false;
               plot_graph_r_Tree($user,$project,$path_in,$path_out);
               //break;
          }
        }
    }

}


#21 check correlation metadata and otu
function plot_graph_r_Biplot($user, $project, $path_in, $path_out){

    echo "plot_graph_r_Biplot" . "\n";
    $jobname = $user . "_plot_graph_r_Biplot";
    $log = $GLOBALS['path_log'];
   
    # check get variable correlation metadata and correlation otu
    $numcheck = null;
  
    $calculator = $GLOBALS['value_method'];
    $cal = trim($calculator);
    
    $cal_replace = explode(" ", $cal);
    $path_data = null;


    #pcoa 
    if ($GLOBALS['check'] == "pcoa") {


      #correlation metadata
      if(($GLOBALS['correlation_meta'] != "0") && ($GLOBALS['correlation_otu'] == "0")){

          $numcheck = "3meta";
          for ($i = 0; $i < sizeof($cal_replace); $i++) {
               $path_data .= "owncloud/data/$user/files/$project/output/final.opti_mcc." . $cal_replace[$i] . "." . $GLOBALS['level'] . ".lt.ave.pcoa.axes-";
               $path_data .= "data_report_mothur/$user/$project/beta_diversity_analysis/PCoA_BiplotwithMetadata_" . $cal_replace[$i] . ".svg-";
               $path_data .= "owncloud/data/$user/files/$project/output/file." . $GLOBALS['method_meta'] . ".corr.axes_" . $cal_replace[$i] . ",";
          }

       }
      #correlation otu
      elseif(($GLOBALS['correlation_meta'] == "0") && ($GLOBALS['correlation_otu'] != "0")){
         
          $numcheck = "3otu";
           for($i = 0; $i < sizeof($cal_replace); $i++) {
                $path_data .= "owncloud/data/$user/files/$project/output/final.opti_mcc." . $cal_replace[$i] . "." . $GLOBALS['level'] . ".lt.ave.pcoa.axes-";
                $path_data .= "data_report_mothur/$user/$project/beta_diversity_analysis/PCoA_BiplotwithOTU_" . $cal_replace[$i] . ".svg-";
                $path_data .= "owncloud/data/$user/files/$project/output/output_biplot_" . $cal_replace[$i] . ".txt".",";
            } 

      }else{
           
           $numcheck = "5all";
           for($i = 0; $i < sizeof($cal_replace); $i++) {
                $path_data .= "owncloud/data/$user/files/$project/output/final.opti_mcc." .$cal_replace[$i] . "." . $GLOBALS['level'] . ".lt.ave.pcoa.axes-";
                $path_data .= "data_report_mothur/$user/$project/beta_diversity_analysis/PCoA_BiplotwithOTU_" . $cal_replace[$i] . ".svg-";
                $path_data .= "owncloud/data/$user/files/$project/output/output_biplot_" . $cal_replace[$i] . ".txt".",";
                $path_data .= "data_report_mothur/$user/$project/beta_diversity_analysis/PCoA_BiplotwithMetadata_" . $cal_replace[$i] . ".svg-";
                $path_data .= "owncloud/data/$user/files/$project/output/file." . $GLOBALS['method_meta'] . ".corr.axes_" . $cal_replace[$i] . ",";
           }
       }

    }

    #nmds
    else if ($GLOBALS['check'] == "nmds") {


      #correlation metadata
      if(($GLOBALS['correlation_meta'] != "0") && ($GLOBALS['correlation_otu'] == "0")){
        
         $numcheck = "3meta";
         for ($i = 0; $i < sizeof($cal_replace); $i++) {
             $path_data .= "owncloud/data/$user/files/$project/output/final.opti_mcc." . $cal_replace[$i] . "." . $GLOBALS['level'] . ".lt.ave.nmds.axes-";
             $path_data .= "data_report_mothur/$user/$project/beta_diversity_analysis/NMDS_BiplotwithMetadata_" . $cal_replace[$i] . ".svg-";
             $path_data .= "owncloud/data/$user/files/$project/output/file." . $GLOBALS['method_meta'] . ".corr.axes_" . $cal_replace[$i] . ",";
         }
      }

      #correlation otu
      elseif(($GLOBALS['correlation_meta'] == "0") && ($GLOBALS['correlation_otu'] != "0")){
         
         $numcheck = "3otu";
         for ($i = 0; $i < sizeof($cal_replace); $i++) {
               $path_data .= "owncloud/data/$user/files/$project/output/final.opti_mcc." . $cal_replace[$i] . "." . $GLOBALS['level'] . ".lt.ave.nmds.axes-";
               $path_data .= "data_report_mothur/$user/$project/beta_diversity_analysis/NMDS_BiplotwithOTU_" . $cal_replace[$i] . ".svg-";
               $path_data .= "owncloud/data/$user/files/$project/output/output_biplot_" . $cal_replace[$i] . ".txt".",";

         }      
    
      }else{

         $numcheck = "5all";
          for ($i = 0; $i < sizeof($cal_replace); $i++) {
            $path_data .= "owncloud/data/$user/files/$project/output/final.opti_mcc." . $cal_replace[$i] . "." . $GLOBALS['level'] . ".lt.ave.nmds.axes-";
            $path_data .= "data_report_mothur/$user/$project/beta_diversity_analysis/NMDS_BiplotwithOTU_" . $cal_replace[$i] . ".svg-";
            $path_data .= "owncloud/data/$user/files/$project/output/output_biplot_" . $cal_replace[$i] . ".txt-";
            $path_data .= "data_report_mothur/$user/$project/beta_diversity_analysis/NMDS_BiplotwithMetadata_" . $cal_replace[$i] . ".svg-";
            $path_data .= "owncloud/data/$user/files/$project/output/file." . $GLOBALS['method_meta'] . ".corr.axes_" . $cal_replace[$i] . ",";
          }
      }    
    }


   
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/php -f  R_Script/biplot_otu_advance.php $path_data $numcheck";

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

             $loop = false;
            plot_graph_r_Tree($user, $project, $path_in, $path_out);
            //break;
        }
    }

}

#22
function plot_graph_r_Tree($user, $project, $path_in, $path_out){

    echo "plot_graph_r_Tree" . "\n";

    $tree_cal = $GLOBALS['tree_cal'];
    $level = $GLOBALS['level'];

    $jobname = $user . "_plot_graph_r_Tree";
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/php -f R_Script/Tree_graph_otu_advance.php $tree_cal $level $user $project";


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

             $loop = false;
            make_biom($user, $project, $path_in, $path_out);
            //break;
        }
    }

}


#23
# Run make_biom for Mothur GO To PICRUSt  and STAMP 
function make_biom($user, $project, $path_in, $path_out){


    echo "make_biom" . "\n";
    $jobname = $user . "_make_biom";

    $make = "make.biom(shared=final.opti_mcc.shared, label=0.03, constaxonomy=final.opti_mcc.0.03.cons.taxonomy, reftaxonomy=gg_13_8_99.gg.tax, picrust=99_otu_map.txt,inputdir=$path_in,outputdir=$path_out)";

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

           $loop = false;
           if($GLOBALS['kegg'] != "0" &&
              $GLOBALS['sample_comparison'] != "0" &&
              $GLOBALS['statistical_test'] != "0"  &&  
              $GLOBALS['ci_method'] != "0" &&
              $GLOBALS['p_value'] != "0"){

               convert_biom($user, $project, $path_in, $path_out);

           }else{

              $loop = false;
              change_name($user,$project,$path_in,$path_out);
              remove_logfile_mothur2($path_out);
              remove_file_tree_sum($path_in);

           }  
        }
    }
}


#24
function convert_biom($user, $project, $path_in, $path_out){


    echo "convert_biom" . "\n";

    $jobname = $user . "_convert_biom";
    $log = $GLOBALS['path_log'];

    $path_input_biom = $path_out . "final.opti_mcc.0.03.biom";
    $path_output_txt = $path_out . "final.opti_mcc.0.03.txt";
    $path_output_biom = $path_out . "new.biom";

    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y picrust-1.1.1/scripts/convert_biom $path_input_biom $path_output_txt $path_output_biom";


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
            phylotype_picrust($user, $project, $path_in, $path_out);
            //break;
        }
    }


}

#25
function phylotype_picrust($user, $project, $path_in, $path_out){

    echo "phylotype_picrust" . "\n";

    $jobname = $user . "_phylotype_picrust";
    $log = $GLOBALS['path_log'];

    $path_input_biom = $path_out . "new.biom";
    $path_output_biom = $path_out . "normalized_otus.0.03.biom";


    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubMoPhylo5andpicrust_norm $path_input_biom $path_output_biom ";

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
            phylotype_picrust2($user,$project,$path_in,$path_out);
            //break;
        }
    }

}


#26
function phylotype_picrust2($user,$project,$path_in,$path_out){
   
   
  echo "phylotype_picrust2"."\n";

  $jobname = $user."_phylotype_picrust2";
  $log = $GLOBALS['path_log'];

  $normalized_otus = $path_out."normalized_otus.0.03.biom";
  $metagenome_predictions = $path_out."metagenome_predictions.0.03.biom";


    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubMoPhylo5andpicrust $normalized_otus $metagenome_predictions";

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
         while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){
                      $loop = false;
                      phylotype_picrust3($user,$project,$path_in,$path_out);
                      //break;  
                   }
          }   


}

#27
function phylotype_picrust3($user,$project,$path_in,$path_out){

  

  echo "phylotype_picrust3"."\n";

    $jobname = $user."_phylotype_picrust3";
    $log = $GLOBALS['path_log'];

      #  silva , rdp => $label = 1
      #  greengene   => $label = 2
      $label = $GLOBALS['label_num'];
      
  
     
  $metagenome_predictions = $path_out."metagenome_predictions.0.03.biom";

   # $L = Please select level of KEGG pathway  level 1,2 or 3
   $L = $GLOBALS['kegg'];

  $predicted_metagenomes = $path_out."predicted_metagenomes.0.03.".$L.".biom";
    

    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubMoPhylo5andpicrust1 $metagenome_predictions $label $predicted_metagenomes ";

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
         while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){
                      $loop = false;
                      biom_to_stamp($user,$project,$path_in,$path_out);
                      //break;  
                   }
          }   

}

#28
function biom_to_stamp($user,$project,$path_in,$path_out){


    echo "biom_to_stamp"."\n";

    $jobname = $user."_biom_to_stamp";
    $log = $GLOBALS['path_log'];

   # $L = Please select level of KEGG pathway  level 1,2 or 3
   $L = $GLOBALS['kegg'];

   $predicted_metagenomes = $path_out."predicted_metagenomes.0.03.".$L.".biom";

   $pathways = $path_out."pathways0.03".$L.".spf";
    

    $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y  picrust-1.1.1/scripts/qsubBiomtoStamp $predicted_metagenomes $pathways";

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
         while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){
                      $loop = false;
                      remove_float($user,$project,$path_in,$path_out);
                      //break;  
                   }
          }   

}


#29
function remove_float($user,$project,$path_in,$path_out){
    
    echo "remove_float"."\n";

    $jobname = $user."_remove_float";
    $log = $GLOBALS['path_log'];

     # $L = Please select level of KEGG pathway  level 1,2 or 3
     $L = $GLOBALS['kegg'];

    $pathways = $path_out."pathways0.03".$L.".spf";

    $cmd = "qsub -N '$jobname' -o $log -cwd -j y -b y  /usr/bin/php -f R_Script/replace_string.php $pathways";
   
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
            $loop = false;
            stamp($user,$project,$path_in,$path_out);
            //break;
        }
    }
}

#30
function stamp($user,$project,$path_in,$path_out){

    

    # $L = Please select level of KEGG pathway  level 1,2 or 3
    $L = $GLOBALS['kegg'];

    echo "stamp"."\n";

    $jobname = $user."_stamp";
    $log = $GLOBALS['path_log'];

    $pathways = "../".$path_out."pathways0.03".$L.".spf";
    $myResultsPathway = "../".$path_out."myResultsPathway".$L.".tsv";

     
    list($sample1,$sample2)=explode("--vs--",  $GLOBALS['sample_comparison']);
    $sample1 = $sample1;
    $sample2 = $sample2;

    #Selected statistical test : 
    $statistical_test = $GLOBALS['statistical_test'];

    if($GLOBALS['statistical_test'] == "Chi-square"  ){
        $statistical_test = "Chi-square test";
    }elseif ($GLOBALS['statistical_test'] =="Chi-square2" ) {
        $statistical_test = "Chi-square test (w/ Yates' correction)";
    }elseif ($GLOBALS['statistical_test'] == "Difference") {
        $statistical_test = "Difference between proportions";
    }elseif ($GLOBALS['statistical_test'] == "Fisher") {
      $statistical_test = "Fisher's exact test";
    }elseif ($GLOBALS['statistical_test'] == "G‐test2" ) {
      $statistical_test = "G-test (w/ Yates' correction)";
    }

    # CI method : 
    $ci_method = null;

    if($GLOBALS['ci_method'] == "DP1"){
         $ci_method = "DP: Newcombe-Wilson";
    }elseif ($GLOBALS['ci_method'] == "DP2") {
        $ci_method = "DP: Asymptotic";
    }elseif ($GLOBALS['ci_method'] == "DP3") {
        $ci_method = "DP: Asymptotic-CC";
    }elseif ($GLOBALS['ci_method'] == "OR1") {
        $ci_method = "OR: Haldane adjustment";
    }else{
        $ci_method = "RP: Asymptotic";
    }

    $p_value = $GLOBALS['p_value'];


    

    $function = '/root/anaconda2/bin/python  STAMP-1.8/commandLine.py --file '.$pathways.' --sample1 '.$sample1.' --sample2 '.$sample2.' --statTest "'.$statistical_test.'" --CI "'.$ci_method.'" -p '.$p_value.' --coverage 0.95 --outputTable '.$myResultsPathway.'';

     file_put_contents($path_in.'qsubStamp.sh', $function);
     chmod($path_in.'qsubStamp.sh',0775);
     $getPath = "../".$path_in."qsubStamp.sh";

     $cmd = "qsub -N '$jobname' -o $log  -cwd -j y -b y STAMP-1.8/$getPath";

    
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
         while ($loop) {

                   $check_run = exec("qstat -j $id_job");

                   if($check_run == false){
                      
                      $loop = false;
                      plot_STAMP($user,$project,$path_in,$path_out,$sample1,$sample2);
                      //break;  
                   }
          }   


}

#31
function plot_STAMP($user,$project,$path_in,$path_out,$sample1,$sample2){

    echo "plot_STAMP"."\n";

    # $L = Please select level of KEGG pathway  level 1,2 or 3
    $L = $GLOBALS['kegg'];

    $myResultsPathway = "owncloud/data/$user/files/$project/output/myResultsPathway".$L.".tsv";
    
    $path_to_save = "data_report_mothur/$user/$project/optional_output/bar_plot_STAMP.svg";


    $jobname = $user ."_plot_STAMP";
    $log = $GLOBALS['path_log'];
    $cmd = "qsub -N $jobname -o $log -cwd -j y -b y /usr/bin/Rscript  R_Script/barplotwitherrorstampModi.R $myResultsPathway $path_to_save $sample1 $sample2";
   
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
             
            $loop = false;
            keep_file_report($user,$project,$path_in,$path_out);
            //break;
        }
    }

}






#32 No print name function to log sungrid
function keep_file_report($user,$project,$path_in,$path_out){

 
        $folder_project = "Log_report/$user/$project/";   
        if (!file_exists($folder_project)){
            mkdir( $folder_project, 0777, true);
        }

        # copy log sungride makesummary to Log_report
        $MakeSummary_file = null;
        $path_summary = "owncloud/data/$user/files/$project/log_full/";
        $file_log = $path_summary;
            if(is_dir($file_log)) {
                if($read = opendir($file_log)){
                       while (($summary = readdir($read)) !== false) {

                         # makesummary
                         if(stripos($summary,'makesummary') !== false){
                             
                             $MakeSummary_file = $summary;
                             $copy_makesummary = $path_summary.$summary;
                             $makesummary = "Log_report/$user/$project/".$summary;
                             copy($copy_makesummary,$makesummary);
                         }
                      }
                    closedir($read);
                }
            }


        # Move file ruu mothur  << count.groups >>

         $copy_file3 = "owncloud/data/$user/files/$project/output/stability.trim.contigs.good.good.count_table";
         if(file_exists($copy_file3)){
                $file3 = "Log_report/$user/$project/stability.trim.contigs.good.good.count_table";
                copy($copy_file3,$file3);
         }


         $copy_file4 = "owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table";
         if(file_exists($copy_file4)){
                $file4 = "Log_report/$user/$project/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table";
                copy($copy_file4,$file4);
         }


         $copy_file5 = "owncloud/data/$user/files/$project/output/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table";
         if(file_exists($copy_file5)){
                $file5 = "Log_report/$user/$project/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table";
                copy($copy_file5,$file5);
         }


        $jobname = $user."_keep_file_report";
        $log = $GLOBALS['path_log'];

        $make = "count.groups(count=stability.trim.contigs.good.good.count_table,inputdir=Log_report/$user/$project/,outputdir=Log_report/$user/$project/)
         count.groups(count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table,inputdir=Log_report/$user/$project/,outputdir=Log_report/$user/$project/)
         count.groups(count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,inputdir=Log_report/$user/$project/,outputdir=Log_report/$user/$project/)";

        file_put_contents('Log_report/'.$user.'/'.$project.'/colum_full.batch', $make);

        $log = $GLOBALS['path_log'];
        $cmd = "qsub  -N '$jobname' -o $log  -cwd -j y -b y Mothur/mothur Log_report/$user/$project/colum_full.batch";
              
        shell_exec($cmd);


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
                   
                  $loop = false;
                  change_name($user,$project,$path_in,$path_out);
                  remove_logfile_mothur2($path_out);
                  remove_file_tree_sum($path_in);
                  //break;
               }
          }

}




#33
function change_name($user, $project, $path_in, $path_out)
{
    echo "change_name" . "\n";
    $dir = $path_out;
    $file_read = array('svg','sharedotus');
    $scan_result = scandir($dir);

    foreach ($scan_result as $key => $value) {

        if (!in_array($value, array('.', '..'))) {

                $type = explode('.', $value);
                $type = array_reverse($type);
      
                if (in_array($type[0], $file_read)) {

                    $file_name = preg_split("/[.]/", $value);
                  
                    if (in_array("svg", $file_name)) {

                        rename($dir."/".$value,$dir."sharedsobs.svg");
                        //echo $value."<br>";
                  
                    }
                    if (in_array("sharedotus", $file_name)) {

                        rename($dir."/".$value,$dir."sharedsobs.sharedotus");
                        //echo $value."<br>";
                    }
                
                }
            
        }
    }
}


# Remove log mothur make biom

function remove_logfile_mothur2($path_out)
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
}

# Remove file gg_13_8_99.gg.tree.sum

function remove_file_tree_sum($path_in)
{

    $path_dir = $path_in;
    if (is_dir($path_dir)) {
        if ($read = opendir($path_dir)) {
            while (($logfile = readdir($read)) !== false) {

                $allowed = array('sum');
                $ext = pathinfo($logfile, PATHINFO_EXTENSION);

                if (in_array($ext, $allowed)) {

                    unlink($path_dir . $logfile);
                }
            }

            closedir($read);
        }
    }
}


?>