<div id="leftblock">
<h2><?php echo __('Navigation') ?></h2>
<ul class="leftmenu">
    <li style="background-image: url(images/edit.png)"><a href="<?php echo URL::site('servers/index') ?>"><?php echo __('Servers') ?></a></li>

    <li class="active" style="background-image: url(images/gear.png)"><a><?php echo __('Permissions') ?></a></li>
</ul>
</div>
<div id="rightblock">
<h2>Permissions</h2>
<table cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td>User</td>
			<td>Server</td>
			<td>Can kick/ban/temp ban/message/user log</td>
			<td>Actions</td>
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
                <a href="<?php echo URL::site('servers/permissions_delete/'.$s['user_id'].'/'.$s['server_id']) ?>" class="button" style="background-image: url(images/delete.png)">Delete</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<h2>Assign new permissions</h2>
<div class="group">
	<div class="content">
		<form action="<?php echo URL::site('servers/permissions') ?>" method="post">
			<div>
				<label>User:</label>
				<?php echo Form::select('user_id', $users) ?>
			</div>
			<div>
				<label>Server:</label>
				<?php echo Form::select('server_id', $servers) ?>
			</div>
			<div>
				<label>Can kick:</label>
				<input type="checkbox" name="can_kick" style="width: auto" value="1" />
			</div>
			<div>
				<label>Can ban:</label>
				<input type="checkbox" name="can_ban" style="width: auto" value="1" />
			</div>
			<div>
				<label>Can temp ban:</label>
				<input type="checkbox" name="can_temp_ban" style="width: auto" value="1" />
			</div>
			<div>
				<label>Can send messages:<br /><small>+ Manage message rotations</small></label>
				<input type="checkbox" name="can_messages" style="width: auto" value="1" />
			</div>
			<div>
				<label>Can view user logs:</label>
				<input type="checkbox" name="can_logs" style="width: auto" value="1" />
			</div>
			<div>
				<input style="width: auto" type="submit" name="submit" value="Submit" />
			</div>
		</form>
	</div>
</div>
</div>