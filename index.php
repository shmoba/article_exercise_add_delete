<?php
	header('Content-Type: text/html;');
	error_reporting(E_ALL);
	mb_internal_encoding('utf-8');
	define('UPLOAD_DIR', DIRECTORY_SEPARATOR.'texts'.DIRECTORY_SEPARATOR);
	if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR); // создать папку если ее нет
	define('ROOT_DIR',dirname(__FILE__).UPLOAD_DIR);
	define('PER_PAGE', 5);
	/*
	Требуется переделать Каталог статей 
	1. изменив в нем количество статей на страницу с 10 на 5. - !!!
	2. Изменив папку со статьями поменять на /texts/ - !!!
	3. В файлах во второй строчке дата публикации статьи, с третьей строки сама статья. - !!!
	4. На главной странице рядом с названием каждой статьи вывести данную стрелку https://www.iconfinder.com/icons/227601/arrow_right_icon - !!!
	5. сделать все в одном файле (не считая папку artilcles), включая стили. !!!
	*/
?>
<!DOCTYPE html>
<html lang='ru'>
<head>
	<meta charset='utf-8'>
	<title>Статьи</title>
	<style type="text/css">

		body {font-family: sans-serif; max-width: 1024px; margin: 20px auto; font-weight: 600;}
		h1{margin-bottom: 5px;}
		a {color: #0f31ea; text-decoration: none;}
		a:hover {color: red;}
		ul {list-style: none; padding-left: 20px; width: 500px;}
		li {margin-bottom: 18px; display: flex; justify-content: space-between; border-bottom: dotted 3px yellow;}
		p{margin-top: 0;}

		#wrap {display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;}
		.main-h1 {margin:15px 0 5px 150px;}
		.text {width: 60%; font-weight: normal; text-align: justify;}
		.upload-action {background-color: #d0cdcd;padding: 19px 100px 35px 100px;border:solid black 2px;height: 20px;font-weight: bold;margin-top: 30px;}
		.upload-form {padding: 8px 12px 7px 14px; font-size: 14px; border:solid black 2px; background: #d0cdcd; width: 70px; height: 19px; z-index: 9999;}
		.upload-url:hover,.upload-form:hover,.upload-action:hover,button:hover {background-color: yellow; cursor: pointer; color: red;}
		.name-form {border: solid; font-weight: 600; padding: 8px 11px 7px 14px;}
		.destroy {font-size: 14px; color: black; text-decoration: none; padding-left: 20px; position: relative; bottom: -1.5px;}
		.upload-url {background-color: #d0cdcd; padding: 15px; border:solid black 2px; height: 20px; position: relative; top: 30px;}
		.back-to-main {display: flex; flex-direction: row-reverse;}
		.error {padding: 15px; border: solid 2px #ff3800; background-color: #ffbf9c; margin-bottom: 15px;}
		.success {padding: 15px; border: solid 2px #14d414; background-color: #b9ffa1; margin-bottom: 15px;}
		.arrow {width: 10px; margin-right: 10px}
		.pub-date {margin-bottom: 5px; font-weight: 600;}

	</style>
</head>
<body>

<?php

	if (isset($_GET['show'])) {
		$text = file_get_contents($_GET['show']);
		$article = explode("\n", $text); // Поделить статьи на строки
		$file_change = filemtime($_GET['show']);
		$file_born = filectime($_GET['show']);

		?>

		<h1><?=$article[0]?></h1> <!--Вывести первое значение с названием-->
		<div><a href='../'>Главная</a> > <?=$article[0]?></div>
		<div id='wrap'>

		<?php

		array_shift($article); // Убрать первое значение с названием статьи

		array_unshift($article, '<div class="pub-date">Дата публикации: '.date ('F d Y H:i:s',$file_change).'<br></div>'); // Добавить первое значение с датой публикации

		$text = implode ($article); // Склеить массив в строку

		?>

		<div class='text'><?=$text?></div>
			<div>
				<p>Дата изменения статьи: <?= date ('F d Y H:i:s',$file_change)?></p>
				<p>Дата создания статьи: <?= date ('F d Y H:i:s',$file_born)?></p>
			</div>
		</div>

		<div class='back-to-main'><a href='index.php'>Вернуться на главную</a></div>

		<?php

	} // if (isset($_GET['show']))

	else {

		?>

		<h1 class='main-h1'>Статьи</h1>
		<div id='wrap'>
		<div>
	
		<?php

			if ( (!empty($_GET['destroy'])) and (file_exists($_GET['destroy'])) ) {

				unlink($_GET['destroy']); // УДАЛЕНИЕ !!

				echo '<div class="success">Статья удалена.</div><script>setTimeout(function(){window.location.reload(1);}, 870);</script>'; // Обновить страницу после сообщения
				}

				$page = isset($_GET['page']) ? (int) $_GET['page'] : 0; // Помещаем номер страницы из массива GET в переменую $page
				$skip = PER_PAGE * $page;
				$entries = array();

				if ($handle = opendir(ROOT_DIR)) {
					$count = 0;
					while (false !== ($entry = readdir($handle))) {
						if ($entry !='.' and $entry != '..') {
							$count++;
							if ($count < $skip || $count > $skip + PER_PAGE) { // Количество выводимых статей
								continue;
							}
							$entries[] = ROOT_DIR. $entry;
							array_multisort(array_map('filectime', $entries), SORT_DESC, $entries); // Сортровка файлов по дате (для filectime(и подобным) обязательно указывать абсолютный путь файла)
						}
				}closedir($handle);

				$pages = (int)$count;
				if ($count % PER_PAGE)
				$pages ++;
				
				foreach($entries as $entry) {
					$file_name = explode(DIRECTORY_SEPARATOR, $entry);
					$file_name = strstr(end($file_name), '.', true);
					?>

					<ul>

						<?php

						echo '<li><a href="index.php?show='.urlencode($entry).'"><img class="arrow" src="https://cdn0.iconfinder.com/data/icons/feather/96/591276-arrow-right-512.png" alt="arrow">'.$file_name.'</a><a class="destroy" href="index.php?destroy='.urlencode($entry).'">'.'Удалить статью</a></li>';
						?>

					</ul>

					<?php

				} // foreach($entries as $entry) {

				?>

					Страницы:

				<?php

					for($i = 0; $i < $pages / PER_PAGE; $i++) {  // Номера страниц  ($i=0; $i<count($entries) / PER_PAGE; $i++)

						?>

						<a href='index.php?page=<?=$i?>'><?=$i+1?></a>

						<?php

					}// for($i = 0; $i < $pages / PER_PAGE; $i++) {
				
					echo '<br><br>Всего статей: ' . $count;

					?>

					</div><div><a class='upload-url' href='upload.php'>Добавить статью</a></div></div>

					<?php
			} // if ( (!empty($_GET['destroy'])) and (file_exists($_GET['destroy'])) ) {
	} // else {
		?>

</body>
</html>