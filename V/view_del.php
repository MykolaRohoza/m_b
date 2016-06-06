<?php
?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <?php if($users):?>
            <?php foreach ($users as $user):?>   
 
            <h4>
                <span class="message"><?=$message?></span>
            </h4>
            <h4>
                <span><?=$user['user_name']?></span>
                <span> </span>
                <span><?=$user['user_second_name']?></span>
                <span> - </span>
                <span><?=$user['description']?></span>
            </h4>
            <h4>
                <span>Вы действительно хотите удалить этого пользователя?</span> 
            </h4>
            <form method="post">
                <input type="hidden" name="id_user" value="<?=$user['id_user'];?>">
                <input type="submit" class="btn btn-primary btn-block save" name="del_user" value="Удалить">
                <a href="/users"><input type="button" class="btn btn-primary btn-block save" name="cansel"  value="Отменить"></a>
            </form>
            <?php endforeach;?>
            <?php endif;?>
        </div>

    </div>
</div>

