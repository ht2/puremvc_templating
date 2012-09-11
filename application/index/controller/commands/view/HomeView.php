<?php
/**
 *
 * @author JamesMullaney
 */
class HomeView extends ExtendedSimpleCommand {
    //put your code here
    
    public function __construct() {
        parent::__construct();
    }
    
    public function execute(\INotification $notification) {
        parent::execute($notification);
        
        switch( $this->command ){
            default:
            case "home":
                $this->viewHome();
                break;
        }
        
    }
    
    private function viewHome(){
        $this->content = $this->loadTemplate('home/home.html');
        $this->buildPage();        
    }
}

?>
