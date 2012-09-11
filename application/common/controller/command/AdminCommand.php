<?php

class AdminCommand extends ExtendedSimpleCommand {
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
        $this->userbar = "Logged in ".easylink("(logout)", $this->logout_link );
        $this->navbar = $this->loadTemplate('common/navbar.html');
    }
    
    public function checkAdminLevel($level_required){
        if (!($this->session->admin->level >= $level_required)) {
            $this->redirect( '/admin/home' );
        }
    }
    
    public function isAdminLevel($level){
        return ($this->session->admin->level >= $level);
    }
    
    public function throwError($error = "<h3>Ressource not accessible</h3> <strong>Sorry, this element/action doesn't exist or require special rights.</strong>") {
        $this->content = $error;
        $this->bc_links[] = '<strong>Error</strong>';
        $this->buildPage();
    }
    
}

?>
