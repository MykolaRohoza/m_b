<?php


//
// Менеджер пользователей
//
class M_Articles
{	
    private static $instance;	// экземпляр класса
    private $msql;				// драйвер БД


    //
    // Получение экземпляра класса
    //
    public static function Instance()
    {
        if (self::$instance == null) {
            self::$instance = new M_Articles();
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
            $query  .= "ORDER BY article_pos LIMIT $start, $onPage";

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
        $query  = "SELECT id_article, article_title_$lang FROM articles ORDER BY article_pos";
        $result = $this->msql->Select($query);
        $articles = $this->validateArtCont($result, $lang);

        return $articles;
    }


    public function setArticlesPosition($articles_pos){
        $artPosArr = explode("||", $articles_pos);
        $table = 'articles';
        $sum  = 0;
        for($i = 0; $i < count($artPosArr); $i++) {
            if($artPosArr[$i]){
                $val = explode("#", $artPosArr[$i]);
                $sum +=  $this->msql->Update($table, array('article_pos'=>$val[0]), "id_article='{$val[1]}'");
            }
        }
        return ($sum > 0);
    }

    
    
    
    public function save($table, $object, $where){

        if((trim($where) === 'id_article=') || !($message = $this->msql->Update($table, $object, $where, true, true))){
            // добавляет новую статью возвращает ее номер и сообщение об успехе
            $message = $this->msql->Insert($table, $object) . '/статья успешно добавлена';
           
        }
        else{

            if(is_numeric($message)){
                $message =  ($message)?'статья успешно обновлена':'изменений в статье не найдено';
            }
            else {
                $message = 'ошибка при сохранения';
            }    
        }

        return $message;
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