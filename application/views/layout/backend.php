<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
    <head>
        <base href="<?php echo URL::base() ?>" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $title ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>style.css" />
        <script type="text/javascript" src="<?php echo URL::base() ?>jquery.js"></script>
        <script type="text/javascript">
			$(document).ajaxStart(function(){
				$('#ajaxLoader').css('display', 'block');
			}).ajaxComplete(function() {
				$('#ajaxLoader').slideUp('slow');
			});

			var BASE_URL = '<?php echo URL::base() ?>';
		</script>
		<script type="text/javascript" src="<?php echo URL::base() ?>app.js"></script>
    </head>
    <body>
		<div id="ajaxLoader">
			Loading...
		</div>
        <div id="logo">
            <h1>Black Ops Remote Console <a href="<?php echo URL::site('login/out') ?>" style="color: #fff">(logout)</a></h1>
        </div>
        <ul id="menu">
        	<li<?php if($tab == 'rcon'): ?> class="active"<?php endif; ?>><a href="<?php echo URL::site('dashboard/index') ?>">Server console</a></li>
			<li<?php if($tab == 'users'): ?> class="active"<?php endif; ?>><a href="<?php echo URL::site('users/index') ?>">Users</a></li>
			<li<?php if($tab == 'servers'): ?> class="active"<?php endif; ?>><a href="<?php echo URL::site('servers/index') ?>">Servers</a></li>
        </ul>
        <?php if($notice): ?>
        <div class="message"><?php echo $notice ?></div>
        <?php endif; ?>
        <div id="container">
        <?php echo $content ?>
        </div>
        <div id="footer">
            Created by <a href="mailto:me2.legion@gmail.com">EpicLegion</a><br />
            Some Icons are Copyright &copy; <a href="http://p.yusukekamiyamane.com/">Yusuke Kamiyamane</a>. All rights reserved
        </div>
    </body>
</html>