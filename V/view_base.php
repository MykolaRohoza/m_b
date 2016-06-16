
<!DOCTYPE html>
<html>
    <head>   
        
<!--        <meta name='yandex-verification' content='7709ab52f2a322a3' />
        <meta name="google-site-verification" content="aLQdRQkJv8Apvf-sa62cAaniTfDdZF5aonlYiBNkHlY" />-->
        <meta charset="utf-8"> 
        <meta content="<?=$metaTags['keywords'];?>" name="keywords">
        <meta content="<?=$metaTags['description'];?>" name='description'>

        
        <meta content="<?=$metaTags['og:url'];?>" property="og:url">
        <meta content="<?=$metaTags['og:title'];?>" property="og:title">       
        <meta content="<?=$metaTags['og:type'];?>" property="og:type">
        <meta content="<?=$metaTags['og:description'];?>" property="og:description" >
  
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script src="/js/jquery-1.10.2.min.js"></script>
        <script src="/js/jquery_ui-1.10.3.min.js"></script>
 
        
        <script src="/js/hammer.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/jquery.fancybox.pack.js"></script>
        <script src="/js/main.js"></script>
        <script src="/js/registration.js"></script>

        <?php if($require['users']):?>
        <script src="/js/users.js"></script>
        <?php endif; ?>
        <?php if($require['edit']):?>
        <script src="/js/holders.js"></script>
        <script src="/js/edit.js"></script>
        <?php endif; ?>
        <link href="/css/bootstrap.css" rel="stylesheet">
        <link href="/css/jquery.fancybox.css" rel="stylesheet" type="text/css" media="screen">
        
        <link type="text/css" rel="stylesheet" href="/css/style.css"/>
        <link type="image/x-icon" rel="shortcut icon" href="/images/favicon.ico">

        <title><?=$title;?></title>
    </head>
    <body>
        
        <button type="button" class="navbar-toggle button-nav" data-toggle="collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="nav-holder">
            <nav class="navbar">
                <div class="navbar-header">

                </div>
                <div class="navbar-collapse">
                    <ul class="nav">
                        <li><a href="/" <?=$nav['main'];?> >Главная</a></li>
                        <li><a href="/prevention" <?=$nav['prevention'];?> >Профилактор</a></li> 
                        <li><a href="/articles" <?=$nav['articles'];?> >Статьи</a></li>
<!--                        <li><a href="/">Расписание</a></li>-->
                        <li><a href="/contacts" <?=$nav['contacts'];?> >Контакты</a></li>
                        <?php if($isAdmin):?>
                        <li><a href="/users" <?=$nav['users'];?> >Пользователи</a></li>
                        <li><a href="/edit" <?=$nav['edit'];?> >Редактор</a></li>
                        <?php endif;?> 
                    </ul>
                </div>
            </nav>
        </div>
        <?php if($needLoginForm || $needCarosel):?>
        <header>
            <div class="container">

                
                <div class="row">
                    <div class="col-sm-7">
                        <?php if($needCarosel && count($images) >0):?>
                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                            <!-- Indicators
                            <ol class="carousel-indicators">
                                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="3"></li>
                                <li data-target="#carousel-example-generic" data-slide-to="4"></li>
                            </ol> -->
                            <!-- Wrapper for slides -->
                            <div class="carousel-inner" role="listbox">
                                <?php for($i = 0; $i < count($images); $i++): ?>
                                    <div class="item <?php if($i==0) {echo 'active';} ?> thumb">
                                        <a rel="img_group" class="fancyimage" data-fancybox-group="group" href="<?=$images[$i]['full_path'];?>"> 
                                            <img class="img-responsive" src="<?=$images[$i]['path'];?>" alt="<?=$images[$i]['alt'];?>">
                                        </a>
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
                        <strong class="logo"><a href="#">MB</a></strong>
                 <?php endif; ?>

                    </div>
                     <?php if($needLoginForm):?>
                    <div class="col-sm-5">
                        <div class="logout <?=$user['logout_collapse']?>">
                            <span class="login-name"> <?=$user['user_second_name']?> <?=$user['user_name']?> </span>
                            <a href="/office">Личный кабинет</a>
                            <form method="post"><input type="submit" class="btn btn-primary btn-block" name="logout" value='Выйти'></form>
                        </div>
                        <form method="post" class="login-form <?=$user['login_collapse']?>">
                            <div class="form-group ">
                                <input type="text" class="form-control" placeholder="Login" name="login">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Password" name="password">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary btn-block" name='login_btn' value="Войти">
                            </div>
                            <button type="button" class="btn btn-sucsess" data-toggle="modal" data-target="#modal-1"> Регистрация</button>
                        </form>
                    </div>
                     <?php endif; ?>
                </div>
            </div>
        </header>
         <?php endif; ?>
        <main>
         <a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button"  data-toggle="tooltip" data-placement="left">
             <span class="glyphicon glyphicon-chevron-up">     
             </span>
         </a>   
         <?=$container_main;?>

        </main>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-sm-4 col-xs-6">
                        <strong class="logo logo-footer"><a href="#">Logo</a></strong>
                    </div>
                    <div class="col-sm-8 col-xs-6">
                        <p>Адрес центра: Харьков, ул. Пушкинская,5 (во дворе, вход через арку)
тел. 096-83-66-709, 050-64-85-055</p>
                    </div>
                </div>
            </div>
        </footer>
        
        
        <div class="modal" id="modal-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content design">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal">
                            &times;
                        </button>
                        <h4 class="modal-title">Регистрация</h4>
                    </div>
                    <div class="modal-body">
                        <form method='post'>
                            <div class="form-group">
                                <label for="input-name">Имя</label>
                                <input id="input-name" required="" type="text" class="form-control" placeholder="Name" name='user_name'>
                            </div>
                            <div class="form-group">
                                <label for="input-sname">Фамилия</label>
                                <input id="input-sname" required="" type="text" class="form-control" placeholder="Second name" name='user_second_name'>
                            </div>
                            <div class="form-group">
                                <label class="input-email">E-mail (используется как login)</label>
                                <input id="input-email" required="" type="text" class="form-control" placeholder="E-mail" name='login'>
                            </div>
                            <div class="form-group">
                                <label for="input-tel">Телефон (пример: 380671234567)</label>
                                <input id="input-tel" required="" type="text" class="form-control" placeholder="Telephone number" name='telephone'>
                            </div>
                            <div class="form-group">
                                <label for="input-pas">Пароль</label>
                                <input id="input-pas" required="" type="password" class="form-control" placeholder="Password" name='password'>
                            </div>
                            <div class="form-group">
                                <label for="input-pas2">Повторите пароль</label>
                                <input id="input-pas2" required="" type="password" class="form-control" placeholder="Confirm password" name='confirm_password'>
                            </div>
                            <div class="form-group">
                                <input type="button" class="btn btn-primary btn-block" name="regestration" value='Зарегестрироваться'>
<!--                                <button type="submit" class="btn btn-primary btn-block" name="regestration">Submit</button>-->
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn button-danger" type="button"  data-dismiss="modal">Закрыть</button> 
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>



