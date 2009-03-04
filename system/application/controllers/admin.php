<?php

class Admin extends Controller {

    private $gnip_conf;
    public $gnip;
    public $gnip_scope;
    public $gnip_filtername;
    public $gnip_pubname;
    
    function Admin() {
        parent::Controller();
        $this->load->library('form_validation');
        $this->load->plugin('gnip');
        
        $this->gnip_conf = $this->config->item('gnip');
        
        $this->gnip = new Services_Gnip($this->gnip_conf['username'], $this->gnip_conf['password']);
        $this->gnip_pubname = $this->gnip_conf['publishername'];
        $this->gnip_filtername = $this->gnip_conf['filtername'];
        $this->gnip_scope = $this->gnip_conf['scope'];
    }

    function index() {
        if($this->session->userdata('logged_in')) {
            redirect('admin/dashboard');
        } else {
            $header['title'] = "Please log in";
            $this->load->view('layouts/header', $header);

            $formstuff = array(
                    array(
                         'field'   => 'username',
                         'label'   => 'username',
                         'rules'   => 'trim|required|max_length[100]|xss_clean'
                     ),
                     array(
                         'field'   => 'password',
                         'label'   => 'password',
                         'rules'   => 'trim|required|max_length[100]|xss_clean'
                     )
                );

            $this->form_validation->set_rules($formstuff);
            $this->form_validation->set_error_delimiters('<span class="error">', '</span>');

            if ($this->form_validation->run() == FALSE){
                $this->load->view('form_login');
            } else {
                if($this->simplelogin->login($this->input->post('username'), md5($this->input->post('password')))) {
                    redirect('admin/dashboard');
                } else {
                    $data['loginfeedback'] = "You entered an incorrect username or password";
                    $this->load->view('form_login', $data);
                }
            }
            $this->load->view('layouts/footer');
        } // end if already logged in check
    }


    function signup() {
        if($this->session->userdata('logged_in')) {
            redirect('admin/dashboard');
        } else {
            $this->load->model('Admin_model');
            $header['title'] = "Sign up";
            $this->load->view('layouts/header', $header);

            $formstuff = array(
                    array(
                         'field'   => 'username',
                         'label'   => 'username',
                         'rules'   => 'trim|required|max_length[100]|xss_clean'
                     ),
                     array(
                         'field'   => 'password',
                         'label'   => 'password',
                         'rules'   => 'trim|required|max_length[100]|xss_clean'
                     ),
                     array(
                          'field'   => 'activationcode',
                          'label'   => 'activation code',
                          'rules'   => 'trim|required|max_length[100]|xss_clean'
                      )
                );

            $this->form_validation->set_rules($formstuff);
            $this->form_validation->set_error_delimiters('<span class="error">', '</span>');

            if ($this->form_validation->run() == FALSE){
                $this->load->view('form_signup');
            } else {
                if($this->Admin_model->verifyCode($this->input->post('activationcode'))){
                    redirect('admin/dashboard');
                } else {
                    $data['signupfeedback'] = "You entered an incorrect activation code or are not approved to be an administrator";
                    $this->load->view('form_signup', $data);
                }
            }
            $this->load->view('layouts/footer');
        } // end if already logged in check
    }

    function dashboard(){
        if(!$this->session->userdata('logged_in')) {
            redirect('');
        } else {
            $this->load->model('Users_model');
            $header['title'] = "User moderation dashboard";
            $this->load->view('layouts/header', $header);
            $data['users'] = $this->Users_model->getAllUsers();
            $this->load->view('admin_dashboard', $data);
            $this->load->view('layouts/footer');
        }
    }
    
    function promote(){
        if(!$this->session->userdata('logged_in')) {
            redirect('');
        } else {
            $this->load->model('Users_model');
            $header['title'] = "New admin user";
            $this->load->view('layouts/header', $header);
            $username = $this->uri->segment(3);
            $data['activationcode'] = $this->Users_model->updateAdminStatus($username, 1);
            $this->load->view('admin_promote', $data);
            $this->load->view('layouts/footer');
        }
    }
    
    /* 
    * Non view functions
    * 
    */

    function revoke(){
        if(!$this->session->userdata('logged_in')) {
            redirect('');
        } else {
            $this->load->model('Users_model');
            $username = $this->uri->segment(3);
            $this->Users_model->updateAdminStatus($username, 0);
            redirect('admin/dashboard');
        }
    }
    
    function logout(){
        if(!$this->session->userdata('logged_in')) {
            redirect('');
        } else {
            $this->simplelogin->logout();
            redirect('');
        }
    }
    
    function addRule($user) {
        if(!$this->session->userdata('logged_in')) {
            redirect('');
        } else {
            $rules_array = array(new Services_Gnip_Rule("actor", $user));
            $status = $this->gnip->addBatchRules($this->gnip_pubname, $this->gnip_filtername, $rules_array, $this->gnip_scope);
            log_message("info", "The status for adding " . $user . " to the filter was: " . $status);
        }
    }
    
    function removeRule($user) {
        if(!$this->session->userdata('logged_in')) {
            redirect('');
        } else {
            $rules_array = array(new Services_Gnip_Rule("actor", $user));
            $status = $this->gnip->addBatchRules($this->gnip_pubname, $this->gnip_filtername, $rules_array, $this->gnip_scope);
            log_message("info", "The status for removing " . $user . " to the filter was: " . $status);
        }
    }
    
    /* AJAX FUNCTIONS */
    
    function toggleuser(){
        if(!$this->session->userdata('logged_in')) {
            redirect('');
        } else {
            $this->load->model('Users_model');
            if($_POST['value'] == 1){
                $status = $this->Users_model->updateActiveStatus($_POST['name'], $_POST['value']);
                $this->addRule($_POST['name']);
            } elseif ($_POST['value'] ==  0) {
                $status = $this->Users_model->updateActiveStatus($_POST['name'], $_POST['value']);
                $this->removeRule($_POST['name']);
            }
            echo $_POST['value'];
        }
    }

}

?>