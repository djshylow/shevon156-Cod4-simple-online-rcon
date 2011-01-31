<?php
class Controller_Dashboard extends Controller_Main {

	protected $action = NULL;
	private $owned = NULL;
	private $current_server = NULL;
	
	public function after()
    {
        $this->view->navigation = new View('dashboard/navigation');
     
        $this->view->navigation->action = $this->action;
        $this->view->navigation->owned = $this->owned;
        $this->view->navigation->current_server_id = $this->current_server['id'];
        
        parent::after();
    }
	
    public function action_ajaxindex()
    {
        // Ajax?
        if(!Request::$is_ajax)
        {
            exit;
        }

        // Only logged in users
        $this->do_force_login();

        // Get owned servers
        $owned = $this->get_owned_servers();
        $this->owned = $owned;

        // No servers?
        if( empty($owned) )
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        $current_server = $this->get_current_server();
		if(!$current_server)
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }
        $this->current_server = $current_server;

        // Catch them all
        try {
            // Rcon connection
            $rcon = new Rcon($current_server['ip'], $current_server['port'], $current_server['password']);
            $rcon->connect();

            // Commands
            $commands = new Rcon_Commands($rcon);

            $first_team = array(); $second_team = array(); $spectators = array();
            
            // Get server info
            $server_info = $commands->get_server_info();
            $server_info += array('ip' => $current_server['ip'], 'port' => $current_server['port']);
            $server_info['colored_hostname'] = Rcon_Constants::colorize($server_info['sv_hostname']);
            $server_info['playlist_name'] = Rcon_Constants::$playlists[$server_info['playlist']];
            $server_info['map_name'] = Rcon_Constants::$maps[$server_info['map']];
            $server_info['gametype_name'] = Rcon_Constants::$gametypes[$server_info['g_gametype']];
            $server_info['gametype_abbrev'] = Rcon_Constants::$gametypes_abbrev[$server_info['g_gametype']];
            
       		$first_team = array(); $second_team = array(); $spectators = array();
            
	        foreach($server_info['players'] as $player)
		    {
		    	if ( $pos = strpos($player['address'], ':') )
		    	{
		    		$player['ip'] = substr($player['address'], 0, $pos);
		    	}
		    	else
		    	{
		    		$player['ip'] = $player['address'];
		    	}
		        if($player['team'] == 1)
		        {
		            $first_team[] = $player;
		        }
		        elseif($player['team'] == 2)
		        {
		            $second_team[] = $player;
		        }
		        else
		        {
		            $spectators[] = $player;
		        }
		    }
		    $server_info['count_players'] = count($server_info['players']);
		    if ( isset($server_info['players'][0]) && $server_info['players'][0]['team'] == 3 && $server_info['players'][0]['name'] == 'democlient'  )
		    {
		    	$server_info['count_players']--;
		    }
        }
        catch(Exception $e)
        {
            echo json_encode(array('error' => $e->getMessage(), 'content' => ''));
            exit;
        }

        // View
        $view = new View('dashboard/ajax');

        // Server info
        $view->server_info = $server_info;
        $view->permissions = $current_server['permissions'];
        $view->first_team = $first_team;
        $view->second_team = $second_team;
        $view->spectators = $spectators;

        // Owned
        $view->owned = $owned;

