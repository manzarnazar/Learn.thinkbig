<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Liveclass_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function update_live_class($course_id) {

        if (!empty($this->input->post('live_class_schedule_date')) && !empty($this->input->post('live_class_schedule_time'))) {
            $data['date']                  = strtotime($this->input->post('live_class_schedule_date'));
            $data['time']                  = strtotime($this->input->post('live_class_schedule_time'));
            //$data['meeting_invite_link'] = $this->input->post('meeting_invite_link');
            $data['zoom_meeting_id'] = $this->input->post('zoom_meeting_id');
            $data['zoom_meeting_password'] = $this->input->post('zoom_meeting_password');
            $data['note_to_students']      = $this->input->post('note_to_students');
            $data['course_id']             = $course_id;
            $previous_data = $this->db->get_where('live_class', array('course_id' => $course_id))->num_rows();
            if ($previous_data > 0) {
                $this->db->where('course_id', $course_id);
                $this->db->update('live_class', $data);
            }else{
                $this->db->insert('live_class', $data);
            }
        }

    }

    public function get_live_class_details($course_id = "") {
        return $this->db->get_where('live_class', array('course_id' => $course_id))->row_array();
    }

    function update_live_class_settings($user_id = ""){
        $data['user_id'] = $user_id;
        $data['client_id'] = $this->input->post('client_id');
        $data['client_secret'] = $this->input->post('client_secret');

        $row = $this->db->where('user_id', $user_id)->get('zoom_live_class_settings');

        if($row->num_rows() > 0){
            $data['updated_at'] = time();
            $this->db->where('user_id', $user_id)->update('zoom_live_class_settings', $data);
        }else{
            $data['created_at'] = time();
            $this->db->insert('zoom_live_class_settings', $data);
        }
    }
}
