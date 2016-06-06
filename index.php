<?php header('Content-type: text/html; charset=utf-8');
?>
<?php 

include_once('model/M_startup.php');
M_startup();
	function __autoload($className){
		$dir = explode('_', $className);
                $dirPath = __DIR__ . '/';
                switch($dir[0]){
                        case'C': 
                            $dirPath .= "controller/";
                            break;
                        case 'M': 
                            $dirPath .= "model/";
                            break;
		}
                 include_once($dirPath . $className . ".php");
	}
	
        $info_ = explode('/', $_GET['q']);
        $info = array();
        foreach ($info_ as $value) {
            if($value != ''){
                $info[] = $value;
            }
        }
        for($i = 1; $i <  count($info); $i += 2){
            $get[$info[$i]] = $info[$i+1];
        }
        
	switch ($info[0])	{
            case('contacts'):
                $controller = new C_Contacts();
                break;
            case('articles'):
                $controller = new C_Articles();
                break;
            case('edit'):
                $controller = new C_Edit($info[1]);
                break;
            case('prevention'):
                $controller = new C_Prevention();
                break;
            case('adm'):
                $controller = new C_Admin();
                break;
            case('cases') : 
                $controller = new C_Cases();
                break;
            case('resp') : 
                $controller = new C_Response();
                break;
            case('activate') : 
                $controller = new C_Activate($info[1]);
                break;
            case('users') : 
                $controller = new C_Users();
                break;
            default : $controller = new C_Main();
	}

        
	$controller->Request($info);
