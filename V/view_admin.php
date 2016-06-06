        <form action="" > 
            <button>Вернуться на главную</button>
        </form>
<div class="field" style="width:370px;">
    <div class="shap">
        <span class="fieldHead">Пользователь</span>
        <br>
        <span style="color: red"> <?=$message_us;?></span>
    </div>
    <form action="" method="post">
        <b>Пользователь:</b> 
        <select name="id_user">
            <?php foreach($names as $id_user=>$user_name):?>
                <option value='<?=$id_user;?>'><?=$user_name;?></option>
            <?php endforeach;?>
        </select>
        <br/>

        
        

            <b>Установить/Изменить login</b>

        <input name="login" size="10" type="text"/>
           
     
        <br/>
        <b>Установить/Изменить роль</b> 
        <select name="id_role">
            <option selected="selected" value="0">Не менять</option>
            <?php foreach($roles as $id_role=>$role_name):?>
                <option value='<?=$id_role;?>'><?=$role_name;?></option>
            <?php endforeach;?>
        </select>
        <br/>
       
        <b>Установить/Изменить группу</b>
        <select name="id_group" size="1">
            <option selected="selected" value="0">Не менять</option>
            <?php foreach ($groups as $id_group=>$group_name):?>
                <option value='<?=$id_group;?>'><?=$group_name;?></option>
            <?php endforeach;?>
        </select>
        <br/>

        <input name="update_user" value="Применить" type="submit">
        <br/>
         <input name="delete_user" value="Удалить" type="submit">
    </form>
</div>
       
     
<div class="field" style="width:370px;">
    <div class="shap">
        <span class="fieldHead">Группы</span>
        <br>
        <span style="color: red"> <?=$message_gr;?></span>
    </div>
    <form action="" method="post">

        <select name="id_group" size="1">
            <?php foreach ($groups as $id_group=>$group_name):?>
                <option value='<?=$id_group;?>'><?=$group_name;?></option>
            <?php endforeach;?>
        </select>
        <input name="delete_group" value="Удалить" type="submit">
        <br/>
        <input name="group_name" size="15" type="text"/>
        <input name="update_group" value="Переименовать" type="submit">
    </form>
</div>
<div class="field" style="width:370px;">
    <div class="shap">
        <span class="fieldHead">Контрагенты</span>
        <br>
        <span style="color: red"> <?=$message_co;?></span>
    </div>
    <form action="" method="post">

        <select name="id_contr" size="1">
            <?php foreach($contractors as $contr_id=>$contr_name):?>
                <option value='<?=$contr_id;?>'><?=$contr_name;?></option>
            <?php endforeach;?>
        </select>
        <input name="delete_contr" value="Удалить" type="submit">
        <br/>
        <input name="contr_name" size="15" type="text"/>
        <input name="update_contr" value="Переименовать" type="submit">
    </form>
</div>
<div class="field" style="width:370px;">
    <div class="shap">
        <span class="fieldHead">Операции</span>
        <br>
        <span style="color: red"> <?=$message_op;?></span>
    </div>
    <form action="" method="post">

        <select name="id_operation" size="1">
            <?php foreach($opers as $id_oper=>$oper_name):?>
                <option value='<?=$id_oper;?>'><?=$oper_name;?></option>
            <?php endforeach;?>
        </select>
        <input name="delete_oper" value="Удалить" type="submit">
        <br/>
        <input name="oper_name" size="15" type="text"/>
        <input name="update_oper" value="Переименовать" type="submit">
    </form>
</div>

<div class="field" style="width:370px;">
    <div class="shap">
        <span class="fieldHead">Автокоррекция</span>
        <br>
        <span style="color: red"> <?=$message_ac;?></span>
    </div>
    <form action="" method="post">
        <b>Установить значение </b>
        <input name="auto_cor_num" size="5" value="<?=$autoCor['value']?>" type="text"> % 
        <br/>
        <label>
        <b>Установить автокоррекцию включенной по умолчанию (работает только с зарплатой) </b>
        <input name="auto_cor_checkbox" type="checkbox" <?=$autoCor['checked']?>/>
        </label>
        <br/>
        <input name="auto_cor" value="Применить" type="submit">
    </form>
</div>