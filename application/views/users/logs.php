<div id="leftblock">
	 <h2><?php echo __('Navigation') ?></h2>
    <ul class="leftmenu">
        <li><a href="<?php echo URL::site('users/index') ?>"><?php echo __('Manage users') ?></a></li>
    	<li class="active" style="background-image: url(images/log.png)"><a><?php echo __('Actions log') ?></a></li>
    </ul>
</div>
<div id="rightblock">
    <h2><?php echo __('Logs') ?></h2>
    <script type="text/javascript" src="<?php echo URL::base() ?>jquery.uitablefilter.js"></script>
    <script type="text/javascript">
    	$(function() {
    		var $t = $('table');

    		$t.find('th').each(function(index){
    			var column = $(this).text();
    			if ( index == 1 || index == 2 || index == 3 ) {
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
    			<th><?php echo __('ID') ?></th>
    			<th><?php echo __('Username') ?></th>
    			<th><?php echo __('Date') ?></th>
    			<th><?php echo __('Actions') ?></th>
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