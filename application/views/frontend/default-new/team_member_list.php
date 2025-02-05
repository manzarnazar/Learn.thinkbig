<?php if (count($members) > 0) : ?>
    <?php foreach ($members as $member) : if ($this->session->userdata('user_id') != $member['id']) : ?>
            <tr>
                <td>
                    <div>
                        <p class="fw-600"><?php echo ucfirst($member['first_name']) . ' ' . $member['last_name']; ?></p>
                        <p class="text-12px"><?php echo $member['email']; ?></p>
                    </div>
                </td>

                <td>
                    <?php $check_member = $this->team_package_model->my_team_member($package_id, $member['id']); ?>
                    <div class="d-flex justify-content-center member-<?php echo $member['id']; ?>" id="<?php echo $member['id'] ?>">
                        <?php if ($check_member) : ?>
                            <a id="remove" class="e_btn secondary danger" href="<?php echo site_url('addons/team_training/remove_member/' . $member['id']); ?>"><i class="fa-solid fa-remove me-2"></i><?php echo get_phrase('remove'); ?></a>
                        <?php else : ?>
                            <a id="add" class="e_btn secondary" href="<?php echo site_url('addons/team_training/add_member/' . $member['id']); ?>"><i class="fa-solid fa-plus me-2"></i><?php echo get_phrase('add_member'); ?></a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
    <?php endif;
    endforeach; ?>
<?php else : ?>
    <tr>
        <td colspan="2">
            <div>
                <p class="text-center fw-600"><?php echo get_phrase('no_data_found'); ?></p>
            </div>
        </td>
    </tr>
<?php endif; ?>

<script>
    'use strict';

    $(document).ready(function() {
        $('.e_btn').on('click', function(e) {
            e.preventDefault();
            var action = $(this).attr('id');
            var user_id = $(this).parent().attr('id');
            var element = $(this);

            $.ajax({
                type: "get",
                url: "<?php echo site_url('addons/team_training/member_action') ?>",
                data: {
                    user_id: user_id,
                    package_id: <?php echo $package_id; ?>,
                    action: action
                },
                success: function(res) {
                    // handle server response
                    if (res == 'removed') {
                        element.removeClass('danger');
                        element.html('<i class="fa-solid fa-plus me-2"></i>Add member');
                        element.attr('href', '<?php echo site_url('addons/team_training/add_member/'); ?>' + user_id);
                        element.attr('id', 'add');

                        // user added msg
                        toastr.error("<?php echo get_phrase('user_removed_from_team') ?>");
                    } else if (res == 'added') {
                        element.addClass('danger');
                        element.html('<i class="fa-solid fa-remove me-2"></i>Remove');
                        element.attr('href', '<?php echo site_url('addons/team_training/remove_member/'); ?>' + user_id);
                        element.attr('id', 'remove');

                        // user removed msg
                        toastr.success("<?php echo get_phrase('user_added_to_the_team') ?>");
                    } else {
                        // other res will be errors
                        toastr.error(res);
                    }
                },
                error: function() {
                    // Handle AJAX request failure
                    toastr.error('An error occurred during the request.');
                }
            });
        });
    });
</script>