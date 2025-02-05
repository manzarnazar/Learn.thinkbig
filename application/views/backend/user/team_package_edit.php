<style>
    .ml--6px{
        margin-left: -6px;
    }
    .w-250px{
        width: 250px;
    }
    .v-hidden{
        visibility: hidden;
    }
</style>

<?php
$team_package_details = $this->team_package_model->get_team_package_by_id($team_package_id)->row_array();

$course_details = $this->crud_model->get_course_by_id($team_package_details['course_id'])->row_array();

$courses = $this->team_package_model->get_status_wise_courses('active', $this->session->userdata('user_id'));

$selected_course_price = $course_details['price'];
$selected_course_expiry = $course_details['expiry_period'];
$selected_course_id = $course_details['id'];

if (empty($selected_course_expiry)) {
    $selected_course_expiry = get_phrase('Lifetime');
} else {
    $selected_course_expiry = $selected_course_expiry . ' ' . get_phrase('months');
}



?>
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('update') . ': ' . $team_package_details['title']; ?></h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <!--ajax page loader-->
            <div class="ajax_loader w-100">
                <div class="ajax_loaderBar"></div>
            </div>
            <!--end ajax page loader-->
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <h4 class="header-title my-1"><?php echo get_phrase('team_package_manager'); ?></h4>
                    </div>
                    <div class="col-md-6">
                        <a href="<?php echo site_url('addons/team_training/team_packages'); ?>" class="alignToTitle btn btn-outline-secondary btn-rounded btn-sm my-1"> <i class=" mdi mdi-keyboard-backspace"></i> <?php echo get_phrase('back_to_package_list'); ?></a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <form class="required-form" action="<?php echo site_url('addons/team_training/team_packages/edit/' . $team_package_details['id']); ?>" method="post" enctype="multipart/form-data">
                            <div id="progressbarwizard">

                                <ul class="nav nav-pills nav-justified form-wizard-header mb-3">
                                    <li class="nav-item">
                                        <a href="#basic" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                            <i class="mdi mdi-fountain-pen-tip"></i>
                                            <span class=""><?php echo get_phrase('basic'); ?></span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#pricing" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                            <i class="mdi mdi-currency-cny"></i>
                                            <span class=""><?php echo get_phrase('pricing'); ?></span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#info" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                            <i class="mdi mdi-information-outline"></i>
                                            <span class=""><?php echo get_phrase('features'); ?></span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#media" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                            <i class="mdi mdi-information-outline"></i>
                                            <span class=""><?php echo get_phrase('media'); ?></span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#finish" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                                            <span class=""><?php echo get_phrase('finish'); ?></span>
                                        </a>
                                    </li>
                                    <li class="w-100 bg-white pb-3">
                                        <!--ajax page loader-->

                                        <!--end ajax page loader-->
                                    </li>
                                </ul>





                                <div class="tab-content b-0 mb-0">
                                    <div class="tab-pane" id="basic">
                                        <div class="row justify-content-center">
                                            <div class="col-xl-8">

                                                <div class="form-group row mb-3">
                                                    <label class="col-md-2 col-form-label" for="pkg_title"><?php echo get_phrase('package_title'); ?> <span class="required">*</span> </label>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control" id="pkg_title" name="title" placeholder="<?php echo get_phrase('enter_package_title'); ?>" value="<?php echo $team_package_details['title']; ?>" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-md-2 col-form-label" for="course_id_on_package_create"><?php echo get_phrase('course'); ?> <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-10">
                                                        <select class="form-control select2" data-toggle="select2" name="course_id" id="course_id_on_package_create" required>
                                                            <option value=""><?php echo get_phrase('select_a_course'); ?></option>

                                                            <?php foreach ($courses as $course) : ?>
                                                                <option value="<?php echo $course['id']; ?>" <?php if ($course['id'] == $team_package_details['course_id']) echo 'selected'; ?>>
                                                                    <?php echo $course['title']; ?></option>
                                                            <?php endforeach; ?>


                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3">
                                                    <label class="col-md-2 col-form-label" for="max_students_for_package_create"><?php echo get_phrase('maximum_number_of_students'); ?> <span class="required">*</span> </label>
                                                    <div class="col-md-10">
                                                        <input type="number" class="form-control" id="max_students_for_package_create" value="<?php echo $team_package_details['max_students']; ?>" name="max_students" placeholder="<?php echo get_phrase('enter_the_maximum_students_limit'); ?>" min="0" required>
                                                    </div>
                                                </div>


                                                <div class="form-group row mb-3">
                                                    <label class="col-md-2 col-form-label" for="pkg_status"><?php echo get_phrase('package_status'); ?> <span class="required">*</span> </label>
                                                    <div class="col-md-10 pt-2">
                                                        <input type="checkbox" name="pkg_status" value="1" id="pkg_status" data-switch="primary" <?php if ($team_package_details['status'] == 1) echo 'checked'; ?>>
                                                        <label for="pkg_status" data-on-label="" data-off-label=""></label>
                                                    </div>
                                                </div>

                                            </div> <!-- end col -->
                                        </div> <!-- end row -->
                                    </div> <!-- end tab pane -->

                                    <div class="tab-pane" id="info">
                                        <div class="row justify-content-center">
                                            <div class="col-xl-8">


                                                <div class="form-group row mb-3 pt-2">
                                                    <label class="col-md-2 col-form-label" for="features"><?php echo get_phrase('features'); ?></label>
                                                    <div class="col-md-10">
                                                        <div id="feature_area">


                                                            <?php if (count(json_decode($team_package_details['features'])) > 0) : ?>
                                                                <?php
                                                                $counter = 0;
                                                                foreach (json_decode($team_package_details['features']) as $feature) : ?>
                                                                    <?php if ($counter == 0) :
                                                                        $counter++; ?>
                                                                        <div class="d-flex mt-2">
                                                                            <div class="flex-grow-1 px-3">
                                                                                <div class="form-group">
                                                                                    <input type="text" class="form-control" name="features[]" id="features" placeholder="<?php echo get_phrase('provide_features'); ?>" value="<?php echo $feature; ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <button type="button" class="btn btn-success btn-sm" name="button" onclick="appendfeature()"> <i class="fa fa-plus"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    <?php else : ?>
                                                                        <div class="d-flex mt-2">
                                                                            <div class="flex-grow-1 px-3">
                                                                                <div class="form-group">
                                                                                    <input type="text" class="form-control" name="features[]" id="features" placeholder="<?php echo get_phrase('provide_features'); ?>" value="<?php echo $feature; ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <button type="button" class="btn btn-danger btn-sm mt-0" name="button" onclick="removefeature(this)"> <i class="fa fa-minus"></i> </button>
                                                                            </div>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            <?php else : ?>
                                                                <div class="d-flex mt-2">
                                                                    <div class="flex-grow-1 px-3">
                                                                        <div class="form-group">
                                                                            <input type="text" class="form-control" name="features[]" id="features" placeholder="<?php echo get_phrase('provide_features'); ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="">
                                                                        <button type="button" class="btn btn-success btn-sm" name="button" onclick="appendfeature()"> <i class="fa fa-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>


                                                            <div id="blank_feature_field">
                                                                <div class="d-flex mt-2">
                                                                    <div class="flex-grow-1 px-3">
                                                                        <div class="form-group">
                                                                            <input type="text" class="form-control" name="features[]" id="features" placeholder="<?php echo get_phrase('provide_features'); ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="">
                                                                        <button type="button" class="btn btn-danger btn-sm mt-0" name="button" onclick="removeRequirement(this)"> <i class="fa fa-minus"></i> </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="pricing">
                                        <div class="row justify-content-center">
                                            <div class="col-xl-8">
                                                <div class="form-group row mb-3">
                                                    <div class="offset-md-2 col-md-10">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="is_free_package" id="is_free_package" value="1" <?php if ($team_package_details['is_free_package'] == 1) echo 'checked'; ?> onclick="togglePriceFields(this.id)">
                                                            <label class="custom-control-label" for="is_free_package"><?php echo get_phrase('check_if_this_is_a_free_package'); ?></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="paid-course-stuffs">
                                                    <div class="form-group row mb-3">
                                                        <label class="col-md-2 col-form-label" for="subtotal"><?php echo get_phrase('subtotal') . ' (' . currency_code_and_symbol() . ')'; ?></label>
                                                        <div class="col-md-10">
                                                            <input class="form-control" id="subtotal" value="<?php echo $selected_course_price * $team_package_details['max_students']; ?>" name="subtotal" placeholder="<?php echo get_phrase('price_without_package'); ?>" min="0" readonly>
                                                            <small class="text-muted subtotal-text">
                                                                <?php echo get_phrase('this_is_current_course_price_for'); ?>
                                                                <span class="limit"><?php echo $team_package_details['max_students'] ?></span>
                                                                <?php echo strtolower(get_phrase('students')); ?>
                                                            </small>
                                                        </div>
                                                    </div>


                                                    <div class="form-group row mb-3">
                                                        <label class="col-md-2 col-form-label" for="price"><?php echo get_phrase('package_price') . ' (' . currency_code_and_symbol() . ')'; ?></label>
                                                        <div class="col-md-10">
                                                            <input type="number" class="form-control" name="price" id="price" onkeyup="calculateDiscountPercentage(this.value)" min="0" value="<?php echo $team_package_details['price']; ?>">
                                                            <small class="text-muted"><?php echo get_phrase('this_package_has'); ?> <span id="discounted_percentage" class="text-danger">0%</span>
                                                                <?php echo get_phrase('discount'); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>



                                                <div class="form-group row mb-3">
                                                    <label class="col-md-2 col-form-label"><?php echo get_phrase('Package_expiry_period'); ?></label>
                                                    <div class="col-md-10 pt-2 d-flex">
                                                        <div class="custom-control custom-radio mr-2">
                                                            <input type="radio" id="lifetime_expiry_period" name="expiry_period" class="custom-control-input" value="lifetime" onchange="checkExpiryPeriod(this)" <?php if ($team_package_details['expiry_period'] == 0) echo 'checked'; ?>>
                                                            <label class="custom-control-label" for="lifetime_expiry_period"><?php echo get_phrase('Lifetime'); ?></label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="limited_expiry_period" name="expiry_period" class="custom-control-input" value="limited_time" onchange="checkExpiryPeriod(this)" <?php if ($team_package_details['expiry_period'] > 0) echo 'checked'; ?>>
                                                            <label class="custom-control-label" for="limited_expiry_period"><?php echo get_phrase('Limited time'); ?></label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-3" id="number_of_month" style="<?php if ($team_package_details['expiry_period'] == '') echo 'display: none'; ?>">
                                                    <label class="col-md-2 col-form-label"><?php echo get_phrase('Number of month'); ?></label>
                                                    <div class="col-md-10">
                                                        <input class="form-control" type="number" name="number_of_month" min="1" id="package_expiry_month" value="<?php echo $team_package_details['expiry_period']; ?>">
                                                        <small class="badge badge-light"><?php echo get_phrase('After purchase, students can access the package course until your selected time.'); ?></small>
                                                    </div>
                                                </div>
                                            </div> <!-- end col -->
                                        </div> <!-- end row -->
                                    </div> <!-- end tab-pane -->

                                    <div class="tab-pane" id="media">
                                        <div class="row justify-content-center">
                                            <?php
                                            $course_media_files = themeConfiguration(get_frontend_settings('theme'), 'course_media_files');
                                            $course_media_placeholders = themeConfiguration(get_frontend_settings('theme'), 'course_media_placeholders');
                                            foreach ($course_media_files as $course_media => $size) : ?>
                                                <div class="col-8">
                                                    <div class="form-group row mb-3">
                                                        <label class="col-3 col-form-label" for="<?php echo $course_media; ?>_label"><?php echo get_phrase('team_thumbnail'); ?></label>
                                                        <div class="col-9">
                                                            <div class="wrapper-image-preview ml--6px">
                                                                <div class="box w-250px">
                                                                    <div class="js--image-preview" style="background-image: url(<?php echo base_url() . 'uploads/team_training/thumbnail/' . $team_package_details['thumbnail'] ?>); background-color: #F5F5F5;">
                                                                    </div>
                                                                    <div class="upload-options">
                                                                        <label for="<?php echo $course_media; ?>" class="btn"> <i class="mdi mdi-camera"></i>
                                                                            <?php echo get_phrase('team_thumbnail'); ?> <br>
                                                                            <small>(<?php echo $size; ?>)</small> </label>
                                                                        <input id="<?php echo $course_media; ?>" type="file" class="image-upload v-hidden" name="thumbnail" accept="image/*">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="finish">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="text-center">
                                                    <h2 class="mt-0"><i class="mdi mdi-check-all"></i></h2>
                                                    <h3 class="mt-0"><?php echo get_phrase("thank_you"); ?> !</h3>

                                                    <p class="w-75 mb-2 mx-auto"><?php echo get_phrase('you_are_just_one_click_away'); ?></p>

                                                    <div class="mb-3 mt-3">
                                                        <button type="button" class="btn btn-primary text-center" onclick="checkRequiredFields()"><?php echo get_phrase('submit'); ?></button>
                                                    </div>
                                                </div>
                                            </div> <!-- end col -->
                                        </div> <!-- end row -->
                                    </div>

                                    <ul class="list-inline mb-0 wizard text-center">
                                        <li class="previous list-inline-item">
                                            <a href="javascript:;" class="btn btn-info"> <i class="mdi mdi-arrow-left-bold"></i> </a>
                                        </li>
                                        <li class="next list-inline-item">
                                            <a href="javascript:;" class="btn btn-info"> <i class="mdi mdi-arrow-right-bold"></i> </a>
                                        </li>
                                    </ul>

                                </div> <!-- tab-content -->
                            </div> <!-- end #progressbarwizard-->
                        </form>
                    </div>
                </div><!-- end row-->
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div>
</div>



