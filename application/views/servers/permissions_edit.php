<h2><?php echo __('Edit permissions') ?></h2>
<div class="group">
	<div class="content">
		<form action="<?php echo URL::site('servers/permissions_edit/'.$user->id.'/'.$server->id) ?>" method="post">
			<div>
				<label><?php echo __('User') ?>:</label>
				<span><?php echo HTML::chars($user->username) ?></span>
			</div>
			<div>
				<label><?php echo __('Server') ?>:</label>
				<span><?php echo HTML::chars($server->name) ?></span>
			</div>
<?php echo $fields ?>
			<div>
				<input style="width: auto" type="submit" name="submit" value="<?php echo __('Apply') ?>" />
			</div>
		</form>
	</div>
</div>
