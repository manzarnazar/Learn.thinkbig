<?php
$enrolled_students = count($this->team_package_model->my_team_member($selected_team['id']));
$max_allowed_students = isset($selected_team) ? $selected_team['max_students'] : 0;
$user_details = $this->user_model->get_all_user($this->session->userdata('user_id'))->row_array();
$course_details = $this->crud_model->get_course_by_id($selected_team['course_id'])->row_array();

$invoices = $this->db->where('package_id', $selected_team['id'])
    ->where('user_id', $this->session->userdata('user_id'))
    ->get('team_package_payment')->result_array();
?>

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

                    <div class="my-course-1-text-heading">
                        <h4><?php echo $selected_team['title']; ?></h4>

                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 text-right">
                                <div class="action-btns d-flex align-items-start justify-content-end gap-3">
                                    <a class="btn btn-primary" href="<?php echo site_url('addons/team_training/my_teams'); ?>">
                                        <?php echo get_phrase('back_to_team'); ?>
                                    </a>
                                    <a class="btn btn-primary" href="#" onclick="showAjaxModal('<?php echo site_url('addons/team_training/team_add_student_form/' . $selected_team['id']); ?>', '<?php echo get_phrase('Add Team Member') ?>', 'lg')">
                                        <?php echo get_phrase('add_student'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="my-course-1-lesson-text mb-2">
                            <div class="icon-1">
                                <p><i class="far fa-play-circle"></i> <?php echo $course_details['title']; ?></p>
                            </div>
                            <div class="icon-1">
                                <p><i class="fas fa-user-circle"></i> <?php echo get_phrase('students') . ' ' . $enrolled_students . '/' . $max_allowed_students; ?></p>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="team-member-list-tab" data-bs-toggle="tab" data-bs-target="#team-member-list" type="button" role="tab" aria-controls="team-member-list" aria-selected="false">
                                <svg id="Group_12" data-name="Group 12" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 15.582 19.666">
                                    <path id="Shape" d="M7.791,1.731a6.06,6.06,0,0,0-6.06,6.06V9.522A.866.866,0,1,1,0,9.522V7.791a7.791,7.791,0,0,1,15.582,0V9.522a.866.866,0,1,1-1.731,0V7.791A6.06,6.06,0,0,0,7.791,1.731Z" transform="translate(0 9.278)" fill="#1e293b" />
                                    <path id="Shape-2" data-name="Shape" d="M5.194,8.656A3.463,3.463,0,1,0,1.731,5.194,3.463,3.463,0,0,0,5.194,8.656Zm0,1.731A5.194,5.194,0,1,0,0,5.194,5.194,5.194,0,0,0,5.194,10.388Z" transform="translate(2.597)" fill="#1e293b" fill-rule="evenodd" />
                                </svg>
                                <span class="ms-2"><?php echo get_phrase('members') ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="team-invoice-tab" data-bs-toggle="tab" data-bs-target="#team-invoice" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                <svg id="Group_13" data-name="Group 13" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 19.692">
                                    <path id="Shape" d="M14,2.5a.5.5,0,0,0-.5-.5H2.5a.5.5,0,0,0-.5.5V16.028a.455.455,0,0,0,.658.407,3,3,0,0,1,2.683,0L7.553,17.54a1,1,0,0,0,.894,0l2.211-1.106a3,3,0,0,1,2.683,0A.455.455,0,0,0,14,16.028Zm2,16.691a.5.5,0,0,1-.724.447l-2.829-1.415a1,1,0,0,0-.894,0L9.342,19.329a3,3,0,0,1-2.683,0L4.447,18.224a1,1,0,0,0-.894,0L.724,19.638A.5.5,0,0,1,0,19.191V0H16Z" transform="translate(2)" fill="#1e293b" fill-rule="evenodd" />
                                    <g id="Shape-2" data-name="Shape" transform="translate(6 4)">
                                        <path id="_5D20F028-8654-4138-BE2C-2596CB0A8C99" data-name="5D20F028-8654-4138-BE2C-2596CB0A8C99" d="M1,0A1,1,0,0,0,1,2H3A1,1,0,0,0,3,0Z" fill="#1e293b" />
                                        <path id="CB5AF5FF-CA28-49F3-8207-42C293893700" d="M1,0A1,1,0,1,0,2,1,1,1,0,0,0,1,0Z" transform="translate(6)" fill="#1e293b" />
                                        <path id="ECA14E2E-A90F-4909-9E68-1DC1F5104902" d="M0,1A1,1,0,0,1,1,0H3A1,1,0,0,1,3,2H1A1,1,0,0,1,0,1Z" transform="translate(0 4)" fill="#1e293b" />
                                        <path id="_841F264B-A82E-487A-AEC1-CFCDCADF7975" data-name="841F264B-A82E-487A-AEC1-CFCDCADF7975" d="M1,0A1,1,0,1,0,2,1,1,1,0,0,0,1,0Z" transform="translate(6 4)" fill="#1e293b" />
                                        <path id="AD528B39-E6BD-4596-94B4-DC58311EEB90" d="M0,1A1,1,0,0,1,1,0H3A1,1,0,0,1,3,2H1A1,1,0,0,1,0,1Z" transform="translate(0 8)" fill="#1e293b" />
                                        <path id="_6CF152B9-DFD7-4CE1-B45B-12E7F5ED6D14" data-name="6CF152B9-DFD7-4CE1-B45B-12E7F5ED6D14" d="M1,0A1,1,0,1,0,2,1,1,1,0,0,0,1,0Z" transform="translate(6 8)" fill="#1e293b" />
                                    </g>
                                    <path id="Shape-3" data-name="Shape" d="M0,1A1,1,0,0,1,1,0H19a1,1,0,0,1,0,2H1A1,1,0,0,1,0,1Z" fill="#1e293b" />
                                </svg>
                                <span class="ms-2"><?php echo get_phrase('invoice') ?></span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="team-member-list" role="tabpanel" aria-labelledby="team-member-list-tab">
                            <?php include "team_student_academic_progress.php"; ?>
                        </div>
                        <div class="tab-pane fade" id="team-invoice" role="tabpanel" aria-labelledby="team-invoice-tab">
                            <div class="invoice-table pt-4">

                                <?php if (count($invoices) > 0) : ?>
                                    <table class="table table-bordered mt-3">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-center"><?php echo get_phrase('serial'); ?></th>
                                                <th scope="col" class="text-center"><?php echo get_phrase('package'); ?></th>
                                                <th scope="col" class="text-center"><?php echo get_phrase('price'); ?></th>
                                                <th scope="col" class="text-center"><?php echo get_phrase('date'); ?></th>
                                                <th scope="col" class="text-center"><?php echo get_phrase('method'); ?></th>
                                                <th scope="col" class="text-center"><?php echo get_phrase('action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($invoices as $key => $invoice) : ?>
                                                <tr>
                                                    <td class="text-center text-14px"><?php echo $key + 1; ?></td>
                                                    <td class="text-center text-14px"><?php echo $invoice['package_id']; ?></td>
                                                    <td class="text-center text-14px"><?php echo currency($invoice['paid_amount']); ?></td>
                                                    <td class="text-center text-14px"><?php echo date('d-M-Y', $invoice['purchase_date']); ?></td>
                                                    <td class="text-center text-14px"><?php echo ucfirst($invoice['payment_method']); ?></td>
                                                    <td class="text-center text-14px">
                                                        <a href="<?php echo site_url('addons/team_training/team_invoice/' . $invoice['id']); ?>">
                                                            <?php echo get_phrase('invoice'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else : ?>
                                    <p class="text-center my-4 fw-semibold"><?php echo get_phrase('data_not_found'); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-------- wish list body section end ------->

<script>
    'use strict';
    
    $(document).ready(function() {
        $('.modal-dialog').addClass('modal-lg');
    });
</script>

<style>
    .invoice-2 tbody,
    td,
    tfoot,
    th,
    thead,
    tr {
        border-bottom: 1.5px solid #6e798a23 !important;
    }

    .table> :not(caption)>*>* {
        width: 80px;
    }

    .action-btns a {
        font-size: 14px;
        padding: 5px 24px !important;
    }


    .nav-link {
        display: flex;
        align-items: center;
        font-size: 14px;
    }

    .nav-link path {
        transition: .3s;
        fill: #101010;
    }

    .nav-link.active path,
    .nav-link:hover path {
        fill: #BADA55
    }

    .nav-link {
        color: #101010;
    }

    .invoice-btn {
        border-radius: 4px;
        background: #BADA55;
        color: #fff;
        padding: 4px 12px;
    }
</style>