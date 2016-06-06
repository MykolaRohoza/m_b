<?php

abstract class C_Base extends C_Controller {

    protected $needLogin; // необходимость авторизации 
    protected $user;  // авторизованный пользователь
    protected $userName;
    protected $controllerPath;
    protected $content;
    protected $isAdmin;
    protected $displayHeader;
    protected $needTimeTest;
    protected $isSuccessfully;
    protected $needStocks;
    protected $needLoginForm;
    protected $needCarosel;
    protected $metaTags;
    protected $contentNeed;
    protected $alts;
    protected $isEdit;
    private $start_time; // время начала генерации страницы
    protected $galery;
    private  $mUsers;
    private  $mArticles;
    private  $mImages;



    //
    // Конструктор.
    //
    function __construct() {
        parent::__construct();
        
        $this->needStocks = true;
        $this->needLoginForm = true;
        $this->needCarosel = true;
        $this->mUsers = M_Users::Instance();
        $this->user = $this->validate($this->mUsers->Get());           
        $this->isAdmin = ($this->user['id_role'] == 1);
    }

    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput() {

        parent::OnInput();
        if($this->IsPost()){
            
            if ($_POST['login_btn'] && $this->mUsers->Login($_POST['login'], 
            $_POST['password'], isset($_POST['remember']))) {
                header("Location: $this->controllerPath");
                die();
            }

            if ($_POST['logout']) {
                $this->mUsers->Logout();
                header("Location: $this->controllerPath");
                die();
            }


        }
        else{
            if ($this->needTimeTest) {
                $this->start_time = microtime(true);
            }

            // Очистка старых сессий и определение текущего пользователя.
            //$this->mUsers->ClearSessions(); 

            if ($this->user == null && $this->needLogin) {

                header("Location: /");
                die();
            }
            $this->isAdmin = $this->user['id_role']*1 === 1;
            $this->mArticles = M_Articles::Instance();
            $this->mImages = M_Images::Instance();
            $this->alts = $this->mImages->getAlts();

            if($this->needCarosel || $this->isEdit) $this->galery = $this->getImages();
            // для массовой обрезки            
            //$this->galery = $this->getImages('images/full');
            $this->display = 'style = "display: none"';

            //$this->userName = $this->c_loginForm->OnInput($this->isAdmDisplay);
            if($this->user == null){
                $this->user['logout_collapse'] = 'collapse';
            }

            $this->content['stocks'] = $this->getStocks();
            
        }
    }
    



    private function validate($arr){

        if($arr != null){
            foreach ($arr as $key => $value) {
                if(preg_match('~id~', $key)){
                    $arr[$key] = (int)$value;
                }


            }
            $arr['login_collapse'] = 'collapse';
        }
       
        return $arr;
    }
    
    function getImages(){
        
        $path = 'images/carousel';
	$handle = opendir($path);
        $pictures = array();
	$i = 0;
        if ($handle != false){
            while (false !== ($file = readdir($handle))){
                if(strlen($file) > 2 && (isset($this->alts[$file]) || $this->isEdit)){
                    $pictures[$i]['path'] = $this->server . '/' . $path . '/' . $file;
                    $pictures[$i]['full_path'] = $this->server . '/images/full/' . $file;
                    $pictures[$i]['alt'] = $this->alts[$file];

                    $i++;
                }
            }
            closedir($handle);

	}
	return $pictures;
    }
    private function getStocks(){
        $result = $this->mArticles->getArticles(1);
        return $result;
    }
            

        //
    // Виртуальный генератор HTML.
    //	
    protected function OnOutput() {


        // Основной шаблон всех страниц.

        $vars = array('container_main' => $this->content['container_main'], 'nav' => $this->content['nav'],
            'images' => $this->galery, 'metaTags' => $this->metaTags, 'user' => $this->user, 
            'needCarosel' => $this->needCarosel, 'needLoginForm' => $this->needLoginForm,
            'isAdmin' => $this->isAdmin
);
        $page = $this->View('V/view_base.php', $vars);

        if ($this->needTimeTest) {
            $time = microtime(true) - $this->start_time;
            $page .= "<!-- Время генерации страницы: $time сек.-->";
        }
        // Вывод HTML.
        echo $page;
        
    }

}
