<div id="leftblock">
<h2><?php echo __('Navigation') ?></h2>
<ul class="leftmenu">
    <li class="active" style="background-image: url(images/edit.png)"><a><?php echo __('Servers') ?></a></li>
    <li style="background-image: url(images/gear.png)"><a href="<?php echo URL::site('servers/permissions') ?>"><?php echo __('Permissions') ?></a></li>
</ul>
</div>
<div id="rightblock">
<h2>Servers</h2>
<table cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td>ID</td>
			<td>Name</td>
			<td>Host</td>
			<td>Port</td>
			<td>Actions</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($servers as $s): ?>
		<tr>
			<td><?php echo $s->id ?></td>
			<td><?php echo $s->name ?></td>
			<td><?php echo $s->ip ?></td>
			<td><?php echo $s->port ?></td>
			<td>
                <a href="<?php echo URL::site('servers/edit/'.$s->id) ?>" class="button" style="background-image: url(images/edit.png)">Edit</a>
                <a href="<?php echo URL::site('servers/delete/'.$s->id) ?>" class="button" style="background-image: url(images/delete.png)">Delete</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<h2>Add server</h2>
<div class="group">
	<div class="content">
		<form action="<?php echo URL::site('servers/index') ?>" method="post">
			<div>
				<label>Name:</label>
				<input type="text" name="name" value="" />
			</div>
			<div>
				<label>Server IP:</label>
				<input type="text" name="ip" value="" />
			</div>
			<div>
				<label>Port:</label>
				<input type="text" name="port" value="" />
			</div>
			<div>
				<label>RCon password:</label>
				<input type="text" name="password" value="" />
			</div>
			<div>
				<input style="width: auto" type="submit" name="submit" value="Submit" />
			</div>
		</form>
	</div>
</div>
</div>