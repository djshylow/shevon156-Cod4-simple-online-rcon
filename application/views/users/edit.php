<h2>Edit user</h2>
<div class="group">
	<div class="content">
		<form action="<?php echo URL::site('users/edit/'.$user->id) ?>" method="post">
			<div>
				<label>Username:</label>
				<?php echo $user->username ?>
			</div>
			<div>
				<label>New password:<br /><small>Empty = no change</small></label>
				<input type="password" name="password" value="" />
			</div>
			<div>
				<label>Allow log management:</label>
				<input style="width: auto" type="checkbox" name="can_log" value="1"<?php if($can_log): ?> checked="checked"<?php endif;?> />
			</div>
			<div>
				<label>Allow servers management:</label>
				<input style="width: auto" type="checkbox" name="can_servers" value="1"<?php if($can_servers): ?> checked="checked"<?php endif;?> />
			</div>
			<div>
				<label>Allow users management:</label>
				<input style="width: auto" type="checkbox" name="can_users" value="1"<?php if($can_users): ?> checked="checked"<?php endif;?> />
			</div>
			<div>
				<input style="width: auto" type="submit" name="submit" value="Submit" />
			</div>
		</form>
	</div>
</div>