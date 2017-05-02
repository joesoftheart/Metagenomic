<?php
if (isset($this->session->userdata['logged_in'])) {
    $username = ($this->session->userdata['logged_in']['username']);
    $email = ($this->session->userdata['logged_in']['email']);
    $id = ($this->session->userdata['logged_in']['_id']);
    $current_project = ($this->session->userdata['current_project']);
} else {
    header("location: main/login");
} ?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12 ">
            <?php echo "User :" . $username . "   Email :" . $email . "   ID :" . $id . "    PROJECT_SESS :" . $current_project ;?>
            <br>
            <?php foreach ($rs as $r) {
                echo "Name project :" . $r['project_name'];
            }
             ?>
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main'){
                    echo "class=active";} ?>><?php if ($controller_name == 'main') {?>Home<?php } else { ?><a href="<?php echo site_url('main')?>">Home</a><?php } ?></li>
                <li <?php if ($controller_name == 'projects'){
                    echo "class=active";} ?>><?php if ($controller_name == 'projects'){?>Current project<?php } else {?><a href="<?php echo site_url('projects/index/'.$current_project)?>">Current project</a><?php } ?></li>
            </ol>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-10">
            <div class="uk-child-width-1-6\@xl" uk-grid>
                <div>
                    <ul class="uk-tab-right" uk-switcher="animation: uk-animation-fade" uk-tab>
                        <li class="uk-active"><a href="#">Standard</a></li>
                        <li><a href="#">Advance</a></li>
                    </ul>
                    <ul class="uk-switcher">

                        <li>
                            <!-- Standard run -->
                            <form action="#" method="post" id="change">
                                <div class="col-lg-8 col-lg-offset-2">
                                    <label>1. Preprocess & Prepare in taxonomy </label><br><br>

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label>** Screen reads  :</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <table  border="0">
                                                <tr>
                                                    <td>maximum ambiguous :</td>
                                                    <td><input class="uk-input" type="text" name="cmd" value="" placeholder="8" disabled></td>
                                                </tr>
                                                <tr>
                                                    <td>ximum homopolymer :</td>
                                                    <td><input class="uk-input" type="text" name="cmd" value="" placeholder="8" disabled></td>
                                                </tr>
                                                <tr>
                                                    <td>manimum reads length :</td>
                                                    <td><input class="uk-input" type="text" name="cmd" value="" placeholder="260" disabled></td>
                                                </tr>
                                                <tr>
                                                    <td>maximum reads length :</td>
                                                    <td><input class="uk-input" type="text" name="cmd" value="" placeholder="260" disabled></td>
                                                </tr>
                                            </table>

                                        </div>
                                    </div><br>
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-4">
                                    <label>**Alignment step :</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <select class="uk-select uk-margin" disabled>
                                                <option>silva.v4.fasta</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label>**Pre-cluster step :</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <select class="uk-select" disabled>
                                                <option>diffs=2</option>

                                            </select>
                                        </div>


                                    </div><br><br>
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <label>**Prepare the taxonomy classification :</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <table>
                                                <tr>
                                                    <td>database :</td>
                                                    <td>
                                                            <select class="uk-select" disabled>
                                                                <option>gg_13_8_99.fasta</option>
                                                            </select>
                                                       </td>

                                                </tr>
                                                <tr>
                                                    <td>cutoff :</td>
                                                    <td><input class="uk-input" type="text" name="cutoff" value="" placeholder="80" disabled></td>
                                                </tr>
                                                <tr>
                                                    <td>Test to remove taxon :</td>
                                                    <td> <textarea class="uk-textarea" type="textarea" name="texonomy" value="" placeholder="Chloroplast-Mitochondria-Eukaryota-unknown" disabled></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>




                                    <div class="row uk-margin">

                                        <p id="show_prephy"></p>
                                    </div>
                                    <button id="btn_prepro"  name="submit" class="btn btn-default pull-right">Run Prepro</button>
                                </div>



                </form>
                            <!-- End Standard run -->
                </li>

                        <li>
                            <div>
                                <ul class="uk-child-width-expand" uk-tab uk-switcher="animation: uk-animation-fade">
                                    <li ><a href="#">#preprocess&&#prepare in taxonomy</a></li>
                                    <li><a href="#">#prepare phylotype analysis</a></li>
                                    <li><a href="#">#analysis</a></li>
                                    <li><a href="#">#result&graph</a></li>
                                </ul>
                                <ul  class="uk-switcher uk-margin">

                                    <li>
                                        <form action="#" method="post" id="change">
                                            <div class="col-lg-8 col-lg-offset-2">
                                                <label>Select option run yos project : </label>
                                                <select class="uk-select uk-margin" disabled>
                                                    <option>silva.v4.fasta</option>
                                                </select>
                                                <div class="row">
                                                    <div class="col-lg-2"><input class="uk-input" type="text" name="start" value="" placeholder="start" id="text"></div>
                                                    <div class="col-lg-2"><input class="uk-input" type="text" name="end" value="" placeholder="end"></div>
                                                    <div class="col-lg-2"><input class="uk-input" type="text" name="cmd" value="" placeholder="maxambig" ></div>
                                                    <div class="col-lg-2"><input class="uk-input" type="text" name="cmd" value="" placeholder="maxhomop" ></div>
                                                    <div class="col-lg-2"><input class="uk-input" type="text" name="cmd" value="" placeholder="maxlength" ></div>
                                                </div>
                                                <div class="row uk-margin" >
                                                    <div class="col-lg-2">
                                                        <label>unique :</label>
                                                        <select class="uk-select">
                                                            <option>diffs=0</option>
                                                            <option>diffs=1</option>
                                                            <option>diffs=2</option>
                                                            <option>diffs=3 </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row uk-margin">

                                                    <p id="show_prepro"></p>
                                                </div>

                                                <label>classify :</label>
                                                <div class="row uk-margin">
                                                    <div class="col-lg-2">
                                                        <select class="uk-select">
                                                            <option>gg_13_8_99.fasta</option>
                                                        </select></div>
                                                    <div class="col-lg-2"><select class="uk-select">
                                                            <option>gg_13_8_99.gg.tex</option>
                                                        </select></div>
                                                    <div class="col-lg-2"><input class="uk-input" type="text" name="cutoff" value="" placeholder="cutoff"></div>
                                                </div>
                                                <label>taxon :</label>
                                                <div class="row uk-margin">

                                                    <div class="col-lg-8">
                                                        <textarea class="uk-textarea" type="textarea" name="texonomy" value="" placeholder="Chloroplast-Mitochondria-Eukaryota-unknown"></textarea>
                                                    </div>

                                                </div>
                                                <button id="btn_prepro"  name="submit" class="btn btn-default pull-right">Run Prepro</button>

                                            </div><!-- close row form -->
                                        </form>

                                    </li>

                                    <li >
                                        <div class="col-lg-8 col-lg-offset-2">
                                            <label>sub sample :</label>
                                            <div class="row uk-margin">
                                                <div class="col-lg-8">
                                                    <input class="uk-input" type="text" name="cutoff" value="" placeholder="5000">
                                                </div>
                                            </div>

                                            <div class="row uk-margin">

                                                <p id="show_prephy"></p>
                                            </div>
                                            <button id="btn_prephy"  name="submit" class="btn btn-default pull-right">Run Prephy</button>



                                        </div><!-- close row form -->
                                    </li>
                                    <li >
                                        <div class="col-lg-8 col-lg-offset-2">
                                            <div class="row uk-margin">
                                                <label>subsample :</label>
                                                <div class="row uk-margin">
                                                    <div class="col-lg-8">
                                                        <input class="uk-input" type="text" name="cutoff" value="" placeholder="5000">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row uk-margin">
                                                <label>subsample :</label>
                                                <div class="row uk-margin">
                                                    <div class="col-lg-8">
                                                        <input class="uk-input" type="text" name="cutoff" value="" placeholder="5000">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row uk-margin">
                                                <label>groups :</label>
                                                <div class="row uk-margin">
                                                    <div class="col-lg-8">
                                                        <input class="uk-input" type="text" name="cutoff" value="" placeholder="soils1_1-soils2_1-soils3_1-soils4_1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row uk-margin">
                                                <label>groups :</label>
                                                <div class="row uk-margin">
                                                    <div class="col-lg-8">
                                                        <input class="uk-input" type="text" name="cutoff" value="" placeholder="soils1_1-soils2_1-soils3_1-soils4_1">
                                                    </div>
                                                </div>
                                            </div>
                                            <button id="btn_prepro"  name="submit" class="btn btn-default pull-right">Run Analysis</button>
                                        </div>
                                    </li>
                                    <li >
                                        <!--                    <div class="uk-cover-container uk-height-large">-->
                                        <!--                        <iframe src="//www.youtube.com/embed/nJx4buMnn34?autoplay=1&amp;controls=0&amp;showinfo=0&amp;rel=0&amp;loop=1&amp;modestbranding=1&amp;wmode=transparent" width="560" height="1000" frameborder="0" allowfullscreen uk-cover></iframe>-->
                                        <!--                    </div>-->
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/jclass.svg">
                                            </div>
                                            <div class="col-lg-6">
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/sharedsobs.svg">
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/">
                                            </div>
                                            <div class="col-lg-6">
                                                <img class="img-thumbnail" src="<?php echo base_url(); ?>uploads/thetayc.svg">
                                            </div>
                                        </div>

                                    </li>


                                </ul>

                            </div>
                        </li>


                    </ul>
                </div>
            </div>
    </div>
</div>

<script >


</script>
    <script>
        $(document).ready(function () {
            $("#btn_prepro").click(function () {
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url();?>projects/run_preprocess",
                    data: {text: $("#text").val()},
                    dataType: "text",
                    cache:false,
                    success:
                        function(data){
                            $("#show_prepro").html(data);
                        }
                });// you have missed this bracket
                return false;
            });

        });

    </script>

    <script>
        $(document).ready(function () {
            $("#btn_prephy").click(function () {
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url();?>projects/run_prepare_phylotype",
                    data: {text: $("#text").val()},
                    dataType: "text",
                    cache:false,
                    success:
                        function(data){
                            $("#show_prephy").html(data);
                        }
                });// you have missed this bracket
                return false;
            });

        });

    </script>

