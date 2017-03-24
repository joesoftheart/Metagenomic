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
    }


    public function index($id_project){
        $data['rs'] = $this->mongo_db->get_where('projects',array('_id' => new \MongoId($id_project)));

        if ($data != null) {
            foreach ($data['rs'] as $r){
              $ar = array("pro_sess" => $r['_id']);
            }
            $this->session->set_userdata($ar);
        }


        $this->load->view('header');
        $this->load->view('projects',$data);
        $this->load->view('footer');
    }

    public function run_preprocess(){
         echo chdir("../test_run/exampledatatraining/exampledatatraining/");
         echo getcwd();

         $run1 = "../../../mothur/mothur '#make.contigs(file=stability.files, processors=8)'";
         echo exec($run1);
         echo "run1";
         $run2 = "../../../mothur/mothur '#summary.seqs(fasta=stability.trim.contigs.fasta, processors=8)'";
         echo exec($run2);
         echo "run2";
         $run3 = "../../../mothur/mothur '#screen.seqs(fasta=stability.trim.contigs.fasta, group=stability.contigs.groups, summary=stability.trim.contigs.summary, maxambig=8, minlength=100, maxlength=260, processors=8)'";
         echo exec($run3);
         echo "run3";
        $run4 = "../../../mothur/mothur '#summary.seqs(fasta=stability.trim.contigs.good.fasta, processors=8)'";
        echo exec($run4);
        echo "run4";
        $run5 = "../../../mothur/mothur '#unique.seqs(fasta=stability.trim.contigs.good.fasta)'";
        echo exec($run5);
        echo "run5";
        $run6 = "../../../mothur/mothur '#count.seqs(name=stability.trim.contigs.good.names, group=stability.contigs.good.groups)'";
        echo exec($run6);
        echo "run6";
        $run7 = "../../../mothur/mothur '#summary.seqs(count=stability.trim.contigs.good.count_table)'";
        echo exec($run7);
        echo "run7";
        $run8 = "../../../mothur/mothur '#align.seqs(fasta=stability.trim.contigs.good.unique.fasta, reference=silva.v4.fasta, processors=8)'";
        echo exec($run8);
        echo "run8";
        $run9 = "../../../mothur/mothur '#summary.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table)'";
        echo exec($run9);
        echo "run9";
        $run10 = "../../../mothur/mothur '#screen.seqs(fasta=stability.trim.contigs.good.unique.align, count=stability.trim.contigs.good.count_table, summary=stability.trim.contigs.good.unique.summary, start=8, end=9582, maxambig=8, maxhomop=8, maxlength=260, processors=8)'";
        echo exec($run10);
        echo "run10";
        $run11 = "../../../mothur/mothur '#summary.seqs(fasta=current, count=current)'";
        echo exec($run11);
        echo "run11";
        $run12 = "../../../mothur/mothur '#filter.seqs(fasta=stability.trim.contigs.good.unique.good.align, vertical=T, trump=., processors=8)'";
        echo exec($run12);
        echo "run12";
        $run13 = "../../../mothur/mothur '#unique.seqs(fasta=stability.trim.contigs.good.unique.good.filter.fasta, count=stability.trim.contigs.good.good.count_table)'";
        echo exec($run13);
        echo "run13";
        $run14 = "../../../mothur/mothur '#pre.cluster(fasta=stability.trim.contigs.good.unique.good.filter.unique.fasta, count=stability.trim.contigs.good.unique.good.filter.count_table, diffs=2)'";
        echo exec($run14);
        echo "run14";
        $run15 = "../../../mothur/mothur '#chimera.uchime(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.count_table, dereplicate=t, processors=8)'";
        echo exec($run15);
        echo "run15";
        $run16 = "../../../mothur/mothur '#remove.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.fasta, accnos=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.uchime.accnos)'";
        echo exec($run16);
        echo "run16";
        $run17 = "../../../mothur/mothur '#summary.seqs(fasta=current, count=current)'";
        echo exec($run17);
        echo "run17";

    }


    public function run_prepare_texonomy(){
        echo chdir("../test_run/exampledatatraining/exampledatatraining/");
        echo getcwd();

        $run18 = "../../../mothur/mothur '#classify.seqs(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.uchime.pick.count_table, reference=gg_13_8_99.fasta, taxonomy=gg_13_8_99.gg.tax, cutoff=80, processors=8)'";
        echo exec($run18);
        echo "run18";
        $run19 = "../../../mothur/mothur '#remove.lineage(fasta=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.fasta, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.uchime.pick.count_table, taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.taxonomy, taxon=Chloroplast-Mitochondria-Eukaryota-unknown-k__Bacteria)'";
        echo exec($run19);
        echo "run19";
        $run20 = "../../../mothur/mothur '#summary.seqs(fasta=current, count=current)'";
        echo exec($run20);
        echo "run20";
        $run21 = "../../../mothur/mothur '#summary.tax(taxonomy=stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy, count=stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.uchime.pick.pick.count_table)'";
        echo exec($run21);
        echo "run21";
        $run22 = "../../../mothur/mothur '#system(cp stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.pick.fasta final.fasta)'";
        echo exec($run22);
        echo "run22";
        $run23 = "../../../mothur/mothur '#system(cp stability.trim.contigs.good.unique.good.filter.unique.precluster.denovo.uchime.pick.pick.count_table final.count_table)'";
        echo exec($run23);
        echo "run23";
        $run24 = "../../../mothur/mothur '#system(cp stability.trim.contigs.good.unique.good.filter.unique.precluster.pick.gg.wang.pick.taxonomy final.taxonomy)'";
        echo exec($run24);
        echo "run24";


    }

    public function run_prepare_phylotype(){

        echo chdir("../test_run/exampledatatraining/exampledatatraining/");
        echo getcwd();

        $run25 = "../../../mothur/mothur '#phylotype(taxonomy=final.taxonomy)'";
        echo exec($run25);
        echo "run25";
        $run26 = "../../../mothur/mothur '#make.shared(list=final.tx.list, count=final.count_table, label=1-2-3-4-5-6)'";
        echo exec($run26);
        echo "run26";
        $run27 = "../../../mothur/mothur '#classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, label=1-2-3-4-5-6)'";
        echo exec($run27);
        echo "run27";
        $run28 = "../../../mothur/mothur '#classify.otu(list=final.tx.list, count=final.count_table, taxonomy=final.taxonomy, basis=sequence, output=simple, label=2)'";
        echo exec($run28);
        echo "run28";
        $run29 = "../../../mothur/mothur '#count.groups(shared=final.tx.shared)'";
        echo exec($run29);
        echo "run29";
        $run30 = "../../../mothur/mothur '#sub.sample(shared=final.tx.shared, size=5000)'";
        echo exec($run30);
        echo "run30";

    }





}




