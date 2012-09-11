<?php
class JSONCommand extends ExtendedSimpleCommand
{    
    protected $success  = true, $error, $sub, $secure = true, $access = false, $user = false;
    
    public function __construct() {
        parent::__construct();
        
        $this->error = "";
        $this->sub = $this->checkPost('sub', false);        
        $this->results = array();
        if (empty($this->command)) {
            $this->command = 'default';
        }
    }
    
    public function printJSON(){
        $this->json['secure']   = $this->secure;
        $this->json['access']   = $this->access;
        $this->json['user']     = $this->user;
        $this->json['results']  = $this->results;
        $this->json['view']     = $this->view;
        $this->json['command']  = $this->command;
        $this->json['id']       = $this->id;
        $this->json['sub']      = $this->sub;
        $this->json['success']  = $this->success;
        $this->json['error']    = $this->error;
        $this->json['unixservertime'] = time();
        
        parent::printJSON();
    }
    
    public function checkVisibility($object_id, $type ){
        if( !$this->facade->retrieveProxy( UsersProxy::NAME )->isVisibileToUser($object_id, $type )) {
            $this->throwJSONError($object_id, $type, 'VISIBILITY');
        }
    }
    
    public function checkOwnership($object_id, $type) {
        if( !$this->facade->retrieveProxy( UsersProxy::NAME )->belongToUser($object_id, $type )) {
            $this->throwJSONError($object_id, $type, 'OWNERSHIP');
        }
    }
    
    protected function throwJSONError($object_id, $object_type, $error_type) {
        $error_type = (empty($object_id))? 'INDEX': $error_type ;
        $object_type = (empty($object_type))? 'undefined_object': $object_type ;
        $message = "";
        switch ($error_type) {
            default:
            case 'INDEX':
                $message = "[Index error]: $object_type identifier is undefined or null.";
            break;
            case 'REQUEST':
                $message = "[Request error]: $object_type '$object_id' is undefined and required.";
            break;
            case 'VISIBILITY':
                $message = "[Visibility error]: $object_type $object_id is inexistant or outside of your visibility scope.";
            break;
            case 'OWNERSHIP':
                $message = "[Ownership error]: $object_type $object_id is inexistant or doesn't belong to you.";
            break;
            case "COMMAND":
                $message = "[Command error]: Action '$object_id' unsupported. Available commands: $object_type.";
        }
        $this->success = false;
        $this->error = array();
        $this->error['type'] = $error_type;
        $this->error['message'] = $message;
        
        $this->printJSON();
    }
    
    public function loginCheck(){
        
        if( $this->secure ){
            //Check we are logged in and return JSON with an error if not
            $this->session = new Session();
            $valid = $this->session->valid();		
            if( !$valid )
            {
                //Not logged in
                $this->access = false;
                $this->success = false;
                $this->error = "You are not logged in";
                $this->printJSON();
            } else {
                $this->accessGranted();
            } 
        } else {
            $this->access = true;
        }
        
        
    }
    
    protected function accessGranted(){
        //User logged in
        $this->access = true;
        $this->user = array(
            'u_id'      =>  $this->session->u_id,
            'fname'     =>  $this->session->fname,
            'lname'     =>  $this->session->lname,
            'email'     =>  $this->session->email,
            'fullname'  =>  $this->session->user_name,
            'groups'    =>  $this->session->groups
        );
    }
}

?>
