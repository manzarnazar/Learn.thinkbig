<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('Zoom live class settings'); ?></h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mb-3 header-title">
                        <?php echo get_phrase('Meeting SDK Configuration');?>
                        <a target="_blank" href="https://developers.zoom.us/docs/internal-apps/create/"><i class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="top" title="<?php echo get_phrase('Zoom OAuth creation instruction'); ?>"></i></a>
                    </h4>

                    <form class="required-form" action="<?php echo site_url('addons/liveclass/settings/update'); ?>" method="post">

                        <div class="form-group">
                            <label for="client_id"><?php echo get_phrase('Client ID'); ?><span class="required">*</span></label>
                            <input type="text" name = "client_id" id = "client_id" class="form-control" value="<?php echo $zoom_live_class_settings->row('client_id');  ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="client_secret"><?php echo get_phrase('Client Secret'); ?><span class="required">*</span></label>
                            <input type="text" name = "client_secret" id = "client_secret" class="form-control" value="<?php echo $zoom_live_class_settings->row('client_secret');  ?>" required>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="checkRequiredFields()"><?php echo get_phrase('save_changes'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading"><?php echo get_phrase('How to create Zoom Meeting SDK?'); ?></h4>
            <p>1. Login to <a href="https://marketplace.zoom.us/" target="_blank">Zoom Marketplace</a></p>
            <p>2. Go to the <a href="https://marketplace.zoom.us/develop/createLegacy" target="_blank">Build Legacy App</a>. This page will appear displaying the available app types.</p>
            <p>3. Click <b>Create</b> in the <b>Meeting SDK</b> section.</p>
            <p>4. Add a name for your app and click <b>Create</b>.</p>
            <p>5. View your client ID and client secret. You'll use these credentials to authenticate with Zoom.</p>
            <p>6. Add information about your app, such as a short description and developer contact information (name and email address required for activation). <a href="https://developers.zoom.us/docs/internal-apps/create/" target="_blank">More details</a></p>
        </div>
    </div>
</div>
