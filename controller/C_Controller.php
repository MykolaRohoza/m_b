<?php
//
// Базовый класс контроллера.
//
abstract class C_Controller
{
    protected $_post;
    protected $_get;
    protected $server;


    //
	// Конструктор.
	//
	function __construct()
	{	
            $this->server = 'http://' . $_SERVER['SERVER_NAME'];
	}
	
	//
	// Полная обработка HTTP запроса.
	//
	public function Request($get)
	{
                if($this->IsGet()) {
                    $this->_get = $get;
                }
		$this->OnInput();
		$this->OnOutput();
	}
	
	//
	// Виртуальный обработчик запроса.
	//
	protected function OnInput()
	{
           
	}
	
	//
	// Виртуальный генератор HTML.
	//	
	protected function OnOutput()
	{
	}
	
	//
	// Запрос произведен методом GET?
	//
	protected function IsGet()
	{
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}

	//
	// Запрос произведен методом POST?
	//
	protected function IsPost()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
        // защита запросов
        

        
        
	//
	// Генерация HTML шаблона в строку.
	//
	protected function View($fileName, $vars = array())
	{
		
            if (count($vars) > 0){
                foreach ($vars as $key => $value){ 
                    $$key = $value;
               
                }
            }     
            ob_start(); 
            include ($fileName); 
            return ob_get_clean(); 	
	}	
}
