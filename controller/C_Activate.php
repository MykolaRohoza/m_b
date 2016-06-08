<?php
//
// Конттроллер страницы-примера.
//
class C_Activate extends C_Base {
    
    // переменные для создания наполнения 


    protected $contVars;
    private $mUsers;




    //
    // Конструктор.
    //
    function __construct($code) 
    {
    	parent::__construct();
       $this->mUsers = M_Users::Instance();
//        $this->needLogin = false;
//    	$this->needTimeTest = true;
//    	$this->needStocks = true;
        $this->controllerPath = "/activate";
        $this->contVars = array('code' =>$code);
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();


        
        // Обработка отправки формы.
        if ($this->IsPost()) {
            
            if(isset($_POST['login'])){
                $obg = array('login' => $_POST['login'], 'password' => $_POST['password']);
                $this->contVars['code'] = $this->mUsers->activateByInvite($_POST['code'], $obg);
               
                header("Location: $this->controllerPath/{$this->contVars['code']}");
                die();
            }
            if(isset($_POST['code'])){
                $this->activate($_POST['code']);
                
                header("Location: $this->controllerPath/{$_POST['code']}");
                die();
            }


        }
        else
        {

            if(isset($this->contVars['code'])){	
                $this->contVars = $this->activate($this->contVars['code']);
            }
        }
                
        
    }
    private function activate($code){
        
        $res = ($code === 'OK')?1:$this->mUsers->activate($code);
        if($res){
            $result['isActive'] = true;
        }
        if(is_numeric($res)){
            switch($res){
                case -1:
                    $result['message'] = 'Данный код уже использован';            
                    break;
                case -2:
                    $result['message'] = 'Пожалуйста введите свой логин (электронная почта) и пароль';
                    $result['needLoginForm'] = true;
                    $result['code'] = $code;
                    break;
                default :
                    $result['message'] = 'Спасибо, что за регестрировались на нашем сайте теперь вы можете использовать свой логин и пароль!';
                    
            }
        }
        
        return $result;
    }

    //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов
        $this->content['container_main'] = $this->View('V/view_activate.php', $this->contVars);
        parent::OnOutput();
        
        
            
    }
            
            
          
 }
