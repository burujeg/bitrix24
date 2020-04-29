<html>
<head>
<!-- meta -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- Bootstrap -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<!-- jquery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- jquery input mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
<!-- Local CSS -->
<link rel="stylesheet" type="text/css" href="/public/styles/style.css">
</head>
<body>
<?

/**
*	Require debug
*/
require 'application/lib/Dev.php';

/**
* Define a variables and arrays
*/
$cities = ['Москва', 'Омск', 'Тюмень']; // array with cities for tag select
$message = ''; // empty message of success submit form


/**
* If for has been submit
*/
if (isset($_POST['email'])){
	
	define('CRM_HOST', 'b24-rxwdvs.bitrix24.ru'); // Ваш домен CRM системы
	define('CRM_PORT', '443'); // Порт сервера CRM. Установлен по умолчанию
	define('CRM_PATH', '/crm/configs/import/lead.php'); // Путь к компоненту lead.rest

	define('CRM_LOGIN', 'vladimirstolbig@gmail.com'); // Логин пользователя Вашей CRM по управлению лидами
	define('CRM_PASSWORD', 'psppsp'); // Пароль пользователя Вашей CRM по управлению лидами

	   $postData = array(
		  'TITLE' => 'Заявка с формы' // Заголовок		  
	   );

	   if (defined('CRM_AUTH'))
	   {
		  $postData['AUTH'] = CRM_AUTH;
	   }
	   else
	   {
		  $postData['LOGIN'] = CRM_LOGIN;
		  $postData['PASSWORD'] = CRM_PASSWORD;
		  $postData['STATUS_ID'] = 'IN_PROCESS';
		  $postData['SOURCE_ID'] = 'WEB';
		  $postData['ADDRESS'] = $_POST['city'];
		  $postData['POST'] = $_POST['post'];
		  $postData['NAME'] = $_POST['name'];
		  $postData['PHONE_MOBILE'] = $_POST['phone'];
		  $postData['EMAIL_WORK'] = $_POST['email'];
	   }

	   $fp = fsockopen("ssl://".CRM_HOST, CRM_PORT, $errno, $errstr, 30);
	   if ($fp)
	   {
		  $strPostData = '';
		  foreach ($postData as $key => $value)
			 $strPostData .= ($strPostData == '' ? '' : '&').$key.'='.urlencode($value);

		  $str = "POST ".CRM_PATH." HTTP/1.0\r\n";
		  $str .= "Host: ".CRM_HOST."\r\n";
		  $str .= "Content-Type: application/x-www-form-urlencoded\r\n";
		  $str .= "Content-Length: ".strlen($strPostData)."\r\n";
		  $str .= "Connection: close\r\n\r\n";

		  $str .= $strPostData;

		  fwrite($fp, $str);

		  $result = '';
		  while (!feof($fp))
		  {
			 $result .= fgets($fp, 128);
		  }
		  fclose($fp);

		  $response = explode("\r\n\r\n", $result);

		  $output = '<pre>'.print_r($response[1], 1).'</pre>';
	   }
	   else
	   {
		  echo 'Connection Failed! '.$errstr.' ('.$errno.')';
	   }
	   /**
	   * Message after submit
	   */
	   $message = "Ваша заявка принята, с вами свяжутся в течение 2-х часов";
	   	   
}
?>
    <div class="container">
     	<div class="row align-items-center mt-5" style="height: 80%; color: white;">
			<div class="col-md-12 mx-auto my-auto">

				<div class="col-md-5 mx-auto justify-content-center">
					<form class="form-signin" method="post" action="index.php">
						<p class="h3 mb-3 font-weight-normal" style="text-align: center">Отправить заявку</p><br>
						<label for="city" class="sr-only">Город</label>
						<select id="city" class="form-control mb-3" name="city">
							<option value="" disabled selected>Город</option>
							<?
								for ($i = 0; $i <= count($cities); $i++){
									echo <<<HTML
									<option>$cities[$i]</option>
HTML;
								}
							?>
						</select>
						<label for="post" class="sr-only">Должность</label>
						<input name="post" type="text" id="post" class="form-control mb-3" placeholder="Должность">
						<label for="name" class="sr-only">Имя</label>
						<input name="name" type="text" id="name" class="form-control mb-3" placeholder="Имя">
						<label for="phone" class="sr-only">Номер телефона</label>
						<input name="phone" type="phone" id="phone" class="form-control mb-3" class="phone_mask" placeholder="Номер телефона" required autofocus>
							<script>
							jQuery(function($) {
								$("input[name=phone]").mask("+7 (099) 999-99-99");
							});
							</script>
						<label for="email" class="sr-only">E-mail</label>
						<input name="email" type="email" id="email" class="form-control mb-3" placeholder="E-mail" required>
						<button class="btn btn-lg btn-light btn-block" type="submit">Отправить</button><br>
						<p class="my-3 font-weight-normal text-white" style="text-align: center"><?=$message?></p>
						<p class="my-5" style="color: white; text-align: center;">&copy; alavenir.ru</p>
					</form>
				</div>
			</div>	
		</div>
	</div>



</body>
</html>
