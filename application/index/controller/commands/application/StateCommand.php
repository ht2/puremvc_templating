<?php

require_once PUREMVC.'patterns/command/SimpleCommand.php';  
require_once PUREMVC.'interfaces/INotification.php';
require_once BASEDIR.'ApplicationFacade.php';

class StateCommand extends SimpleCommand
{
	public function execute( INotification $notification )
	{	
        
        $mysql = new MySQL();
        
        $request  = str_replace( $mysql->base_dir, "", $_SERVER['REQUEST_URI']); 
        $all_params = explode( '?', $request );
        if( isset($all_params)){
            $params = explode("/", $all_params[0]);
        } else {
            $params = array();
        }
        
        
        $this->view = ( isset( $params[1] ) ) ? strtolower($params[1]) : '';
        
        switch( $this->view )
        {		
            default:
            case "home":
                $this->facade->sendNotification( ApplicationFacade::HOME_VIEW );
                break;
        }
    }
        
        
    private function defaultViews(){
        
    }

    public function adminViews() {
        switch( $this->view ){
            //Define views
        }
    }
}

?>
