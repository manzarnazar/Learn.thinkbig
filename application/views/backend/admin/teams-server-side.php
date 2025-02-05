<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('teams'); ?>

                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>



<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title"><?php echo get_phrase('course_list'); ?></h4>


                <form class="row justify-content-center" action="<?php echo site_url('addons/team_training/teams_list'); ?>" method="get">
                    <!-- Course Categories -->
                    <div class="col-xl-3">
                        <div class="form-group">
                            <label for="category_id"><?php echo get_phrase('categories'); ?></label>
                            <select class="form-control select2" data-toggle="select2" name="category_id" id="category_id">
                                <option value="<?php echo 'all'; ?>" <?php if ($selected_category_id == 'all') echo 'selected'; ?>><?php echo get_phrase('all'); ?></option>
                                <?php foreach ($categories->result_array() as $category) : ?>
                                    <optgroup label="<?php echo $category['name']; ?>">
                                        <?php $sub_categories = $this->crud_model->get_sub_categories($category['id']);
                                        foreach ($sub_categories as $sub_category) : ?>
                                            <option value="<?php echo $sub_category['id']; ?>" <?php if ($selected_category_id == $sub_category['id']) echo 'selected'; ?>><?php echo $sub_category['name']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>


                    <!-- Courses -->
                    <div class="col-xl-3">
                        <div class="form-group">
                            <label for="course_id"><?php echo get_phrase('course'); ?></label>
                            <select class="form-control server-side-select2" name="course_id" id='course_id' action="<?php echo site_url('addons/team_training/get_select2_course_data_for_team_training/all'); ?>">
                                <option value="all" <?php if ($selected_instructor_id == 'all') echo 'selected'; ?>><?php echo get_phrase('all'); ?></option>

                                <?php if (isset($_GET['course_id']) && $_GET['course_id'] != 'all') : ?>
                                    <?php $course_details = $this->crud_model->get_course_by_id($_GET['course_id'])->row_array(); ?>
                                    <option value="<?php echo $_GET['course_id']; ?>" selected><?php echo $course_details['title']; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Instructors -->
                    <div class="col-xl-3">
                        <div class="form-group">
                            <label for="instructor_id"><?php echo get_phrase('instructor'); ?></label>
                            <select class="form-control server-side-select2" name="instructor_id" id='instructor_id' action="<?php echo site_url('admin/get_select2_instructor_data/all'); ?>">
                                <option value="all" <?php if ($selected_instructor_id == 'all') echo 'selected'; ?>><?php echo get_phrase('all'); ?></option>

                                <?php if (isset($_GET['instructor_id']) && $_GET['instructor_id'] != 'all') : ?>
                                    <?php $instructor_details = $this->user_model->get_all_user($_GET['instructor_id'])->row_array(); ?>
                                    <option value="<?php echo $_GET['instructor_id']; ?>" selected><?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-xl-2">
                        <label for=".." class="text-white">..</label>
                        <button type="submit" class="btn btn-primary btn-block" name="button"><?php echo get_phrase('filter'); ?></button>
                    </div>
                </form>

                <div class="table-responsive-sm mt-4">
                    <table id="teams-datatable-server-side" class="table table-striped dt-responsive nowrap" width="100%" data-page-length='25'>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo get_phrase('title'); ?></th>
                                <th><?php echo get_phrase('lesson_and_section'); ?></th>
                                <th><?php echo get_phrase('enrolled_student'); ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    'use strict';
    
    $(document).ready(function() {
        var table = $('#teams-datatable-server-side').DataTable({
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo base_url('addons/team_training/get_teams') ?>",
                "dataType": "json",
                "type": "POST",
                "data": {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    selected_category_id: '<?php echo $selected_category_id; ?>',
                    selected_course_id: '<?php echo $selected_course_id ?>',
                    selected_instructor_id: '<?php echo $selected_instructor_id ?>',
                }


            },
            "columns": [{
                    "data": "#"
                },
                {
                    "data": "title"
                },
                {
                    "data": "lesson_and_section"
                },
                {
                    "data": "enrolled_student"
                }
            ],
            order: [
                [1, 'asc']
            ]


        });
    });

    function refreshServersideTable(tableId) {
        $('#' + tableId).DataTable().ajax.reload();
    }
</script>