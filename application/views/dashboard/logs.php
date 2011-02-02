<div id="leftblock">
	<?php echo $navigation		/* views/dashboard/navigation.php */ ?>
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
	<h2><?php echo __('Player log') ?></h2>
	<script type="text/javascript" src="<?php echo URL::base() ?>jquery.uitablefilter.js"></script>
	<script type="text/javascript">
		$(function() {
			var $t = $('table');
			
			$t.find('th').each(function(index){
				var column = $(this).text();
				if ( index == 0 || index == 2 || index == 3 ) {
					var $search = $('<img src="images/magnifier-small.png" />')
						.css({'vertical-align':'middle', 'cursor':'pointer'})
						.click(function() {	
							$filter.toggle(); 
						});
					var $filter = $('<input type="text" size="10"/>')
						.css({'display':'none', 'font-size':'10px'})
						.keyup(function() {
							$.uiTableFilter( $t, this.value, column );
							$('#nvisible').text($t.find('tbody > tr:visible').size());
						});
					$(this).append($search).append('<br>').append($filter);
				}
			});

			$t.before('<div><i>Showing <b id="nvisible">'+$t.find('tbody > tr:visible').size()+'</b> rows of <b id="ntotal">'+$t.find('tbody > tr:visible').size()+'</b> total</i></div>');
		});  
	</script>
	<table cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo __('GUID') ?></th>
				<th><?php echo __('Last scan') ?></th>
				<th><?php echo __('Last name') ?></th>
				<th><?php echo __('Last IP') ?></th>
				<th><?php echo __('Details') ?></th>
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
					<a class="button" onclick="rconPlayerDetails(<?php echo $log['id'] ?>)"><?php echo __('Details') ?></a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>