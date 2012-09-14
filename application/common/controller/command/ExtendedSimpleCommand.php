<?php
class ExtendedSimpleCommand extends SimpleCommand
{   
    protected $url_start = "", $params;
    
	//Setup vars
	protected $session, $mysql, $view, $command, $id;
	
	//HTML vars
	protected $includes = '', $inits = '', $module, $header, $container, $layout, $content, $footer;
	
	//Token vars
	protected $pre_tokens = array(), $post_tokens = array();
	
	//Proxies
    protected $template_proxy;
    
	//Error vals
	public $error_vals = array( 'There was an error', 'You must fill in the required fields (*)' ), $actions;
	
	public function __construct(){		
		parent::__construct();	
		$this->mysql 		= new MySQL();	
		$this->template 	= new Template();
        
		
        //Layouts
        $this->container    = $this->loadTemplate('common/container.html');
        $this->layout       = $this->loadTemplate('layouts/onecol.html');
        
        $this->header       = $this->loadTemplate('common/header.html');
        $this->footer       = $this->loadTemplate('common/footer.html');
        
        
        $this->submitted    = ( strlen( $this->checkPost('submitted'))>0 );
        $this->error        = $this->checkPost('error', 0, 2);
		
        $this->setParams();
        
        //Includes and Initialisations        
		$this->addInclude('jquery');
        $this->addInclude('bootstrap');
		$this->addInclude('common');
        
		$this->site_title 	= 'HT2 PureMVC PHP Templating';
        
		$this->bc_links = array();
        
        //Proxies
        $this->template_proxy = $this->facade->retrieveProxy(TemplateProxy::NAME);
		
	}
    
    protected function setParams(){
        $request  = str_replace( $this->url_start, "", $_SERVER['REQUEST_URI']); 
        $all_params = explode( '?', $request );
        if( isset($all_params)){
            $this->params = explode("/", $all_params[0]);
        } else {
            $this->params = array();
        }
        
        $view_default       = isset($this->params[2]) ? $this->params[2] : "";
        $command_default    = isset($this->params[3]) ? $this->params[3] : "";
        $id_default         = isset($this->params[4]) ? $this->params[4] : "";
        $sub_default        = isset($this->params[5]) ? $this->params[5] : "";
        
        $this->view     = $this->checkPost('view',      1,  $view_default );
        $this->command  = $this->checkPost('command',   1,  $command_default );
        $this->id       = $this->checkPost('id',        1,  $id_default );
        $this->sub      = $this->checkPost('sub',       1,  $sub_default );
    }


    public function execute( INotification $notification ){
		//This function is run in all pages where we need to be logged in
		//Check we are actually a valid user
        //$this->loginCheck();
	}
	
    protected function addInclude( $include_type ){
		$this->includes .= $this->facade->retrieveProxy( IncludesProxy::NAME )->includes( $include_type );
    }
    
	
	protected function getUniversalTokens()
	{		
		return array(	
			'{INCLUDES}' 			=> $this->includes,
			'{INITIALISERS}' 		=> $this->inits,
			'{HEADER}'				=> $this->header,	
            '{CONTENT}'             => $this->content,
			'{FOOTER}'				=> $this->footer,
			'{ID}' 					=> $this->id,
			'{VIEW}' 				=> $this->view,
			'{COMMAND}' 			=> $this->command,
            '{SITE_TITLE}'          => $this->site_title,
            '{SITE_DIR}'            => $this->mysql->site_root.$this->url_start
		);
	}
	
	protected function loadFile( $file ){ return $this->facade->retrieveProxy(TemplateProxy::NAME)->loadFile( $file ); }	
	protected function loadTemplate( $file ){ return $this->loadFile( HTML.$file ); }
	
    protected function addPreToken( $key, $value ){
        $this->pre_tokens[] = array( $key => $value );
    }
    
    protected function addPreTokens( $array ){
        $this->pre_tokens[] = $array;
    }
    
    protected function addPostToken( $key, $value ){
        $this->post_tokens[] = array( $key => $value );
    }
    
    protected function addPostTokens( $array ){
        $this->post_tokens[] = $array;
    }
	
	public function buildPage( $pdf = false )
	{
		//Select the template
        
		$this->facade->sendNotification( ApplicationFacade::TEMPLATE, $this->container );
        
		$this->facade->sendNotification( ApplicationFacade::TOKENIZE, array( '{MAIN}' => $this->layout ) );
		
		//Add pre-universal tokens
		foreach($this->pre_tokens as $pre_t)
			if(is_array($pre_t)) 
				$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $pre_t );
		
		//Add universal tokens
		$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $this->getUniversalTokens() );
		
		//Add post-universal tokens
		foreach($this->post_tokens as $post_t)
			if( is_array($post_t) ) 
				$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $post_t );
           
         
		//Render page
        $this->facade->sendNotification( ApplicationFacade::RENDER );
        exit();
	}
	
    
	protected function checkPost( $val, $type=1, $default=null, $striptags=false )
	{
        if( $default == null ){
            switch( $type ){
                default:
                case 1:
                    $default = "";
                break;

                case 2:
                    $default = 0;
                break;

                case 3:
                    $default = false;
                break;

                case 4:
                    $default = array();
                break;
            }
        }
        
		$return_val = isset( $_REQUEST[$val] ) ?	$_REQUEST[$val]	 : $default;
		
        if( $striptags ){
            $return_val = strip_tags($striptags);
        }
        
		switch( $type )
		{
			default:
			case 1:
				return trim($return_val);
			break;
			
			case 2:
				return intval($return_val);
			break;
			
			case 3:
				return (boolean)$return_val;
			break;
        
            case 4:
                return (array)$return_val;
            break;
		}
	}
	
	protected function redirect( $goto='/' )
	{
		header('Location:'.$goto );
		exit();
	}
	
	protected function deniedAccess( $message="Denied access" )
	{
		$this->content = $message;
		$this->buildPage();
	}
    
    public function detectFileType ($name) {
        if (preg_match("/^.*\.(jpg|jpeg|jp2|png|gif|bmp|tiff|ico)$/i",$name)) return 'Image';
        elseif(preg_match("/^.*\.(mp3|m4a|wma|aac|wav|flac|aif|aiff|aifc|au|ra|mpc|mid)$/i",$name)) return 'Audio';
        elseif(preg_match("/^.*\.(mp4|wmv|avi|mov|3gp|dat|fla|m4v|mkv|rm|mpg|mpeg|ogg|swf)$/i",$name)) return 'Video';
        elseif(preg_match("/^.*\.(pdf|txt|htm|html|xml|doc|docx|ppt|pptx|xls|xlsx||odt|ods|odp|odg|odc|odf|odb|odm|odi)$/i",$name)) return 'Document';
        else return 'Unknown';
    }
    
}
?>