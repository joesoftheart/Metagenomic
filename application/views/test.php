<div id="page-wrapper">
    <div class="row">

        <?php

        $img_source = 'images/check.png';
        $img_code = base64_encode(file_get_contents($img_source));

        $src = 'data:' . mime_content_type($img_source) . ';base64,' . $img_code;

        echo '<img src="', $src, '"/>';


        ?>


    </div>
</div>
