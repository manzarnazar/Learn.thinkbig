<?php
$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
$lessons = $this->crud_model->get_lessons('course', $course_details['id']);
$instructor_details = $this->user_model->get_all_user($package['user_id'])->row_array();
$course_duration = $this->crud_model->get_total_duration_of_lesson_by_course_id($course_details['id']);
$purchased = $this->team_package_model->package_sells($package['id']);
$expiry_date = date('d-M-Y', strtotime('+' . $package['expiry_period'] . 'month'));
?>

<!---------- Banner Start ---------->
<section>
    <div class="bread-crumb courses-details">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-sm-12 col-12">
                    <div class="courses-details-1st-text">
                        <h1><?php echo $package['title']; ?></h1>

                        <div class="review">
                            <div class="row ">
                                <div class="col-12 course-heading-info d-flex align-items-center gap-3 mb-4">
                                    <div class="info-tag d-flex align-items-center">
                                        <img width="25px" height="25px" class="rounded-circle object-fit-cover me-1" src="<?php echo $this->user_model->get_user_image_url($instructor_details['id']); ?>">
                                        <p class="text-14px me-2"><?php echo get_phrase('creator'); ?>: </p>
                                        <p class="text-14px">
                                            <a class="created-by-instructor" href="<?php echo site_url('home/instructor_page/' . $package['user_id']); ?>"><?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?></a>
                                        </p>
                                    </div>

                                    <div class="info">
                                        <p class="text-14px">
                                            <span><?php echo get_phrase('purchased') . ': ' . $purchased; ?></span>
                                        </p>
                                    </div>

                                    <?php if ($package['expiry_period'] == 'lifetime') : ?>
                                        <div class="info">
                                            <p class="text-14px">
                                                <span><?php echo get_phrase('expiry'); ?>: </span>
                                                <span><?php echo get_phrase('lifetime'); ?></span>
                                            </p>
                                        </div>
                                    <?php else : ?>
                                        <div class="info">
                                            <p class="text-14px">
                                                <span><?php echo get_phrase('expiry'); ?>: </span>
                                                <span><?php echo $expiry_date; ?></span>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<!---------- Banner Area End ---------->

