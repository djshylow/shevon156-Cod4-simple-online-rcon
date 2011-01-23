<div id="leftblock">
	<h2>Navigation</h2>
    <ul class="leftmenu">
        <li class="active"><a><?php echo __('Manage users') ?></a></li>
    	<li style="background-image: url(images/log.png)"><a href="<?php echo URL::site('users/logs') ?>"><?php echo __('Actions log') ?></a></li>
    </ul>
</div>
<div id="rightblock">
<h2>Users</h2>
<table cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td>ID</td>
			<td>Username</td>
			<td>Email</td>
			<td>Last login</td>
			<td>Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($users as $u): ?>
		<tr>
			<td><?php echo $u->id ?></td>
			<td><?php echo strip_tags($u->username) ?></td>
			<td><?php echo strip_tags($u->email) ?></td>
			<td><?php echo date('Y-m-d H:i', $u->last_login) ?></td>
			<td>
                <a href="<?php echo URL::site('users/edit/'.$u->id) ?>" class="button" style="background-image: url(images/edit.png)">Edit</a>
                <a href="<?php echo URL::site('users/delete/'.$u->id) ?>" class="button" style="background-image: url(images/delete.png)">Delete</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<h2><?php echo __('Add user') ?></h2>
<div class="group">
	<div class="content">
		<form action="<?php echo URL::site('users/index') ?>" method="post">
			<div>
				<label>Username:<br /><small>Unique. Minimum 4 chars, max 32 characters.</small></label>
				<input type="text" name="username" value="" />
			</div>
			<div>
				<label>Password:<br /><small>Minimum 5 chars, max 42 chars.</small></label>
				<input type="password" name="password" value="" />
			</div>
			<div>
				<label>Confirm password:</label>
				<input type="password" name="password_confirm" value="" />
			</div>
			<div>
				<label>Email:<br /><small>Unique and valid email address</small></label>
				<input type="text" name="email" value="" />
			</div>
			<div>
				<label>Allow log management:</label>
				<input style="width: auto" type="checkbox" name="can_log" value="1" />
			</div>
			<div>
				<label>Allow servers management:</label>
				<input style="width: auto" type="checkbox" name="can_servers" value="1" />
			</div>
			<div>
				<label>Allow users management:</label>
				<input style="width: auto" type="checkbox" name="can_users" value="1" />
			</div>
			<div>
				<input style="width: auto" type="submit" name="submit" value="Submit" />
			</div>
		</form>
	</div>
</div>
</div>