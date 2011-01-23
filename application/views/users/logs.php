<div id="leftblock">
	<h2>Navigation</h2>
    <ul class="leftmenu">
        <li><a href="<?php echo URL::site('users/index') ?>"><?php echo __('Manage users') ?></a></li>
    	<li class="active" style="background-image: url(images/log.png)"><a><?php echo __('Actions log') ?></a></li>
    </ul>
</div>
<div id="rightblock">
<h2>Logs</h2>
<table cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td>ID</td>
			<td>Username</td>
			<td>Date</td>
			<td>Action</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($logs as $l): ?>
		<tr>
			<td><?php echo $l['id'] ?></td>
			<td><?php echo strip_tags($l['username']) ?></td>
			<td><?php echo $l['date'] ?></td>
			<td><?php echo strip_tags($l['content']) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>