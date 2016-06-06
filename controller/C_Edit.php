<?php

//
// Конттроллер страницы-примера.
//
class C_Edit extends C_Base{
    
    // переменные для создания наполнения 


    protected $contVars;
    private $mArticles;




    //
    // Конструктор.
    //
    function __construct() 
    {
    	parent::__construct();
        $this->mUsers = M_Users::Instance();
        $this->needLogin = true;
        $this->needStocks = false;
        $this->needLoginForm = false;
        $this->needCarosel = false;
    	$this->needTimeTest = true;
        $this->mArticles = M_Articles::Instance();
        $this->controllerPath = '/edit/' . $_POST['id_article'];
        $this->isEdit = true;
        $this->content = array();
    }


    
    //
    // Виртуальный обработчик запроса.
    //
    protected function OnInput(){
        
        parent::OnInput();

        
        // Обработка отправки формы.
        if ($this->IsPost()) {

            if(isset($_POST['save'])){
                $message = '/' .$this->save();
            }
            if(isset($_POST['delete'])){
                $this->delete();
                $this->controllerPath = '/edit';
            }
            if(isset($_POST['delete_img'])){
                $message = '/' . $this->deleteImg($_POST['old_name']);     
            }
            if(isset($_POST['upload_img'])){
                $message = '/' . $this->uploadImg();
            }            

            header("Location: $this->controllerPath$message");
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
            $this->content['nav']['edit'] = 'class="active"';
            if(isset($this->_get[1]) && $this->_get[1] > 0){
                $temp_arr = $this->mArticles->getArticles(0, $this->_get[1]);
                $this->content['articles'] = $temp_arr[0];
            }

            
            $this->content['articles']['article_list'] = $this->mArticles->getArticlesNames();
            $this->content['articles']['message'] = $this->_get[2];
            $this->content['articles']['message_article'] = $this->_get[2];
          
        }
                
        
    }
    private function save(){
       
        $queryKeys = array('article_title', 'article_text', 'article_order', 'article_func', 'article_dest', 'article_img_name',
            'article_img_place', 'article_secondary_to');
        $queryObj =array();
        foreach ($queryKeys as  $key) {
            if($key != 'article_text' && $key != 'article_title'){
                $queryObj[$key] = $_POST[$key];
            }
            else{

               $queryObj[$key . '_ru'] = $_POST[$key]; 
            }
        }
        $message = $this->mArticles->save('articles', $queryObj, "id_article={$_POST['id_article']}");
        return $message;
        
        
        
    }
    private function delete(){
        $this->mArticles->delete('articles', "id_article={$_POST['id_article']}");
    }
    private function uploadImg(){
        $file = $_FILES['img'];
        $message = '';
        $path = 'images/';

        if($file['name'] =='' && !isset($_POST['name'])){
            $message .= 'Файл не выбран ';
        }
        if(isset($_POST['name']) && strlen($_POST['name']) > 0){
            $file['name'] = $this->prepareName($file['name'], $_POST['name']);
        }
        
        
        if(strlen($file['tmp_name']) > 0){ 
        
            if($file['size'] > 30000000){
                $message .= 'Файл слишком велик ';
            }
            if($file['type'] != 'image/png' && $file['type'] != 'image/jpeg' && $file['type'] != 'image/gif'){
                $message .= 'Формат файла не соответствует ';
            }
            if($file['error'] !=0){
                $message .= 'Ошибка сервера ';
            }
            if($message === ''){
                $this->mArticles->setAlts($file['tmp_name'], $_POST['alt'], $file['name'], $_POST['image_show']);
                new M_SimpleImage($file['tmp_name'], $path . 'carousel/' . $file['name']);
                new M_SimpleImage($file['tmp_name'], $path . 'full/' . $file['name'], false);
                $message = 'Файл успешно загружен ';
            }
            
            
        }
        else{
            $message = $this->renameImg($_POST['old_name'], $_POST['name']);
        }
        return $message;
    }

    private function renameImg($oldName, $newName){
            $path = "images/";
            $message = '';
        if(strlen($oldName) > 1 && strlen($newName) > 1 && file_exists($path . 'full/' . $oldName)){
            $newName = $this->prepareName($oldName, $newName);
            if($newName != $oldName){
                rename($path . 'full/' . $oldName, $path . 'full/' . $newName);
                rename($path . 'carousel/' . $oldName, $path . 'carousel/' . $newName);
                $this->mArticles->renameArticleImg($newName, $oldName);
                $this->mArticles->setAlts($oldName, $_POST['alt'], $newName, $_POST['image_show']);
                $message = 'Файл успешно переименован';
            }
            else{
                $this->mArticles->setAlts($oldName, $_POST['alt'], $newName, $_POST['image_show']);
                $message = 'Описание добавлено';
            }
        }
        else{
            $message = 'Ошибка сервера';
            
        }
        return $message;
    }
    private function deleteImg($name){
        $path = "images/";
        
        if(file_exists($path . 'full/' . $name) && file_exists($path . 'carousel/' . $name)) {
            unlink($path . 'full/' . $name);
            unlink($path . 'carousel/' . $name);
            $this->mArticles->delAlt($_POST['old_name']);
            
            return 'Файл успешно удален';
        }
        else{
            return 'Ошибка при попытке удаления';
        }
    }
    private function translite($str){

    $convertor = array('а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'g', 'з'=>'z',
        'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p', 'р'=>'r',
        'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e', 'А'=>'A', 'Б'=>'B', 'В'=>'V',
        'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'G', 'З'=>'Z', 'И'=>'I', 'Й'=>'Y', 'К'=>'K', 'Л'=>'L',
        'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R', 'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F',
        'Ы'=>'I', 'Э'=>'E', 'ё'=>'yo', 'х'=>'h', 'ц'=>'ts', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shch',
        'ъ'=>'', 'ь'=>'', 'ю'=>'yu', 'я'=>'ya', 'Ё'=>'YO', 'Х'=>'H', 'Ц'=>'TS', 'Ч'=>'CH', 'Ш'=>'SH',
        'Щ'=>'SHCH', 'Ъ'=>'', 'Ь'=>'', 'Ю'=>'YU', 'Я'=>'YA', 'Ї'=>'YI', 'ї'=>'yi', 'І'=>'I', 'і'=>'i'  );


    $tmp = str_replace(' ', "_", $str);
    $tmp = str_replace('.', "_", $tmp);
    $tmp = str_replace('/:|;', "", $tmp);
    $result = strtr($tmp, $convertor);
    return $result;

    }
    private function prepareName($oldName, $newName){
        $exp = explode('.', $oldName);
        $exp = $exp[count($exp) - 1];
        $result = $this->translite($newName) . '.' . $exp;
        return $result;
    }

        //
    // Виртуальный генератор HTML.
    //
    public function OnOutput() {   	

        //Генерация вложенных шаблонов
        
        $this->content['articles']['images'] =  $this->galery;

        $this->content['container_main'] = $this->View('V/view_edit.php', $this->content['articles']);
        parent::OnOutput();
             
    }          
          
 }
