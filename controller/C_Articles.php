<?php

class C_Articles extends C_Base {
    
    // переменные для создания наполнения 


    protected $contVars;




    //
    // Конструктор.
    //
    function __construct() 
    {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->needLogin = false;
    	$this->needTimeTest = true;
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();

        
        // Обработка отправки формы.
        if ($this->IsPost()) {
            header("Location: $this->controllerPath");
            die();


        }
        else
        {	
            if ($this->user == null && $this->needLogin)
            {       	
                header("Location: /");
                die();
            }
            // сбор разрешений и организация массивов
            $this->content['nav']['articles'] = 'class="active"';
            $this->content['title'] = 'Здоровая спина';
            
            $mArticles = M_Articles::Instance();
            $this->content['articles'] = $mArticles->getArticles(2, 0, 3);
            
            
            $this->metaTags['keywords'] = 'Шняга, Харьков, профилактор Евминова, лечение и профилактика заболеваний позвоночника';
            $this->metaTags['description'] = 'Профилактика и лечение проблем позвоночника, межпозвоночные грыжи,'
                    . ' избыточный вес, реабилитация пациентов после перенесенных травм и оперативного вмешательства,'
                    . ' укрепление мышечного корсета, Индивидуальные занятия с каждым пациентом.';
            
            $this->metaTags['og:url'] = "www.mind-body.ho.ua/";
            $this->metaTags['og:description'] = 'Профилактика и лечение проблем позвоночника, межпозвоночные грыжи,'
                    . ' избыточный вес, реабилитация пациентов после перенесенных травм и оперативного вмешательства,'
                    . ' укрепление мышечного корсета, Индивидуальные занятия с каждым пациентом.';
            $this->metaTags['og:title'] = 'Центр Mind Body - профилактор Евминова';
            $this->metaTags['og:type'] = "Article";
            
        }
                
        
    }
    //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов
        if($this->needStocks && count($this->content['stocks']) > 0){
            $vars['stocks'] = $this->View('V/view_stocks.php',
                    array('stocks' => $this->content['stocks'], 'isAdmin' => $this->isAdmin));
        }
        $vars['isAdmin'] = $this->isAdmin;
        $vars['articles'] = $this->content['articles'];
        $this->content['container_main'] = $this->View('V/view_prevention.php', $vars);
        parent::OnOutput();
             
    }         
 }