<script>
    'use strict';
    
    $(document).ready(function() {

        togglePriceFields('is_free_package');

        calculateDiscountPercentage(<?php echo $team_package_details['price']; ?>);


        $('#course_id_on_package_create').on('change', function() {

            let courseId = $(this).val();

            let maxStudent = $("#max_students_for_package_create").val();

            if (courseId !== '') {

                $.ajax({
                    type: 'GET',
                    url: '<?php echo base_url('addons/team_training/get_course_by_id/'); ?>' + courseId,

                    success: function(response) {
                        response = JSON.parse(response);

                        console.log(response.expiry_period)

                        if (response.expiry_period == null) {



                            $("#lifetime_expiry_period").prop('checked', true);
                            $("#lifetime_expiry_period").trigger('change');
                        } else {

                            $("#package_expiry_month").val(response.expiry_period);


                            $("#limited_expiry_period").prop('checked', true);
                            $("#limited_expiry_period").trigger('change');
                        }

                        if (maxStudent == '' || maxStudent == 0) {

                            $("#subtotal").val(response.price);
                        } else {

                            $("#subtotal").val(response.price * maxStudent);
                        }

                    },
                    error: function() {
                        alert('Error: Unable to communicate with the server.');
                    }
                });
            }

        });

        $('#max_students_for_package_create').change(function(e) {
            e.preventDefault();
            var limit = $(this).val();
            $('.limit').text(limit);
        });



        $('#max_students_for_package_create').on('change', function() {

            let maxStudent = $(this).val();

            let courseId = $("#course_id_on_package_create").val();

            if (courseId !== '') {

                $.ajax({
                    type: 'GET',
                    url: '<?php echo base_url('addons/team_training/get_course_by_id/'); ?>' + courseId,

                    success: function(response) {
                        response = JSON.parse(response);


                        if (maxStudent == '' || maxStudent == 0) {

                            $("#subtotal").val(response.price);
                        } else {

                            $("#subtotal").val(response.price * maxStudent);
                        }

                    },
                    error: function() {
                        alert('Error: Unable to communicate with the server.');
                    }
                });
            }

        });




    });
