<!-- related course packages -->

<?php
$CI    = &get_instance();
$CI->load->model('addons/team_package_model');

$related_packages = $CI->team_package_model->course_related_packages($course_id);
?>

<?php if (count($related_packages) > 0) : ?>
    <section class="mb-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><?php echo get_phrase('available_team_training_package'); ?></h1>
                </div>
            </div>

            <div class="row">
                <?php foreach ($related_packages as $package) : ?>
                    <div class="col-lg-3">
                        <a href="<?php echo site_url('addons/team_training/package_details/') . $package['id']; ?>" class="checkPropagation courses-card-body">

                            <!-- package banner -->
                            <div class="package-thumbnail mb-3">
                                <img loading="lazy" src="<?php echo $CI->team_package_model->get_package_thumbnail($package['id']); ?>">
                            </div>

                            <!-- package body -->
                            <div class="courses-text px-3 pb-3">
                                <h5 class="mb-3 text-15px fw-600 ellipsis"><?php echo $package['title']; ?></h5>

                                <!-- selected course -->
                                <p class="text-12px course-head"><?php echo get_phrase('course_title'); ?>:</p>
                                <p class="selected-course ellipsis fw-600 mb-2"><?php echo $course_details['title']; ?></p>


                                <div class="d-flex align-items-center justify-content-between">
                                    <?php $sells = $CI->team_package_model->package_sells($package['id']); ?>
                                    <p class="text-12px course-head mb-2"><?php echo get_phrase('sold'); ?>: <?php echo $sells; ?></p>
                                    <p class="text-12px course-head mb-2"><?php echo get_phrase('capacity'); ?>: <?php echo $package['max_students']; ?></p>
                                </div>

                                <div class="courses-price-border">
                                    <div class="courses-price d-flex align-items-center justify-content-between">
                                        <div class="courses-price-left fw-600">
                                            <?php echo currency($package['price']); ?>
                                        </div>

                                        <?php $purchased = $CI->team_package_model->is_purchased($package['id']); ?>

                                        <?php if ($purchased) : ?>
                                            <button class="buy-package checkPropagation" onclick="redirectTo('<?php echo site_url('addons/team_training/join_team/' . $package['id']) ?>');">
                                                <?php echo get_phrase('join_now'); ?>
                                            </button>
                                        <?php else : ?>
                                            <button class="buy-package checkPropagation" onclick="redirectTo('<?php echo site_url('addons/team_training/purchase/' . $package['id']) ?>');">
                                                <?php echo get_phrase('buy_now'); ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <style>
        .courses-card-body:hover .package-thumbnail img {
            scale: 1.1;
        }

        h1 {
            margin-bottom: 30px !important;
        }

        .package-thumbnail {
            width: 100%;
            height: 150px;
            overflow: hidden;
        }

        .course-head {
            color: #6e798a !important
        }

        .package-thumbnail img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            object-position: center;
            transition: .5s;
        }

        .courses-text h5 {
            overflow: hidden;
            height: 38px;
        }

        .courses-text h5,
        .courses-price-left {
            color: var(--color-1);
            transition: .3s !important;
        }

        .courses-price-border {
            padding-top: 16px;
            border-top: 1.5px solid #676c7d3a;
        }

        .courses-card-body {
            width: 100%;
        }

        .courses-card-body:hover .courses-text h5 {
            color: #754FFE !important;
        }

        .ellipsis {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .selected-course {
            height: 38px;
            line-height: 18px;
            color: var(--color-1) !important;
        }


        .buy-package {
            background-color: #754FFE;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            padding: 4px 16px;
        }
    </style>
<?php endif; ?>