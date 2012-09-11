<?php

require_once PUREMVC.'patterns/command/SimpleCommand.php';  
require_once PUREMVC.'interfaces/INotification.php';
require_once BASEDIR.'ApplicationFacade.php';

class StateCommand extends SimpleCommand
{
	public function execute( INotification $notification )
	{	
        $request  = str_replace( "", "", $_SERVER['REQUEST_URI']); 
        $all_params = explode( '?', $request );
        if( isset($all_params)){
            $params = explode("/", $all_params[0]);
        } else {
            $params = array();
        }
        
        $this->state = ( isset( $params[1] ) ) ? strtolower($params[1]) : '';
        $this->view = ( isset( $params[2] ) ) ? strtolower($params[2]) : '';
        
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
