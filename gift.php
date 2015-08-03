<?php
header("Content-Type: text/html; charset=utf-8");
echo '<title>Управление гифт-кодами</title>';
$config = array(

'password' => 'default', // пароль от утилиты

'giftpath' => 'gifts.sqlite3' // путь к базе данных с гифтами

);

if(! file_exists($config['giftpath'])) die("Не найдена база данных SQLite3 с гифтами!");

if(! empty($_GET['passwd'])) {
    if($_GET['passwd'] === $config['password']) {
    echo '<h2>Управление гифтами</h2><hr>
<label>Навигация</label>
<form method="get">
<select name="p">
  <option value="">Главная</option>
  <option value="add">Добавить гифт</option>
  <option value="del">Удалить гифт</option>
</select>
<br><input type="submit" value="Перейти">
<input type="hidden" name="passwd" value="'.$_GET['passwd'].'">
</form><br><br>';
        $sqlite = new SQLite3($config['giftpath']);
        if(empty($_GET['search'])) $gifts = $sqlite->query("SELECT * FROM `gifts`"); else $gifts = $sqlite->query("SELECT * FROM `gifts` WHERE `gift` LIKE '%".$_GET['search']."%'");
        $all_gifts_count = $sqlite->query("SELECT COUNT(*) as count FROM `gifts`")->fetchArray();
        if(! empty($_GET['p']) && ! empty($_GET['g'])) die("Ошибка!");
        if(empty($_GET['p']) && empty($_GET['g'])) {
?>
<br><br>
<form method="get">
<input type="text" name="search" placeholder="Найти гифт"><br><br>

<input type="submit" value="Искать">
<input type="hidden" name="passwd" value="<? echo $_GET['passwd']; ?>">
</form>
<br>
<b>Все гифты</b><br>
<form method="post" action="gift.php?passwd=<? echo $_GET['passwd']; ?>">
<table border>
<tr><th>Гифт</th><th>Тип</th><th>Приз</th><th>Кол-во предметов</th></tr>
<?
            if($all_gifts_count['count']) {
                while($gift = $gifts->fetchArray()) {
                    if($gift['type'] == "item") $type = "Вещь"; elseif($gift['type'] == "group") $type = "Группа"; else $type = "Ошибка";
                    echo "<tr><td>".$gift['gift']."</td><td>$type</td><td>".$gift['give']."</td><td>".$gift['item_count']."</td></tr>";
                }
            }
            else echo 'Гифтов нет';
            echo '</table></form>';
        }
        elseif(isset($_GET['p']) && $_GET['p'] == "add" && empty($_GET['g'])) {
            if(isset($_POST['addgift'])) {
                $gift = $_POST['gift'];
                $type = $_POST['type'];
                $give = $_POST['give'];
                $item_count = $_POST['item_count'];
                
                $check = $sqlite->query("SELECT COUNT(*) as count FROM `gifts` WHERE `gift`='$gift'")->fetchArray();
                
                if(! $check['count']) {
                    if(empty($_POST['item_count'])) $item_count = 0;
                    $sqlite->exec("INSERT INTO `gifts` (`gift`,`type`,`give`,`item_count`) VALUES ('$gift','$type','$give','$item_count')");
                    echo "Гифт $gift добавлен!";
                }
                else echo "Гифт $gift уже существует!";
            }
?>
<h4>Добавить гифт</h4>
<form method="post">
<b>Гифт-код</b><br>
<input type="text" name="gift"><br><br>

<b>Тип</b><br>
<select name="type">
  <option value="item">Предмет</option>
  <option value="group">Группа</option>
</select><br><br>

<b>Что выдать</b><br>
<input type="text" name="give" placeholder="ID предмета или название группы"><br><br>

<b>Количество предметов (если это предмет)</b><br>
<input type="number" name="item_count"><br><br>

<input type="submit" name="addgift" value="Добавить">
</form>
<?
        }
        elseif(isset($_GET['p']) && $_GET['p'] == "del" && empty($_GET['g'])) {
            if(isset($_POST['remove'])) {
                $gift = $_POST['gift'];
                
                $check = $sqlite->query("SELECT COUNT(*) as count FROM `gifts` WHERE `gift`='$gift'")->fetchArray();
                
                if($check['count']) {
                    $sqlite->exec("DELETE FROM `gifts` WHERE `gift`='$gift'");
                    echo "Гифт $gift удалён!";
                }
                else echo "Гифт $gift не найден!";
            }
?>
<h4>Удалить гифт</h4>
<form method="post">
<b>Гифт-код</b><br>
<input type="text" name="gift"><br><br>

<input type="submit" name="remove" value="Удалить">
</form>
<?
        }
        else echo 'Страница не найдена!';
    }
    else echo 'Неверный пароль!<br><form method="get"><input type="submit" value="Попробовать снова?"><input type="hidden" name="passwd" value=""></form>';
}
else echo '<form method="get"><input type="password" name="passwd" placeholder="Пароль"><br><br><input type="submit" value="Вход"></form>';
