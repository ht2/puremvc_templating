<?php

class SurveyCommand extends ExtendedSimpleCommand {
    //put your code here
    
    public function __construct() {
        parent::__construct();
        
        //$this->bc_links[] = easylink('Main Menu', '/admin/home');
    }
    
    protected function redirect($goto = '/admin') {
        parent::redirect($goto);
    }
    
    public function loginCheck() {
        parent::loginCheck();
        
        if ( !$this->session->admin->level ){
            $this->doLogout();    
        }
        $this->userbar = "Logged in ".easylink("(logout)", $this->logout_link );
        $this->navbar = $this->loadTemplate('common/navbar.html');
    }
    
    public function doLogout() {
        $this->session = new Session();
		$this->session->destroy();
		$this->redirect('/admin/login');
    }
    
}

?>