</script>



<script type="text/javascript">
    'use strict';

    var blank_faq = jQuery('#blank_faq_field').html();
    var blank_outcome = jQuery('#blank_outcome_field').html();
    var blank_requirement = jQuery('#blank_feature_field').html();
    jQuery(document).ready(function() {
        jQuery('#blank_faq_field').hide();
        jQuery('#blank_outcome_field').hide();
        jQuery('#blank_feature_field').hide();
    });



    function appendRequirement() {
        jQuery('#feature_area').append(blank_requirement);
    }

    function removeRequirement(requirementElem) {
        jQuery(requirementElem).parent().parent().remove();
    }

    function priceChecked(elem) {
        if (jQuery('#discountCheckbox').is(':checked')) {

            jQuery('#discountCheckbox').prop("checked", false);
        } else {

            jQuery('#discountCheckbox').prop("checked", true);
        }
    }

    function topCourseChecked(elem) {
        if (jQuery('#isTopCourseCheckbox').is(':checked')) {

            jQuery('#isTopCourseCheckbox').prop("checked", false);
        } else {

            jQuery('#isTopCourseCheckbox').prop("checked", true);
        }
    }

    function isFreeCourseChecked(elem) {

        if (jQuery('#' + elem.id).is(':checked')) {
            $('#price').prop('required', false);
        } else {
            $('#price').prop('required', true);
        }
    }

    function calculateDiscountPercentage(discounted_price) {
        if (discounted_price > 0) {
            var actualPrice = jQuery('#subtotal').val();
            if (actualPrice > 0) {
                var reducedPrice = actualPrice - discounted_price;
                var discountedPercentage = (reducedPrice / actualPrice) * 100;
                if (discountedPercentage > 0) {
                    jQuery('#discounted_percentage').text(discountedPercentage.toFixed(2) + '%');

                } else {
                    jQuery('#discounted_percentage').text('<?php echo '0%'; ?>');
                }
            }
        }
    }
</script>

<style media="screen">
    body {
        overflow-x: hidden;
    }
</style>