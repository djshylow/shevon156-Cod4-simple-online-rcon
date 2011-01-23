<?php
class Controller_Dashboard extends Controller_Main {

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

        // Catch them all
        try {
            // Rcon connection
            $rcon = new Rcon($current_server->ip, $current_server->port, $current_server->password);
            $rcon->connect();

            // Commands
            $commands = new Rcon_Commands($rcon);

            // Get server info
            $server_info = $commands->get_server_info();
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
        $view->permissions = $permissions;

        // Get available servers
        $servers = array();

        // Iterate
        foreach($owned as $o)
        {
            $servers[] = $o['server_id'];
        }

        // Owned
        $owned = array();

        // Get server names
        foreach(DB::select('id', 'name')->from('servers')->where('id', 'IN', $servers)->execute() as $serv)
        {
            $owned[] = $serv;
        }

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
        // Only logged in users
        $this->do_force_login();

        // Title
        $this->title = __('Remote console');

        // Get owned servers
        $owned = DB::select()->from('servers_users')->where('user_id', '=', $this->user->id)->execute();

        // No servers?
        if(count($owned) <= 0)
        {
            $this->view = new View('dashboard/noservers');
            return;
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
            throw new Kohana_Exception('Invalid server');
        }

        // Rcon connection
        $rcon = new Rcon($current_server->ip, $current_server->port, $current_server->password);
        $rcon->connect();

        // Commands
        $commands = new Rcon_Commands($rcon);

        // Exception catch
        try {
            // Get server info
            $server_info = $commands->get_server_info();
        }
        catch(Exception $e) {
           // Default
           $server_info = array('map' => '', 'players' => array(), 'error' => $e->getMessage());
        }

        // View
        $this->view = new View('dashboard/index');

        // Server info
        $this->view->server_info = $server_info;
        $this->view->permissions = $permissions;

        // Get available servers
        $servers = array();

        // Iterate
        foreach($owned as $o)
        {
            $servers[] = $o['server_id'];
        }

        // Owned
        $owned = array();

        // Get server names
        foreach(DB::select('id', 'name')->from('servers')->where('id', 'IN', $servers)->execute() as $serv)
        {
            $owned[] = $serv;
        }

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

    public function action_set_server()
    {
        // Invalid request
        if(!isset($_POST['server']) OR !ctype_digit($_POST['server']))
        {
            throw new Kohana_Exception('Invalid request');
        }

        // Only logged in users
        $this->do_force_login();

        // ID
        $id = (int) $_POST['server'];
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
            throw new Kohana_Exception('Invalid server');
        }

        // Check permissions
        if(!($permissions & SERVER_MESSAGE))
        {
            throw new Kohana_Exception('No permissions');
        }

        // Form submit
        if(isset($_POST['submit']) AND isset($_POST['message']))
        {
            DB::insert('messages', array('user_id', 'server_id', 'message'))->values(array(
                $this->user->id, (int) $current_server->id, Security::xss_clean($_POST['message'])
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
            if(!$message->loaded() OR $message->server_id != $current_server->id)
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
        $this->view->messages = DB::select('messages.id', 'messages.message', 'users.username')->where('server_id', '=', $current_server->id)
                                ->join('users', 'LEFT')->on('users.id', '=', 'messages.user_id')
                                ->from('messages')->order_by('messages.id', 'DESC')->execute();
    }

    public function action_logs($guid = NULL)
    {
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
            throw new Kohana_Exception('Invalid server');
        }

        // Check permissions
        if(!($permissions & SERVER_USER_LOG))
        {
            throw new Kohana_Exception('No permissions');
        }

        // Ajax
        if(Request::$is_ajax AND $guid !== NULL AND ctype_digit($guid))
        {

            // Find
            $log = DB::select('names', 'ip_addresses')->where('server_id', '=', $current_server->id)
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
        $this->view->logs = DB::select('*')->where('server_id', '=', $current_server->id)->from('players')->order_by('last_update', 'DESC')->execute();
    }
}