<?php
class Controller_Servers extends Controller_Main {

    public function before()
    {
        parent::before();
        $this->tab = 'servers';

        $this->do_force_login('servers');
    }

    public function action_permissions()
    {
        $this->title = __('Servers permissions');

        $servers = array(); $users = array();

        foreach(ORM::factory('server')->find_all() as $s)
        {
            $servers[$s->id] = $s->name;
        }

        foreach(ORM::factory('user')->find_all() as $s)
        {
            $users[$s->id] = $s->username;
        }

        if(isset($_POST['user_id']) AND isset($_POST['server_id']) AND ctype_digit($_POST['user_id']) AND ctype_digit($_POST['server_id']))
        {
            $server_id = (int) $_POST['server_id'];
            $user_id = (int) $_POST['user_id'];

            if(!isset($servers[$server_id]) OR !isset($users[$user_id]))
            {
                $this->notice('Invalid user/server');
                $this->request->redirect('servers/permissions');
            }

            if(count(DB::select('user_id')->from('servers_users')->where('user_id', '=', $user_id)->where('server_id', '=', $server_id)->execute()))
            {
                $this->notice('Already exists');
                $this->request->redirect('servers/permissions');
            }

            $permissions = 0;

            if(isset($_POST['can_kick']) AND $_POST['can_kick'] == '1')
            {
                $permissions |= SERVER_KICK;
            }

            if(isset($_POST['can_ban']) AND $_POST['can_ban'] == '1')
            {
                $permissions |= SERVER_BAN;
            }

            if(isset($_POST['can_temp_ban']) AND $_POST['can_temp_ban'] == '1')
            {
                $permissions |= SERVER_TEMP_BAN;
            }

            if(isset($_POST['can_messages']) AND $_POST['can_messages'] == '1')
            {
                $permissions |= SERVER_MESSAGE;
            }

        	if(isset($_POST['can_message_rotation']) AND $_POST['can_message_rotation'] == '1')
            {
                $permissions |= SERVER_MESSAGE_ROTATION;
            }

            if(isset($_POST['can_logs']) AND $_POST['can_logs'] == '1')
            {
                $permissions |= SERVER_USER_LOG;
            }
            
        	if(isset($_POST['can_playlists']) AND $_POST['can_playlists'] == '1')
            {
                $permissions |= SERVER_PLAYLIST;
            }

            DB::insert('servers_users', array('user_id', 'server_id', 'permissions'))
                      ->values(array(
                'user_id' => $user_id,
                'server_id' => $server_id,
                'permissions' => $permissions,
            ))->execute();

            $this->log_action(__('Added permissions to user :user for server: :server', array(
                ':user' => ORM::factory('user', $user_id)->username,
                ':server' => ORM::factory('server', $server_id)->name,
            )));

			$this->notice('Permissions added');
			$this->request->redirect('servers/permissions');
        }

        $this->view = new View('servers/permissions');
        $this->view->list = DB::select()->from('servers_users')->execute();

        $this->view->servers = $servers;
        $this->view->users = $users;
    }

    public function action_permissions_edit($user_id, $server_id)
    {
    	// Validate ID
        if(!ctype_digit($user_id) OR !ctype_digit($server_id))
        {
            throw new Kohana_Exception('Invalid parameter');
        }

        $user_id = (int) $user_id; $server_id = (int) $server_id;
        
		$server = ORM::factory('server', $server_id);
		$user = ORM::factory('user', $user_id);

		if(!$server->loaded() OR !$user->loaded())
		{
			throw new Kohana_Exception('Invalid parameter');
		}
		
		// Process POST
		if ( isset($_POST['submit']) )
		{
			$permissions = 0;
			
			if(isset($_POST['can_kick']) AND $_POST['can_kick'] == '1')
            {
                $permissions |= SERVER_KICK;
            }

            if(isset($_POST['can_ban']) AND $_POST['can_ban'] == '1')
            {
                $permissions |= SERVER_BAN;
            }

            if(isset($_POST['can_temp_ban']) AND $_POST['can_temp_ban'] == '1')
            {
                $permissions |= SERVER_TEMP_BAN;
            }

            if(isset($_POST['can_messages']) AND $_POST['can_messages'] == '1')
            {
                $permissions |= SERVER_MESSAGE;
            }
            
			if(isset($_POST['can_message_rotation']) AND $_POST['can_message_rotation'] == '1')
            {
                $permissions |= SERVER_MESSAGE_ROTATION;
            }

            if(isset($_POST['can_logs']) AND $_POST['can_logs'] == '1')
            {
                $permissions |= SERVER_USER_LOG;
            }
            
        	if(isset($_POST['can_playlists']) AND $_POST['can_playlists'] == '1')
            {
                $permissions |= SERVER_PLAYLIST;
            }
            
            DB::update('servers_users')->set(array('permissions'=>$permissions))
            	->where('user_id', '=', $user_id)
            	->where('server_id', '=', $server_id)
            ->execute();
            
			$this->log_action(__('Edited :user permissions for server: :server', array(':user' => $user->username, ':server' => $server->name)));
			
			$this->notice('Permissions saved');
			$this->request->redirect('servers/permissions');
		}
		
		$permissions = DB::select('permissions')->from('servers_users')->where('user_id', '=', $user_id)->where('server_id', '=', $server_id)->execute();
		$permissions = $permissions->as_array();
		$permissions = (int) $permissions[0]['permissions'];
		
		$this->view = new View('servers/permissions_edit');

        $this->view->server = $server;
        $this->view->user = $user;
        $this->view->permissions = $permissions;
    }

