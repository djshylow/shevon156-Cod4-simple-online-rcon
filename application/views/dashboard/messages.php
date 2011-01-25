<div id="leftblock">
	<h2><?php echo __('Navigation') ?></h2>
    <ul class="leftmenu">
        <li><a href="<?php echo URL::site('dashboard/index') ?>"><?php echo __('Players list') ?></a></li>
    	<li style="background-image: url(images/log.png)"><a href="<?php echo URL::site('dashboard/logs') ?>"><?php echo __('Player log') ?></a></li>
		<li style="background-image: url(images/msg.png)" class="active"><a><?php echo __('Message rotation') ?></a></li>
    </ul>
</div>
<div id="rightblock">
	 <h2><?php echo __('Message rotation') ?></h2>
	<table cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<td><?php echo __('Username') ?></td>
				<td><?php echo __('Message') ?></td>
				<td><?php echo __('Remove') ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($messages as $msg): ?>
			<tr>
				<td><?php echo strip_tags($msg['username']) ?></td>
				<td><?php echo strip_tags($msg['message']) ?></td>
				<td>
                <a href="<?php echo URL::site('dashboard/msgrotation/'.$msg['id']) ?>" class="button" style="background-image: url(images/delete.png)">Delete</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<h2><?php echo __('Add message') ?></h2>
    <div class="group">
        <div class="content">
            <form action="<?php echo URL::site('dashboard/msgrotation') ?>" method="post">
                <div>
                    <label for="message"><?php echo __('Message') ?>:</label>
                    <input type="text" name="message" id="message" />
                </div>
                <div>
                    <input type="submit" style="width: auto" name="submit" value="<?php echo __('Add') ?>" />
                </div>
            </form>
        </div>
    </div>
</div>