<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Team_training extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set(get_settings('timezone'));
        $this->load->database();
        $this->load->library('session');
        $this->load->model('addons/team_package_model');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        ini_set('memory_limit', '128M');
    }

    /*--------------------------------------------------------------------------------------------------*/
    //                                       reuseable functions
    /*--------------------------------------------------------------------------------------------------*/
    function sendRes($type = '', $msg = '', $url = '')
    {
        $msg_type = 'flash_message';
        if ($type == 'err') {
            $msg_type = 'error_message';
        }
        $this->session->set_flashdata($msg_type, site_phrase($msg));
        if ($url != '') {
            redirect(site_url($url), 'refresh');
        }
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function team_packages($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('is_instructor') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == "add") {
            $insert = $this->team_package_model->add_team_package();
            $this->sendRes($insert[0], $insert[1]);
        } elseif ($param1 == "edit") {
            $this->team_package_model->edit_team_package($param2);
            redirect(site_url('addons/Team_training/team_packages'), 'refresh');
        } elseif ($param1 == "delete") {
            $this->team_package_model->delete_team_package($param2);
            redirect(site_url('addons/Team_training/team_packages'), 'refresh');
        }

        $page_data['page_name'] = 'team_packages';
        $page_data['page_title'] = get_phrase('team_packages');
        $this->load->view('backend/index', $page_data);
    }

    public function team_package_form($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('is_instructor') != 1) {
            redirect(site_url('login'), 'refresh');
        }


        if ($param1 == 'add_team_package_form') {
            $page_data['page_name'] = 'team_package_add';
            $page_data['page_title'] = get_phrase('team_package_add');
            $this->load->view('backend/index', $page_data);
        } elseif ($param1 == 'edit_team_package_form') {
            $page_data['page_name'] = 'team_package_edit';
            $page_data['team_package_id'] = $param2;
            $page_data['page_title'] = get_phrase('team_package_edit');
            $this->load->view('backend/index', $page_data);
        }
    }

    public function server_side_team_packages_data()
    {
        $data = array();
        //mentioned all with colum of database table that related with html table
        $columns = array('id', 'title',  'status', 'price', 'id');

        $limit = htmlspecialchars_($this->input->post('length'));
        $start = htmlspecialchars_($this->input->post('start'));
        $column_index = $columns[$this->input->post('order')[0]['column']];

        $dir = $this->input->post('order')[0]['dir'];
        $search = $this->input->post('search')['value'];

        if ($this->session->userdata('role_id') == 1) {
            $total_number_of_row = $this->db->get('team_packages')->num_rows();
        } else {
            $user_id = $this->session->userdata('user_id');
            $this->db->where('team_packages.user_id', $user_id);
            $total_number_of_row = $this->db->get('team_packages')->num_rows();
        }

        $filtered_number_of_row = $total_number_of_row;

        //FOR ADMIN
        if ($this->session->userdata('role_id') == 1) {
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('team_packages.title', $search)
                    ->or_like('course.title', $search)
                    ->or_like('team_packages.price', $search)
                    ->or_like('team_packages.max_students', $search)
                    ->or_like('team_packages.expiry_period', $search)
                    ->or_like('team_packages.privacy', $search);
                $this->db->group_end();
            }
            $this->db->select('team_packages.*, course.title as course_title');
            $this->db->join('course', 'course.id = team_packages.course_id');
            $this->db->limit($limit, $start);
            $this->db->order_by($column_index, $dir);
            $team_packages = $this->db->get('team_packages')->result_array();
            $filtered_number_of_row = count($team_packages);
        }

        //FOR INSTRUCTOR
        else {
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->where('team_packages.user_id', $this->session->userdata('user_id'))
                    ->like('team_packages.title', $search)
                    ->or_like('course.title', $search)
                    ->or_like('team_packages.price', $search)
                    ->or_like('team_packages.max_students', $search)
                    ->or_like('team_packages.expiry_period', $search)
                    ->or_like('team_packages.privacy', $search);
                $this->db->group_end();
            }
            $this->db->where('team_packages.user_id', $this->session->userdata('user_id'));
            $this->db->select('team_packages.*, course.title as course_title');
            $this->db->join('course', 'course.id = team_packages.course_id');
            $this->db->limit($limit, $start);
            $this->db->order_by($column_index, $dir);
            $team_packages = $this->db->get('team_packages')->result_array();
            $filtered_number_of_row = count($team_packages);
        }

        foreach ($team_packages as $key => $team_package) :
            $price_badge = "badge-dark-lighten";
            $price = 0;
            if ($team_package['is_free_package'] == null) {

                $price = currency($team_package['price']);
            } elseif ($team_package['is_free_package'] == 1) {
                $price_badge = "badge-success-lighten";
                $price = get_phrase('free');
            }

            $price_field = '<p class="text-13 mb-0"><span class="badge ' . $price_badge . '">' . $price . '</span></p>';
            //max students
            $price_field .= '<p class="text-13 mb-0">' . '1-' . $team_package['max_students'] . ' ' . get_phrase('Students') . '</p>';

            if ($team_package['expiry_period'] > 0) {
                $price_field .= '<p class="text-13 mb-0">' . $team_package['expiry_period'] . ' ' . get_phrase('Months') . '</p>';
            } else {
                $price_field .= '<p class="text-13 ">' . get_phrase('Lifetime') . '</p>';
            }

            $status_badge = "badge-dark-lighten";
            $status = 0;
            if ($team_package['status'] == 0) {

                $status = get_phrase('inactive');
            } elseif ($team_package['status'] == 1) {
                $status_badge = "badge-success-lighten";
                $status = get_phrase('active');
            }

            $status_field = '<p ><span class="badge ' . $status_badge . '">' . $status . '</span></p>';

            if ($team_package['status'] == 1) {
                $course_status_changing_message = get_phrase('mark_as_inactive');
                $course_status_changing_action = "confirm_modal('" . site_url('addons/Team_training/change_package_status/' . $team_package['id']) . '/inactive' . "')";
            } else {
                $course_status_changing_message = get_phrase('mark_as_active');
                $course_status_changing_action = "confirm_modal('" . site_url('addons/Team_training/change_package_status/' . $team_package['id']) . '/active' . "')";
            }

            $action =  '<div class="dropright dropright">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-rounded btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="' . site_url('addons/Team_training/team_package_form/edit_team_package_form/' . $team_package['id']) . '">' . get_phrase('edit') . '</a></li>
                                <li><a class="dropdown-item" href="#" onclick="confirm_modal(&#39;' . site_url('addons/Team_training/team_packages/delete/' . $team_package['id']) . '&#39;);">' . get_phrase('delete') . '</a></li>' . '<li><a class="dropdown-item" href="javascript:;" onclick="' . $course_status_changing_action . '">' . $course_status_changing_message . '</a></li>' .
                '</ul>
                        </div>';
            //ADMIN ONLY
            if ($this->session->userdata('role_id') == 1) {
                $nestedData['pkg_privacy'] = $team_package['privacy'];
                $academic_progress_url = site_url('admin/course_form/course_edit/' . $team_package['course_id'] . '?tab=academic_progress');
            } else {
                $academic_progress_url = site_url('user/course_form/course_edit/' . $team_package['course_id'] . '?tab=academic_progress');
            }

            $nestedData['pkg_title'] = $team_package['title'] . '<br>
            <span class="text-muted">' . get_phrase('course') . ': <b>' . '<a href="' . $academic_progress_url . '">' . $team_package['course_title'] . '</a>' . '</b></span>';
            $nestedData['key'] = ++$key;
            $nestedData['pkg_status'] = $status_field;
            $nestedData['pkg_price'] = $price_field;
            $nestedData['action'] = $action . '<script>$("a, i").tooltip();</script>';
            $data[] = $nestedData;
        endforeach;

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($total_number_of_row),
            "recordsFiltered" => intval($filtered_number_of_row),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function get_course_list_by_status($status = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            $courses = $this->team_package_model->get_status_wise_courses('active', $this->session->userdata('user_id'));
        } else {
            $courses = $this->team_package_model->get_status_wise_courses($status);
        }
        echo json_encode($courses);
    }


    public function get_course_by_id($course_id)
    {
        if ($this->session->userdata('is_instructor') != 1) {
            redirect(site_url('login'), 'refresh');
        }
        $course = $this->crud_model->get_course_by_id($course_id)->row_array();
        echo json_encode($course);
    }




    public function change_package_status($team_package_id, $updated_status)
    {
        if ($this->team_package_model->is_package_creator_or_admin($team_package_id) != true) {
            redirect(site_url('login'), 'refresh');
        }
        if ($updated_status == 'active') {
            $updater = array('status' => 1);
            $this->db->where('id', $team_package_id);
            $this->db->update('team_packages', $updater);
        } elseif ($updated_status == 'inactive') {
            $updater = array('status' => 0);
            $this->db->where('id', $team_package_id);
            $this->db->update('team_packages', $updater);
        }
        $status = $this->input->post('status');
        $this->session->set_flashdata('flash_message', get_phrase('package_status_updated'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function my_teams()
    {
        if ($this->session->userdata('user_login') != true) {
            redirect(site_url('home'), 'refresh');
        }
        $teams = $this->db->select('team_package_payment.package_id, team_package_payment.expiry_date, team_package_payment.max_students, team_packages.course_id, team_packages.title')
            ->from('team_package_payment')
            ->join('team_packages', 'team_packages.id = team_package_payment.package_id')
            ->where('team_package_payment.user_id', $this->session->userdata('user_id'))
            ->get()
            ->result_array();
        foreach ($teams as &$team) {
            $team['student_count'] = count($this->team_package_model->my_team_member($team['package_id']));
            $team['team_progress'] = $this->team_package_model->team_progress($team['package_id'], $this->session->userdata('user_id'));
        }
        $page_data['page_name'] = "my_teams";
        $page_data['teams'] = $teams;
        $page_data['page_title'] = site_phrase("my_teams");
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function my_selected_team($package_id)
    {
        if ($this->session->userdata('user_login') != true) {
            redirect(site_url('home'), 'refresh');
        }

        // check existence and ownership
        $package_details = $this->db->where('id', $package_id)->get('team_packages')->row_array();
        $leader = $this->team_package_model->is_team_leader($package_id);
        if (!$package_details && !$leader) {
            $this->sendRes('err', 'data_not_found');
        }

        // check purchase
        $purchase = $this->team_package_model->is_purchased($package_id);
        if (!$purchase) {
            $this->sendRes('err', 'package_validity_expired', 'addons/team_training/package_details/' . $package_id);
        }

        $selected_team = $this->db->where('id', $package_id)->get('team_packages')->row_array();
        $page_data['page_name'] = "my_selected_team";
        $page_data['selected_team'] = $selected_team;
        $page_data['page_title'] = site_phrase("my_teams");
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    // member action
    function member_action()
    {
        $package_id = $this->input->get('package_id');
        $package_details = $this->db->where('id', $package_id)->get('team_packages')->row_array();

        $user_id = $this->input->get('user_id');
        $action = $this->input->get('action');

        // check team member capacity
        $capacity_info = $this->team_package_model->check_member_capacity($package_id);
        if ($capacity_info['available'] < 1) {
            echo get_phrase('max_limit_reached');
            die();
        }

        // enroll member to the selected course
        $this->db->where(['user_id' => $user_id, 'course_id' => $package_details['course_id']]);
        $enrolled = $this->db->order_by('id', 'desc')->get('enrol')->row_array();

        $leader = $this->team_package_model->is_team_leader($package_id);
        if (!$leader) {
            echo get_phrase('data_not_found');
            die();
        }

        $member_exists = $this->team_package_model->my_team_member($package_id, $user_id);
        if ($action == 'add') {
            if ($member_exists) {
                echo get_phrase('already_in_team');
                die();
            }

            if (!$enrolled) {
                $enroll['user_id'] = $user_id;
                $enroll['course_id'] = $package_details['course_id'];
                $enroll['team_package_id'] = $package_id;
                $enroll['gifted_by'] = 0;
                $enroll['expiry_date'] = strtotime('+' . $package_details['expiry_period'] . 'months');
                $enroll['date_added'] = time();
                $enroll['last_modified'] = $enroll['date_added'];
                $this->db->insert('enrol', $enroll);
                $insert_member = $this->team_package_model->insert_member($package_id, $user_id);
            } else {
                // check user exists in same course
                if ($enrolled['team_package_id'] > 0) {
                    echo get_phrase('user_already_enrolled_in_this_course');
                    die();
                } else {
                    // if user already enrolled then 
                    // check expiry of both course and package and assign the big value
                    $expiry_date = strtotime('+' . $package_details['expiry_period'] . 'months');
                    if ($enrolled['expiry_date'] >= $expiry_date) {
                        $expiry_date = $enrolled['expiry_date'];
                    }

                    // update expiry date
                    $this->db->where(['user_id' => $user_id, 'course_id' => $package_details['course_id']]);
                    $this->db->update('enrol', ['expiry_date' => $expiry_date, 'team_package_id' => $package_id]);
                    $insert_member = $this->team_package_model->insert_member($package_id, $user_id);
                }
            }

            echo $insert_member ? 'added' : get_phrase('something_went_wrong');
        } elseif ($action == 'remove') {
            if (!$member_exists) {
                echo get_phrase('data_not_found');
                die();
            }

            // delete from members table
            $this->db->where(['team_id' => $package_id, 'member_id' => $user_id, 'leader_id' => $this->session->userdata('user_id')]);
            $delete = $this->db->delete('team_members');

            // change expire date with selected course expire date
            $selected_course = $this->db->where('id', $package_details['course_id'])->get('course')->row_array();

            $expire_date = time();
            if ($enrolled['team_package_id'] > 0) {
                $extend = strtotime('+' . $selected_course['expiry_period'] . 'months') - time();
                $expire_date = $extend + $selected_course['date_added'];
            }

            // update user enroll expiry
            $this->db->where(['user_id' => $user_id, 'course_id' => $package_details['course_id'], 'team_package_id' => $package_id]);
            $update = $this->db->update('enrol', ['expiry_date' => $expire_date, 'team_package_id' => 0]);
            echo $update ? 'removed' : get_phrase('something_went_wrong');
        }
    }

    // team invoice
    function team_invoice($invoice_id)
    {
        if (is_numeric($invoice_id) && $invoice_id > 0) {
            $invoice_details = $this->db->where('id', $invoice_id)->get('team_package_payment')->row_array();

            // if id has invoice
            if ($invoice_details) {
                $leader = $this->team_package_model->is_team_leader($invoice_details['package_id']);
                if ($leader) {
                    $page_data['invoice'] = $invoice_details;
                    $page_data['page_name'] = 'team_invoice';
                    $page_data['page_title'] = 'invoice_id';
                    return $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
                }
            }
        }
        $this->sendRes('err', 'data_not_found', 'addons/team_training/my_teams');
    }

    // join team
    function join_team($team_id)
    {
        if (is_numeric($team_id) && $team_id > 0) {
            $team_details = $this->db->where('id', $team_id)->get('team_packages')->row_array();
            if (!$team_details) {
                $this->sendRes('err', 'data_not_found');
            }

            $leader = $this->team_package_model->is_team_leader($team_id);
            if ($leader) {
                redirect(site_url('addons/team_training/my_selected_team/' . $team_id), 'refresh');
            }
        }
        $this->sendRes('err', 'data_not_found');
    }

    public function packages()
    {
        $selected_category_id = "all";
        $selected_price = "all";

        $selected_language = "all";
        $selected_rating = "all";
        $selected_sorting = "newest";

        $scorm_status = addon_status('scorm_course');
        $h5p_status = addon_status('h5p');

        // check script inject
        foreach ($_GET as $key => $value) {
            //check double quote and script text in the search string
            if (preg_match('/"/', strtolower($value)) >= 1 && strpos(strtolower($value), "script") >= 1) {
                $this->session->set_flashdata('error_message', site_phrase('such_script_searches_are_not_allowed') . '!');
                redirect(site_url('addons/team_training/packages'), 'refresh');
            }
            $_GET[htmlspecialchars_($key)] = htmlspecialchars_($value);
        }

        if (isset($_GET['search_input']) && $_GET['search_input'] != "") {
            $search_string = $_GET['search_input'];
        } else {
            $search_string = "";
        }

        // Get the category ids
        if (isset($_GET['category']) && !empty($_GET['category'] && $_GET['category'] != "all")) {
            $selected_category_id = $this->crud_model->get_category_id($_GET['category']);
        }

        // Get the selected price
        if (isset($_GET['price']) && !empty($_GET['price'])) {
            $selected_price = $_GET['price'];
        }

        // Get the selected rating
        if (isset($_GET['sort_by']) && !empty($_GET['sort_by'])) {
            $selected_sorting = $_GET['sort_by'];
        }

        //category
        if ($selected_category_id != "all") {
            $category_details = $this->crud_model->get_category_details_by_id($selected_category_id)->row_array();

            if ($category_details['parent'] > 0) {
                $category_type = 'sub_category';
            } else {
                $category_type = 'parent_category';
            }
        } else {
            $category_type = 'all';
        }

        $this->db->select('team_packages.*, course.title as course_title', 'course.category_id as category_id', 'course.sub_category_id as sub_category_id');
        $this->db->join('course', 'course.id = team_packages.course_id');

        //category
        if ($category_type != "all" && $category_type == 'sub_category') {

            $this->db->where('course.sub_category_id', $selected_category_id);
        } elseif ($category_type != "all" && $category_type == 'parent_category') {

            $this->db->where('course.category_id', $selected_category_id);
        }

        //price
        if ($selected_price != "all" && $selected_price == "paid") {

            $this->db->where('team_packages.is_free_package', null);
        } elseif ($selected_price != "all" && $selected_price == "free") {

            $this->db->where('team_packages.is_free_package', 1);
        }
        //search
        if (!empty($search_string)) {
            $this->db->like('team_packages.title', $search_string);
            $this->db->or_like('course.title', $search_string);
            $this->db->or_like('team_packages.price', $search_string);
            $this->db->or_like('team_packages.max_students', $search_string);
            $this->db->or_like('team_packages.expiry_period', $search_string);
            $this->db->or_like('team_packages.privacy', $search_string);
        }

        $category_slug = isset($_GET['category']) ? $_GET['category'] : 'all';

        if ($search_string != "") {
            $search_string_val = "search_input=" . $search_string . "&";
        } else {
            $search_string_val = "";
        }

        $packages = $this->db->order_by('id', 'desc')->get('team_packages')->result_array();
        $filtered_number_of_row = count($packages);

        $config = array();
        $config = pagintaion($filtered_number_of_row, 9);
        $config['base_url']  = site_url('addons/Team_training/packages/');

        $config['suffix']  = '?' . $search_string_val . 'category=' . $category_slug . '&price=' . $selected_price . '&sort_by=' . $selected_sorting;
        $config['first_url']  = site_url('addons/Team_training/packages/') . '?' . $search_string_val . 'category=' . $category_slug . '&price=' . $selected_price . '&sort_by=' . $selected_sorting;
        $this->pagination->initialize($config);

        $page_data['packages'] = $packages;
        $page_data['total_result'] = $filtered_number_of_row;
        $page_data['page_name']  = "team_packages_page";
        $page_data['page_title'] = site_phrase('Team');
        $page_data['selected_category_id']     = $selected_category_id;
        $page_data['selected_price']     = $selected_price;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    public function package_details($package_id = "")
    {
        $package = $this->db->where('id', $package_id)->get('team_packages')->row_array();
        $page_data['package'] = $package;
        $page_data['course_id'] = $package['course_id'];
        $page_data['page_name'] = "package_details_page";
        $page_data['page_title'] = site_phrase('package');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    //                                       package purchase
    /*--------------------------------------------------------------------------------------------------*/
    function purchase($param)
    {
        // check user login status
        if (!$this->session->userdata('user_login')) {
            set_url_history(site_url('addons/team_training/package_details/' . $param));
            redirect(site_url('login'), 'refresh');
        }

        // validate param
        if (is_numeric($param) && $param > 0) {
            $package_details = $this->db->where('id', $param)->get('team_packages')->row_array();
            if (!$package_details) {
                $this->sendRes('err', 'data_not_found.');
            }
        } else {
            $this->sendRes('err', 'data_not_found.');
        }

        // creator can not buy own package
        if ($package_details['user_id'] == $this->session->userdata('user_id')) {
            $this->sendRes('err', 'you_can_not_buy_own_items');
        }

        // check selected package is free or not
        if ($package_details['is_free_package'] == 1) {
            $this->sendRes('err', 'this_item_is_free.');
        }

        // check selected package price over 0
        if ($package_details['price'] < 1) {
            $this->sendRes('err', 'invalid_price');
        }

        // check selected item purchased or not
        $purchased = $this->team_package_model->is_purchased($param);
        if ($purchased) {
            $this->sendRes('err', 'item_already_purchased.');
        }

        // proceed to payment configuration
        $this->team_package_model->configure_package_payment($package_details);
        redirect(site_url('payment'));
    }

    // package payment success function
    function success_package_payment($payment_method = "")
    {
        //STARTED payment model and functions are dynamic here
        $response = false;
        $payer_user_id = $this->session->userdata('user_id');
        $enrol_user_id = $payer_user_id;
        $payment_details = $this->session->userdata('payment_details');
        $payment_gateway = $this->db->get_where('payment_gateways', ['identifier' => $payment_method])->row_array();
        $model_name = strtolower($payment_gateway['model_name']);

        if ($payment_gateway['is_addon'] == 1 && $model_name != null) {
            $this->load->model('addons/' . strtolower($payment_gateway['model_name']));
        }

        if ($model_name != null) {
            $payment_check_function = 'check_' . $payment_method . '_payment';
            $response = $this->$model_name->$payment_check_function($payment_method, 'package');
        }

        //ENDED payment model and functions are dynamic here
        if ($response === true) {
            $payment = $this->team_package_model->store_payment_history($payment_method, $payment_details);
            $this->sendRes($payment[0], $payment[1], $payment[2]);
        } else {
            $this->session->set_flashdata('error_message', site_phrase('an_error_occurred_during_payment'));
            redirect('addons/team_training/package_details/' . $payment_details['items'][0]['id'], 'refresh');
        }
    }

    function team_add_student_form($package_id)
    {
        if ($this->session->userdata('user_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($this->team_package_model->is_team_leader($package_id) == true) {
            $list = $this->team_package_model->my_team_member($package_id);
            $data['package_id'] = $package_id;
            $data['members'] = $list ? $list : [];
            $this->load->view('frontend/' . get_frontend_settings('theme') . '/add_team_member_form', $data);
        }
    }

    public function get_students_list()
    {
        $query = $this->input->get('query');
        $package_id = $this->input->get('package_id');

        $this->db->where('email', $query, 'both');
        $result = $this->db->get('users')->result_array();
        $members = $result ? $result : [];

        $page_data['members'] = $members ? $members : [];
        $page_data['package_id'] = $package_id;
        $page_data['searched'] = true;
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/team_member_list', $page_data);
    }
    function add_user_to_team($user_id = "", $package_id = "")
    {
        $userId = $_POST['userId']; // Retrieves the value of 'userId' from the query string
        $packageId = $_POST['package_id'];

        $this->team_package_model->enrol_in_package($package_id, $user_id);
    }

    //frontend purchase history
    function team_invoices()
    {
        if ($this->session->userdata('user_login') != true) {
            redirect(site_url('home'), 'refresh');
        }

        $total_rows = $this->team_package_model->user_package_purchase_history($this->session->userdata('user_id'))->num_rows();
        $config = array();
        $config = pagintaion($total_rows, 10);
        $config['base_url']  = site_url('addons/team_training/user_package_purchase_history');
        $this->pagination->initialize($config);
        $page_data['per_page']   = $config['per_page'];
        $page_data['page_name']  = "team_invoices";
        $page_data['page_title'] = site_phrase('team_invoices');
        $this->load->view('frontend/' . get_frontend_settings('theme') . '/index', $page_data);
    }

    //backend purchase history
    function purchase_history()
    {
        if ($this->session->userdata('is_instructor') != 1) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['page_name'] = 'team_package_purchase_history';
        if ($this->session->userdata('role_id') == 2) {
            $page_data['purchase_history'] = $this->team_package_model->backend_purchase_history($this->session->userdata('user_id'));
        } else {
            $page_data['purchase_history'] = $this->team_package_model->backend_purchase_history();
        }
        $page_data['page_title'] = get_phrase('team_package_purchase_history');
        $this->load->view('backend/index', $page_data);
    }


    //backend teams list
    public function teams_list()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['selected_category_id']   = isset($_GET['category_id']) ? $_GET['category_id'] : "all";
        $page_data['selected_instructor_id'] = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : "all";
        $page_data['page_name']              = 'teams-server-side';
        $page_data['categories']             = $this->crud_model->get_categories();
        $page_data['page_title']             = get_phrase('active_teams');
        $this->load->view('backend/index', $page_data);
    }

    function get_select2_course_data_for_team_training($default = "")
    {
        if ($this->session->userdata('admin_login') != true || $this->session->userdata('is_instructor') != 1) {
            $response = array();
            if ($default != '') {
                $response[] = array(['id' => $default, 'text' => get_phrase($default)]);
            } else {
                if ($this->session->userdata('admin_login') == true) {
                    $result = $this->db->where("status !=", 'draft')->group_start()->like('title', $_GET['searchVal'])->or_like('description', $_GET['searchVal'])->group_end()->limit(100)->get('course')->result_array();
                } else {
                    $result = $this->db->where("status !=", 'draft')->where('user_id', $this->session->userdata('user_id'))->group_start()->like('title', $_GET['searchVal'])->or_like('description', $_GET['searchVal'])->group_end()->limit(100)->get('course')->result_array();
                }
            }

            foreach ($result as $key => $row) {
                $response[] = ['id' => $row['id'], 'text' => $row['title']];
            }
            echo json_encode($response);
        }
    }
}