    public function action_permissions_delete($id, $id2)
    {
            // Validate ID
        if(!ctype_digit($id) OR !ctype_digit($id2))
        {
            throw new Kohana_Exception('Invalid parameter');
        }

        $id = (int) $id; $id2 = (int) $id2;

        $server = ORM::factory('server', $id2);
        $user = ORM::factory('user', $id);

        if(!$server->loaded() OR !$user->loaded())
        {
            throw new Kohana_Exception('Invalid parameter');
        }

        $this->log_action(__('Removed :user permissions for server: :server', array(':user' => $user->username, ':server' => $server->name)));

        DB::delete('servers_users')->where('user_id', '=', $id)->where('server_id', '=', $id2)->execute();

            $this->notice('Permissions removed');
            $this->request->redirect('servers/permissions');
    }

    public function action_index()
    {
        if(isset($_POST['name']) AND isset($_POST['ip']) AND isset($_POST['port']) AND isset($_POST['password']) AND ctype_digit($_POST['port'])
           AND filter_var($_POST['ip'], FILTER_VALIDATE_IP))
        {
            $server = new Model_Server;

            $server->name = HTML::entities($_POST['name']);
            $server->ip = HTML::entities($_POST['ip']);
            $server->port = (int) $_POST['port'];
            $server->password = Security::xss_clean($_POST['password']);

            $server->save();

            $this->log_action(__('Added server: :server', array(':server' => $server->name)));
            $this->notice('Server added');
            $this->request->redirect('servers');
        }

        $servers = ORM::factory('server')->find_all();

        $this->title = __('Servers management');

        $this->view = new View('servers/index');

        $this->view->servers = $servers;
    }

    public function action_delete($id)
    {
        // Validate ID
        if(!ctype_digit($id))
        {
            throw new Kohana_Exception('Invalid parameter');
        }

        // Get server
        $id = ORM::factory('server', (int) $id);

        // Validate
        if(!$id->loaded())
        {
            throw new Kohana_Exception('Server not found');
        }

        $this->log_action(__('Removed server: :server', array(':server' => $id->name)));

        // Delete
        $id->delete();

        $this->notice('Server removed');
        $this->request->redirect('servers');
    }

    public function action_edit($id)
    {
        // Validate ID
        if(!ctype_digit($id))
        {
            throw new Kohana_Exception('Invalid parameter');
        }

        // Get server
        $id = ORM::factory('server', (int) $id);

        // Validate
        if(!$id->loaded())
        {
            throw new Kohana_Exception('Server not found');
        }

        if(isset($_POST['name']) AND isset($_POST['ip']) AND isset($_POST['port']) AND isset($_POST['password']) AND ctype_digit($_POST['port'])
           AND filter_var($_POST['ip'], FILTER_VALIDATE_IP))
        {
            $id->name = HTML::entities($_POST['name']);
            $id->ip = HTML::entities($_POST['ip']);
            $id->port = (int) $_POST['port'];
            $id->password = Security::xss_clean($_POST['password']);

            $id->save();

            $this->log_action(__('Updated server: :server', array(':server' => $id->name)));

            $this->notice(__('Server updated'));
            $this->request->redirect('servers');
        }

        // Title
        $this->title = 'Server edit';

        // View
        $this->view = new View('servers/edit');
        $this->view->server = $id;
    }
}