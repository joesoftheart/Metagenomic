<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <?php $controller_name = $this->uri->segment(1); ?>
            <br>
            <ol class="breadcrumb">
                <li <?php if ($controller_name == 'main') {
                    echo "class=active";
                } ?>><?php if ($controller_name == 'main') { ?>Home<?php } else { ?><a
                        href="<?php echo site_url('main') ?>">Home</a><?php } ?></li>
                <li class="active">View message</li>

            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ul class="uk-comment-list">
                <li>
                    <?php foreach ($rs_message as $rs_m) { ?>
                        <article class="uk-comment uk-visible-toggle">
                            <header class="uk-comment-header uk-position-relative">
                                <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto">
                                        <img class="uk-comment-avatar" src="http://placehold.it/100x100">
                                    </div>
                                    <div class="uk-width-expand">
                                        <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#">Author</a>
                                        </h4>
                                        <p class="uk-comment-meta uk-margin-remove-top"><a class="uk-link-reset"
                                                                                           href="#">12 days ago</a></p>
                                    </div>
                                </div>
                                <div class="uk-position-top-right uk-position-small uk-hidden-hover"><a
                                            class="uk-link-muted" href="#">Reply</a></div>
                            </header>
                            <div class="uk-comment-body">
                                <dl>
                                    <dt>
                                        <?php echo $rs_m['message_title']; ?>
                                    </dt>
                                </dl>
                                <p><?php echo $rs_m['message_detail']; ?></p>
                            </div>
                        </article>
                    <?php } ?>
                    <!--                    <ul>-->
                    <!--                        <li>-->
                    <!--                            <article class="uk-comment uk-comment-primary uk-visible-toggle">-->
                    <!--                                <header class="uk-comment-header uk-position-relative">-->
                    <!--                                    <div class="uk-grid-medium uk-flex-middle" uk-grid>-->
                    <!--                                        <div class="uk-width-auto">-->
                    <!--                                            <img class="uk-comment-avatar" src="http://placehold.it/100x100">-->
                    <!--                                        </div>-->
                    <!--                                        <div class="uk-width-expand">-->
                    <!--                                            <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#">Author</a></h4>-->
                    <!--                                            <p class="uk-comment-meta uk-margin-remove-top"><a class="uk-link-reset" href="#">12 days ago</a></p>-->
                    <!--                                        </div>-->
                    <!--                                    </div>-->
                    <!--                                    <div class="uk-position-top-right uk-position-small uk-hidden-hover"><a class="uk-link-muted" href="#">Reply</a></div>-->
                    <!--                                </header>-->
                    <!--                                <div class="uk-comment-body">-->
                    <!--                                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>-->
                    <!--                                </div>-->
                    <!--                            </article>-->
                    <!--                        </li>-->
                    <!--                        <li>-->
                    <!--                            <article class="uk-comment uk-visible-toggle">-->
                    <!--                                <header class="uk-comment-header uk-position-relative">-->
                    <!--                                    <div class="uk-grid-medium uk-flex-middle" uk-grid>-->
                    <!--                                        <div class="uk-width-auto">-->
                    <!--                                            <img class="uk-comment-avatar" src="http://placehold.it/100x100">-->
                    <!--                                        </div>-->
                    <!--                                        <div class="uk-width-expand">-->
                    <!--                                            <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#">Author</a></h4>-->
                    <!--                                            <p class="uk-comment-meta uk-margin-remove-top"><a class="uk-link-reset" href="#">12 days ago</a></p>-->
                    <!--                                        </div>-->
                    <!--                                    </div>-->
                    <!--                                    <div class="uk-position-top-right uk-position-small uk-hidden-hover"><a class="uk-link-muted" href="#">Reply</a></div>-->
                    <!--                                </header>-->
                    <!--                                <div class="uk-comment-body">-->
                    <!--                                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>-->
                    <!--                                </div>-->
                    <!--                            </article>-->
                    <!--                        </li>-->
                    <!--                    </ul>-->
                </li>
            </ul>
        </div>
    </div>
</div>