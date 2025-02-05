<?php
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'all';
$selected_price    = isset($_GET['price']) ? $_GET['price'] : 'all';
$selected_level    = isset($_GET['level']) ? $_GET['level'] : 'all';
$selected_language = isset($_GET['language']) ? $_GET['language'] : 'all';
$selected_rating   = isset($_GET['rating']) ? $_GET['rating'] : 'all';
$selected_sorting  = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'all';
$selected_searching  = isset($_GET['search_input']) ? $_GET['search_input'] : '';
?>

<?php include "breadcrumb.php"; ?>


<section class="grid-view courses-list-view">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-4 col-12">
                <form action="<?php echo site_url('addons/team_training/packages'); ?>" method="get" id="course_filter_form">

                    <?php if (isset($_GET['query']) && !empty($_GET['query'])) : ?>
                        <input type="hidden" name="query" value="<?php echo $_GET['query']; ?>">
                    <?php endif; ?>

                    <div class="course-all-category">
                        <div class="course-category">
                            <h3><?php echo get_phrase('Categories'); ?></h3>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="all" name="category" id="category_all" onchange="filterCourse()" <?php if ($selected_category == 'all') echo 'checked'; ?>>
                                <label class="form-check-label" for="category_all">
                                    <div class="category-heading">
                                        <span class="text-13px"><?php echo get_phrase('All category') ?></span>
                                    </div>
                                </label>
                            </div>

                            <div class="webdesign webdesign-category less">
                                <?php $categories = $this->crud_model->get_categories()->result_array(); ?>
                                <?php foreach ($categories as $category) : ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="<?php echo $category['slug'] ?>" name="category" id="category-<?php echo $category['id']; ?>" onchange="filterCourse()" <?php if ($selected_category == $category['slug']) echo 'checked'; ?>>
                                        <label class="form-check-label" for="category-<?php echo $category['id']; ?>">
                                            <div class="category-heading">
                                                <span class="text-13px"><?php echo $category['name']; ?></span>
                                            </div>
                                        </label>
                                    </div>

                                    <ul>
                                        <?php foreach ($this->crud_model->get_sub_categories($category['id']) as $sub_category) : ?>
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" value="<?php echo $sub_category['slug'] ?>" name="category" id="sub_category-<?php echo $sub_category['id']; ?>" onchange="filterCourse()" <?php if ($selected_category == $sub_category['slug']) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="sub_category-<?php echo $sub_category['id']; ?>">
                                                        <span class="text-13px"><?php echo $sub_category['name']; ?></span>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endforeach; ?>
                            </div>

                            <div class="show-more">
                                <a class="show-more-less-btn" href="#" onclick="$('.course-all-category .course-category .webdesign-category').toggleClass('less'); $('.show-more-less-btn').toggleClass('d-none');"><?php echo get_phrase('Show More'); ?></a>
                                <a class="show-more-less-btn d-none" href="#" onclick="$('.course-all-category .course-category .webdesign-category').toggleClass('less'); $('.show-more-less-btn').toggleClass('d-none');"><?php echo get_phrase('Show Less'); ?></a>
                            </div>
                        </div>

                        <div class="course-price course-category">
                            <h3><?php echo get_phrase('Price'); ?></h3>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" value="all" id="price_all" onchange="filterCourse()" <?php if ($selected_price == 'all') echo 'checked'; ?>>
                                <label class="form-check-label" for="price_all">
                                    <span class="text-13px"><?php echo get_phrase('All'); ?></span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" value="free" id="price_free" onchange="filterCourse()" <?php if ($selected_price == 'free') echo 'checked'; ?>>
                                <label class="form-check-label" for="price_free">
                                    <span class="text-13px"><?php echo get_phrase('Free'); ?></span>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" value="paid" id="price_paid" onchange="filterCourse()" <?php if ($selected_price == 'paid') echo 'checked'; ?>>
                                <label class="form-check-label" for="price_paid">
                                    <span class="text-13px"><?php echo get_phrase('Paid'); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <input id="sorting_hidden_input" type="hidden" name="sort_by" value="<?php echo $selected_sorting; ?>">
                    <input id="searching_hidden_input" type="hidden" name="search_input" value="<?php echo $selected_searching; ?>">
                </form>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-8">
                <div class="grid-view-body courses courses-list-view-body">
                    <div id="btnContainer ">
                        <div class="list-card-control d-flex align-items-center">
                            <p class="text-14px"><?php echo site_phrase('showing') . ' ' . count($packages) . ' ' . site_phrase('of') . ' ' . $total_result . ' ' . site_phrase('results'); ?></p>
                            <div class="search-box ms-auto">
                                <input type="text" id="searching_input" name="search_input" class="form-control" placeholder="<?php echo get_phrase('Search here'); ?>" onchange="filterCourse()" value="<?php echo $selected_searching; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="courses-card courses-list-view-card">
                        <?php foreach ($packages as $package) : ?>
                            <?php
                            $lessons = $this->crud_model->get_lessons('course', $package['course_id']);
                            $instructor_details = $this->user_model->get_all_user($package['user_id'])->row_array();
                            $course_duration = $this->crud_model->get_total_duration_of_lesson_by_course_id($package['course_id']);
                            $total_rating =  $this->crud_model->get_ratings('course', $package['course_id'], true)->row()->rating;
                            $number_of_ratings = $this->crud_model->get_ratings('course', $package['course_id'])->num_rows();
                            $course_details = $this->crud_model->get_course_by_id($package['course_id'])->row_array();
                            $expiry_period = get_phrase('Lifetime');
                            if ($package['expiry_period'] != 'lifetime') {
                                $expiry_period = $package['expiry_period'] . ' ' . get_phrase('Months');
                            }
                            ?>
                            <!-- Course List Card -->
                            <a href="<?php echo site_url('addons/team_training/package_details/' . $package['id']); ?>" class="courses-list-view-card-body courses-card-body checkPropagation">
                                <div class="courses-card-image ">
                                    <img src="<?php echo $this->team_package_model->get_package_thumbnail($package['id']); ?>">
                                    <div class="courses-card-image-text">
                                        <h3><?php echo get_phrase($package['max_students']) . ' ' . get_phrase('students'); ?></h3>
                                    </div>
                                </div>

                                <div class="courses-text w-100">
                                    <div class="courses-d-flex-text">
                                        <h5><?php echo $package['title']; ?></h5>
                                    </div>

                                    <div class="review-icon">
                                        <p class=""><?php echo get_phrase('Created By'); ?>: </p>
                                        <p class="created-by-instructor ms-1">
                                            <?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?>
                                        </p>
                                    </div>


                                    <div class=" pb-2">
                                        <div class="courses-price">
                                            <div class="courses-price-left">
                                                <p class=""><?php echo get_phrase('Course'); ?>: </p>
                                                <p class="mx-1"><?php echo $course_details['title']; ?></p>
                                                <p class="mx-1"><i class="fas fa-closed-captioning"></i> <?php echo site_phrase($course_details['language']); ?></p>
                                            </div>
                                            <div class="courses-price-right ">
                                                <p class="me-2"><i class="fa-regular fa-list-alt p-0 text-15px"></i> <?php echo $lessons->num_rows() . ' ' . get_phrase('lessons'); ?></p>
                                                <p><i class="fa-regular fa-clock text-15px p-0"></i> <?php echo $course_duration; ?></p>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="courses-price-border">
                                        <div class="courses-price">
                                            <div class="courses-price-left">
                                                <?php if ($package['is_free_package']) : ?>
                                                    <h5 class="price-free"><?php echo get_phrase('Free'); ?></h5>

                                                <?php else : ?>
                                                    <h5><?php echo currency($package['price']); ?></h5>
                                                <?php endif; ?>
                                            </div>
                                            <div class="courses-price-right ">
                                                <p><?php echo $expiry_period; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>

                        <!------- pagination Start ------>
                        <div class="pagenation-items mb-0 mt-3">
                            <?php echo $this->pagination->create_links(); ?>
                        </div>
                    </div>
                </div>

                <?php if (count($packages) == 0) : ?>
                    <div class="not-found w-100 text-center d-flex align-items-center flex-column">
                        <img width="80px" src="<?php echo base_url('assets/global/image/not-found.svg'); ?>">
                        <h5><?php echo get_phrase('Package Not Found'); ?></h5>
                        <p><?php echo get_phrase('Sorry, try using more similar words in your search.') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
    .courses-card .courses-card-body .courses-card-image {
        position: relative;
        width: 300px;
        aspect-ratio: 16/9;
    }
</style>


<script type="text/javascript">
    'use strict';
    
    function filterCourse() {
        //sorting value added to the filter form
        var sort_by = $('#sorting_select_input').val();
        $('#sorting_hidden_input').val(sort_by);

        var search_by = $('#searching_input').val();
        $('#searching_hidden_input').val(search_by);

        $('#course_filter_form').trigger('submit');
    }
</script>