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
				$output .= br('<script type="text/javascript" src="/view/packages/jquery.ui/js/jquery-ui-1.8.21.custom.min.js"></script>');
				$output .= $this->includeCSS( 'jquery-ui-1.8.21.custom.css', '{SITE_DIR}/view/packages/jquery.ui/css/smoothness/' );
				$output .= br('<script type="text/javascript" src="{SITE_DIR}/view/javascript/commonJQuery.js"></script>');	;	
			break;
			
            case 'validate':
				$output .= $this->includeJS( '{SITE_DIR}/view/packages/jquery-validation-1.9.0/jquery.validate.min.js' );
			break;
        
			case "datatables":
				$output .= $this->includeJS( '{SITE_DIR}/view/packages/DataTables-1.9.1/media/js/jquery.dataTables.min.js' );
				$output .= $this->includeCSS( 'jquery.dataTables_themeroller.css', '/view/packages/DataTables-1.9.1/media/css/' );
				$output .= $this->includeJS( '{SITE_DIR}/view/packages/DataTables-1.9.1/media/js/dataTables.init.js' );
            break;
        
			case "lightbox":
				$output .= $this->includeJS( '{SITE_DIR}/view/packages/jquery-lightbox-0.5/js/jquery.lightbox-0.5.min.js' );
				$output .= $this->includeCSS( 'jquery.lightbox-0.5.css', '{SITE_DIR}/view/packages/jquery-lightbox-0.5/css/' );
				$output .= $this->includeJS( '{SITE_DIR}/view/packages/jquery-lightbox-0.5/js/lightbox.init.js' );
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