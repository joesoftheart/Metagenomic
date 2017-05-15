<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/13/17
 * Time: 11:07 PM
 */
class Projects extends CI_Controller{


    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $CI = &get_instance();
        $CI->load->library("session");
        include (APPPATH.'../setting_sge.php');
        putenv("SGE_ROOT=$SGE_ROOT");
        putenv("PATH=$PATH");
    }


    public function index($id_project){
        $data['rs'] = $this->mongo_db->get_where('projects',array('_id' => new \MongoId($id_project)));
        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');
        $data['rs_notifi'] = $this->mongo_db->limit(3)->get('notification');



        if ($data != null) {
            foreach ($data['rs'] as $r) {
                $ar = (string)$r['_id'];
            }
            $this->session->set_userdata('current_project', $ar);
        }

        $data['rs_mes'] = $this->mongo_db->limit(3)->get('messages');


        $this->load->view('header',$data);
        $this->load->view('projects',$data);
        $this->load->view('footer');
    }



    public function standard_run($id){
        $data = $this->mongo_db->get_where("projects",array("_id" => new MongoId($id)));
        foreach ($data as $r ){
            $sample_folder =  $r['project_path'];
            $id = $r['_id'];
        }
        $project =  basename($sample_folder);
        $user = $this->session->userdata['logged_in']['username'];

        $path = "owncloud/data/$user/files/$project/data/input/";
//
//        $config['upload_path'] = $path;
//        $config['allowed_types'] = '*';
//        $config['max_size'] = '3000';
//        // $config['max_width'] = '1024';
//        // $config['max_height'] = '1024';
//        $this->load->library('upload');
//        $this->upload->initialize($config);
//
//        if ($this->upload->do_upload("design")) {
//            $data = $this->upload->data();
//
//        }else{
//            echo $this->upload->display_errors();
//        }
//
//        if ($this->upload->do_upload("metadata")) {
//            $data = $this->upload->data();
//
//        }else{
//            echo $this->upload->display_errors();
//        }


        $id_project = "58ff5cca838488480e7759de";

        $cmd = "qsub -N 'q_test' -cwd -b y /usr/bin/php -f Scripts/standard_run.php $user $id $project $path";

        exec($cmd);


    }



    public function run_r($id){


        $data = $this->mongo_db->get_where("projects",array("_id" => new MongoId($id)));
        foreach ($data as $r ){
            $sample_folder =  $r['project_path'];
        }
        $project =  basename($sample_folder);
        $user = $this->session->userdata['logged_in']['username'];

        if ($project != null){
            $this->check_file($user, $project);
        }else{
            echo " This Project not have path to sample files";
        }




    }


    public function check_file($user, $project){
        echo "check file";
        $path = "owncloud/data/$user/files/$project/data/input/stability.files";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $path_file = FCPATH."$path";

        #stability.files
        if(file_exists($path_file)) {

            $this->check_oligos($user,$project);
        }
        #fileList.paired.file
        else {

            $out_var = $this->run_makefile($user,$project);

            if($out_var == "0"){
                echo  "Run makefile complete"."<br/>";

                $this->check_file($user,$project);

            }

        }



    }





    public function check_oligos($user,$project){
        echo "check oligo";
        $path = "owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $total_oligo = 0;

        $path_dir = FCPATH."$input_path";
        if (is_dir($path_dir)) {
            if ($read = opendir($path_dir)){
                while (($file_oligo = readdir($read)) !== false) {

                    $allowed =  array('oligo');
                    $ext = pathinfo($file_oligo, PATHINFO_EXTENSION);

                    if(in_array($ext,$allowed)) {

                        $total_oligo +=1;
                        echo "have ==> filename: ".$file_oligo." is type oligos"."<br/>";
                        $this->make_contigs_oligos($file_oligo,$user,$project);
                    }

                }

                closedir($read);
            }
        }

        if($total_oligo == 0){

           $this->makecontig_summary($user,$project);

        }

    }


    # make.file
    public function run_makefile($user,$project){
        echo "run make file";
        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $jobname = $user."_run_makefile";


        #make.file
        $make = "make.file($input_path,$output_path)";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $make);


        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
                return "0";
            }
        }

    }



    # make.contigs remove primer
    public function make_contigs_oligos($file_oligo,$user,$project){
        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";
        echo "make_contigs_oligos";

        $jobname = $user."_oligo";

        $cmd = "make.contigs(file=stability.files, oligos=$file_oligo ,processors=8 ,$input_path,$output_path)";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);



        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
               // echo "Run make contigs oligos complete";
                $this->summary_seqs($user,$project);
            }
        }

    }


    # make.contigs && summary.seqs
    public function makecontig_summary($user,$project){
        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $jobname = $user."_makesummary";

        $cmd ="make.contigs(file=stability.files,processors=8,$input_path,$output_path)
               summary.seqs(fasta=stability.trim.contigs.fasta,processors=8,$input_path,$output_path)";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
               // echo "Run make.contigs && summary.seqs complete"."<br/>";
                $this->summary_seqs($user,$project);

            }
        }



    }


    public function summary_seqs($user,$project){
        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $jobname = $user."_summary_seqs";

        $cmd ="screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8,$input_path,$output_path)
summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8,$input_path,$output_path)
unique.seqs(fasta=stability.trim.contigs.good.fasta,$input_path,$output_path)
count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups,$input_path,$output_path)
summary.seqs(count=stability.trim.contigs.good.count_table,$input_path,$output_path)
align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=silva.v4.fasta, processors=8,$input_path,$output_path)
summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table,$input_path,$output_path)";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
               // echo "Run _summary_seqs"."<br/>";
                $this->screen_remove($user,$project);

            }
        }






    }

    public function screen_remove($user,$project){

        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $jobname = $user."screen_remove";

        $cmd ="screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=8, end=9582, maxambig=8, maxhomop=8, maxlength=260, processors=8,$input_path,$output_path)
summary.seqs(fasta=current, count=current)
filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8,$input_path,$output_path)
unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table,$input_path,$output_path)
pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=2,$input_path,$output_path)
chimera.vsearch(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8,$input_path,$output_path)
remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.accnos,$input_path,$output_path)
summary.seqs(fasta=current, count=current,$input_path,$output_path)";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
             //   echo "Run screen_remove"."<br/>";
                $this->classify_system($user,$project);

            }
        }
    }
    public function classify_system($user,$project){
        $path_copy = "/var/www/html/owncloud/data/$user/files/$project/data/input/";
        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $jobname = $user."classify_system";

        $cmd ="classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=80, processors=8,$input_path,$output_path)
remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.count_table, taxon=taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria;k__Bacteria_unclassified-k__Archaea;k__Archaea_unclassified,$input_path,$output_path)
summary.seqs(fasta=current, count=current,$input_path,$output_path)
summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table,$input_path,$output_path)
system(cp /var/www/html/owncloud/data/$user/files/$project/data/input/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta final.fasta)
system(cp /var/www/html/owncloud/data/$user/files/$project/data/input/stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.vsearch.pick.pick.count_table final.count_table)
system(cp /var/www/html/owncloud/data/$user/files/$project/data/input/stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy final.taxonomy) ";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
            //    echo "Run classify_system"."<br/>";
                $this->phylotype_count($user,$project);

            }
        }
    }




    public function phylotype_count($user,$project){

        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $jobname = $user."phylotype_count";

        $cmd ="phylotype(taxonomy=final.taxonomy,$input_path,$output_path)
make.shared(list=final.tx.list, count=final.count_table, label=1-2-3-4-5-6,$input_path,$output_path)
classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, label=1-2-3-4-5-6,$input_path,$output_path)
#classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, basis=sequence, output=simple, label=2,$input_path,$output_path)
count.groups(shared=final.tx.shared,$input_path,$output_path)";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
             //   echo "Run phylotype_count"."<br/>";
                $this->sub_sample_sammary($user, $project);

            }
        }
    }

    public function sub_sample_sammary($user,$project){

        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $jobname = $user."sub_sample_sammary";

        $cmd ="sub.sample(shared=final.tx.shared, size=5000,$input_path,$output_path)
collect.single(shared=final.tx.shared, calc=chao, freq=100,$input_path,$output_path)
rarefaction.single(shared=final.tx.shared, calc=sobs, freq=100, processors=8,$input_path,$output_path)
summary.single(shared=final.tx.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon, subsample=5000,$input_path,$output_path)";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
             //   echo "Run sub_sample_sammary"."<br/>";
                $this->plot_graph($user, $project);

            }
        }
    }


    public function plot_graph($user,$project){

        $output_path = "outputdir=owncloud/data/$user/files/$project/data/input/";
        $input_path = "inputdir=owncloud/data/$user/files/$project/data/input/";

        $jobname = $user."plot_graph";

        $cmd ="heatmap.bin(shared=final.tx.2.subsample.shared, scale=log2, numotu=10,$input_path,$output_path)
dist.shared(shared=final.tx.shared, calc=thetayc-jclass-lennon-morisitahorn-braycurtis, subsample=5000,$input_path,$output_path)
heatmap.sim(phylip=final.tx.thetayc.2.lt.ave.dist,$input_path,$output_path) #No need
heatmap.sim(phylip=final.tx.jclass.2.lt.ave.dist,$input_path,$output_path) #No need
summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=soils1_1-soils2_1-soils3_1-soils4_1, all=T,$input_path,$output_path)
venn(shared=final.tx.2.subsample.shared, groups=soils1_1-soils2_1-soils3_1-soils4_1,$input_path,$output_path)
tree.shared(phylip=final.tx.thetayc.2.lt.ave.dist,$input_path,$output_path)
tree.shared(phylip=final.tx.morisitahorn.2.lt.ave.dist,$input_path,$output_path)
tree.shared(phylip=final.tx.jclass.2.lt.ave.dist,$input_path,$output_path)
tree.shared(phylip=final.tx.braycurtis.2.lt.ave.dist,$input_path,$output_path)
tree.shared(phylip=final.tx.lennon.2.lt.ave.dist,$input_path,$output_path)
parsimony(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design,  groups=all,$input_path,$output_path) #No need
unifrac.weighted(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design, random=T,$input_path,$output_path) #No need
unifrac.unweighted(tree=final.tx.thetayc.2.lt.ave.tre, group=soil.design, random=T, groups=all,$input_path,$output_path) #No need
pcoa(phylip=final.tx.morisitahorn.2.lt.ave.dist,$input_path,$output_path)
pcoa(phylip=final.tx.thetayc.2.lt.ave.dist,$input_path,$output_path)
pcoa(phylip=final.tx.jclass.2.lt.ave.dist,$input_path,$output_path)
nmds(phylip=final.tx.morisitahorn.2.lt.ave.dist, mindim=3, maxdim=3,$input_path,$output_path)
nmds(phylip=final.tx.thetayc.2.lt.ave.dist, mindim=2, maxdim=2,$input_path,$output_path)
nmds(phylip=final.tx.jclass.2.lt.ave.dist, mindim=3, maxdim=3,$input_path,$output_path)
amova(phylip=final.tx.thetayc.2.lt.ave.dist, design=soil.design,$input_path,$output_path) #No need
homova(phylip=final.tx.thetayc.2.lt.ave.dist, design=soil.design,$input_path,$output_path)
corr.axes(axes=final.tx.thetayc.2.lt.ave.nmds.axes, shared=final.tx.2.subsample.shared, method=spearman, numaxes=2, label=2,$input_path,$output_path)
corr.axes(axes=final.tx.thetayc.2.lt.ave.nmds.axes, metadata=soilpro.metadata, method=pearson, numaxes=2, label=2,$input_path,$output_path)";

        file_put_contents('owncloud/data/'.$user.'/files/'.$project.'/data/input/run.batch', $cmd);

        $cmd = "qsub -N '$jobname' -cwd -b y Mothur/mothur owncloud/data/$user/files/$project/data/input/run.batch ";

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

            $check_run = exec("qstat -u apache  '$id_job' ");

            if($check_run == false){
                $loop = false;
            //    echo "Run plot_graph"."<br/>";

            }
        }
    }


}