        echo json_encode(array('error' => 'None', 'content' => $view->render()));
        exit;
    }

    public function action_kick($id)
    {
        // To int
        if(!ctype_digit($id))
        {
            echo json_encode(array('error' => 'Invalid parameter', 'content' => ''));
            exit;
        }

        $id = (int) $id;

        // Ajax?
        if(!Request::$is_ajax)
        {
            exit;
        }

        // Only logged in users
        $this->do_force_login();

        // Get owned servers
        $owned = DB::select()->from('servers_users')->where('user_id', '=', $this->user->id)->execute();

        // No servers?
        if(count($owned) <= 0)
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        // Default server
        $current_server = 0;

        // Get ID from session
        if(is_int($this->session->get('current_server')) OR ctype_digit($this->session->get('current_server')))
        {
            foreach($owned as $o)
            {
                if($o['server_id'] == (int) $this->session->get('current_server'))
                {
                    $current_server = $o;
                    break;
                }
            }
        }

        // Get default
        if(!$current_server)
        {
            foreach($owned as $o)
            {
                $current_server = $o;
                break;
            }
        }

        // Fetch server data
        $permissions = (int) $current_server['permissions'];
        $current_server = ORM::factory('server', $current_server['server_id']);

        // Found?
        if(!$current_server->loaded())
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        // Can kick
        if(!($permissions & SERVER_KICK))
        {
            echo json_encode(array('error' => 'No permission', 'content' => ''));
            exit;
        }

        // Catch them all
        try {
            // Rcon connection
            $rcon = new Rcon($current_server->ip, $current_server->port, $current_server->password);
            $rcon->connect();

            // Commands
            $commands = new Rcon_Commands($rcon);

            // Kick
            $commands->kick($id);

            $server_info = Kohana::cache('teamstatus');

            if(!$server_info OR !is_array($server_info) OR !isset($server_info['error']) OR $server_info['error'] != '')
            {
                $this->log_action(__('Kicked player with ID #:id', array(':id' => $id)));
            }
            else
            {
                if(isset($server_info['players'][$id]))
                {
                    $this->log_action(__('Kicked player :name', array(
                    ':name' => $server_info['players'][$id]['name']
                    )));
                }
                else
                {
                    $this->log_action(__('Kicked player with ID #:id', array(':id' => (int) $id)));
                }
            }

            echo json_encode(array('error' => 'None', 'content' => ''));
            exit;
        }
        catch(Exception $e)
        {
            echo json_encode(array('error' => $e->getMessage(), 'content' => ''));
            exit;
        }
    }

    public function action_ban($id)
    {
            // To int
        if(!ctype_digit($id))
        {
            echo json_encode(array('error' => 'Invalid parameter', 'content' => ''));
            exit;
        }

        $id = (int) $id;

        // Ajax?
        if(!Request::$is_ajax)
        {
            exit;
        }

        // Only logged in users
        $this->do_force_login();

        // Get owned servers
        $owned = DB::select()->from('servers_users')->where('user_id', '=', $this->user->id)->execute();

        // No servers?
        if(count($owned) <= 0)
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        // Default server
        $current_server = 0;

        // Get ID from session
        if(is_int($this->session->get('current_server')) OR ctype_digit($this->session->get('current_server')))
        {
            foreach($owned as $o)
            {
                if($o['server_id'] == (int) $this->session->get('current_server'))
                {
                    $current_server = $o;
                    break;
                }
            }
        }

        // Get default
        if(!$current_server)
        {
            foreach($owned as $o)
            {
                $current_server = $o;
                break;
            }
        }

            // Fetch server data
        $permissions = (int) $current_server['permissions'];
        $current_server = ORM::factory('server', $current_server['server_id']);

        // Found?
        if(!$current_server->loaded())
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        // Can kick
        if(!($permissions & SERVER_BAN))
        {
            echo json_encode(array('error' => 'No permission', 'content' => ''));
            exit;
        }

        // Catch them all
        try {
            // Rcon connection
            $rcon = new Rcon($current_server->ip, $current_server->port, $current_server->password);
            $rcon->connect();

            // Commands
            $commands = new Rcon_Commands($rcon);

            // Kick
            $commands->ban($id);

                    $server_info = Kohana::cache('teamstatus');

            if(!$server_info OR !is_array($server_info) OR !isset($server_info['error']) OR $server_info['error'] != '')
            {
                $this->log_action(__('Banned player with ID #:id', array(':id' => $id)));
            }
            else
            {
                if(isset($server_info['players'][$id]))
                {
                    $this->log_action(__('Banned player :name', array(
                    ':name' => $server_info['players'][$id]['name']
                    )));
                }
                else
                {
                    $this->log_action(__('Banned player with ID #:id', array(':id' => (int) $id)));
                }
            }

            echo json_encode(array('error' => 'None', 'content' => ''));
            exit;
        }
        catch(Exception $e)
        {
            echo json_encode(array('error' => $e->getMessage(), 'content' => ''));
            exit;
        }
    }

    public function action_tempban($id)
    {
                // To int
        if(!ctype_digit($id))
        {
            echo json_encode(array('error' => 'Invalid parameter', 'content' => ''));
            exit;
        }

        $id = (int) $id;

        // Ajax?
        if(!Request::$is_ajax)
        {
            exit;
        }

        // Only logged in users
        $this->do_force_login();

        // Get owned servers
        $owned = DB::select()->from('servers_users')->where('user_id', '=', $this->user->id)->execute();

        // No servers?
        if(count($owned) <= 0)
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        // Default server
        $current_server = 0;

        // Get ID from session
        if(is_int($this->session->get('current_server')) OR ctype_digit($this->session->get('current_server')))
        {
            foreach($owned as $o)
            {
                if($o['server_id'] == (int) $this->session->get('current_server'))
                {
                    $current_server = $o;
                    break;
                }
            }
        }

        // Get default
        if(!$current_server)
        {
            foreach($owned as $o)
            {
                $current_server = $o;
                break;
            }
        }

        // Fetch server data
        $permissions = (int) $current_server['permissions'];
        $current_server = ORM::factory('server', $current_server['server_id']);

        // Found?
        if(!$current_server->loaded())
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        // Can kick
        if(!($permissions & SERVER_TEMP_BAN))
        {
            echo json_encode(array('error' => 'No permission', 'content' => ''));
            exit;
        }

        // Catch them all
        try {
            // Rcon connection
            $rcon = new Rcon($current_server->ip, $current_server->port, $current_server->password);
            $rcon->connect();

            // Commands
            $commands = new Rcon_Commands($rcon);

            // Kick
            $commands->temp_ban($id);

                    $server_info = Kohana::cache('teamstatus');

            if(!$server_info OR !is_array($server_info) OR !isset($server_info['error']) OR $server_info['error'] != '')
            {
                $this->log_action(__('Temp-banned player with ID #:id', array(':id' => $id)));
            }
            else
            {
                if(isset($server_info['players'][$id]))
                {
                    $this->log_action(__('Temp-banned player :name', array(
                    ':name' => $server_info['players'][$id]['name']
                    )));
                }
                else
                {
                    $this->log_action(__('Temp-banned player with ID #:id', array(':id' => (int) $id)));
                }
            }

            echo json_encode(array('error' => 'None', 'content' => ''));
            exit;
        }
        catch(Exception $e)
        {
            echo json_encode(array('error' => $e->getMessage(), 'content' => ''));
            exit;
        }
    }

    public function action_index()
    {
    	$this->action = 'index';
    	
        // Only logged in users
        $this->do_force_login();
        
        // Title
        $this->title = __('Remote console');

        $owned = $this->get_owned_servers();
        $this->owned = $owned;
        
        if ( empty($owned) )
        {
        	$this->view = new View('dashboard/noservers');
            return;
        }
        
		$current_server = $this->get_current_server();
		if(!$current_server)
        {
            throw new Kohana_Exception('Invalid server');
        }
        $this->current_server = $current_server;

        // Rcon connection
        $rcon = new Rcon($current_server['ip'], $current_server['port'], $current_server['password']);
        $rcon->connect();

        // Commands
        $commands = new Rcon_Commands($rcon);

        try {
        	$first_team = array(); $second_team = array(); $spectators = array();
            // Get server info
            $server_info = $commands->get_server_info();
            $server_info += array('ip' => $current_server['ip'], 'port' => $current_server['port']);
            $server_info['colored_hostname'] = Rcon_Constants::colorize($server_info['sv_hostname']);
            $server_info['playlist_name'] = Rcon_Constants::$playlists[$server_info['playlist']];
            $server_info['map_name'] = Rcon_Constants::$maps[$server_info['map']];
            $server_info['gametype_name'] = Rcon_Constants::$gametypes[$server_info['g_gametype']];
            $server_info['gametype_abbrev'] = Rcon_Constants::$gametypes_abbrev[$server_info['g_gametype']];
            
	        foreach($server_info['players'] as $player)
		    {
		    	if ( $pos = strpos($player['address'], ':') )
		    	{
		    		$player['ip'] = substr($player['address'], 0, $pos);
		    	}
		    	else
		    	{
		    		$player['ip'] = $player['address'];
		    	}
		        if($player['team'] == 1)
		        {
		            $first_team[] = $player;
		        }
		        elseif($player['team'] == 2)
		        {
		            $second_team[] = $player;
		        }
		        else
		        {
		            $spectators[] = $player;
		        }
		    }
		    $server_info['count_players'] = count($server_info['players']);
		    if ( isset($server_info['players'][0]) && $server_info['players'][0]['team'] == 3 && $server_info['players'][0]['name'] == 'democlient'  )
		    {
		    	$server_info['count_players']--;
		    }
        }
        catch(Exception $e) {
           // Default
           $server_info = array('map' => '', 'players' => array(), 'error' => $e->getMessage());
        }

        // View
        $this->view = new View('dashboard/index');

        // Server info
        $this->view->server_info = $server_info;
        $this->view->permissions = (int) $current_server['permissions'];
        $this->view->current_server_id = $current_server['id'];
        $this->view->first_team = $first_team;
        $this->view->second_team = $second_team;
        $this->view->spectators = $spectators;

        // Owned
        $this->view->owned = $owned;
    }

    public function action_message()
    {
        // To int
        if(!isset($_POST['message']) OR !isset($_POST['target']) OR (!ctype_digit($_POST['target']) AND $_POST['target'] != 'all'))
        {
            echo json_encode(array('error' => 'Invalid parameter', 'content' => ''));
            exit;
        }

        // Ajax?
        if(!Request::$is_ajax)
        {
            exit;
        }

        // Only logged in users
        $this->do_force_login();

        // Get owned servers
        $owned = DB::select()->from('servers_users')->where('user_id', '=', $this->user->id)->execute();

        // No servers?
        if(count($owned) <= 0)
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        // Default server
        $current_server = 0;

        // Get ID from session
        if(is_int($this->session->get('current_server')) OR ctype_digit($this->session->get('current_server')))
        {
            foreach($owned as $o)
            {
                if($o['server_id'] == (int) $this->session->get('current_server'))
                {
                    $current_server = $o;
                    break;
                }
            }
        }

        // Get default
        if(!$current_server)
        {
            foreach($owned as $o)
            {
                $current_server = $o;
                break;
            }
        }

        // Fetch server data
        $permissions = (int) $current_server['permissions'];
        $current_server = ORM::factory('server', $current_server['server_id']);

        // Found?
        if(!$current_server->loaded())
        {
            echo json_encode(array('error' => 'Invalid server', 'content' => ''));
            exit;
        }

        // Can kick
        if(!($permissions & SERVER_MESSAGE))
        {
            echo json_encode(array('error' => 'No permission', 'content' => ''));
            exit;
        }

        // Catch them all
        try {
            // Rcon connection
            $rcon = new Rcon($current_server->ip, $current_server->port, $current_server->password);
            $rcon->connect();

            // Commands
            $commands = new Rcon_Commands($rcon);

            if($_POST['target'] == 'all')
            {
                $this->log_action(__('Sent global message'));
            }
            else
            {
                $server_info = Kohana::cache('teamstatus');
                if(!$server_info OR !is_array($server_info) OR !isset($server_info['error']) OR $server_info['error'] != '')
                {
                    $this->log_action(__('Sent private message to #:id player', array(':id' => (int) $_POST['target'])));
                }
                else
                {
                    if(isset($server_info['players'][(int) $_POST['target']]))
                    {
                        $this->log_action(__('Sent private message to :name', array(
                        ':name' => $server_info['players'][(int) $_POST['target']]['name']
                        )));
                    }
                    else
                    {
                        $this->log_action(__('Sent private message to #:id player', array(':id' => (int) $_POST['target'])));
                    }
                }
            }

            // Kick
            $commands->message($_POST['message'], $_POST['target']);

            echo json_encode(array('error' => 'None', 'content' => ''));
            exit;
        }
        catch(Exception $e)
        {
            echo json_encode(array('error' => $e->getMessage(), 'content' => ''));
            exit;
        }
    }

    public function action_set_server($id = NULL)
    {	
        // Invalid request
        if( (!isset($_POST['server']) OR !ctype_digit($_POST['server']))
        	AND
        	(!isset($_GET['server']) OR !ctype_digit($_GET['server']))
        	AND
        	!ctype_digit($id) 
        )
        {
            throw new Kohana_Exception('Invalid request');
        }

        // Only logged in users
        $this->do_force_login();

        // ID
        $id = (int) ( 
        			isset($_POST['server']) ? $_POST['server'] : 
        			( isset($_GET['server']) ? $_GET['server'] : 
       					$id ) 
        			);
        $found = FALSE;

        // Get owned servers
        $owned = DB::select()->from('servers_users')->where('user_id', '=', $this->user->id)->execute();

        // Iterate
        foreach($owned as $o)
        {
            if($o['server_id'] == $id)
            {
                $found = TRUE;
                break;
            }
        }

        // Found?
        if(!$found)
        {
            throw new Kohana_Exception('Invalid server');
        }

        // Free memory
        unset($found);

        // Now try fetch it
        $id = ORM::factory('server', $id);

        // Found?
        if(!$id->loaded())
        {
            throw new Kohana_Exception('Invalid server');
        }

        // Set
        $this->session->set('current_server', $id->id);

        // Redirect
        $this->request->redirect('dashboard/index');
    }

    public function action_msgrotation($id = NULL)
    {
    	$this->action = 'msgrotation';
    	
        // Only logged in users
        $this->do_force_login();

        // Title
        $this->title = __('Message rotation');

        // Get owned servers
        $owned = DB::select()->from('servers_users')->where('user_id', '=', $this->user->id)->execute();

        // No servers?
        if(count($owned) <= 0)
        {
            $this->view = new View('dashboard/noservers');
            return;
        }

     	$owned = $this->get_owned_servers();
        $this->owned = $owned;
        
        if ( empty($owned) )
        {
        	$this->view = new View('dashboard/noservers');
            return;
        }
        
		$current_server = $this->get_current_server();
		if(!$current_server)
        {
            throw new Kohana_Exception('Invalid server');
        }
        $this->current_server = $current_server;

        // Check permissions
        if(!( ( (int)$current_server['permissions'] ) & SERVER_MESSAGE))
        {
            throw new Kohana_Exception('No permissions');
        }

        // Form submit
        if(isset($_POST['submit']) AND isset($_POST['message']))
        {
            DB::insert('messages', array('user_id', 'server_id', 'message'))->values(array(
                $this->user->id, (int) $current_server['id'], Security::xss_clean($_POST['message'])
            ))->execute();

            $this->notice(__('Message added'));
            $this->log_action(__('Added message to rotation'));

            $this->request->redirect('dashboard/msgrotation');
        }

        // Remove
        if($id !== NULL AND ctype_digit($id))
        {
            // Find
            $message = new Model_Message((int) $id);

            // Found?
            if(!$message->loaded() OR $message->server_id != $current_server['id'])
            {
                throw new Exception('Not found');
            }

            // Change rotation
            if($message->current == '1')
            {
                $random = ORM::factory('message')->where('current', '=', '0')->find();

                if($random->loaded())
                {
                    $random->current = '1';

                    $random->save();
                }
            }

            // Delete
            $message->delete();

            $this->notice(__('Message removed'));
            $this->log_action(__('Removed rotation message'));

            $this->request->redirect('dashboard/msgrotation');
        }

        // View
        $this->view = new View('dashboard/messages');

        // Messages
        $this->view->messages = DB::select('messages.id', 'messages.message', 'users.username')->where('server_id', '=', $current_server['id'])
                                ->join('users', 'LEFT')->on('users.id', '=', 'messages.user_id')
                                ->from('messages')->order_by('messages.id', 'DESC')->execute();
    }

    public function action_logs($guid = NULL)
    {
    	$this->action = 'logs';
    	
        // Only logged in users
        $this->do_force_login();

        // Title
        $this->title = __('Player logs');

        // Get owned servers
        $owned = DB::select()->from('servers_users')->where('user_id', '=', $this->user->id)->execute();

        // No servers?
        if(count($owned) <= 0)
        {
            $this->view = new View('dashboard/noservers');
            return;
        }

     	$owned = $this->get_owned_servers();
        $this->owned = $owned;
        
        if ( empty($owned) )
        {
        	$this->view = new View('dashboard/noservers');
            return;
        }
        
		$current_server = $this->get_current_server();
		if(!$current_server)
        {
            throw new Kohana_Exception('Invalid server');
        }
        $this->current_server = $current_server;

        // Check permissions
        if(!( ( (int)$current_server['permissions'] ) & SERVER_USER_LOG))
        {
            throw new Kohana_Exception('No permissions');
        }

        // Ajax
        if(Request::$is_ajax AND $guid !== NULL AND ctype_digit($guid))
        {

            // Find
            $log = DB::select('names', 'ip_addresses')->where('server_id', '=', $current_server['id'])
                   ->where('id', '=', (int) $guid)->from('players')->execute();

            // Valid
            if(count($log))
            {
                $log = $log->as_array();
                $log = current($log);

                $log['names'] = empty($log['names']) ? array() : unserialize($log['names']);
                $log['ip_addresses'] = empty($log['ip_addresses']) ? array() : unserialize($log['ip_addresses']);
                array_map('strip_tags', $log['names']);
                echo json_encode(array('error' => 'None', 'ip' => '<li>'.implode('</li><li>', $log['ip_addresses']).'</li>',
                                       'names' => '<li>'.implode('</li><li>', $log['names']).'</li>'));
            }
            else
            {
                echo json_encode(array('error' => 'Log not found'));
            }

            // Exit
            exit;
        }

        // View
        $this->view = new View('dashboard/logs');

        // Logs
        $this->view->logs = DB::select('*')->where('server_id', '=', $current_server['id'])->from('players')->order_by('last_update', 'DESC')->execute();
    }
    
    public function action_playlists($playlist_id_to_switch = NULL, $active = NULL)
    {
    	$this->action = 'playlists';
    	
		// Only logged in users
        $this->do_force_login();

        // Title
        $this->title = __('Playlists');

		// Get owned servers
     	$owned = $this->get_owned_servers();
        $this->owned = $owned;
        
        if ( empty($owned) )
        {
        	$this->view = new View('dashboard/noservers');
            return;
        }
        
		$current_server = $this->get_current_server();
		if(!$current_server)
        {
            throw new Kohana_Exception('Invalid server');
        }
        $this->current_server = $current_server;

        // Check permissions
        if(!( ( (int)$current_server['permissions'] ) & SERVER_PLAYLIST))
        {
            throw new Kohana_Exception('No permissions');
        }
        
    	// Fill all playlists
		$all_playlists_res = DB::select()->from('playlists')->where('gametype_codename', 'IN', array('tdm','dm','ctf','sd','koth','dom','sab','dem'))
								/*->order_by('gamemode_codename')->order_by('size')*/->execute();
		
		if ( count($all_playlists_res) <=0 )
		{
			throw new Kohana_Exception('List of playlists not found');
		}
		
		$all_playlists_ids = array_keys($all_playlists_res->as_array('id'));
		
		// Switch playlist activity
		if ( ctype_digit($playlist_id_to_switch) && ($active == 0 || $active == 1) )
		{
			if ( !in_array((int) $playlist_id_to_switch, $all_playlists_ids) )
			{
				$this->notice('Invalid playlist specified');
				$this->request->redirect('dashboard/playlists');
			}
			
			DB::update('servers_playlists')->set(array('is_active'=>(int)$active))
	            	->where('server_id','=',(int) $current_server['id'])
	            	->where('server_playlist_id','=',$playlist_id_to_switch)->execute();
	        if ( $active )
	        {
	        	DB::update('servers_playlists')->set(array('is_active'=>0))
	            	->where('server_id','=',(int) $current_server['id'])
	            	->where('server_playlist_id','<>',$playlist_id_to_switch)->execute();
	        }
	        
	        $this->notice('Custom playlist '.($active?'activated':'desactivated'));
			$this->request->redirect('dashboard/playlists');
		}
        
		// Add new custom playlist
        if ( isset($_POST['playlists_ids']) && is_array($_POST['playlists_ids']) && !empty($_POST['playlists_ids']) )
        {
        	$playlist_name = $_POST['playlist_name'];
        	$make_active = isset($_POST['make_active']);
        	
        	$playlists_ids = array_unique($_POST['playlists_ids']);
			
        	foreach ($playlists_ids as $playlist_id)
        	{
        		if ( !in_array($playlist_id, $all_playlists_ids) )
        		{
        			$this->notice('Invalid default playlist specified');
					$this->request->redirect('dashboard/playlists');
        		}
        	}
        	
       		if(count(DB::select()->from('servers_playlists')->where('server_id', '=', (int) $current_server['id'])
       														->where('server_playlist_name', '=', Security::xss_clean($playlist_name))
       														->execute()))
            {
                $this->notice('This playlist name already exists');
                $this->request->redirect('dashboard/playlists');
            }
            
            $res = DB::insert('servers_playlists', array('server_id', 'server_playlist_name', 'is_active'))->values(array(
            	(int) $current_server['id'], Security::xss_clean($playlist_name), $make_active
            ))->execute();
            
            $server_playlist_id = $res[0];
            
            if ( $make_active )
            {
	            DB::update('servers_playlists')->set(array('is_active'=>0))
	            	->where('server_id','=',(int) $current_server['id'])
	            	->where('server_playlist_id','<>',$server_playlist_id);
			}
            
            $insert = DB::insert('custom_playlists', array('server_playlist_id', 'server_id', 'playlist_id'));
            
        	foreach ( $playlists_ids as $playlist_id )
            {
            	$insert->values(array($server_playlist_id, $current_server['id'], $playlist_id));
            }
            
            $insert->execute();
        	
        	$this->notice('Custom playlist added');
			$this->request->redirect('dashboard/playlists');
        }
        
        // Fill custom playlists
        $playlists = array();
        
        $custom_playlists = DB::select('server_playlist_id', 'server_playlist_name', 'is_active')->from('servers_playlists')
        						->where('server_id', '=', $current_server['id'])
                                ->execute();
        foreach ($custom_playlists as $custom_playlist)
        {
        	$playlists_in_custom_playlist = DB::select('custom_playlists.playlist_id', 'playlists.name', 'playlists.gametype_codename', 'playlists.gamemode_codename', 'playlists.size')
				        					->from('custom_playlists')->where('server_id', '=', $current_server['id'])->where('server_playlist_id', '=', $custom_playlist['server_playlist_id'])
				        					->join('playlists')->on('custom_playlists.playlist_id', '=', 'playlists.id')
				        					->execute();
			
        	$gametypes = array();
        	foreach ($playlists_in_custom_playlist as $playlist)
        	{
        		$gametypes[] = array(
        			'name' => $playlist['name'],
        			'abbrev' => Rcon_Constants::$gametypes_abbrev[$playlist['gametype_codename']],
        			'mode' => $playlist['gamemode_codename'],
        		);
        	}
        						
        	$playlists[] = array(
        		'id' => $custom_playlist['server_playlist_id'],
        		'name' => $custom_playlist['server_playlist_name'],
        		'is_active' => $custom_playlist['is_active'],
        		'gametypes' => $gametypes,
        	);
        }
        
		$grouped_playlists = array(
			'normal'=>array(),
			'hardcore'=>array(),
			'barebones'=>array()
		);
		foreach ($all_playlists_res as $playlist)
		{
			$grouped_playlists[ $playlist['gamemode_codename'] ] += array( $playlist['id'] => $playlist['name'] );
		}
    	
		$this->view = new View('dashboard/playlists');
		
		$this->view->playlists = $playlists;
		$this->view->grouped_playlists = $grouped_playlists;
    }
    
    public function action_playlist_edit($server_playlist_id)
    {
    	$this->action = 'playlists';
    	
    	if ( !ctype_digit($server_playlist_id) )
        {
            throw new Kohana_Exception('Invalid request');
        }
        
        // Only logged in users
        $this->do_force_login();
        
    	// get owned, get current, check permissions
    	
	    // Get owned servers
     	$owned = $this->get_owned_servers();
        $this->owned = $owned;
        
        if ( empty($owned) )
        {
        	$this->view = new View('dashboard/noservers');
            return;
        }
        
		$current_server = $this->get_current_server();
		if(!$current_server)
        {
            throw new Kohana_Exception('Invalid server');
        }
        $this->current_server = $current_server;

        // Check permissions
        if(!( ( (int)$current_server['permissions'] ) & SERVER_PLAYLIST))
        {
            throw new Kohana_Exception('No permissions');
        }
        
        $playlist_info = DB::select('server_playlist_name')->from('servers_playlists')->where('server_playlist_id', '=', (int) $server_playlist_id)
       														->where('server_id', '=', (int) $current_server['id'])
       														->execute();
        
    	if(!count($playlist_info))
		{
			$this->notice('Invalid playlist');
			$this->request->redirect('dashboard/playlists');
		}
		$playlist_info = $playlist_info->as_array();
        $playlist_info = current($playlist_info);
        
    	// Fill all playlists
		$all_playlists_res = DB::select()->from('playlists')->where('gametype_codename', 'IN', array('tdm','dm','ctf','sd','koth','dom','sab','dem'))
								/*->order_by('gamemode_codename')->order_by('size')*/->execute();
		
		if ( count($all_playlists_res) <=0 )
		{
			throw new Kohana_Exception('List of playlists not found');
		}
		
    	$grouped_playlists = array(
			'normal'=>array(),
			'hardcore'=>array(),
			'barebones'=>array()
		);
		foreach ($all_playlists_res as $value)
		{
			$grouped_playlists[ $value['gamemode_codename'] ] += array( $value['id'] => $value['name'] );
		}
		
		// Process post
		if ( isset($_POST['playlists_ids']) && is_array($_POST['playlists_ids']) && !empty($_POST['playlists_ids']) )
        {
        	$playlist_name = Security::xss_clean($_POST['playlist_name']);
        	
        	$playlists_ids = array_unique($_POST['playlists_ids']);
			$all_playlists_ids = array_keys($all_playlists_res->as_array('id'));
        	
        	foreach ($playlists_ids as $playlist_id)
        	{
        		if ( !in_array($playlist_id, $all_playlists_ids) )
        		{
        			$this->notice('Invalid default playlist specified');
					$this->request->redirect('dashboard/playlists');
        		}
        	}
        	
       		if(count(DB::select()->from('servers_playlists')->where('server_id', '=', (int) $current_server['id'])
       														->where('server_playlist_name', '=', $playlist_name)
       														->where('server_playlist_id', '<>', (int) $server_playlist_id)
       														->execute()))
            {
                $this->notice('This playlist name already exists');
                $this->request->redirect('dashboard/playlists');
            }
            
            if ( $playlist_name <> $playlist_info['server_playlist_name'])
            {
            	DB::update('servers_playlists')->set(array('server_playlist_name' => $playlist_name))
            									->where('server_playlist_id', '=', (int) $server_playlist_id)
        										->where('server_id', '=', (int) $current_server['id'])
        		->execute();
            }
            
            // Delete old, insert new
            $res = DB::delete('custom_playlists')->where('server_playlist_id', '=', (int) $server_playlist_id)
        										->where('server_id', '=', (int) $current_server['id'])
        	->execute();
            
			$insert = DB::insert('custom_playlists', array('server_playlist_id', 'server_id', 'playlist_id'));
            
        	foreach ( $playlists_ids as $playlist_id )
            {
            	$insert->values(array($server_playlist_id, $current_server['id'], $playlist_id));
            }
            
            $insert->execute();
        	
        	$this->notice('Playlist saved');
			$this->request->redirect('dashboard/playlists');
        }
		
		$this->title = 'Playlist edit';
		
		$playlists_in_custom_playlist = DB::select('custom_playlists.playlist_id', 'playlists.name')
				        					->from('custom_playlists')->where('server_id', '=', (int) $current_server['id'])->where('server_playlist_id', '=', (int) $server_playlist_id)
				        					->join('playlists')->on('custom_playlists.playlist_id', '=', 'playlists.id')
				        					->execute();
		
		$playlist['id'] = $server_playlist_id;
		$playlist['name'] = $playlist_info['server_playlist_name'];
		$playlist['playlists'] = $playlists_in_custom_playlist->as_array();
		
		$this->view = new View('dashboard/playlist_edit');
		$this->view->playlist = $playlist;
		$this->view->grouped_playlists = $grouped_playlists;
    }
    
    public function action_playlist_delete($server_playlist_id)
    {
    	if ( !ctype_digit($server_playlist_id) )
        {
            throw new Kohana_Exception('Invalid request');
        }
        
        // Only logged in users
        $this->do_force_login();
        
    	// get owned, get current, check permissions
    	
	    // Get owned servers
     	$owned = $this->get_owned_servers();
        $this->owned = $owned;
        
        if ( empty($owned) )
        {
        	$this->view = new View('dashboard/noservers');
            return;
        }
        
		$current_server = $this->get_current_server();
		if(!$current_server)
        {
            throw new Kohana_Exception('Invalid server');
        }
        $this->current_server = $current_server;

        // Check permissions
        if(!( ( (int)$current_server['permissions'] ) & SERVER_PLAYLIST))
        {
            throw new Kohana_Exception('No permissions');
        }
        
        $res = DB::delete('servers_playlists')->where('server_playlist_id', '=', (int) $server_playlist_id)
        										->where('server_id', '=', (int) $current_server['id'])
        	->execute();
        
        $this->notice('Custom playlist deleted');
		$this->request->redirect('dashboard/playlists');
    }
    
    protected function get_owned_servers()
    {
    	$owned = DB::select('server_id', 'permissions')->from('servers_users')->where('user_id', '=', $this->user->id)->execute();
    	
    	if(count($owned) <= 0)
    	{
    		return array();
    	}
    	
	    // Get available servers
        $servers = array();

        // Iterate
        foreach($owned as $o)
        {
            $servers[(int) $o['server_id']] = $o;
        }

        // Get server names
        foreach(DB::select('id', 'name')->from('servers')->where('id', 'IN', array_keys($servers))->execute() as $serv)
        {
            $servers[$serv['id']] += $serv;
        }
        
        return $servers;
    }
    
    protected function get_current_server()
    {
    	// Default server
        $current_server = 0;

        // Get ID from session and check if the user ownes the current
        if(is_int($this->session->get('current_server')) OR ctype_digit($this->session->get('current_server')))
        {
            foreach($this->owned as $o)
            {
                if($o['server_id'] == (int) $this->session->get('current_server'))
                {
                    $current_server = $o;
                    break;
                }
            }
        }

        // Get default
        if(!$current_server)
        {
            foreach($this->owned as $o)
            {
                $current_server = $o;
                break;
            }
        }
        
        // Get all the info (id, name, ip, port, password) for current server
        $server = DB::select()->from('servers')->where('id', '=', $current_server['server_id'])->execute();
        
        if ( count($server)<=0 )
        {
        	return false;
        }
        
        $current_server += $server[0];
        
        return $current_server;
    }
}