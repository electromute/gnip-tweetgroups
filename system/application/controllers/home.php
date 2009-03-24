<?php

class Home extends Controller {

    private $gnip_conf;
    public $gnip;
    public $gnip_filtername;
    public $gnip_pubname;
    public $gnip_scope;
    public $pager_action;
    public $pager_page;
    public $items_per_page;
    
    function Home() {
        parent::Controller();
        $this->load->model('Users_model'); 
        $this->load->model('Gnipdata_model'); 
        $this->load->plugin('gnip');
        
        $this->gnip_conf = $this->config->item('gnip');
        
        $this->gnip = new Services_Gnip($this->gnip_conf['username'], $this->gnip_conf['password']);
        $this->gnip_pubname = $this->gnip_conf['publishername'];
        $this->gnip_filtername = $this->gnip_conf['filtername'];
        $this->gnip_scope = $this->gnip_conf['scope'];
        $this->items_per_page = 20;
    }

    function index() {
        // do a little work to prevent them from dumping in random stuff here
        if (isset($_GET['page'])){
            switch ($_GET['action']){
                case "next":
                    $this->pager_action = "next";
                break;
                case "previous":
                    $this->pager_action = "previous";
                break;
                default: 
                    $this->pager_action = "next";
            }
            if (is_numeric($_GET['page'])){
                $this->pager_page = $_GET['page'] + 1;
            } else {
                $this->pager_page = 2;
            }
        } else {
            $this->pager_action = "next";
            $this->pager_page = 2;
        }
        $offset = (($this->pager_page - 1) * $this->items_per_page) - ($this->items_per_page - 1);
        
        $this->load->library('form_validation');
        $header['title'] = "Welcome to Boulder/Denver Tweets, Twitter data from Boulder and Denver, Colorado";
        $this->load->view('layouts/header', $header);
        
        $this->load->model('Users_model');
        $data['users'] = $this->Users_model->getUserMeta();
        $data['admins'] = $this->Users_model->getAdminMeta();
        $query = $this->Gnipdata_model->getTweets($this->items_per_page, $offset);
        
        $data['recordLimit'] = $this->items_per_page; 
        $topEnd = (($this->pager_page - 1) * $this->items_per_page);
        if ($query->num_rows() < $this->items_per_page){
            $data['endRecord'] = $query->num_rows();
        } else {
            $data['endRecord'] = $topEnd;
        }

        $tweets = $query->result();
        $data['tweets'] = $tweets;
        $data['total_tweets'] = $query->num_rows();
        $data['most_recent_tweetid'] = $this->Gnipdata_model->getMostRecentTweetID();
        $data['page'] = $this->pager_page;

        $formstuff = array(
            array(
                'field'   => 'username',
                'label'   => 'twitter username',
                'rules'   => 'trim|required|callback_user_already_exists|callback_is_valid_twitteruser|xss_clean'),
            );
        
        $this->form_validation->set_rules($formstuff);
        $this->form_validation->set_message('required', 'Please add a Twitter user');
        $this->form_validation->set_error_delimiters('<span class="error">', '<br /></span>');
        if ($this->form_validation->run() == FALSE)
        {
            $this->load->view('home', $data);
        }
        else
        {
            $status = $this->Users_model->insertUser();
            $rule_status = $this->addRule();
            if ($status < 1){
                die("there was a problem adding the user, please try again.");
            } else {
                redirect('');
            }
        }
        $this->load->view('layouts/footer');
    }
    
    function about(){
       
       $header['title'] = "Welcome to Boulder/Denver Tweets, Twitter data from Boulder and Denver, Colorado";
       $this->load->view('layouts/header', $header); 
       $this->load->view('about');
       $this->load->view('layouts/footer');
    }


    /*
    * NON VIEW FUNCTIONS
     */

    function user_already_exists($username) {
        $status = $this->Users_model->userAlreadyExists($username);
        if(!$status){
            // the user doesn't yet exist
            return TRUE;
        } else {
            $this->form_validation->set_message('user_already_exists', "Hey, this user already exists in our system!");
            return FALSE;
        }
    }

    function is_valid_twitteruser($username) {
        $this->load->plugin('twitterprofile');
        $twitterstuff = new Twitter_Profile($username);
        $xml = $twitterstuff->getUserXML();
        if (!$xml){
            $this->form_validation->set_message('is_valid_twitteruser', "This is not a valid Twitter user.");
            return FALSE;
        } else {
            $xml_element = new SimpleXMLElement($xml);
            $protected = strlen(strval($xml_element->protected)) ? strval($xml_element->protected) : null;
            if ($protected == 'true') {
                $this->form_validation->set_message('is_valid_twitteruser', "This user's updates are protected. Please try again when they change their status.");
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }


    function addRule() {
        $rules_array = array(new Services_Gnip_Rule("actor", $this->input->post("username")));
        $status = $this->gnip->addBatchRules($this->gnip_pubname, $this->filtername, $rules_array, $this->scope);
        log_message("debug", "The status for adding " . $this->input->post("username") . " to the filter was: " . $status);
    }
    
    /* AJAX FUNCTIONS */
    function refreshPhoto() {
        if (strlen($_POST['value'])){
            $username = $_POST['value'];
            $this->load->plugin('twitterprofile');
            $this->twitprofile = new Twitter_Profile($username);
            $new_imageURL = $this->twitprofile->getUserPicURL();
            $this->Users_model->updateUserPicURL($username, $new_imageURL);
            echo $new_imageURL;
        }
    }
    
    function checkForNewTweets() {
        if (is_numeric($_POST['value'])){
            $origMaxNum = $_POST['value'];
            $newMaxNum = $this->Gnipdata_model->getMostRecentTweetID();
            if ($newMaxNum > $origMaxNum) {
                echo true;
            }
        }
    }

}

?>