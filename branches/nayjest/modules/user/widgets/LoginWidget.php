<?php
/**
* Login widget for Yii-user
* @author Vitaliy Stepanenko
* 
* Using:
* 
* <pre>
* 	$this->widget('LoginWidget');
* </pre>
* 
*/
class LoginWidget extends CWidget {

	protected function publishAssets()
	{
		if(($theme = Yii::app()->getTheme()) !== null){
					
			$className=str_replace('\\','_',ltrim(get_class($this),'\\')); // possibly namespaced class
			$path=$theme->getViewPath().DIRECTORY_SEPARATOR.$className;
			if (!is_dir($path)) $path = $this->getViewPath();
			
		}else{						
			$path=$this->getViewPath();			
		}   
		
		return Yii::app()->assetManager->publish(
			$path . DIRECTORY_SEPARATOR . 'assets',
			false,
			-1,
			YII_DEBUG
		);             
	}



	public function run() {      
		$assetUrl	=	$this->publishAssets() . DIRECTORY_SEPARATOR;
		$model		=	new UserLogin;
		$viewName	=	'loginWidgetForm';
		$module		=	Yii::app()->getModule('user');
		
		if (Yii::app()->user->isGuest) {            
			if(isset($_POST['UserLogin']))	
			{
				$model->attributes=$_POST['UserLogin'];                
				if($model->validate()) {	
					$user = $module->user();
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
			'user'		=>	$module->user(),
			'assetUrl'	=>	$assetUrl,
			'module'	=>	$module,
		));       
	}
}