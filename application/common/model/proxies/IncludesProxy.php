<?php

require_once PUREMVC.'patterns/proxy/Proxy.php';
require_once COMMON.'model/vo/VO.php';
require_once BASEDIR.'ApplicationFacade.php';

class IncludesProxy extends Proxy
{
	const NAME = "IncludesProxy";
	
	public function __construct()
	{
		parent::__construct( IncludesProxy::NAME, new VO() );
	}
	
	public function includeCSS( $file, $location='/view/css/' )
	{
		return br('<link href="'.$location.$file.'" rel="stylesheet" type="text/css" />');
	}
	public function includeJS( $file_location )
	{
		return br('<script type="text/javascript" src="'.$file_location.'"></script>');
	}
	
	public function includes( $type=NULL )
	{
		$output = "";
		switch( $type )
		{
			case 'jquery':
				$output .= br('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>');
				$output .= br('<script type="text/javascript" src="{SITE_DIR}/view/javascript/commonJQuery.js"></script>');	;	
			break;
            case "bootstrap":
                $output .= $this->includeJS( '{SITE_DIR}/view/packages/bootstrap/js/bootstrap.min.js' );
				$output .= $this->includeCSS( 'bootstrap.min.css', '{SITE_DIR}/view/packages/bootstrap/css/' );
            break;
            case "common":
                $output .= $this->includeJS( '{SITE_DIR}/view/javascript/common.js' );
				$output .= $this->includeCSS( 'reset.css', '{SITE_DIR}/view/css/' );
				$output .= $this->includeCSS( 'style.css', '{SITE_DIR}/view/css/' );
            break;
		}
		return $output;
	}

	public function vo()
	{
		return $this->getData();
	}
}
?>