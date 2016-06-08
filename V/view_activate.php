<?php 

?>       
<div class="container">
    <div class="row">
        <div class="col-sm-12">

            <div class="article article-left clearfix">
               
                <?php if($isActive):?>
                    <p style="font-size:22px; color:green;"><?=$message?></p>
                        <a href="/">Перейти на главную страницу</a>
                <?php endif; ?>
                <?php if(!$isActive): ?>
                    <p>Код регистрации просрочен либо просто отсутствует</p>
                    <form method="post">
                        <label>Вставьте код регистрации <input type="text" size="30" name="code"></label>
                        <input type="submit" name="code_btn">
                    </form>
                <?php endif;?>
                <?php if($needLoginForm): ?>
                    <form method="post" class="form_activate">
                        <input type="hidden" name="code" value="<?=$code;?>">
                        <div class="form-group">
                            <label for="input-name">Логин</label>
                            <input id="input-name" required="" type="text" class="form-control"
                                   placeholder="Введите логин" name="login">
                        </div>
                        <div class="form-group">
                            <label for="input-sname">Пароль</label>
                            <input id="input-sname" required="" type="text" class="form-control" 
                                   placeholder="Введите  пароль" name="password">
                        </div>
                        <div class="form-group">
                            <label for="input-sname">Повторите пароль</label>
                            <input id="input-sname" required="" type="text"
                                   class="form-control" placeholder="Подтвердите пароль" name="confirm_password">
                        </div>
                        <input type="submit" class="btn btn-primary btn-block save" name="activate" value="Активировать">
                    </form>
                <?php endif;?>
            </div>
        </div>
    </div>                  
</div>