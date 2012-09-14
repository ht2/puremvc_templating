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
        $this->mysql = new MySQL();
    }
   
}

?>