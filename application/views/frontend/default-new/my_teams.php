<?php $user_details = $this->user_model->get_all_user($this->session->userdata('user_id'))->row_array(); ?>
<?php include "breadcrumb.php"; ?>

<!-------- Wish List body section start ------>
<section class="wish-list-body ">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-12">
                <?php include "profile_menus.php"; ?>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-12">
                <div class="my-course-1-full-body">
                    <h1><?php echo get_phrase('Teams'); ?></h1>
                    <div class="row">
                        <?php foreach ($teams as $team) :
                            $course_id = $team['course_id'];
                            $course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
                            $instructor_details = $this->user_model->get_all_user($course_details['creator'])->row_array();
                            $course_duration = $this->crud_model->get_total_duration_of_lesson_by_course_id($course_details['id']);
                            $lectures = $this->db->get_where('lesson', ['course_id' => $course_details['id'], 'lesson_type !=' => 'quiz']);
                            $quizzes = $this->db->get_where('lesson', ['course_id' => $course_details['id'], 'lesson_type' => 'quiz']);
                            $watch_history = $this->crud_model->get_watch_histories($this->session->userdata('user_id'), $course_details['id'])->row_array();
                            $course_progress = isset($team['team_progress']) ? $team['team_progress'] : 0;
                        ?>
                            <div class="col-lg-12 col-md-12 col-sm-6 col-12 team">
                                <div class="my-course-1-full-body-card">
                                    <div class="my-course-1-img" onclick="">
                                        <a href="<?php echo site_url('addons/team_training/my_selected_team/') . $team['package_id']; ?>">
                                            <img src="<?php echo $this->team_package_model->get_package_thumbnail($team['package_id']); ?>" alt="">
                                        </a>
                                    </div>
                                    <div class="my-course-1-text pt-1">
                                        <div class="my-course-1-text-heading">
                                            <h3><a href="<?php echo site_url('addons/team_training/my_selected_team/') . $team['package_id']; ?>"><?php echo $team['title']; ?></a></h3>
                                            <div class="child-icon">
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle py-0" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="dropdownMenuButton2">
                                                        <li>
                                                            <a class="dropdown-item py-2" href="<?php echo site_url('home/course/' . rawurlencode(slugify($course_details['title'])) . '/' . $course_details['id']); ?>"><?php echo get_phrase('Go to course page') ?></a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item py-2" href="<?php echo site_url('home/instructor_page/' . $course_details['creator']) ?>"><?php echo get_phrase('Author profile') ?></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-course-1-lesson-text mb-2">
                                            <div class="icon-1">
                                                <p><i class="far fa-play-circle"></i> <?php echo $course_details['title']; ?></p>
                                            </div>
                                            <div class="icon-1">
                                                <p><i class="fas fa-user-circle"></i> <?php echo get_phrase('Students') . ' ' . $team['student_count'] . '/' . $team['max_students']; ?></p>
                                            </div>
                                        </div>


                                        <?php include 'live_class_scadule.php'; ?>

                                        <div class="my-course-1-last">
                                            <div class="icon-img d-grid">
                                                <?php $my_rating = $this->crud_model->get_user_specific_rating('course', $course_details['id']); ?>
                                                <div class="d-flex align-items-center">
                                                    <img class="ms-0" src="<?php echo $this->user_model->get_user_image_url($this->session->userdata('user_id')); ?>" alt="">
                                                    <span class="text-14px ms-1 mt-1"><?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?> </span>
                                                    <div class="star m-0">
                                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                            <i class="fa-solid fa-star <?php if ($my_rating['rating'] >= $i) echo 'gold'; ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                <?php if ($team['expiry_date'] > 0 && $team['expiry_date'] < time()) : ?>
                                                    <span class="text-12px text-start mt-2"><?php echo get_phrase('Expired') ?> - <b class="text-danger"><?php echo date('d M Y, H:i A', $enrolment['expiry_date']); ?></b></span>
                                                <?php else : ?>
                                                    <?php if ($team['expiry_date'] == 0) : ?>
                                                        <span class="text-12px text-start mt-2"><?php echo get_phrase('Expiry period') ?> - <b class="text-success text-uppercase"><?php echo get_phrase('Lifetime Access'); ?></b></span>
                                                    <?php else : ?>
                                                        <span class="text-12px text-start mt-2"><?php echo get_phrase('Expiration On') ?> - <b><?php echo date('d M Y, H:i A', $team['expiry_date']); ?></b></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="my-course-1-btn pt-4 me-4">
                                                <?php if ($team['expiry_date'] > 0 && $team['expiry_date'] < time()) : ?>
                                                    <a class="btn text-14px py-1 text-white bg-danger" href="#">
                                                        <?php echo get_phrase('Join again'); ?>
                                                    </a>
                                                <?php else : ?>
                                                    <a class="btn btn-primary text-14px py-1" href="<?php echo site_url('addons/team_training/my_selected_team/') . $team['package_id']; ?>">
                                                        <?php echo get_phrase('team_details'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-------- wish list body section end ------->


<style>
    .team {
        margin-bottom: 16px !important;
    }

    .team:nth-last-of-type(1) {
        margin-bottom: 0 !important;
    }

    .my-course-1-text-heading h3 a {
        color: var(--primary-clr)
    }
</style>