<div id="leftblock">
	<h2>Navigation</h2>
    <ul class="leftmenu">
        <li><a href="<?php echo URL::site('dashboard/index') ?>"><?php echo __('Players list') ?></a></li>
    	<li style="background-image: url(images/log.png)" class="active"><a>Player log</a></li>
		<li style="background-image: url(images/msg.png)"><a href="<?php echo URL::site('dashboard/msgrotation') ?>">Message rotation</a></li>
    </ul>
</div>
<div class="detail-window">
	<h2>Details <a style="cursor: pointer" onclick="$(this).parent().parent().hide('slow')">[close window]</a></h2>
	<h3>IP addresses</h3>
	<ul id="detail-ip-addresses">
	</ul>
	<h3>Player names</h3>
	<ul id="detail-names">
	</ul>
</div>
<div id="rightblock">
	<h2>Player log</h2>
	<table cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<td>GUID</td>
				<td>Last scan</td>
				<td>Last name</td>
				<td>Last IP</td>
				<td>Details</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($logs as $log): ?>
			<?php
			    $log['names'] = empty($log['names']) ? array('Unknown') : unserialize($log['names']);
			    $log['ip_addresses'] = empty($log['ip_addresses']) ? array('Unknown') : unserialize($log['ip_addresses']);

			    if(!$log['names'] OR empty($log['names'])) $log['names'] = array('Unknown');
			    if(!$log['ip_addresses'] OR empty($log['ip_addresses'])) $log['ip_addresses'] = array('Unknown');
			?>
			<tr>
				<td><?php echo $log['id'] ?></td>
				<td><?php echo date('d.m.Y H:i', (int) $log['last_update']) ?>
				</td>
				<td>
					<?php echo strip_tags(end($log['names'])); ?>
				</td>
				<td>
					<?php echo strip_tags(end($log['ip_addresses'])); ?>
				</td>
				<td>
					<a class="button" onclick="rconPlayerDetails(<?php echo $log['id'] ?>)">Details</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>