<?php
	header('Content-Type: text/html;');
    error_reporting(E_ALL);
    mb_internal_encoding('utf-8');
    define('UPLOAD_DIR', DIRECTORY_SEPARATOR.'texts'.DIRECTORY_SEPARATOR);
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR); // создать папку если ее нет
    define('ROOT_DIR',dirname(__FILE__).UPLOAD_DIR);
?>
<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='utf-8'>
    <title>Загрузить файл</title>
    <link rel='stylesheet' href='style.css'>
</head>
<body>

<h1>Добавление статьи</h1>

<?php

        if(isset($_POST['upload_process'])){

            $allowed_filetypes = array('.txt'); // Допустимые форматы
            //$new_name = $_POST["userfilename"]; // Имя файла задается пользователем
            $filename = $_FILES['filename']['name']; // Исходное имя файла, такое, каким его видел пользователь, выбирая файл
            //$filename = $new_name.'.txt';

            $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);

            $errors = array();

            function error_style($string){ // Стиль для текста ошибки
                return '<div class="error">'.$string.'<br><br><a href="upload.php">Попробовать еще раз!</a></div>';
            }
            function success_style($string){ // Стиль для текста ошибки
                return '<div class="success">'.$string.'<br><br><a href="upload.php">Загрузить еще статью</a></div>';
            }

            if(!in_array($ext,$allowed_filetypes))
            die($errors[] = error_style('Данный формат не поддерживается.'));
            
            if(strlen($_FILES['filename']['tmp_name']) < 1)
            die($errors[] = error_style('Сперва укажите файл для загрузки.'));

            if(filesize($_FILES['filename']['tmp_name']) > 1024*1024*5)
            die($errors[] = error_style('Файл превышает допустимый размер.'));

            if(!is_writable(ROOT_DIR))
            die($errors[] = error_style('Директория закрыта от записи.'));

            if(strlen($_POST['userfilename']) < 1)
            die($errors[] = error_style('Укажите название статьи.'));

            $fp = fopen($_FILES['filename']['tmp_name'], 'r+'); // Добавляется первая строчка в загружаемый файл
            fwrite($fp, ($_POST['userfilename'])."\r\n");
            fclose($fp);

            if(move_uploaded_file($_FILES['filename']['tmp_name'],ROOT_DIR. $filename))
            die(success_style('Статья успешно загружена.'));

            else
            echo $errors[] = error_style('При загрузке возникли ошибки.');
        }

?>

<form method='post' enctype='multipart/form-data'>

    <input type='hidden' name='MAX_FILE_SIZE' value='30000'>

    <h3>Название статьи:</h3>

    <input class='name-form' type='text' name='userfilename' value='Новая статья' placeholder='Введите название статьи' onfocus="this.value=''" ><br> <!-- по умолчанию дается название статьи - 'Новая статья' -->

    <h3>Файл</h3>

    <label class ='upload-form' for="files">Обзор...</label>
    <input id='files' style='visibility:hidden;' type='file' name='filename' class='select_file'><br>

    <button class='upload-action' name='upload_process'>Добавить</button>

</form>

<a class='back-to-main' href='index.php'>Вернуться на главную</a>

</body>
</html>