<!--------- course Decription Page Start ------>
<section class="course-decription mb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 order-2 order-lg-1">
                <div class="course-left-side">
                    <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="course-overview-tab" data-bs-toggle="tab" data-bs-target="#course-overview" type="button" role="tab" aria-controls="course-overview" aria-selected="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="18.666" viewBox="0 0 14 18.666">
                                    <g id="Group_8" data-name="Group 8" transform="translate(14 0) rotate(90)">
                                        <path id="Shape" d="M7,14.307l3.7,3.78c1.3,1.326,3.3.227,3.3-1.81V0H0V16.277c0,2.037,2,3.136,3.3,1.81ZM2,2.385V16.277l5-5.11,5,5.11V2.385Z" transform="translate(0 14) rotate(-90)" fill="#1e293b" fill-rule="evenodd" />
                                    </g>
                                </svg>

                                <span class="ms-2"><?php echo get_phrase('Overview'); ?></button></span>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="instructor-tab" data-bs-toggle="tab" data-bs-target="#instructor" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                <svg id="Group_12" data-name="Group 12" xmlns="http://www.w3.org/2000/svg" width="15.582" height="19.666" viewBox="0 0 15.582 19.666">
                                    <path id="Shape" d="M7.791,1.731a6.06,6.06,0,0,0-6.06,6.06V9.522A.866.866,0,1,1,0,9.522V7.791a7.791,7.791,0,0,1,15.582,0V9.522a.866.866,0,1,1-1.731,0V7.791A6.06,6.06,0,0,0,7.791,1.731Z" transform="translate(0 9.278)" fill="#1e293b" />
                                    <path id="Shape-2" data-name="Shape" d="M5.194,8.656A3.463,3.463,0,1,0,1.731,5.194,3.463,3.463,0,0,0,5.194,8.656Zm0,1.731A5.194,5.194,0,1,0,0,5.194,5.194,5.194,0,0,0,5.194,10.388Z" transform="translate(2.597)" fill="#1e293b" fill-rule="evenodd" />
                                </svg>

                                <span class="ms-2"><?php echo get_phrase('Instructor') ?></span></button>
                        </li>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="course-overview" role="tabpanel" aria-labelledby="course-overview-tab">

                            <div class="course-description">
                                <h3 class="description-head"><?php echo get_phrase('Package Course') ?></h3>

                                <!-- selected course -->
                                <div class="courses-card courses-list-view-card">

                                    <?php
                                    $lessons = $this->crud_model->get_lessons('course', $course_details['id']);
                                    $instructor_details = $this->user_model->get_all_user($course_details['user_id'])->row_array();
                                    $course_duration = $this->crud_model->get_total_duration_of_lesson_by_course_id($course_details['id']);
                                    $total_rating =  $this->crud_model->get_ratings('course', $course_details['id'], true)->row()->rating;
                                    $number_of_ratings = $this->crud_model->get_ratings('course', $course_details['id'])->num_rows();
                                    if ($number_of_ratings > 0) {
                                        $average_ceil_rating = ceil($total_rating / $number_of_ratings);
                                    } else {
                                        $average_ceil_rating = 0;
                                    }
                                    ?>
                                    <!-- Course List Card -->
                                    <a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course_details['title'])) . '/' . $course_details['id']); ?>" class="courses-list-view-card-body courses-card-body checkPropagation">
                                        <div class="courses-card-image ">
                                            <img loading="lazy" src="<?php echo $this->crud_model->get_course_thumbnail_url($course_details['id']); ?>">
                                            <div class="courses-icon <?php if (in_array($course_details['id'], $my_wishlist_items)) echo 'red-heart'; ?>" id="coursesWishlistIcon<?php echo $course_details['id']; ?>">
                                                <i class="fa-solid fa-heart checkPropagation" onclick="actionTo('<?php echo site_url('home/toggleWishlistItems/' . $course_details['id']); ?>')"></i>
                                            </div>
                                            <div class="courses-card-image-text">
                                                <h3><?php echo get_phrase($course_details['level']); ?></h3>
                                            </div>
                                        </div>
                                        <div class="courses-text w-100">
                                            <div class="courses-d-flex-text">
                                                <h5><?php echo $course_details['title']; ?></h5>
                                                <span class="compare-img checkPropagation" onclick="redirectTo('<?php echo base_url('home/compare?course-1=' . slugify($course_details['title']) . '&course-id-1=' . $course_details['id']); ?>');">
                                                    <img loading="lazy" src="<?php echo base_url('assets/frontend/default-new/image/compare.png') ?>">
                                                    <?php echo get_phrase('Compare'); ?>
                                                </span>
                                            </div>
                                            <div class="review-icon">
                                                <p><?php echo $average_ceil_rating; ?></p>
                                                <p><i class="fa-solid fa-star <?php if ($number_of_ratings > 0) echo 'filled'; ?>"></i></p>
                                                <p>(<?php echo $number_of_ratings; ?> <?php echo get_phrase('Reviews') ?>)</p>
                                                <p><i class="fas fa-closed-captioning"></i><?php echo site_phrase($course_details['language']); ?></p>
                                            </div>
                                            <p class="ellipsis-line-2"><?php echo $course_details['short_description']; ?></p>
                                            <div class="courses-price-border">
                                                <div class="courses-price">
                                                    <div class="courses-price-left">
                                                        <?php if ($course_details['is_free_course']) : ?>
                                                            <h5 class="price-free"><?php echo get_phrase('Free'); ?></h5>
                                                        <?php elseif ($course_details['discount_flag']) : ?>
                                                            <h5><?php echo currency($course_details['discounted_price']); ?></h5>
                                                            <p class="mt-1"><del><?php echo currency($course_details['price']); ?></del></p>
                                                        <?php else : ?>
                                                            <h5><?php echo currency($course_details['price']); ?></h5>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="courses-price-right ">
                                                        <p class="me-2"><i class="fa-regular fa-list-alt p-0 text-15px"></i> <?php echo $lessons->num_rows() . ' ' . get_phrase('lessons'); ?></p>
                                                        <p><i class="fa-regular fa-clock text-15px p-0"></i> <?php echo $course_duration; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="course-description requirements">
                                <h3 class="description-head"><?php echo get_phrase('Package Features') ?></h3>
                                <ul>
                                    <?php foreach (json_decode($package['features']) as $feature) : ?>
                                        <?php if ($feature != "") : ?>
                                            <li><?php echo $feature; ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="instructor" role="tabpanel" aria-labelledby="instructor-tab">
                            <?php include "course_page_instructor.php"; ?>
                        </div>


                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 order-1 order-lg-2">
                <div class="course-right-section">
                    <div class="course-card">
                        <div class="card-img">
                            <div class="courses-card-image">
                                <img class="w-100" src="<?php echo $this->team_package_model->get_package_thumbnail($package['id']); ?>">
                            </div>
                        </div>
                        <div class="ammount d-flex">
                            <?php if ($package['is_free_package']) : ?>
                                <h1 class="fw-500"><?php echo get_phrase('Free'); ?></h1>

                            <?php else : ?>
                                <h1 class="fw-500"><?php echo currency($package['price']); ?></h1>
                            <?php endif; ?>
                        </div>


                        <div class="enrol">
                            <div class="icon">
                                <img src="<?php echo base_url('assets/frontend/default-new/image/c-enrold-2.png') ?>">
                                <h4><?php echo get_phrase('Enroll Limit') ?></h4>
                            </div>
                            <h5><?php echo get_phrase($package['max_students']) . ' ' . get_phrase('enrolls'); ?></h5>
                        </div>


                        <div class="enrol">
                            <div class="icon">
                                <img src="<?php echo base_url('assets/frontend/default-new/image/c-enrold-5.png') ?>">
                                <h4><?php echo get_phrase('Expiry period') ?></h4>
                            </div>
                            <h5>
                                <?php if ($package['expiry_period'] == 'lifetime') : ?>
                                    <?php echo get_phrase('Lifetime') ?>
                                <?php else : ?>
                                    <?php echo $package['expiry_period'] . ' ' . get_phrase('Months'); ?>
                                <?php endif; ?>
                            </h5>
                        </div>


                        <!-- button -->
                        <div class="button">
                            <?php $purchased = $this->team_package_model->is_purchased($package['id']); ?>
                            <?php if (!$purchased) : ?>
                                <a href="<?php echo site_url('addons/team_training/purchase/' . $package['id']); ?>"><i class="fas fa-credit-card"></i> <?php echo get_phrase('Buy Now'); ?></a>
                            <?php else : ?>
                                <a href="<?php echo site_url('addons/team_training/my_selected_team/' . $package['id']); ?>"><i class="fa-solid fa-right-to-bracket"></i>
                                    <?php echo get_phrase('join_now'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--------- course Description Page end ------>

<style>
    .course-card {
        margin-top: -150px;
    }

    .course-heading-info .info-tag {
        margin: 0;
    }
</style>