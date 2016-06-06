<?php


//
// Менеджер пользователей
//
class M_Images
{	
    private static $instance;	// экземпляр класса
    private $msql;				// драйвер БД


    //
    // Получение экземпляра класса
    //
    public static function Instance()
    {
        if (self::$instance == null) {
            self::$instance = new M_Images();
        }

        return self::$instance;
    }

    //
    // Конструктор
    //
    private function __construct()
    {
            $this->msql = M_MSQL::Instance();


    }

    
    public function imgOper($file, $request, $query){
        $message = '';

    
        if($query['file_name']){
            //загрузка с возожным переименованием и регистрацией в альте
            $message = $this->imgUpload($file, $request);
        }
        else{
            if($query['new_name']){
                //переименование со сменой альта
                $message = $this->renameImg($request);
            }
            else{
                //смена Альта  
                if($request['old_name']){
                    $message = $this->setAlts($request['old_name'], $request['name'],
                        $request['alt'], $request['image_show']);
                }
                else {
                    $message = 'Файл не выбран';
                }
            }

        }

        return $message;
    }
    
    
    private function imgUpload($file, $request){
        $new_name = ($request['name'])?$this->prepareName($file['tmp_name'], $new_name):$this->prepareName($file['tmp_name'], $file['name']);
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
            new M_SimpleImage($file['tmp_name'], 'images/carousel/' . $new_name);
            new M_SimpleImage($file['tmp_name'], 'images/full/' . $new_name, false);
            
            $this->setAlts($new_name,  $new_name, $request['alt'], $request['image_show']);
            
            $message = 'Файл успешно загружен ';
        }  
        return $message;
    }

    public function getAlts($isAdmin = false){
        $query = "SELECT image_name, image_alt, images_last_update FROM images";
        if(!$isAdmin) {
            $query .= " WHERE image_show='1'";
        }
        $res = $this->msql->Select($query);
        $result = array();
        if(count($res[0]) > 0){
            foreach ($res as $value){ 
                $result[$value['image_name']] = $value['image_alt']
                        /* . date('d.m.Y - [H:m:s]', strtotime($value['images_last_update']))*/;
            }
        }
        return $result;
    }

    public function setAlts($image_name,  $image_new_name, $image_alt, $image_show){  
        $img_show = ($image_show)?1:0;
        $object = array('image_alt' => $image_alt, 'image_name' => $image_new_name, 'image_show' => $img_show,
            'images_last_update' => date('Y-m-d H:i:s'));
        
        $table = 'images';
        $result = ($this->msql->Update($table, $object, "image_name='$image_name'", true, true) > 0);
        if(!$result){
            $result = $this->msql->Insert($table, $object);
        }
        if($result){
            $result = 'состояние фото успешно обновлено';
        }
        else{
            $result = 'серверная ошибка';
        }
 

        return $result;
    }
    
    
    
    public function renameArticleImg($newName, $oldName){
        return $this->msql->Update('articles', array('article_img_name' => $newName), 
                "article_img_name='$oldName'");

    }
    private function renameImg($request){
            $path = "images/";
            $message = '';
            $oldName = $request['old_name'];
            $newName = $request['name']; 

        if(strlen($oldName) > 1 && strlen($newName) > 1 && file_exists($path . 'full/' . $oldName)){
            $newName = $this->prepareName($oldName, $newName);
            if($newName != $oldName){
                rename($path . 'full/' . $oldName, $path . 'full/' . $newName);
                rename($path . 'carousel/' . $oldName, $path . 'carousel/' . $newName);
                $this->renameArticleImg($newName, $oldName);
                $message = 'Файл успешно переименован';
            }
            $atlt_mes = $this->setAlts($oldName, $newName, $request['alt'], $request['image_show']);
            
            $message = ($message)?$message:$atlt_mes;
            $message = ($message)?$message:'Изменения отсутствуют';
 
        }
        else{
            $message = 'Ошибка сервера';
        }
        return $message;
    }
    
    public function deleteImg($name){
        if(!$name) {
            return 'Выберите файл перед удалением';
        }
        $path = "images/";

        if(file_exists($path . 'full/' . $name) && file_exists($path . 'carousel/' . $name)) {
            unlink($path . 'full/' . $name);
            unlink($path . 'carousel/' . $name);
            $this->delAlt($name);
            
            return 'Файл успешно удален';
        }
        else{
            return 'Ошибка при попытке удаления';
        }
    } 
    private function delAlt($name){

        $tmp = "image_name='%s'";
        $where = sprintf($tmp, $name);
        $result = $this->msql->Del('images', $where);
        $tmp = "article_img_name='%s'";
        $where = sprintf($tmp, $name);
        $this->msql->Update('articles', array('article_img_name' => '', 'article_img_place' => 'none'),
                $where, true, true);

        return $result;
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
        if(strpos($newName, $exp)){
            $result = $newName;
        }
        else {
            $result = $this->translite($newName) . '.' . $exp;
        }
        return $result;
    }
}