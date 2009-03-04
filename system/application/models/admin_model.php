<?php
class Admin_model extends Model {


    function Admin_model()
    {
        parent::Model();
        $this->load->helper('timestamp');
    }
    
    function verifyCode($activationcode) {
        $code = trim($activationcode);
        if (empty($code)) return false;
        $query = $this->db->getwhere('admin', array('activationcode' => $code, 'username' => $this->input->post('username')));
        if ($query->num_rows() == 1) {
            $admin = $query->row();

            $this->db->where('activationcode', $code);
            $this->db->update('admin', array('activationcode' => NULL, 'password' => md5($this->input->post('password'))));

            $status = $this->simplelogin->login($admin->username, md5($this->input->post('password')));
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
}
?>