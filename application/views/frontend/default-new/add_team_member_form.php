<style>
    .has-search {
        position: relative;
    }

    .has-search .form-control-feedback {
        position: absolute;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        pointer-events: none;
        color: #ccc;
        padding-left: 15px;
    }

    .has-search .form-control {
        padding-left: 36px;
    }

    tbody,
    td,
    tfoot,
    th,
    thead,
    tr:last-child {
        border-bottom: 1.5px solid #6e798a23 !important;
    }

    .e_btn {
        padding: 4px 12px !important;
        color: #fff;
        font-size: 13px;
        border-radius: 4px;
    }

    .secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        -webkit-box-shadow: 0 2px 6px 0 rgba(108, 117, 125, .5);
        box-shadow: 0 2px 6px 0 rgba(108, 117, 125, .5);
        transition: .3s;
    }

    .secondary:hover {
        color: #fff;
        background-color: #5a6268;
        border-color: #545b62;
    }

    .danger {
        color: #fff;
        background-color: #fa5c7c;
        border-color: #fa5c7c;
        -webkit-box-shadow: 0 2px 6px 0 rgba(250, 92, 124, .5);
        box-shadow: 0 2px 6px 0 rgba(250, 92, 124, .5);
    }

    .danger:hover {
        color: #fff;
        background-color: #f9375e;
        border-color: #f82b54;
    }

    .disable {
        opacity: .5;
        color: .5;
    }

    #search_results_add thead th:nth-of-type(1) {
        width: 70%;
    }

    #search_results_add thead th:nth-of-type(2) {
        width: 30%;
    }
</style>




<form class="py-4 px-4 bg-white">

    <div class="row">
        <div class="col-12 mb-3">
            <div class="form-group has-search">
                <span class="fa fa-search form-control-feedback"></span>
                <input type="text" id="search_input_name" class="form-control" placeholder="<?php echo get_phrase('search_with_email'); ?>" autocomplete="off">
            </div>
        </div>


        <div class="col-12 mb-3">
            <table class="table table-bordered" id="search_results_add">
                <thead>
                    <th scope="col"><?php echo get_phrase('Student'); ?></th>
                    <th scope="col" class="text-center"><?php echo get_phrase('Invite'); ?></th>
                </thead>

                <tbody>
                    <?php include 'team_member_list.php' ?>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
    'use strict';
    
    $(document).ready(function() {
        $('#search_input_name').on('input', function() {
            var query = $(this).val();

            if (query !== '') {
                $.ajax({
                    url: "<?php echo base_url('addons/team_training/get_students_list'); ?>",
                    method: "GET",
                    data: {
                        query: query,
                        package_id: <?php echo $package_id; ?>
                    },
                    success: function(data) {
                        $('#search_results_add tbody').empty();
                        $('#search_results_add tbody').append(data);
                    }
                });
            }
        });
    });
</script>