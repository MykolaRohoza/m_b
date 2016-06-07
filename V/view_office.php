<?php
?>
<div class="container">

<!--    <div class="row">
        <div class="col-sm-1"></div>
    <div class="col-sm-7">
        <?php if(count($images) > 0):?>
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="false">
            <div class="carousel-inner" role="listbox">
                <?php for($i = 0; $i < count($images); $i++): ?>
                    <div class="item <?php if($i==0) {echo 'active';} ?>">
                        <img src="<?=$images[$i]['path'];?>" alt="<?=$images[$i]['alt'];?>" onclick="put(this)">
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
    </div>-->
    <div class="row">
        <div class="col-sm-9" id = "contacts_container">
        <?php if($users):?> 
        <ul class="contacts">
        <?php foreach ($users as $id_user => $user):?>                   
            <li class="clearfix">
                <div class="user_card">
                    <?php if($user['article_img_name']):?>
                    <img class="photo" src="<?='http://' . $_SERVER['SERVER_NAME'] . '/images/carousel/' . $user['article_img_name']?>" alt="<?=$user['image_alt']?>">
                    <?php endif;?>
                    <h4>
                        <span><?=$user['user_name']?></span>
                        <span> </span>
                        <span><?=$user['user_second_name']?></span>
                        <span> - </span>
                        <span><?=$user['description']?></span>
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
                        <span  ondblclick="new_diagnosis(this)">Диагноз: </span>
                        <?php if($user['diagnosis']):?>
                        <span  ondblclick="span2changeble(this)"><?=$user['diagnosis']?></span><span>.</span>
                        <?php endif;?>
                    </h4>

                    <div class="full_container"> <h4>Упражнения:</h4>
                        <div class="exercises_container">
                        <?php foreach ($user['exercises'] as $exercise):?>    
                            <?php
                                $modCount = $exercise['count']%10;
                                $modRepeat = $exercise['repeat']%10;
                                $countEnd = ($modCount > 4 || $modCount == 0)?'ов':(($modCount == 1)?'':'а'); 
                                $repeatEnd = ($modRepeat > 4 || $modRepeat == 0)?'ов':(($modRepeat == 1)?'':'а'); 

                            ?>
                            
                            <div class="exercise" style="display: inline-block;">
                                <input type="hidden" value="<?=$exercise['id_exercise'];?>" name="id_exercise">
                                <span class="ex"><?=$exercise['ex'];?></span>
                                <span class="counts"><?=$exercise['count'];?> счет<?=$countEnd?>,</span>
                                <span class="repeat"><?=$exercise['repeat'];?> подход<?=$repeatEnd?></span>
                            </div>
                        <?php endforeach;?>
                        </div>
                            
                        <form class="exercises">
                            <input type="hidden" value="<?=$id_user?>" name="id_user">
                        </form>
                    </div>
                </div> 

            </li>
            
        <?php endforeach;?>
            </ul>
        <?php endif;?>                     
        </div>                
    </div>
</div>


