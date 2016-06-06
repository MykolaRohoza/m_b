<div class="col-sm-4">
    <?php foreach($stocks as $stock): ?>
            <div class="advertise">
            <?php if($isAdmin):?>
                <a href="/edit/<?=$stock['id_article'];?>" class=" edit edit_action"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
            <?php endif;?>
                <h3><?=$stock['article_title'];?></h3>
                <p> <?=$stock['article_text'];?></p>
                <hr/>
            </div>    
    <?php endforeach;?>
</div>


