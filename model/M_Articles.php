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
    public function getArticlesCount($article_func = 0, $id_article = 0, $article_dest = 0){
        $query  = "SELECT COUNT(id_article) AS count FROM articles WHERE 1=1 ";
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

        }

        $result = $this->msql->Select($query);
        

        return $result[0]['count'];
       
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
    public function getPageNavigation($page_num, $articlesCount, $path, $articlesNum){
        $disabled = 'style="background: #636b7b"';
        $page_num = ($page_num)?$page_num:1;
        $pageSum = (int)($articlesCount/$articlesNum);
        if( $articlesCount % $articlesNum != 0) {
            $pageSum++;
        }
        $result['page_num'] = $page_num;
        
        if($page_num > 1){
            $result['back']['path'] = $path . ($page_num - 1);
            $result['home']['path'] = $path . 1;
        }
        else{
            $result['back']['disabled'] =  $disabled;
            $result['home']['disabled'] =  $disabled;
        }
        if($page_num < $pageSum){
            $result['next']['path'] = $path . ($page_num + 1);
            $result['end']['path'] = $path . $pageSum;
        }
        else{
            $result['next']['disabled'] = $disabled;
            $result['end']['disabled'] = $disabled;
        }

        return $result;
    }
    
//    public function getPageNavigation($page_num, $articlesCount, $mod, $articlesNum = 5){
//        $page_num = ($page_num)?$page_num:1;
//        $pageNav = array();
//        if($articlesCount == 0){
//            $pageNav['pageNum'] = $page_num;
//            $pageNav['next']['disabled'] = "disabled='disabled' style='color:grey'";
//            $pageNav['end']['disabled'] = "disabled='disabled' style='color:grey'";
//            $pageNav['home']['disabled'] = "disabled='disabled' style='color:grey'";
//            $pageNav['back']['disabled'] = "disabled='disabled' style='color:grey'";
//            $pageNav['next']['pagesLeft'] = '';
//            $pageNav['end']['pagesLeft'] = '';
//            $pageNav['pageTotal'] = $pageNav['pageNum'];
//            return $pageNav;
//            
//        }
//        $pageNum = (int)($articlesCount/$articlesNum);
//        if( $articlesCount % $articlesNum != 0) {
//            $pageNum++;
//            $pagesLeft = $articlesCount % $articlesNum;
//        }
//        else {
//            $pagesLeft = $articlesNum;
//        }
//        $pageNav['pageTotal'] = $pageNum;
//        
//        switch ($mod){
//            case -2:
//                $page_num = 1;
//                break;
//            case -1:
//                --$page_num;  
//                break;
//            case 1:
//                ++$page_num;
//                break;
//            case 2:
//                $page_num = $pageNum;
//                break;
//        }
//        
//        
//        
//        
//        
//        $pageNav['pageNum'] = $page_num;
//        if($page_num <= 1) {
//            $pageNav['home']['disabled'] = "disabled='disabled' style='color:grey'";
//            $pageNav['back']['disabled'] = "disabled='disabled' style='color:grey'";
//        }
//        else{
//            $pageNav['back']['pagesLeft'] = $articlesNum;
//        }
//
//        if($page_num == ($pageNum - 1)){
//            $pageNav['next']['pagesLeft'] = $pagesLeft;
//        }
//        else{
//            $pageNav['next']['pagesLeft'] = $articlesNum;
//        }
//
//        $pageNav['end']['pagesLeft'] = $pagesLeft;
//
//        if($pageNum  == $page_num){
//            $pageNav['next']['disabled'] = "disabled='disabled' style='color:grey'";
//            $pageNav['end']['disabled'] = "disabled='disabled' style='color:grey'";
//            $pageNav['next']['pagesLeft'] = '';
//            $pageNav['end']['pagesLeft'] = '';
//        }
//
//
//        return $pageNav;
//
//    }
    
    
}