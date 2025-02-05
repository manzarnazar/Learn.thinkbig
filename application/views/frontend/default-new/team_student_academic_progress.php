<?php
$members = $this->db->where('leader_id', $this->session->userdata('user_id'))
    ->where('team_id', $selected_team['id'])
    ->get('team_members')->result_array();
?>

<div class="table-responsive mt-4">
    <?php if (count($members) > 0) : ?>
        <table class="studentAcademicProgress table table-striped table-centered">
            <thead>
                <tr>
                    <th><?php echo get_phrase('Student'); ?></th>
                    <th class="text-center"><?php echo get_phrase('Progress'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $key => $member) :
                    $student = $this->db->where('id', $member['member_id'])->get('users')->row_array();
                    $watch_history = $this->crud_model->get_watch_histories($member['member_id'], $selected_team['course_id'])->row_array();
                    $course_progress = isset($watch_history['course_progress']) ? $watch_history['course_progress'] : 0;
                ?>
                    <tr>
                        <td>
                            <p class="my-0"><?php echo ucfirst($student['first_name']) . ' ' . $student['last_name']; ?></p>
                            <small><span class="fw-semibold fst-italic"><?php echo $student['email']; ?></span></small>
                        </td>

                        <td>
                            <div class="my-course-1-skill">
                                <div class="skill-bar-container">
                                    <div class="skill-bar" style="width: <?php echo $course_progress; ?>%; animation: unset"></div>
                                </div>
                                <p><?php echo $course_progress; ?>%</p>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p class="text-center my-4 fw-semibold"><?php echo get_phrase('data_not_found'); ?></p>
    <?php endif; ?>
</div>

<script type="text/javascript">
    'use strict';

    $('[data-toggle=tooltip]').tooltip();
</script>

<style>
    .my-course-1-skill p {
        margin-top: 0 !important;
    }

    .my-course-1-skill {
        display: flex;
        align-items: center;
        height: 49px;
    }

    .skill-bar-container {
        margin-bottom: 0;
        height: 10px !important;
    }
</style>