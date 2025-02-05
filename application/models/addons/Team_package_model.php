<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Team_package_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    // reuseable functions
    // 1. get record from any table
    function get_table($table, $id = '', $sort = '')
    {
        if ($sort != '') {
            $this->db->order_by('order_by', $sort);
        }
        if ($id != '' && is_numeric($id)) {
            $this->db->where('id', $id);
        } elseif ($id != '' && !is_numeric($id)) {
            $arr = explode('-', $id);
            $this->db->where($arr[0], $arr[1]);
        }

        return $this->db->get($table);
    }

    // 2. file uploader function
    function upload_files($name, $path, $original = '')
    {
        if (isset($_FILES[$name]) && $_FILES[$name]['name'] != "") {
            $extension = pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
            if ($original == 'original') {
                $file_name = $_FILES[$name]['name'];
            } elseif ($original == 'class_record') {
                if ($extension == 'mp4' || $extension == 'mov' || $extension == 'avi' || $extension == 'wmv' || $extension == 'WebM') {
                    $file_name = 'cls_rec_' . rand(100000, 999999) . '.' . $extension;
                } else {
                    return FALSE;
                }
            } elseif ($original == '') {
                $file_name = md5(rand(10000000, 20000000)) . '.' . $extension;
            }

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            move_uploaded_file($_FILES[$name]['tmp_name'], $path . $file_name);
            return $file_name;
        }
        return FALSE;
    }

    // 3. trim and json response
    function trim_and_return_json($untrimmed_array = [])
    {
        if (!is_array($untrimmed_array)) {
            $untrimmed_array = [];
        }
        $trimmed_array = array();
        if (sizeof($untrimmed_array) > 0) {
            foreach ($untrimmed_array as $row) {
                if ($row != "") {
                    array_push($trimmed_array, $row);
                }
            }
        }
        return json_encode($trimmed_array);
    }

    // .4 delete row
    function delete_item($table, $id)
    {
        $this->db->where('id', $id);
        $this->db->delete($table);
        return true;
    }

    // 5. get package thumbnail
    function get_package_thumbnail($package_id)
    {
        if (is_numeric($package_id) && $package_id > 0) {
            $package_details = $this->db->where('id', $package_id)->get('team_packages')->row_array();
            if (!empty($package_details) && !empty($package_details['thumbnail'])) {
                return base_url() . 'uploads/team_training/thumbnail/' . $package_details['thumbnail'];
            }
        }
        $course_media_placeholders = themeConfiguration(get_frontend_settings('theme'), 'course_media_placeholders');
        return base_url() . $course_media_placeholders['course_thumbnail' . '_placeholder'];
    }

    // 6. is purchased
    function is_purchased($package_id)
    {
        $package_details = $this->db->where('id', $package_id)->get('team_packages')->row_array();
        $user = $this->session->userdata('user_id');
        $this->db->where(['package_id' => $package_id, 'user_id' => $user]);
        $this->db->order_by('id', 'desc');
        $purchase_details = $this->db->get('team_package_payment')->row_array();

        if ($purchase_details && $package_details['expiry_period'] == 'lifetime') {
            return true;
        }

        $is_purchase_valid = $purchase_details && $purchase_details['expiry_date'] > time();
        return $is_purchase_valid ? $purchase_details : false;
    }

    function get_package_purchase($package_id)
    {
        $purchase = $this->db->where('package_id', $package_id)->get('team_package_payment')->num_rows();
        return $purchase;
    }

    // check_member_capacity
    function check_member_capacity($package_id)
    {
        $package_details = $this->db->where('id', $package_id)->get('team_packages')->row_array();
        $occupied = $this->db->where(['team_id' => $package_id, 'leader_id' => $this->session->userdata('user_id')])
            ->get('team_members')->num_rows();

        $capacity_info = [
            'max_capacity' => $package_details['max_students'],
            'occupied' => $occupied,
            'available' => $package_details['max_students'] - $occupied,
        ];
        return $capacity_info;
    }

    // related course packages
    function course_related_packages($course_id)
    {
        $related_packages = $this->db->where('course_id', $course_id)
            ->order_by('id', 'desc')->get('team_packages', 10)->result_array();
        return $related_packages;
    }

    // package sells
    function package_sells($package_id)
    {
        return $this->db->where('package_id', $package_id)->get('team_package_payment')->num_rows();
    }

    // get team members and check member exists or not
    function my_team_member($package_id, $member_id = '')
    {
        if ($member_id == '') {
            $this->db->select('team_members.*, team_members.id as member_table_id, users.id as id, users.first_name, users.last_name, users.email');
            $this->db->from('team_members');
            $this->db->join('users', 'team_members.member_id = users.id');
            $this->db->where('team_members.leader_id', $this->session->userdata('user_id'));
            $this->db->where('team_members.team_id', $package_id);
            $query = $this->db->get()->result_array();
            return $query;
        } else {
            $selected_member = $this->db->where('leader_id', $this->session->userdata('user_id'))
                ->where('member_id', $member_id)
                ->where('team_id', $package_id)
                ->get('team_members')->row_array();
            return $selected_member ? $selected_member : false;
        }
    }

    //                                       	6. package payment config
    /*------------------------------------------------------------------------------------------------------------*/
    function configure_package_payment($package_details)
    {
        $items = [];
        $total_payable_amount = 0;

        //item detail
        $item_details['id'] = $package_details['id'];
        $item_details['title'] = $package_details['title'];
        $item_details['creator_id'] = $package_details['user_id'];
        $item_details['user_id'] = $this->session->userdata('user_id');
        $item_details['price'] = $package_details['price'];
        $item_details['actual_price'] = $package_details['price'];

        //ended item detail
        $items += [$item_details];

        //common structure for all payment gateways and all type of payment
        $data['total_payable_amount'] = $item_details['actual_price'];
        $data['items'] = $items;
        $data['is_instructor_payout_user_id'] = 0;
        $data['payment_title'] = get_phrase('pay_for_package_purchase');
        $data['success_url'] = site_url('addons/team_training/success_package_payment');
        $data['cancel_url'] = site_url('payment');
        $data['back_url'] = site_url('addons/team_training/package_details/' . $item_details['id']);
        $this->session->set_userdata('payment_details', $data);
    }

    //                                       	7. bootcamp payment history
    /*------------------------------------------------------------------------------------------------------------*/
    function store_payment_history($payment_method, $payment_details)
    {
        $package_details = $this->db->where('id', $payment_details['items'][0]['id'])->get('team_packages')->row_array();

        if (empty($package_details)) {
            return ['err', 'package_not_found'];
        }

        // store payment history
        $data['package_id'] = $payment_details['items'][0]['id'];
        $data['purchase_date'] = time();
        $data['expiry_date'] = strtotime('+' . $package_details['expiry_period'] . 'month');
        $data['max_students'] = $package_details['max_students'];
        $data['user_id'] = $payment_details['items'][0]['user_id'];
        $data['paid_amount'] = $payment_details['items'][0]['actual_price'];
        $data['payment_method'] = $payment_method;
        $data['payment_keys'] = isset($_GET['session_id']) ? json_encode(['transaction_id' => $_GET['session_id']]) : '';
        $data['instructor_revenue'] = round($data['paid_amount'] * (get_settings('instructor_revenue') / 100));
        $data['admin_revenue'] = round($data['paid_amount'] - $data['instructor_revenue']);
        $data['instructor_payment_status'] = 0;
        $data['date_added'] = time();
        $data['updated_date'] = $data['date_added'];

        $insert = $this->db->insert('team_package_payment', $data);
        if ($insert) {
            return ['', 'payment_successful.', 'addons/team_training/package_details/' . $data['package_id']];
        }
        return ['err', 'something_went_wrong', ''];
    }

    // add new team member
    function insert_member($package_id, $user_id)
    {
        $member['team_id'] = $package_id;
        $member['leader_id'] = $this->session->userdata('user_id');
        $member['member_id'] = $user_id;
        $member['date_added'] = time();
        $member['updated_date'] = $member['date_added'];
        $insert = $this->db->insert('team_members', $member);
        return $insert ? true : false;
    }

    public function add_team_package()
    {
        // check package price
        if ($this->input->post('price') < 1) {
            return ['err', 'invalid_price'];
        }

        // check package expiry
        $expiryPeriod = $this->input->post('expiry_period');
        $numberOfMonths = $this->input->post('number_of_month');

        if ($expiryPeriod != 'limited_time') {
            $data['expiry_period'] = 'lifetime';
        } elseif ($numberOfMonths > 0) {
            $data['expiry_period'] = $numberOfMonths;
        } else {
            return ['err', 'please_set_valid_schedule'];
        }

        $enable_this_package = $this->input->post('pkg_status');
        if (isset($enable_this_package) && $enable_this_package) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        // upload team thumbnail
        $thumbnail = $this->team_package_model->upload_files('thumbnail', 'uploads/team_training/thumbnail/');
        if ($thumbnail) {
            $data['thumbnail'] = $thumbnail;
        }

        $data['features'] = $this->crud_model->trim_and_return_json($this->input->post('features'));
        $data['title'] = html_escape($this->input->post('title'));
        $data['course_id'] = html_escape($this->input->post('course_id'));
        $data['max_students'] = html_escape($this->input->post('max_students'));
        $data['price'] = html_escape($this->input->post('price'));
        $data['privacy'] = html_escape($this->input->post('privacy'));

        //for instructors package creation
        if (empty($data['privacy'])) {
            $data['privacy'] = 'public';
        }

        $data['is_free_package'] = html_escape($this->input->post('is_free_package'));
        $course_details = $this->crud_model->get_course_by_id($data['course_id'])->row_array();
        $data['user_id'] = $course_details['creator'];
        $data['date_added'] = time();
        $data['updated_date'] = $data['date_added'];

        $insert = $this->db->insert('team_packages', $data);
        return $insert ? ['', 'package_added_successfully'] : ['err', 'failed_to_create_package'];
    }

    public function edit_team_package($team_package_id = "")
    {

        //pkg expiry period
        if ($this->input->post('expiry_period') == 'limited_time' && is_numeric($this->input->post('number_of_month')) && $this->input->post('number_of_month') > 0) {
            $data['expiry_period'] = $this->input->post('number_of_month');
        } else {
            $data['expiry_period'] = null;
        }


        $enable_this_package = $this->input->post('pkg_status');
        if (isset($enable_this_package) && $enable_this_package) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        // upload bootcamp
        if ($_FILES['thumbnail']['name'] != '') {
            $thumbnail = $this->team_package_model->upload_files('thumbnail', 'uploads/team_training/thumbnail/');
            if ($thumbnail) {
                $data['thumbnail'] = $thumbnail;
            }
        }


        $data['features'] = $this->crud_model->trim_and_return_json($this->input->post('features'));
        $data['title'] = html_escape($this->input->post('title'));
        $data['course_id'] = html_escape($this->input->post('course_id'));
        $data['max_students'] = html_escape($this->input->post('max_students'));
        $data['price'] = html_escape($this->input->post('price'));

        $data['privacy'] = $this->input->post('privacy');

        //for instructors package creation
        if (empty($data['privacy'])) {
            $data['privacy'] = 'public';
        }

        $data['is_free_package'] = html_escape($this->input->post('is_free_package'));

        $course_details = $this->crud_model->get_course_by_id($data['course_id'])->row_array();

        $data['user_id'] = $course_details['creator'];
        $data['updated_date'] = time();


        $this->db->where('id', $team_package_id);
        $this->db->update('team_packages', $data);
        $this->session->set_flashdata('flash_message', get_phrase('team_package_updated_successfully'));
    }

    public function delete_team_package($team_package_id)
    {
        $this->db->where('id', $team_package_id);
        $this->db->delete('team_packages');
        $this->session->set_flashdata('flash_message', get_phrase('team_package_deleted_successfully'));
    }




    public function get_status_wise_courses($status, $user_id = "")
    {
        $scorm_status = addon_status('scorm_course');
        $h5p_status = addon_status('h5p');

        $this->db->select('id, title, price, expiry_period');
        $this->db->where('status', $status);

        if ($user_id) {
            $this->db->where('creator', $user_id);
        }

        $this->db->where('course_type', 'general');
        if ($scorm_status) {
            $this->db->or_where('course_type', 'scorm');
        }
        if ($h5p_status) {
            $this->db->or_where('course_type', 'h5p');
        }
        $courses = $this->db->get('course')->result_array();
        return $courses;
    }

    public function get_team_package_by_id($team_package_id = "")
    {
        return $this->db->get_where('team_packages', array('id' => $team_package_id));
    }

    public function change_team_package_status($status = "", $team_package_id = "")
    {
        if ($status == 'active') {
            if ($this->session->userdata('admin_login') != true) {
                redirect(site_url('login'), 'refresh');
            }
        }
        $updater = array(
            'status' => $status
        );
        $this->db->where('id', $team_package_id);
        $this->db->update('team_packages', $updater);
    }




    function package_purchase($method = "", $package_id = "", $amount = "", $transaction_id = "", $session_id = "")
    {
        $package_details = $this->team_package_model->get_team_package_by_id($package_id)->row_array();
        $data['paid_amount'] = $package_details['price'];
        $user_id = $this->session->userdata('user_id');
        $data['package_id'] = $package_id;
        $data['user_id'] = $user_id;
        $data['payment_method'] = $method;
        $data['payment_keys'] = json_encode(array('transaction_id' => $transaction_id, 'session_id' => $session_id));

        if (get_user_role('role_id', $package_details['user_id']) == 1) {
            $data['admin_revenue'] = $data['paid_amount'];
            $data['instructor_revenue'] = 0;
            $data['instructor_payment_status'] = 1;
        } else {
            if (get_settings('allow_instructor') == 1) {
                $instructor_revenue_percentage = get_settings('instructor_revenue');
                $data['instructor_revenue'] = ceil(($data['paid_amount'] * $instructor_revenue_percentage) / 100);
                $data['admin_revenue'] = $data['paid_amount'] - $data['instructor_revenue'];
            } else {
                $data['instructor_revenue'] = 0;
                $data['admin_revenue'] = $data['paid_amount'];
            }
            $data['instructor_payment_status'] = 0;
        }
        $data['max_students'] =  $package_details['max_students'];
        $data['purchase_date'] =  time();
        if ($package_details['expiry_period'] != null || $package_details['expiry_period'] != '') {
            $data['expiry_date'] = strtotime("+" . $package_details['expiry_period'] . "months", $data['purchase_date']);
        }
        $data['date_added'] =  time();
        $payment = $this->db->get_where('team_package_payment', array('package_id' => $package_id, 'user_id' => $user_id));
        if ($payment->num_rows() <= 0) {
            $this->db->insert('team_package_payment', $data);
        }
    }



    function team_progress($package_id = "", $user_id = "")
    {
        $team_users = $this->team_members($package_id);
        $course_id = $this->get_course_by_package_id($package_id);
        $team_progress = 0;

        if (isset($user_id) && in_array($user_id, $team_users) || $this->session->userdata('admin_login') == true) {
            foreach ($team_users as $team_user) {
                $watch_history = $this->crud_model->get_watch_histories($team_user, $course_id)->row_array();
                $team_progress += isset($watch_history['course_progress']) ? $watch_history['course_progress'] : 0;
            }
            return $team_progress;
        }
    }

    function team_members($package_id)
    {
        $this->db->select('user_id');
        $this->db->where('team_package_id', $package_id);
        $result = $this->db->get('enrol')->result_array();
        $userIds = array_column($result, 'user_id');
        return $userIds;
    }

    function get_course_by_package_id($package_id)
    {
        $course_id = $this->db->select('course_id')
            ->get_where('team_packages', array('id' => $package_id))
            ->row()->course_id;

        return $course_id;
    }

    function enrol_in_package($package_id, $user_id)
    {
        if (1) {
            $course_id = $this->get_course_by_package_id($package_id);
            $expiry_date = $this->check_package_expiry($package_id, 'return_value');
            $data['expiry_date'] = $expiry_date;
            $data['gifted_by'] = 0;
            $data['team_package_id'] = $package_id;

            if ($this->db->get_where('enrol', ['user_id' => $user_id, 'course_id' => $course_id])->num_rows() == 0) {
                $data['user_id'] = $user_id;
                $data['course_id'] = $course_id;
                $data['date_added'] = strtotime(date('D, d-M-Y'));
                $this->db->insert('enrol', $data);
            } else {
                $data['last_modified'] = time();
                $this->db->where('course_id', $course_id);
                $this->db->where('user_id', $user_id);
                $this->db->update('enrol', $data);
            }
        }
    }


    function is_package_creator_or_admin($package_id)
    {
        $owner_claim = $this->db->where('id', $package_id)->where('user_id', $this->session->userdata('user_id'))->get('team_packages')->num_rows();
        if ($owner_claim > 0 || $this->session->userdata('role_id') == 1) {
            return true;
        } else {
            return false;
        }
    }

    function is_team_leader($package_id, $user_id = '')
    {
        $user_id = $user_id ? $user_id : $this->session->userdata('user_id');
        $this->db->where('package_id', $package_id);
        $this->db->where('user_id', $user_id);
        $package = $this->db->get('team_package_payment', $user_id)->row_array();
        return $package ? $package : false;
    }

    function check_package_expiry($package_id, $return_value = "")
    {
        $validity = $this->db->select('expiry_date')
            ->get_where('team_package_payment', array('package_id' => $package_id))
            ->row()->expiry_date;
        //return the expiry date
        if (isset($return_value) && $return_value != '') {
            return $validity;
        }
        //return whether its expired or not in true or false
        else {
            if ($validity == '' || $validity == null) {
                return true;
            } else {
                if (time() <= $validity) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }



    function check_package_max_students($package_id, $return_value = "")
    {

        $max_allowed_students = $this->db->select('max_students')
            ->get_where('team_package_payment', array('package_id' => $package_id))
            ->row()->max_students;

        if (isset($return_value) && $return_value != '') {

            return $max_allowed_students;
        } else {

            if ($max_allowed_students > count($this->team_members($package_id))) {

                return true;
            } else {

                return false;
            }
        }
    }



    public function user_package_purchase_history($user_id = "")
    {
        if ($user_id > 0) {
            return $this->db->get_where('team_package_payment', array('user_id' => $user_id));
        } else {
            return $this->db->get('team_package_payment');
        }
    }


    public function backend_purchase_history($user_id = "")
    {
        $this->db->select('team_package_payment.*, team_packages.user_id as package_creator, team_packages.title as package_title, team_packages.course_id as package_course');
        $this->db->join('team_packages', 'team_packages.id = team_package_payment.package_id');

        if ($user_id > 0) {
            $this->db->where('team_packages.user_id', $user_id);
        }
        return $this->db->get('team_package_payment')->result_array();
    }

    public function get_admin_details()
    {
        return $this->db->get_where('users', array('role_id' => 1));
    }

    public function get_user($user_id = 0)
    {
        if ($user_id > 0) {
            $this->db->where('id', $user_id);
        }
        $this->db->where('role_id', 2);
        return $this->db->get('users');
    }

    public function get_all_user($user_id = 0)
    {
        if ($user_id > 0) {
            $this->db->where('id', $user_id);
        }
        return $this->db->get('users');
    }

    public function add_shortcut_user($is_instructor = false)
    {
        $validity = $this->check_duplication('on_create', $this->input->post('email'));
        if ($validity == false) {
            $response['status'] = 0;
            $response['message'] = get_phrase('this_email_already_exits') . '. ' . get_phrase('please_use_another_email');
            return json_encode($response);
        } else {
            $data['first_name'] = html_escape($this->input->post('first_name'));
            $data['last_name'] = html_escape($this->input->post('last_name'));
            $data['email'] = html_escape($this->input->post('email'));
            $data['password'] = sha1(html_escape($this->input->post('password')));
            $social_link['facebook'] = '';
            $social_link['twitter'] = '';
            $social_link['linkedin'] = '';
            $data['social_links'] = json_encode($social_link);
            $data['role_id'] = 2;
            $data['date_added'] = strtotime(date("Y-m-d H:i:s"));
            $data['wishlist'] = json_encode(array());
            $data['status'] = 1;
            $data['image'] = md5(rand(10000, 10000000));

            // Add paypal keys
            $payment_keys = array();

            $paypal['production_client_id']  = '';
            $paypal['production_secret_key'] = '';
            $payment_keys['paypal'] = $paypal;

            // Add Stripe keys
            $stripe['public_live_key'] = '';
            $stripe['secret_live_key'] = '';
            $payment_keys['stripe'] = $stripe;

            // Add razorpay keys
            $razorpay['key_id'] = '';
            $razorpay['secret_key'] = '';
            $payment_keys['razorpay'] = $razorpay;

            //All payment keys
            $data['payment_keys'] = json_encode(array());

            if ($is_instructor) {
                $data['is_instructor'] = 1;
            }
            $this->db->insert('users', $data);

            $user_id = $this->db->insert_id();

            $this->session->set_flashdata('flash_message', get_phrase('user_added_successfully'));
            $response['status'] = 1;
            return json_encode($response);
        }
    }

    public function check_duplication($action = "", $email = "", $user_id = "")
    {
        $duplicate_email_check = $this->db->get_where('users', array('email' => $email));

        if ($action == 'on_create') {
            if ($duplicate_email_check->num_rows() > 0) {
                if ($duplicate_email_check->row()->status == 1) {
                    return false;
                } else {
                    return 'unverified_user';
                }
            } else {
                return true;
            }
        } elseif ($action == 'on_update') {
            if ($duplicate_email_check->num_rows() > 0) {
                if ($duplicate_email_check->row()->id == $user_id) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
    }

    public function unlock_screen_by_password($password = "")
    {
        $password = sha1($password);
        return $this->db->get_where('users', array('id' => $this->session->userdata('user_id'), 'password' => $password))->num_rows();
    }

    public function register_user($data)
    {
        $this->db->insert('users', $data);
        $user_id = $this->db->insert_id();
        return $user_id;
    }

    public function register_user_update_code($data, $status = "")
    {
        //If get back disabled user and again signup
        $update_code['status'] = $status;
        $update_code['verification_code'] = $data['verification_code'];
        $update_code['password'] = $data['password'];
        $this->db->where('email', $data['email']);
        $this->db->update('users', $update_code);
    }

    public function my_courses($user_id = "")
    {
        if ($user_id == "") {
            $user_id = $this->session->userdata('user_id');
        }
        return $this->db->get_where('enrol', array('user_id' => $user_id));
    }

    public function upload_user_image($image_code)
    {
        if (isset($_FILES['user_image']) && $_FILES['user_image']['name'] != "") {
            move_uploaded_file($_FILES['user_image']['tmp_name'], 'uploads/user_image/' . $image_code . '.jpg');
            $this->session->set_flashdata('flash_message', get_phrase('user_update_successfully'));
        }
    }

    public function update_account_settings($user_id)
    {
        $validity = $this->check_duplication('on_update', $this->input->post('email'), $user_id);
        if ($validity) {
            if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
                $user_details = $this->get_user($user_id)->row_array();
                $current_password = $this->input->post('current_password');
                $new_password = $this->input->post('new_password');
                $confirm_password = $this->input->post('confirm_password');
                if ($user_details['password'] == sha1($current_password) && $new_password == $confirm_password) {
                    $data['password'] = sha1($new_password);
                } else {
                    $this->session->set_flashdata('error_message', get_phrase('mismatch_password'));
                    return;
                }
            }
            $this->db->where('id', $user_id);
            $this->db->update('users', $data);
            $this->session->set_flashdata('flash_message', get_phrase('updated_successfully'));
        } else {
            $this->session->set_flashdata('error_message', get_phrase('email_duplication'));
        }
    }

    public function change_password($user_id)
    {
        $data = array();
        if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            $user_details = $this->get_all_user($user_id)->row_array();
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('new_password');
            $confirm_password = $this->input->post('confirm_password');

            if ($user_details['password'] == sha1($current_password) && $new_password == $confirm_password) {
                $data['password'] = sha1($new_password);
            } else {
                $this->session->set_flashdata('error_message', get_phrase('mismatch_password'));
                return;
            }
        }
        $this->db->where('id', $user_id);
        $this->db->update('users', $data);
        $this->session->set_flashdata('flash_message', get_phrase('password_updated'));
    }

    public function get_instructor($id = 0)
    {
        if ($id > 0) {
            return $this->db->get_where('users', array('id' => $id, 'is_instructor' => 1));
        } else {
            return $this->db->get_where('users', array('is_instructor' => 1));
        }
    }

    public function get_instructor_by_email($email = null)
    {
        return $this->db->get_where('users', array('email' => $email, 'is_instructor' => 1));
    }

    public function get_admins($id = 0)
    {
        if ($id > 0) {
            return $this->db->get_where('users', array('id' => $id, 'role_id' => 1));
        } else {
            return $this->db->get_where('users', array('role_id' => 1));
        }
    }

    public function get_number_of_active_courses_of_instructor($instructor_id)
    {
        $result = $this->crud_model->get_courses_by_instructor_id($instructor_id, 'active');
        return $result->num_rows();
    }

    public function get_user_image_url($user_id)
    {
        $user_profile_image = $this->db->get_where('users', array('id' => $user_id))->row('image');
        if (file_exists('uploads/user_image/optimized/' . $user_profile_image . '.jpg')) {
            return base_url() . 'uploads/user_image/optimized/' . $user_profile_image . '.jpg';
        } elseif (file_exists('uploads/user_image/' . $user_profile_image . '.jpg')) {
            //resizeImage
            resizeImage('uploads/user_image/' . $user_profile_image . '.jpg', 'uploads/user_image/optimized/', 220);
            return base_url() . 'uploads/user_image/' . $user_profile_image . '.jpg';
        } else {
            return base_url() . 'uploads/user_image/placeholder.png';
        }
    }

    public function get_instructor_list()
    {
        return $this->db->get_where('users', array('status' => '1', 'is_instructor' => '1'));
    }

    public function update_instructor_paypal_settings($user_id = '')
    {
        $user_details = $this->get_all_user($user_id)->row_array();
        $payment_keys = json_decode($user_details['payment_keys'], true);
        // Update paypal keys
        $paypal['production_client_id'] = html_escape($this->input->post('paypal_client_id'));
        $paypal['production_secret_key'] = html_escape($this->input->post('paypal_secret_key'));
        $payment_keys['paypal'] = $paypal;

        //All payment keys
        $data['payment_keys'] = json_encode($payment_keys);

        $this->db->where('id', $user_id);
        $this->db->update('users', $data);
    }
    public function update_instructor_stripe_settings($user_id = '')
    {
        $user_details = $this->get_all_user($user_id)->row_array();
        $payment_keys = json_decode($user_details['payment_keys'], true);
        // Update stripe keys
        $stripe['public_live_key'] = html_escape($this->input->post('stripe_public_key'));
        $stripe['secret_live_key'] = html_escape($this->input->post('stripe_secret_key'));
        $payment_keys['stripe'] = $stripe;

        //All payment keys
        $data['payment_keys'] = json_encode($payment_keys);

        $this->db->where('id', $user_id);
        $this->db->update('users', $data);
    }

    public function update_instructor_razorpay_settings($user_id = '')
    {
        $user_details = $this->get_all_user($user_id)->row_array();
        $payment_keys = json_decode($user_details['payment_keys'], true);
        // Update razorpay keys
        $razorpay['key_id'] = html_escape($this->input->post('key_id'));
        $razorpay['secret_key'] = html_escape($this->input->post('secret_key'));
        $payment_keys['razorpay'] = $razorpay;

        //All payment keys
        $data['payment_keys'] = json_encode($payment_keys);

        $this->db->where('id', $user_id);
        $this->db->update('users', $data);
    }

    // POST INSTRUCTOR APPLICATION FORM AND INSERT INTO DATABASE IF EVERYTHING IS OKAY
    public function post_instructor_application($user_id = "")
    {
        if ($user_id == "") {
            $user_id = $this->input->post('id');
        }
        $user_details = $this->get_all_user($user_id)->row_array();

        if ($this->input->post('email')) {
            $email = $this->input->post('email');
        } else {
            $email = $user_details['email'];
        }

        // CHECK IF THE PROVIDED ID AND EMAIL ARE COMING FROM VALID USER
        if ($user_details['email'] == $email) {

            // GET PREVIOUS DATA FROM APPLICATION TABLE
            $previous_data = $this->get_applications($user_details['id'], 'user')->num_rows();
            // CHECK IF THE USER HAS SUBMITTED FORM BEFORE
            if ($previous_data > 0) {
                $this->session->set_flashdata('error_message', get_phrase('already_submitted'));
                redirect(site_url('user/become_an_instructor'), 'refresh');
            }
            $data['user_id'] = $user_id;
            $data['address'] = $this->input->post('address');
            $data['phone'] = $this->input->post('phone');
            $data['message'] = $this->input->post('message');
            if (isset($_FILES['document']) && $_FILES['document']['name'] != "") {
                if (!file_exists('uploads/document')) {
                    mkdir('uploads/document', 0777, true);
                }
                $accepted_ext = array('doc', 'docs', 'pdf', 'txt', 'png', 'jpg', 'jpeg');
                $path = $_FILES['document']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if (in_array(strtolower($ext), $accepted_ext)) {
                    $document_custom_name = random(15) . '.' . $ext;
                    $data['document'] = $document_custom_name;
                    move_uploaded_file($_FILES['document']['tmp_name'], 'uploads/document/' . $document_custom_name);
                } else {
                    $this->session->set_flashdata('error_message', get_phrase('invalide_file'));
                    redirect(site_url('user/become_an_instructor'), 'refresh');
                }
            }
            $this->db->insert('applications', $data);
            $this->session->set_flashdata('flash_message', site_phrase('You have successfully submitted your application.') . ' ' . get_phrase('We will review it and notify you via email notification'));
            redirect(site_url('user/become_an_instructor'), 'refresh');
        } else {
            $this->session->set_flashdata('error_message', get_phrase('user_not_found'));
            redirect(site_url('user/become_an_instructor'), 'refresh');
        }
    }

    function instructor_application()
    {
        // FIRST GET THE USER DETAILS
        $user = $this->db->get_where('users', ['email' => $this->input->post('email')]);
        if ($user->num_rows() > 0) {
            $user_details = $user->row_array();
            $previous_data = $this->get_applications($user_details['id'], 'user')->num_rows();
            if ($previous_data == 0) {
                if (!file_exists('uploads/document')) {
                    mkdir('uploads/document', 0777, true);
                }
                $data['user_id'] = $user_details['id'];
                $data['address'] = $user_details['address'];
                $data['phone'] = $this->input->post('phone');
                $data['message'] = $this->input->post('message');

                $document_custom_name = random(15) . '.' . pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
                $data['document'] = $document_custom_name;
                move_uploaded_file($_FILES['document']['tmp_name'], 'uploads/document/' . $document_custom_name);
                $this->db->insert('applications', $data);
            }
        }
    }


    // GET INSTRUCTOR APPLICATIONS
    public function get_applications($id = "", $type = "")
    {
        if ($id > 0 && !empty($type)) {
            if ($type == 'user') {
                $applications = $this->db->get_where('applications', array('user_id' => $id));
                return $applications;
            } else {
                $applications = $this->db->get_where('applications', array('id' => $id));
                return $applications;
            }
        } else {
            $this->db->order_by("id", "DESC");
            $applications = $this->db->get_where('applications');
            return $applications;
        }
    }

    // GET APPROVED APPLICATIONS
    public function get_approved_applications()
    {
        $applications = $this->db->get_where('applications', array('status' => 1));
        return $applications;
    }

    // GET PENDING APPLICATIONS
    public function get_pending_applications()
    {
        $applications = $this->db->get_where('applications', array('status' => 0));
        return $applications;
    }

    //UPDATE STATUS OF INSTRUCTOR APPLICATION
    public function update_status_of_application($status, $application_id)
    {
        $application_details = $this->get_applications($application_id, 'application');
        if ($application_details->num_rows() > 0) {
            $application_details = $application_details->row_array();
            if ($status == 'approve') {
                $application_data['status'] = 1;
                $this->db->where('id', $application_id);
                $this->db->update('applications', $application_data);

                $instructor_data['is_instructor'] = 1;
                $this->db->where('id', $application_details['user_id']);
                $this->db->update('users', $instructor_data);

                $this->session->set_flashdata('flash_message', get_phrase('application_approved_successfully'));
                redirect(site_url('admin/instructor_application'), 'refresh');
            } else {
                $this->db->where('id', $application_id);
                $this->db->delete('applications');
                $this->session->set_flashdata('flash_message', get_phrase('application_deleted_successfully'));
                redirect(site_url('admin/instructor_application'), 'refresh');
            }
        } else {
            $this->session->set_flashdata('error_message', get_phrase('invalid_application'));
            redirect(site_url('admin/instructor_application'), 'refresh');
        }
    }

    // ASSIGN PERMISSION
    public function assign_permission()
    {
        $argument = html_escape($this->input->post('arg'));
        $argument = explode('-', $argument);
        $admin_id = $argument[0];
        $module = $argument[1];

        // CHECK IF IT IS A ROOT ADMIN
        if (is_root_admin($admin_id)) {
            return false;
        }

        $permission_data['admin_id'] = $admin_id;
        $previous_permissions = json_decode($this->get_admins_permission_json($permission_data['admin_id']), TRUE);

        if (in_array($module, $previous_permissions)) {
            $new_permission = array();
            foreach ($previous_permissions as $permission) {
                if ($permission != $module) {
                    array_push($new_permission, $permission);
                }
            }
        } else {
            array_push($previous_permissions, $module);
            $new_permission = $previous_permissions;
        }

        $permission_data['permissions'] = json_encode($new_permission);

        $this->db->where('admin_id', $admin_id);
        $this->db->update('permissions', $permission_data);
        return true;
    }

    // GET ADMIN'S PERMISSION JSON
    public function get_admins_permission_json($admin_id)
    {
        $admins_permissions = $this->db->get_where('permissions', ['admin_id' => $admin_id])->row_array();
        return $admins_permissions['permissions'];
    }

    // GET MULTI INSTRUCTOR DETAILS WITH COURSE ID
    public function get_multi_instructor_details_with_csv($csv)
    {
        $instructor_ids = explode(',', $csv);
        $this->db->where_in('id', $instructor_ids);
        return $this->db->get('users')->result_array();
    }

    function quiz_submission_checker($quiz_id = "")
    {
        $quiz_details = $this->crud_model->get_lessons('lesson', $quiz_id)->row_array();
        $total_quiz_seconds = time_to_seconds($quiz_details['duration']);

        $this->db->where('quiz_id', $quiz_id);
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $query = $this->db->order_by('quiz_result_id', 'desc')->get('quiz_results');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            if (($total_quiz_seconds + $row['date_added']) < time() && $total_quiz_seconds > 0 || $row['is_submitted'] == 1) {

                if ($row['is_submitted'] != 1) {
                    $this->db->where('quiz_id', $quiz_id);
                    $this->db->where('user_id', $this->session->userdata('user_id'));
                    $this->db->update('quiz_results', array('is_submitted' => 1));
                }

                return 'submitted';
            } else {
                return 'on_progress';
            }
        } else {
            return 'no_data';
        }
    }

    // For device login tracker
    public function new_device_login_tracker($user_id = "", $is_verified = '')
    {
        $pre_sessions = array();
        $updated_session_arr = array();
        $current_session_id = session_id();
        $this->db->where('id', $user_id);
        $sessions = $this->db->get('users');

        if ($sessions->row('role_id') == 1) {
            return;
        }

        $pre_sessions = json_decode($sessions->row('sessions'), true);

        if (is_array($pre_sessions) && count($pre_sessions) > 0) {
            if ($is_verified == true && !in_array($current_session_id, $pre_sessions)) {
                $allowed_device = get_settings('allowed_device_number_of_loging');
                $previous_tatal_device = count($pre_sessions) + 1; //current device

                $removeable_device = $previous_tatal_device - $allowed_device;

                foreach ($pre_sessions as $key => $pre_session) {
                    if ($removeable_device >= 1) {
                        $this->db->where('id', $pre_session);
                        $this->db->delete('ci_sessions');
                    } else {

                        if ($this->db->get_where('ci_sessions', ['id' => $pre_session])->num_rows() > 0) {
                            array_push($updated_session_arr, $pre_session);
                        }
                    }
                    $removeable_device = $removeable_device - 1;
                }
                array_push($updated_session_arr, $current_session_id);
            } else {
                if (!in_array($current_session_id, $pre_sessions)) {
                    if (count($pre_sessions) >= get_settings('allowed_device_number_of_loging')) {
                        $this->email_model->new_device_login_alert($user_id);
                        redirect(site_url('login/new_login_confirmation'), 'refresh');
                    } else {
                        $updated_session_arr = $pre_sessions;
                        array_push($updated_session_arr, $current_session_id);
                    }
                }
            }
        } else {
            $updated_session_arr = [$current_session_id];
        }

        if (count($updated_session_arr) > 0) {
            $data['sessions'] = json_encode($updated_session_arr);
            $this->db->where('id', $user_id);
            $this->db->update('users', $data);
        }
    }

    function set_login_userdata($user_id = "")
    {
        // Checking login credential for admin
        $query = $this->db->get_where('users', array('id' => $user_id));

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $this->session->set_userdata('custom_session_limit', (time() + 864000));
            $this->session->set_userdata('user_id', $row->id);
            $this->session->set_userdata('role_id', $row->role_id);
            $this->session->set_userdata('role', get_user_role('user_role', $row->id));
            $this->session->set_userdata('name', $row->first_name . ' ' . $row->last_name);
            $this->session->set_userdata('is_instructor', $row->is_instructor);
            $this->session->set_flashdata('flash_message', get_phrase('welcome') . ' ' . $row->first_name . ' ' . $row->last_name);
            if ($row->role_id == 1) {
                $this->session->set_userdata('admin_login', '1');
                redirect(site_url('admin/dashboard'), 'refresh');
            } else if ($row->role_id == 2) {
                $this->session->set_userdata('user_login', '1');
                if ($this->session->userdata('url_history')) {
                    redirect($this->session->userdata('url_history'), 'refresh');
                }
                redirect(site_url('home'), 'refresh');
            }
        } else {
            $this->session->set_flashdata('error_message', get_phrase('invalid_login_credentials'));
            redirect(site_url('login'), 'refresh');
        }
    }

    function check_session_data($user_type = "")
    {
        $this->remove_garbage_collection();

        if (!$this->session->userdata('cart_items')) {
            $this->session->set_userdata('cart_items', array());
        }

        if (!$this->session->userdata('language')) {
            $this->session->set_userdata('language', get_settings('language'));
        }

        if ($user_type == 'admin') {
            if ($this->session->userdata('custom_session_limit') >= time()) {
                $this->session->set_userdata('custom_session_limit', (time() + 864000));
            } else {
                $this->session_destroy();
                redirect(site_url('login'), 'refresh');
            }

            if ($this->session->userdata('admin_login') != true) {
                redirect(site_url('login'), 'refresh');
            }
        } elseif ($user_type == 'user') {
            if ($this->session->userdata('custom_session_limit') >= time()) {
                $this->session->set_userdata('custom_session_limit', (time() + 864000));
            } else {
                $this->session_destroy();
                redirect(site_url('login'), 'refresh');
            }

            if ($this->session->userdata('user_login') != true) {
                redirect(site_url('login'), 'refresh');
            } else {
                if ($this->get_all_user($this->session->userdata('user_id'))->num_rows() == 0) {
                    $this->session_destroy();
                    redirect(site_url('login'), 'refresh');
                }
            }
        } elseif ($user_type == 'login') {
            if ($this->session->userdata('admin_login')) {
                redirect(site_url('admin'), 'refresh');
            } elseif ($this->session->userdata('user_login')) {
                redirect(site_url('home/my_courses'), 'refresh');
            }
        }
    }

    public function session_destroy()
    {
        $this->remove_garbage_collection();

        $logged_in_user_id = $this->session->userdata('user_id');
        if ($logged_in_user_id > 0 && $this->session->userdata('user_login') == 1) {
            $pre_sessions = array();
            $updated_session_arr = array();
            $current_session_id = session_id();

            $this->db->where('id', $logged_in_user_id);
            $sessions = $this->db->get('users')->row('sessions');
            $pre_sessions = json_decode($sessions, true);
            if (is_array($pre_sessions)) {
                foreach ($pre_sessions as $key => $pre_session) {
                    if ($pre_session != $current_session_id) {
                        if ($this->db->get_where('ci_sessions', ['id' => $pre_session])->num_rows() > 0) {
                            array_push($updated_session_arr, $pre_session);
                        }
                    } else {
                        $this->db->where('id', $pre_session);
                        $this->db->delete('ci_sessions');
                    }
                }
                $data['sessions'] = json_encode($updated_session_arr);
                $this->db->where('id', $logged_in_user_id);
                $this->db->update('users', $data);
            }
        }

        $this->session->unset_userdata('admin_login');
        $this->session->unset_userdata('user_login');
        $this->session->unset_userdata('custom_session_limit');
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('role_id');
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('name');
        $this->session->unset_userdata('is_instructor');
        $this->session->unset_userdata('url_history');
        $this->session->unset_userdata('app_url');
        $this->session->unset_userdata('total_price_of_checking_out');
        $this->session->unset_userdata('register_email');
        $this->session->unset_userdata('applied_coupon');
        $this->session->unset_userdata('new_device_code_expiration_time');
        $this->session->unset_userdata('new_device_user_email');
        $this->session->unset_userdata('new_device_user_id');
        $this->session->unset_userdata('new_device_verification_code');
    }

    function remove_garbage_collection()
    {
        $this->db->where('timestamp <', time() - 864000);
        $this->db->delete('ci_sessions');
    }

    function get_user_by_email($email = "")
    {
        if ($email) {
            $this->db->where('email', $email);
        }
        return $this->db->get('users');
    }
}
