<h2><?php echo __('Navigation') ?></h2>

<ul class="leftmenu">

	<?php if($action == 'index'): ?>

	<li class="active"><a><?php echo __('Players list') ?></a><img title="<?php echo __('Refresh player list') ?>" alt="Rfrsh" src="images/refresh.png" onclick="rconRefresh()" /></li>

	<?php else: ?>

	<li><a href="<?php echo URL::site('dashboard/index') ?>"><?php echo __('Players list') ?></a></li>

	<?php endif; ?>

	<li<?php if($action == 'logs'): ?> class="active"<?php endif; ?> style="background-image: url(images/log.png)"><a href="<?php echo URL::site('dashboard/logs') ?>"><?php echo __('Player log') ?></a></li>

	<li<?php if($action == 'msgrotation'): ?> class="active"<?php endif; ?> style="background-image: url(images/msg.png)"><a href="<?php echo URL::site('dashboard/msgrotation') ?>"><?php echo __('Message rotation') ?></a></li>

	<li<?php if($action == 'playlists'): ?> class="active"<?php endif; ?> style="background-image: url(images/lists.png)"><a href="<?php echo URL::site('dashboard/playlists') ?>"><?php echo __('Playlists') ?></a></li>

</ul>

<br />

<h2><?php echo __('Select server') ?></h2>

<ul class="leftmenu servers">

<?php foreach($owned as $serv): ?>

	<li<?php if($current_server_id == $serv['id']): ?> class="active"<?php endif; ?>><a href="<?php echo URL::site('dashboard/set_server/'.$serv['id'])?>"><?php echo HTML::chars($serv['name']); echo HTML::chars($serv['game']) ?></a></li>

<?php endforeach; ?>

</ul>

