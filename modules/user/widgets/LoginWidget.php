<?php
/**
* Login widget for Yii-user
* 
* Usage:
* 
* <code>
*   # Basic:
* 	$this->widget('LoginWidget');
* </code>
* 
* @author Vitaliy Stepanenko <mail@vitaliy.in>
* @license BSD
* @package yii-user.widgets
* @version $Id:$
* @since File available since revision 91
*/

/**
* Login widget for Yii-user
*/
class LoginWidget extends CPortlet {
	
	/**
	* List of Urls, on which this widget will be not rendered.
	* 
	* @var array
	*/
	public $ignoredUrls = array(
		'user/registration',
		'user/login',
		'user/profile'
	);
	
	/**
	* Portlet title, that displays for logged in users.
	* If you don't need title, set to null.
    * If you wont to use default module-generated title, set to true.
	* @var string
	*/
	public $loggedInTitle = true;
	
	/**
	* Portlet title, that displays for siteguests.
	* If you don't need title, set to null.
    * If you wont to use default module-generated title, set to true.
	* @var string
	*/
	public $guestTitle = true;
	
	/**
    * Portlet title string.
    * If you don't need title, set to null.
    * If you wont to use $loggedInTitle and $guestTitle, set to true.
    * 
    * @var string
    */
	public $title = true;
	
	private $_module;
	
	/**
	* Checks that current Url is not in ignore list
	* @see ignoredUrls
	* @return bool
	* 
	*/
	protected function isCurrentUrlAllowed()
	{		
		if (!empty($this->ignoredUrls)) {
			$route = Yii::app()->urlManager->parseUrl(Yii::app()->request);
			foreach ($this->ignoredUrls as $url){			
				if (strpos($route,$url) === 0){
					return false;
				}
			} 
		}
		return true;
	}

	/**
	* Publish widget assets
	*/
	protected function publishAssets()
	{
		if(($theme = Yii::app()->getTheme()) !== null) {
					
			$className = str_replace('\\', '_', ltrim(get_class($this), '\\')); # Possibly namespaced class
			$path = $theme->getViewPath() . DIRECTORY_SEPARATOR . $className;
			if (!is_dir($path)) $path = $this->getViewPath();
			
		}else{						
			$path = $this->getViewPath();			
		}   
		
		return Yii::app()->assetManager->publish(
			$path . DIRECTORY_SEPARATOR . 'assets',
			false,
			-1,
			YII_DEBUG
		);             
	}

	public function init() {
		$this->_module		=	Yii::app()->getModule('user');	# Call this before using any other classes from Yii-user
		                                                		# to provide import of all needed classes in UserModule::init()
		if ($this->title === true) {
			if (Yii::app()->user->isGuest) {
				if ($this->guestTitle === true) {
					$this->guestTitle = UserModule::t('Login');
				}
				$this->title = $this->guestTitle;
				
			} else {
				if ($this->loggedInTitle === true) {
					$this->loggedInTitle = UserModule::t('Your profile');
				}
				$this->title = $this->loggedInTitle;
			}            
		}                                                		
		
		parent::init();                                                		
		
	}
	
    /**
	* Executes the widget.
	* This method is called by {@link CBaseController::endWidget}.
	*/
	public function renderContent() 
	{  
		if (!$this->isCurrentUrlAllowed()) {
			return;
		}	
	    
		$assetUrl	=	$this->publishAssets();		
		$model		=	new UserLogin;					
		$viewName	=	'loginWidgetForm';		
		
		if (Yii::app()->user->isGuest) {            
			if(isset($_POST['UserLogin'])){
				$model->attributes = $_POST['UserLogin'];                
				if($model->validate()) {	
					$user = $this->_module->user();
					$user->lastvisit = time();
					$user->save();
					#Not shure, that we need this code, coz redirecting can be controlled in RBAC system
					//if ($actionId == 'registration') Yii::app()->controller->redirect('/');                    
					$viewName = 'loginWidgetDone';
				}
			}				
		} else {
			$viewName = 'loginWidgetDone';
		}
		        
		$this->render($viewName,array(
			'model'		=>	$model,
			'user'		=>	$this->_module->user(),
			'assetUrl'	=>	$assetUrl,
			'module'	=>	$this->_module,
		));       
	}
}
