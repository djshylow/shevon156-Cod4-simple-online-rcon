<div id="leftblock">
	<h2>Navigation</h2>
    <ul class="leftmenu">
        <li class="active"><a><?php echo __('Players list') ?></a></li>
        <li onclick="rconRefresh()" style="background-image: url(images/refresh.png)"><a onclick="rconRefresh()"><?php echo __('Refresh player list') ?></a></li>
    	<li style="background-image: url(images/log.png)"><a href="<?php echo URL::site('dashboard/logs') ?>">Player log</a></li>
		<li style="background-image: url(images/msg.png)"><a href="<?php echo URL::site('dashboard/msgrotation') ?>">Message rotation</a></li>
    </ul>
</div>
<div id="rightblock">
<?php $first = array(); $second = array(); $spectators = array(); ?>
<?php
    foreach($server_info['players'] as $player)
    {
        if($player['team'] == 1)
        {
            $first[] = $player;
        }
        elseif($player['team'] == 2)
        {
            $second[] = $player;
        }
        else
        {
            $spectators[] = $player;
        }
    }
?>
<h2><?php echo __('Server info') ?></h2>
<div id="refreshcontainer">
<?php if($server_info['error']): ?>
<div class="message error">
    <?php echo strip_tags($server_info['error']) ?>
</div>
<?php endif; ?>
<h3>Map: <?php echo $server_info['map'] ?></h3>
<h3><?php echo __('First team') ?></h3>
<table cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <td style="width: 5%">ID</td>
            <td style="width: 25%">Name</td>
            <td style="width: 5%">Ping</td>
            <td style="width: 15%">IP</td>
            <td style="width: 5%">Score</td>
            <td style="width: 15%">GUID</td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($first as $player): ?>
        <tr>
            <td><?php echo (int) $player['id']?></td>
            <td><?php echo strip_tags($player['name']) ?></td>
            <td><?php echo htmlspecialchars($player['ping'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?php echo htmlspecialchars($player['address'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?php echo (int) $player['score'] ?></td>
            <td><?php echo (int) $player['guid'] ?></td>
            <td>
                <?php if(($permissions & SERVER_KICK)): ?>
                <a class="button" style="background-image: url(images/kick.png)" onclick="rconKick(<?php echo (int) $player['id'] ?>)">Kick</a>
                <?php endif; ?>
                <?php if(($permissions & SERVER_BAN)): ?>
                <a class="button" style="background-image: url(images/banhammer.png)" onclick="rconBan(<?php echo (int) $player['id'] ?>)">Ban</a>
                <?php endif; ?>
                <?php if(($permissions & SERVER_TEMP_BAN)): ?>
                <a class="button" style="background-image: url(images/tempban.png)" onclick="rconTempBan(<?php echo (int) $player['id'] ?>)">Temp ban</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h3><?php echo __('Second team') ?></h3>
<table cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <td style="width: 5%">ID</td>
            <td style="width: 25%">Name</td>
            <td style="width: 5%">Ping</td>
            <td style="width: 15%">IP</td>
            <td style="width: 5%">Score</td>
            <td style="width: 15%">GUID</td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($second as $player): ?>
        <tr>
            <td><?php echo (int) $player['id']?></td>
            <td><?php echo strip_tags($player['name']) ?></td>
            <td><?php echo htmlspecialchars($player['ping'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?php echo htmlspecialchars($player['address'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?php echo (int) $player['score'] ?></td>
            <td><?php echo (int) $player['guid'] ?></td>
            <td>
                <?php if(($permissions & SERVER_KICK)): ?>
                <a class="button" style="background-image: url(images/kick.png)" onclick="rconKick(<?php echo (int) $player['id'] ?>)">Kick</a>
                <?php endif; ?>
                <?php if(($permissions & SERVER_BAN)): ?>
                <a class="button" style="background-image: url(images/banhammer.png)" onclick="rconBan(<?php echo (int) $player['id'] ?>)">Ban</a>
                <?php endif; ?>
                <?php if(($permissions & SERVER_TEMP_BAN)): ?>
                <a class="button" style="background-image: url(images/tempban.png)" onclick="rconTempBan(<?php echo (int) $player['id'] ?>)">Temp ban</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h3><?php echo __('Spectators') ?></h3>
<table cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <td style="width: 5%">ID</td>
            <td style="width: 25%">Name</td>
            <td style="width: 5%">Ping</td>
            <td style="width: 15%">IP</td>
            <td style="width: 5%">Score</td>
            <td style="width: 15%">GUID</td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($spectators as $player): ?>
        <tr>
            <td><?php echo (int) $player['id']?></td>
            <td><?php echo strip_tags($player['name']) ?></td>
            <td><?php echo htmlspecialchars($player['ping'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?php echo htmlspecialchars($player['address'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?php echo (int) $player['score'] ?></td>
            <td><?php echo (int) $player['guid'] ?></td>
            <td>
                <?php if(($permissions & SERVER_KICK)): ?>
                <a class="button" style="background-image: url(images/kick.png)" onclick="rconKick(<?php echo (int) $player['id'] ?>)">Kick</a>
                <?php endif; ?>
                <?php if(($permissions & SERVER_BAN)): ?>
                <a class="button" style="background-image: url(images/banhammer.png)" onclick="rconBan(<?php echo (int) $player['id'] ?>)">Ban</a>
                <?php endif; ?>
                <?php if(($permissions & SERVER_TEMP_BAN)): ?>
                <a class="button" style="background-image: url(images/tempban.png)" onclick="rconTempBan(<?php echo (int) $player['id'] ?>)">Temp ban</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<h2>Select server</h2>
<div class="group">
    <div class="content">
        <form action="<?php echo URL::site('dashboard/set_server') ?>" method="post">
            <div>
                <label for="server"><?php echo __('Servers') ?>:</label>
                <select name="server" id="server">
                    <?php foreach($owned as $serv): ?>
                    <option value="<?php echo $serv['id'] ?>"<?php if($permissions['server_id'] == $serv['id']): ?> selected="selected"<?php endif; ?>><?php echo $serv['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <input type="submit" style="width: auto" name="submit" value="<?php echo __('Submit') ?>" />
            </div>
        </form>
    </div>
</div>
<?php if($permissions & SERVER_MESSAGE): ?>
<h2>Message</h2>
<div class="group">
    <div class="content">
        <div>
            <label for="msg-message"><?php echo __('Message') ?>:</label>
            <input type="text" name="msg-message" id="msg-message" />
        </div>
        <div>
        	<label for="msg-target"><?php echo __('Target') ?></label>
        	<select name="msg-target" id="msg-target">
        		<option value="all">Global</option>
        		<?php foreach($server_info['players'] as $player): ?>
        		<option value="<?php echo $player['id'] ?>"><?php echo Security::xss_clean($player['name']) ?></option>
        		<?php endforeach; ?>
        	</select>
        </div>
        <div>
            <input type="button" onclick="rconMessage()" id="msg-submit" style="width: auto" name="msg-submit" value="<?php echo __('Send') ?>" />
        </div>
    </div>
</div>
<?php endif; ?>
</div>