<?php

require_once PUREMVC.'patterns/proxy/Proxy.php';
require_once COMMON.'model/vo/VO.php';
require_once BASEDIR.'ApplicationFacade.php';
require_once COMMON.'Session.php';

class UsersProxy extends Proxy
{
    const NAME = "UsersProxy";
    var $mysql, $user;
    
    public function __construct(){
        parent::__construct( UsersProxy::NAME, new VO() );
        $this->session = new Session();
        $this   ->mysql = new MySQL();
    }
    
    public function getUser( $u_id ){
        $u_id = intval($u_id);
        $this->mysql->select("users_norm", "u_id=$u_id");
        $user = $this->mysql->singleResult();
        $user->admin_scope = $this->facade->retrieveProxy( GroupsProxy::NAME )->getAdminScope($user);
        return $user;
    }
    
    public function editUser($user, $u_id) {
        $this->mysql->update('users', $user, 'u_id', $u_id);
    }
    
    public function updateUser(){
        $user = $this->getUser($this->session->u_id);
        $this->session->user( $user );
    }
   
}

?>