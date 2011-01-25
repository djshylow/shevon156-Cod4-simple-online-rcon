<div id="leftblock">
<h2><?php echo __('Navigation') ?></h2>
<ul class="leftmenu">
    <li style="background-image: url(images/edit.png)"><a href="<?php echo URL::site('servers/index') ?>"><?php echo __('Servers') ?></a></li>

    <li class="active" style="background-image: url(images/gear.png)"><a><?php echo __('Permissions') ?></a></li>
</ul>
</div>
<div id="rightblock">
<h2><?php echo __('Permissions') ?></h2>
<table cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td><?php echo __('User') ?></td>
			<td><?php echo __('Server') ?></td>
			<td><?php echo __('Can kick/ban/temp ban/message/user log') ?></td>
			<td><?php echo __('Actions') ?></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($list as $s): ?>
		<?php $permissions = (int) $s['permissions'] ?>
		<tr>
			<td><?php echo $users[$s['user_id']] ?></td>
			<td><?php echo $servers[$s['server_id']] ?></td>
			<td>
			    <?php echo ($permissions & SERVER_KICK) ? 'Yes' : 'No' ?>/
			    <?php echo ($permissions & SERVER_BAN) ? 'Yes' : 'No' ?>/
			    <?php echo ($permissions & SERVER_TEMP_BAN) ? 'Yes' : 'No' ?>/
			    <?php echo ($permissions & SERVER_MESSAGE) ? 'Yes' : 'No' ?>/
			    <?php echo ($permissions & SERVER_USER_LOG) ? 'Yes' : 'No' ?>
			</td>
			<td>
                <a href="<?php echo URL::site('servers/permissions_delete/'.$s['user_id'].'/'.$s['server_id']) ?>" class="button" style="background-image: url(images/delete.png)"><?php echo __('Delete') ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<h2><?php echo __('Assign new permissions') ?></h2>
<div class="group">
	<div class="content">
		<form action="<?php echo URL::site('servers/permissions') ?>" method="post">
			<div>
				<label><?php echo __('User') ?>:</label>
				<?php echo Form::select('user_id', $users) ?>
			</div>
			<div>
				<label><?php echo __('Server') ?>:</label>
				<?php echo Form::select('server_id', $servers) ?>
			</div>
			<div>
				<label><?php echo __('Can kick') ?>:</label>
				<input type="checkbox" name="can_kick" style="width: auto" value="1" />
			</div>
			<div>
				<label><?php echo __('Can ban') ?>:</label>
				<input type="checkbox" name="can_ban" style="width: auto" value="1" />
			</div>
			<div>
				<label><?php echo __('Can temp ban') ?>:</label>
				<input type="checkbox" name="can_temp_ban" style="width: auto" value="1" />
			</div>
			<div>
				<label><?php echo __('Can send messages') ?>:<br /><small>+<?php echo __('Manage message rotations') ?></small></label>
				<input type="checkbox" name="can_messages" style="width: auto" value="1" />
			</div>
			<div>
				<label><?php echo __('Can view user logs') ?>:</label>
				<input type="checkbox" name="can_logs" style="width: auto" value="1" />
			</div>
			<div>
				<input style="width: auto" type="submit" name="submit" value="<?php echo __('Add/Apply') ?>" />
			</div>
		</form>
	</div>
</div>
</div>