<?php


//
// Менеджер пользователей
//
class M_Exercises
{	
    private static $instance;	// экземпляр класса
    private $msql;				// драйвер БД


    //
    // Получение экземпляра класса
    //
    public static function Instance()
    {
        if (self::$instance == null) {
            self::$instance = new M_Exercises();
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
    /**
     * 
     * @param type $group 
     * 1- для главной
     * 2- для профилактора
     * 3 - акции 
     * 4 - статьи
     * @param type $page - номер страницы
     * @param type $onPage - номер страницы
     */
    public function getArticlesFull($article_func, $id_article, $article_dest, $page, $onPage, $lang){
        $query  = "SELECT id_article, article_order, article_func, article_dest, "
                . "article_title_$lang, article_text_$lang, article_img_name, article_img_place,"
                . " article_stock, article_secondary, article_secondary_to, images.image_alt FROM articles LEFT JOIN images "
                . "ON articles.article_img_name = images.image_name  WHERE 1=1 ";
        if($id_article != 0){
            $query  .= "AND id_article=$id_article";
        }
        else{
            if($article_func != 0){
                $query  .= "AND article_func=$article_func ";
            }
            if($article_dest != 0){
                $query  .= "AND article_dest=$article_dest ";
            }
            if ($page <= 1){
                $start = 0;
            }
            else{
                $start = ($page - 1)*$onPage;
            }
            $query  .= "ORDER BY id_article DESC LIMIT $start, $onPage";

        }

        $result = $this->msql->Select($query);
        $articles = $this->validateArtCont($result, $lang);

        return $articles;
       
    }
    
    /**
     * 
     * @param int $article_func 1 - статья, 2 - акция
     * @param int $id_article
     * @param int $article_dest 1 - главная, 2 - профилактор, 3 - статьи 
     * @param int $page
     * @param int $onPage
     * @param string $lang
     * @return mixed array 
     */
    
    public function getArticles($article_func = 0, $id_article = 0, $article_dest = 0, $page = 1, $onPage = 5, $lang = 'ru'){
        return $this->getArticlesFull($article_func, $id_article, $article_dest, $page, $onPage, $lang);
    }
    public function getArticlesNames($lang='ru'){
        $query  = "SELECT id_article, article_title_$lang FROM articles ORDER BY id_article DESC";
        $result = $this->msql->Select($query);
        $articles = $this->validateArtCont($result, $lang);
                //M_Lib::addLog($result);
        return $articles;
    }
    public function getAlts(){
        $query = "SELECT image_name, image_alt FROM images WHERE image_show='1'";
        $res = $this->msql->Select($query);
        $result = array();
        if(count($res[0]) > 0){
            foreach ($res as $value){ 
                 
                $result[$value['image_name']] = $value['image_alt'];
            }
        }
        return $result;
    }
    public function setAlts($image_name, $image_alt, $image_new_name, $image_show){  
        $img_show = ($image_show)?1:0;
        $object = array('image_alt' => $image_alt, 'image_name' => $image_new_name, 'image_show' => $img_show);
        
        $table = 'images';
        if(!($this->msql->Update($table, $object, "image_name='$image_name'", true, true) > 0)){
            $this->msql->Insert($table, $object);
        }
        
    }
    
    
    
    public function saveExercises($id_ex, $new_ex){
        $object = array('exercise' => $new_ex);
        $table = 'exercises';
        if ($id_ex != 0){
        $object['id_exercise'] = $id_ex;
            $message = $this->msql->Update($table, $object, $where, true, true);
        }
        else{
            $message = $this->msql->Insert($table, $object) ;
            if(is_numeric($message)){
                $message =  'Упражнение успешно добавлено';
                
            }
            else{
                
                $message =  'Упражнение не было добавлено';
            }
        }
        return message;
    }
    public function getExercises(){
        $query  = "SELECT id_exercise, exercise FROM exercises ORDER BY exercise";
        $result = $this->msql->Select($query);
        $exercises = array();
        foreach ($result as $value) {    
            $exercises[$value['id_exercise']] = $value['exercise'];
        }
        return $exercises;
    }
    public function deleteExercises($id_ex){
        $tmp = "id_exercise='%d'";
        $where = sprintf($tmp, $id_ex);
        return ($this->msql->Del('exercises', $where) > 0);
    }


    public function delete($table, $where){    
        return ($this->msql->Del($table, $where) > 0);
    }


    
    private function validateArtCont($assocRes, $lang) {
        $articles = array();      
        if(count($assocRes) > 0){
            foreach ($assocRes as $article){
                $tmp = array();  
                foreach ($article as $key => $value) {
                    if(preg_match('~title~', $key) || preg_match('~text~', $key) ){
                        $fineKey = str_replace('_' . $lang, '', $key);

                    }
                    else{
                        $fineKey = $key;
                    }

                    $tmp[$fineKey] = $value;
                }
                $articles[] = $tmp;
            }
        }
        return $articles;
    }
    
    
}