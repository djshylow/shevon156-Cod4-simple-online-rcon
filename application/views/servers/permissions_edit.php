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
			<div>
				<label><?php echo __('Can kick') ?>:</label>
				<input type="checkbox" name="can_kick" style="width: auto" value="1"<?php if($permissions & SERVER_KICK): ?> checked="checked"<?php endif;?>/>
			</div>
			<div>
				<label><?php echo __('Can ban') ?>:</label>
				<input type="checkbox" name="can_ban" style="width: auto" value="1"<?php if($permissions & SERVER_BAN): ?> checked="checked"<?php endif;?>/>
			</div>
			<div>
				<label><?php echo __('Can temp ban') ?>:</label>
				<input type="checkbox" name="can_temp_ban" style="width: auto" value="1"<?php if($permissions & SERVER_TEMP_BAN): ?> checked="checked"<?php endif;?>/>
			</div>
			<div>
				<label><?php echo __('Can send messages') ?>:</label>
				<input type="checkbox" name="can_messages" style="width: auto" value="1"<?php if($permissions & SERVER_MESSAGE): ?> checked="checked"<?php endif;?>/>
			</div>
			<div>
				<label><?php echo __('Can manage message rotations') ?>:</label>
				<input type="checkbox" name="can_message_rotation" style="width: auto" value="1"<?php if($permissions & SERVER_MESSAGE_ROTATION): ?> checked="checked"<?php endif;?>/>
			</div>
			<div>
				<label><?php echo __('Can view user logs') ?>:</label>
				<input type="checkbox" name="can_logs" style="width: auto" value="1"<?php if($permissions & SERVER_USER_LOG): ?> checked="checked"<?php endif;?>/>
			</div>
			<div>
				<label><?php echo __('Can set playlists') ?>:</label>
				<input type="checkbox" name="can_playlists" style="width: auto" value="1"<?php if($permissions & SERVER_PLAYLIST): ?> checked="checked"<?php endif;?>/>
			</div>
			<div>
				<input style="width: auto" type="submit" name="submit" value="<?php echo __('Apply') ?>" />
			</div>
		</form>
	</div>
</div>
