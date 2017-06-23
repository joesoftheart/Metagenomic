<?php

    include('setting_sge.php');
    putenv("SGE_ROOT=$SGE_ROOT");
    putenv("PATH=$PATH");

         $user = $argv[1];
         $project = $argv[2];
         $path_in = $argv[3];
         $path_out = $argv[4];


// collect.single(shared=final.opti_mcc.shared, calc=chao, freq=100, label=0.03)
// rarefaction.single(shared=final.opti_mcc.shared, calc=sobs, freq=100, label=0.03, processors=8)
// summary.single(shared=final.opti_mcc.shared, calc=nseqs-coverage-sobs-invsimpson-chao-shannon-npshannon-simpson, subsample=10000, label=0.03)

// dist.shared(shared=final.opti_mcc.shared, calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, subsample=10000, processors=8)

// summary.shared(calc=lennon-jclass-morisitahorn-sorabund-thetan-thetayc-braycurtis, groups=soils1_1-soils2_1-soils3_1-soils4_1, all=T)
// venn(shared=final.opti_mcc.0.03.subsample.shared, groups=soils1_1-soils2_1-soils3_1-soils4_1)
// venn(shared=final.opti_mcc.0.03.subsample.shared, groups=soils1_1-soils2_1-soils4_1)
// tree.shared(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, processors=8)
// tree.shared(phylip=final.opti_mcc.morisitahorn.0.03.lt.ave.dist)
// tree.shared(phylip=final.opti_mcc.jclass.0.03.lt.ave.dist)
// tree.shared(phylip=final.opti_mcc.braycurtis.0.03.lt.ave.dist)
// tree.shared(phylip=final.opti_mcc.lennon.0.03.lt.ave.dist)
// parsimony(tree=final.opti_mcc.thetayc.0.03.lt.ave.tre, group=soil.design, groups=all)

// pcoa(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist)
// nmds(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, mindim=3, maxdim=3)
// amova(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, design=soil.design)
// homova(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist, design=soil.design)
// corr.axes(axes=final.opti_mcc.thetayc.0.03.lt.ave.pcoa.axes, shared=final.opti_mcc.0.03.subsample.shared, method=spearman, numaxes=3, label=0.03)
// system(mv final.opti_mcc.0.03.subsample.spearman.corr.axes final.opti_mcc.0.03.subsample.spearman.corr.axesThetayc3D)
// corr.axes(axes=final.opti_mcc.thetayc.0.03.lt.ave.pcoa.axes, metadata=soilpro.metadata, method=pearson, numaxes=3, label=0.03)

# hide output

// heatmap.bin(shared=final.opti_mcc.0.03.subsample.shared, scale=log2, numotu=50)
// heatmap.sim(phylip=final.opti_mcc.thetayc.0.03.lt.ave.dist)
// heatmap.sim(phylip=final.opti_mcc.jclass.0.03.lt.ave.dist)
// unifrac.weighted(tree=final.opti_mcc.thetayc.0.03.lt.ave.tre, group=soil.design, random=T)
// unifrac.unweighted(tree=final.opti_mcc.thetayc.0.03.lt.ave.tre, group=soil.design, random=T, groups=all)



?>