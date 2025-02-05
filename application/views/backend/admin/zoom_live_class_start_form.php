<?php
$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
$live_class_details = $this->db->where('course_id', $course_id)->get('live_class')->row_array();

$user_id = $this->session->userdata('user_id');
$user_details = $this->user_model->get_all_user($user_id)->row_array();
$credentials = $this->db->where('user_id', $course_details['creator'])->get('zoom_live_class_settings');

if ($credentials->num_rows() == 0 || $credentials->row('client_id') == '' || $credentials->row('client_secret') == '') {
    $this->session->set_flashdata('error_message', site_phrase('To start the Zoom meeting, you need to configure the Zoom settings from the Course Creator account. Also, the meeting ID and password must be configured from the same account.'));
    redirect(site_url('admin/course_form/course_edit/' . $course_details['id'] . '?tab=live-class'), 'refresh');
}

if ($this->crud_model->is_course_instructor($course_details['id'], $user_id) || $this->session->userdata('admin_login')) {
    $is_host = 1;
    if ($this->session->userdata('admin_login')) {
        $leaveUrl = site_url('admin/course_form/course_edit/' . $course_details['id'] . '?tab=live-class');
    } else {
        $leaveUrl = site_url('user/course_form/course_edit/' . $course_details['id'] . '?tab=live-class');
    }
} elseif (enroll_status($course_details['id']) == 'valid') {
    $is_host = 0;
    $leaveUrl = site_url('home/my_courses');
} else {
    $this->session->flashdata('error_message', get_phrase('You do not have access to this course'));
    redirect(site_url('home/my_courses'), 'refresh');
}

?>

<!DOCTYPE html>

<head>
    <title><?php echo $course_details['title']; ?> | <?php get_phrase('Live Class'); ?></title>
    <meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <style type="text/css">
        .ax-outline-blue-important:first-child {
            display: none !important;
        }
    </style>
</head>

<body>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/lodash.min.js"></script>
    <script src="https://source.zoom.us/zoom-meeting-3.1.6.min.js"></script>

    <script>
        var mn = "<?php echo $live_class_details['zoom_meeting_id']; ?>";
        var user_name = "<?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?>";
        var pwd = "<?php echo $live_class_details['zoom_meeting_password']; ?>";
        var role = <?php echo $is_host; ?>;
        var email = "<?php echo $user_details['email']; ?>";
        var lang = "en-US";
        var china = 0;

        var sdkKey = "<?php echo $credentials->row('client_id'); ?>"; //SDK Key or Client ID
        var sdkSecret = "<?php echo $credentials->row('client_secret'); ?>"; //SDK Secret or Client Secret
        var leaveUrl = "<?php echo $leaveUrl; ?>";



        //Generate signature here
        ZoomMtg.generateSDKSignature({
            meetingNumber: mn,
            sdkKey: sdkKey,
            sdkSecret: sdkSecret,
            role: role,
            success: function(signature) {
                console.log(ZoomMtg.checkSystemRequirements())
                console.log(signature)

                //After generating the signature, initializing the meeting
                ZoomMtg.preLoadWasm();
                ZoomMtg.prepareWebSDK();
                ZoomMtg.i18n.load(lang);
                ZoomMtg.init({
                    leaveUrl: leaveUrl,
                    disableCORP: !window.crossOriginIsolated, // default true
                    success: function() {

                        //Join to the meeting
                        ZoomMtg.join({
                            meetingNumber: mn,
                            userName: user_name,
                            signature: signature,
                            sdkKey: sdkKey,
                            userEmail: email,
                            passWord: pwd,
                            success: function(res) {
                                console.log("join meeting success");
                                console.log("get attendeelist");
                                ZoomMtg.getAttendeeslist({});
                                ZoomMtg.getCurrentUser({
                                    success: function(res) {
                                        console.log("success getCurrentUser", res.result.currentUser);
                                    },
                                });
                            },
                            error: function(res) {
                                console.log(res);
                            },
                        });
                    },
                    error: function(res) {
                        console.log(res);
                    },
                });

                ZoomMtg.inMeetingServiceListener("onUserJoin", function(data) {
                    console.log("inMeetingServiceListener onUserJoin", data);
                });

                ZoomMtg.inMeetingServiceListener("onUserLeave", function(data) {
                    console.log("inMeetingServiceListener onUserLeave", data);
                });

                ZoomMtg.inMeetingServiceListener("onUserIsInWaitingRoom", function(data) {
                    console.log("inMeetingServiceListener onUserIsInWaitingRoom", data);
                });

                ZoomMtg.inMeetingServiceListener("onMeetingStatus", function(data) {
                    console.log("inMeetingServiceListener onMeetingStatus", data);
                });
            },
        });
    </script>
</body>

</html>