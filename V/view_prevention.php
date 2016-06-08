<?php
?>
<div class="container">
    <div class="row">
        <div class="col-sm-8">
            <?php if($articles):?> 
            <?php foreach ($articles as $article):?>
                <div class="article article-<?=$article['article_img_place']?> clearfix">
                    <h3><?=$article['article_title']?></h3>
                    <img src="<?='http://' . $_SERVER['SERVER_NAME'] . '/images/carousel/' . $article['article_img_name']?>" alt="<?=$article['image_alt']?>">
                        <?php if($isAdmin):?>
                            <a href="/edit/<?=$article['id_article']?>" class=" edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                        <?php endif;?>

                <?=$article['article_text']?>
                </div>
            <?php endforeach;?>
            <?php endif;?>  
           
            <?php if($pageNav):?> 

            <div class="pages">
                <div <?=$pageNav['home']['disabled'];?>>
                    <a href="<?=$pageNav['home']['path'];?>">
                        <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
                    </a>
                </div>
                <div <?=$pageNav['back']['disabled'];?>>
                    <a href="<?=$pageNav['back']['path'];?>">
                        <span class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
                    </a>
                </div>
                <span>Страница номер <?=$pageNav['page_num'];?></span>
                <div <?=$pageNav['next']['disabled'];?>>
                    <a href="<?=$pageNav['next']['path'];?>">
                        <span class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
                    </a>
                </div>
                <div <?=$pageNav['end']['disabled'];?>>
                    <a href="<?=$pageNav['end']['path'];?>">
                        <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
                    </a>
                </div>
            </div>
            <?php endif;?>       
            
            
        </div>

<?=$stocks;?>
    </div>                  
</div>