<?php

/*
 * Includes functions & constants
 * -------------------------------------------------------------------------------------
*/

if(file_exists('config.php') AND !is_dir('config.php')) {
	require 'config.php';
	require 'functions.php';
} else {
	die('Error : You have to renamme config.inc.php to config.php AND configure it !');
}

if(file_exists(DB_NAME) AND !is_dir(DB_NAME)) {
	$userManager = UsersManager::getReference();
	$usersList = $userManager->getAllUsers(1);

	if(!empty($usersList))
		header('Location: ' . pathinfo(HTTP_ROOT, PATHINFO_DIRNAME));
}

/*
 * Preloading
 * -------------------------------------------------------------------------------------
*/

Tool::preload();

$complete = false;
$Theme = Tool::loadTheme();
$Lang = Tool::loadLanguage();
$Theme->location = str_replace('install.php', '', $Theme->location);

/*
 * Entry point
 * -------------------------------------------------------------------------------------
*/

if(isset($_POST['init'])) {

	/*
	 * Creating database & tables
	 * -------------------------------------------------------------------------------------
	*/

	$db = PDOSQLite::getDBLink(false);

	$db->query('CREATE TABLE IF NOT EXISTS `snippets` (`name` VARCHAR(255), `id_user` INT(1), `last_update` BIGINT(32), `content` TEXT, `language` INT(1), `comment` TEXT, `category` VARCHAR(80), `tags` TEXT, `private` INT(1))');

	$db->query('CREATE TABLE IF NOT EXISTS `users` (`admin` INT(1), `name` VARCHAR(30), `email` VARCHAR(80), `avatar` INT(1), `password` VARCHAR(64), `locked` INT(1), `theme` VARCHAR(50), `language` VARCHAR(10), `favorite_lang` TEXT)');

	/*
	 * Adding first user (admin)
	 * -------------------------------------------------------------------------------------
	*/

	if(!empty($_POST['init-login']) AND !empty($_POST['init-email'])
		AND !empty($_POST['init-password-1']) AND !empty($_POST['init-password-2'])) {

		if(filter_var($_POST['init-email'], FILTER_VALIDATE_EMAIL)) {
			if($_POST['init-password-1'] === $_POST['init-password-2']) {
				$adminArray = array(
					'admin' => 1,
					'name' => $_POST['init-login'],
					'email' => $_POST['init-email'],
					'avatar' => 0,
					'password' => hash('sha256', $_POST['init-password-1']),
					'locked' => 0,
					'theme' => 'default',
					'language' => 'en_US',
					'favorite_lang' => array()
				);

				$admin = new User($adminArray);

				if($admin->addNewUser()) {
					Tool::appendMessage($Lang->success_installation, Tool::M_SUCCESS);
					Tool::appendMessage($Lang->info_install_remove_file, Tool::M_WARNING);
					$complete = true;
				}
			} else {
				Tool::appendMessage($Lang->error_password_are_different, Tool::M_ERROR);
			}
		} else {
			Tool::appendMessage($Lang->error_email_is_not_a_valid_email, Tool::M_ERROR);
		}
	} else {
			Tool::appendMessage($Lang->error_all_fields_are_required, Tool::M_ERROR);
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Snipeg Project</title>
		<meta charset="<?php echo PHP_CHARSET; ?>"/>
		<link rel="stylesheet" href="<?php echo $Theme->location;?>style/style.css" />
		<script src="<?php echo $Theme->location;?>js/jquery-1.6.2.min.js"></script>
		<script src="<?php echo $Theme->location;?>js/jquery.uniform.min.js"></script>
		<script src="<?php echo $Theme->location;?>js/scripts.js"></script>
		<style>#init input[type=text], #init input[type=password] { width: 256px; } label {padding-left: 0;}</style>
	</head>
	<body>
		<div id="topbar"></div>

		<?php Tool::readMessages();?>

		<div id="main" class="container_12">

			<form method="post" action="<?php echo HTTP_ROOT ?>" id="install" class="prefix_4 grid_4" autocomplete="off">

				<div id="init">

					<h1>Snipeg Initialisation</h1>

					<label for="login-name">Admin name</label>
					<input type="text" name="init-login" id="init-name" value="<?php remind_post('init-login'); ?>" tabindex="10" autofocus />

					<label for="login-email">Email</label>
					<input type="text" name="init-email" id="init-email" value="<?php remind_post('init-email'); ?>" tabindex="20" />

					<label for="login-password-1">Password</label>
					<input type="password" name="init-password-1" id="init-password-1" value="<?php remind_post('init-password-1'); ?>" tabindex="20" />

					<label for="login-password-2">Password ( Retype )</label>
					<input type="password" name="init-password-2" id="init-password-2" value="<?php remind_post('init-password-2'); ?>" tabindex="20" />

					<div class="clear"></div>
					<input type="submit" name="init" value="Init" tabindex="30"/>

				</div>

			</form>

		</div>

		<?php if($complete) : ?>
		<script type="text/javascript">
			$('#init').hide();
		</script>
		<?php endif; ?>

<?php include(THEME_PATH . $Theme->dirname  . '/' . 'footer.php');
