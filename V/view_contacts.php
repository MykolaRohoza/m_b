<?php 
?>
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <?php if($users):?> 

                <ul class="contacts">
                <?php foreach ($users as $user):?> 
                    
                    
                    
                    <li class="clearfix">
                        <?php if($user['user_image']):?>
                        <img class="photo" src="<?=$user['user_image'];?>" alt="<?=$user['image_alt'];?>">
                        <?php endif;?>
                        <div>
                        <h4>
                            <span><?=$user['user_name']?></span>
                            <span> </span>
                            <span><?=$user['user_second_name']?></span>
                            <span> - </span>
                            <span><?=$user['description']?></span>
                        </h4>
                            <?php if(!$user['contacts']):?>
                            <ul>
                            <?php foreach($user['contacts'] as $contact):?>
                                <li><?=$contact['contact'];?></li>
                            <?php endforeach;?>
                            </ul>
                            <?php endif;?>
                        </div>
                    </li>
                    <?php endforeach;?>
                </ul>
                <?php endif;?>    
                <div class="google-map">
                    <p>Адрес центра: Харьков, ул. Пушкинская,5 (во дворе, вход через арку)
тел. 096-83-66-709, 050-64-85-055, 093-640-57-70.</p>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1282.5042813798184!2d36.23363552148457!3d49.99244891912077!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4127a0f03a78b1cd%3A0xdb276d52be119c52!2z0LLRg9C7LiDQn9GD0YjQutGW0L3RgdGM0LrQsCwgNSwg0KXQsNGA0LrRltCyLCDQpdCw0YDQutGW0LLRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjA!5e0!3m2!1sru!2sua!4v1460638254973" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
            </div>

        </div>                  
    </div>

