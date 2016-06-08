<?php
   
?>
<div class="container">

    <div class="row">
        <div class="col-sm-1"></div>
    <div class="col-sm-7">
        <?php if(count($images) > 0):?>
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="false">
            <div class="carousel-inner" role="listbox">
                <?php for($i = 0; $i < count($images); $i++): ?>
                    <div class="item <?php if($i==0) {echo 'active';} ?>">
                        <img src="<?=$images[$i]['path'];?>" alt="<?=$images[$i]['alt'];?>">
                    </div>
                <?php endfor; ?>

            </div>
            <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-menu-left glyphicon1" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                <span class="glyphicon glyphicon-menu-right glyphicon1" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
    </div>
    <div class="row">
        <div class="col-sm-9" id = "contacts_container">
            <div class="user_roles">
                <a href="/users/admins">
                    Администраторы
                </a>
                <a href="/users/couchers">
                    Тренера
                </a>
                <a href="/users/visitors">
                    Посетители
                </a>
                <a href="/users/all">
                    Все
                </a>
                <a href="/users/new">
                   <b>+</b> Новый пользователь
                </a>
                <a href="/users/code">
                    Код
                </a>
            </div>
        <?php if(!$users):?>
            <form class="new_user" method="post">
                <div class="form-group">
                    <label for="input-name">Имя</label>
                    <input id="input-name" required="" type="text" class="form-control" placeholder="Name" name='user_name'>
                </div>
                <div class="form-group">
                    <label for="input-sname">Фамилия</label>
                    <input id="input-sname" required="" type="text" class="form-control" placeholder="Second name" name='user_second_name'>
                </div>
                <input type="submit" class="btn btn-primary btn-block save" name="create_new_user" value="Сохранить">
                <a href="/users"><input type="button" class="btn btn-primary btn-block save" value="Отменить"></a>
            </form>
        <?php endif;?>
        <?php if($users):?> 

        <ul class="contacts">
        <?php foreach ($users as $id_user => $user):?>                   
            <li class="clearfix">
                <div class="user_card">
                    <div class="del_user">
                        <a href="../del/<?=$id_user?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                    </div>

                    <div class="more">
                        <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                    </div>
                    <?php if($user['user_image']):?>
                    <div class="photo">
                        <img class="photo" src="<?=$user['user_image']?>" alt="<?=$user['image_alt']?>">
<!--                        'http://' . $_SERVER['SERVER_NAME'] . '/images/carousel/' . -->
                        <div class="img_del">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                        </div>
                    </div>
                    <?php endif;?>
                    <?php if(!$user['user_image']):?>
                    <div class="insert_photo">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    </div>
                    <?php endif;?>
                    <h4>
                        <span><?=$user['user_name']?></span>
                        <span> </span>
                        <span><?=$user['user_second_name']?></span>
                        <span> - </span>
                        <span ondblclick="span2changeble(this)"><?=$user['description']?></span>
                    </h4>

                    <h4>
                        <span ondblclick="new_contact(this)">Контакты: </span>
                        <?php $i = 0; foreach($user['contacts'] as $contact){
                        if($contact){
                            echo '<span id="' . $contact['id_info'] . '_' . $contact['contact'] . '" ondblclick="span2changeble(this)">' . $contact['contact'];
                            echo '</span>';
                            if($i < (count($user['contacts']) - 1)){
                                echo '<span>, </span>';
                            }
                            else {
                                echo '<span>.</span>';
                            }
                            $i++;
                        }

                        }?>
                    </h4>
                    <h4>
                        <span ondblclick="new_diagnosis(this)">Диагноз: </span>
                        <?php if($user['diagnosis']):?>
                        <span ondblclick="span2changeble(this)"><?=$user['diagnosis']?></span><span>.</span>
                        <?php endif;?>
                    </h4>

                    <div class="full_container"> <h4>Упражнения:</h4>
                        <div class="exercises_container">
                        <?php foreach ($user['exercises'] as $exercise):?>    
                                <div class="exercise" style="display: inline-block;">
                                    <input type="hidden" value="<?=$exercise['id_exercise'];?>" name="id_exercise">
                                    <span class="ex"><?=$exercise['ex'];?></span>
                                    <span class="counts"><?=$exercise['count'];?></span>
                                    <div class="pd_btn plus" onclick="plus_counts(this)"></div>
                                    <div class="pd_btn deg" onclick="deg_counts(this)"></div>
                                    <span> X </span>
                                    <span class="repeat"><?=$exercise['repeat'];?></span>
                                    <div class="pd_btn plus" onclick="plus_rep(this)"></div>
                                    <div class="pd_btn deg" onclick="deg_rep(this)"></div>
                                </div>

                        <?php endforeach;?>
                        </div>

                        <form class="exercises">
                            <input type="hidden" value="<?=$id_user?>" name="id_user">
                            <textarea style="display:none" name="exercises"></textarea>
                            <input type="button" class="btn btn-primary btn-block save" name="exercise" value="Сохранить">
<!--                            <input type="button" class="btn btn-primary btn-block save" name="cansel" value="Отменить">-->
                        </form>
                    </div>
                    <h4>
                        <span>Отображение в контактах : </span> 
                        <span  ondblclick="span2changeble(this, true)"><?=$user['display_description']?></span><span>.</span>
                    </h4>
                    <?php if($user['user_code']):?>
                    <h4>
                        <span>Ссылка для активации: </span> 
                        <span>http://<?=$_SERVER['SERVER_NAME'];?>/activate/<?=$user['user_code']?></span>
                    </h4>
                    <?php endif;?>
                </div> 


            </li>
        <?php endforeach;?>
            </ul>
        <?php endif;?>                     
                    </div>


        <div class="col-sm-3" style="padding:0px">
            <div id="pusher"></div>
            <div id="exercise_bank">        
                <h4>Упражнения: </h4>
                <input type="text" name="new_ex" onblur="" class="form-control">
                <input type="button" value="добавить" class="btn btn-primary btn-block"  name="add_ex">
                <h5>Список упражнений:</h4>
                <div class="container_add_ex">  
                <?php foreach ($exercises as $id_exercise => $exercise):?>    
                    <div class="exercise">
                        <input type="hidden" name="id_exercise"  value="<?=$id_exercise?>">
                        <span class="ex"><?=$exercise?></span>
                        <div class="pd_btn deg" onclick="deg_ex(this)"></div>
                    </div> 
                <?php endforeach;?>
                </div>  
            </div>
        </div>                  
    </div>
</div>