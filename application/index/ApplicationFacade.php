<?php
require_once PUREMVC.'patterns/facade/Facade.php';
require_once COMMON.'controller/command/CommonInitialiseCommand.php';
require_once BASEDIR.'controller/commands/application/StateCommand.php';

foreach( glob(BASEDIR.'controller/commands/view/*.php') as $filename ) require $filename;

class ApplicationFacade extends Facade
{
	// Global commands
	const INITIALISE 	= "application/initialise";
	const TEMPLATE		= "application/template";
	const TOKENIZE		= "application/tokenize";
	const RENDER		= "application/render";
	const RENDER_JSON   = "application/render/json";
	const STATE		 	= "application/state";
	
	// API commands
    
	const HOME_VIEW        = "view/users";
    
    
	static public function getInstance()
	{
		if (parent::$instance == null) parent::$instance = new ApplicationFacade();
		return parent::$instance;
	}
	
	protected function initializeController()
	{
		parent::initializeController();
		
		// Global commands
		$this->registerCommand( ApplicationFacade::INITIALISE, 'CommonInitialiseCommand' );
		$this->registerCommand( ApplicationFacade::STATE,      'StateCommand' );		
		
		// API views
		$this->registerCommand( ApplicationFacade::HOME_VIEW, 'HomeView' );
        
	}	
	
	public function initialise()
	{
		$this->sendNotification( ApplicationFacade::INITIALISE );
		$this->removeCommand( ApplicationFacade::INITIALISE );
	}
}

?>