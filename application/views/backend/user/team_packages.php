<div class="row ">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
          <a href="<?php echo site_url('addons/team_training/team_package_form/add_team_package_form'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"><i class="mdi mdi-plus"></i><?php echo get_phrase('add_team_package'); ?></a>
        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body" data-collapsed="0">
        <h4 class="mb-3 header-title"><?php echo get_phrase('team_packages'); ?></h4>
        <table class="table table-sm  table-centered w-100" id="team_packages_data">
          <thead>
            <tr>
              <th>#</th>
              <th><?php echo get_phrase('title'); ?></th>
              <th><?php echo get_phrase('status'); ?></th>
              <th><?php echo get_phrase('pricing'); ?></th>
              <th><?php echo get_phrase('action'); ?></th>

            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div><!-- end col-->
</div>


<script>
  'use strict';
  
  $(document).ready(function() {
    var table = $('#team_packages_data').DataTable({
      responsive: true,
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "<?php echo base_url('addons/team_training/server_side_team_packages_data') ?>",
        "dataType": "json",
        "type": "POST",
        "data": {
          '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        }

      },
      "columns": [{
          "data": "key"
        },

        {
          "data": "pkg_title"
        },
        {
          "data": "pkg_status"
        },
        {
          "data": "pkg_price"
        },
        {
          "data": "action"
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