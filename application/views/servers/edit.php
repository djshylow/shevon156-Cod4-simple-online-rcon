<h2>Edit server</h2>
<div class="group">
	<div class="content">
		<form action="<?php echo URL::site('servers/edit/'.$server->id) ?>" method="post">
			<div>
				<label>Name:</label>
				<input type="text" name="name" value="<?php echo $server->name ?>" />
			</div>
			<div>
				<label>Server IP:</label>
				<input type="text" name="ip" value="<?php echo $server->ip ?>" />
			</div>
			<div>
				<label>Port:</label>
				<input type="text" name="port" value="<?php echo $server->port ?>" />
			</div>
			<div>
				<label>RCon password:</label>
				<input type="text" name="password" value="<?php echo $server->password ?>" />
			</div>
			<div>
				<input style="width: auto" type="submit" name="submit" value="Submit" />
			</div>
		</form>
	</div>
</div>