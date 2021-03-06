<?php defined('SYSPATH') or die('No direct script access.');

/**
 * User controller
 *
 * Copyright (c) 2010, EpicLegion
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
 * OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author		EpicLegion, Maximusya
 * @package		rcon
 * @subpackage	controller
 * @license		http://www.opensource.org/licenses/bsd-license.php	New BSD License
 */
class Controller_Servers extends Controller_Main {

    /**
     * Delete server
     *
     * @param	string	$id
     */
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

        // Log action
        $this->log_action(__('Removed server: :server', array(':server' => $id->name)));

        // Delete
        $id->delete();

        // Set notice and redirect
        $this->notice(__('Server removed'));
        $this->request->redirect('servers');
    }

    /**
     * Edit server
     *
     * @param	string	$id
     */
    public function action_edit($id)
    {
        // Games
        $games = Gameloader::get_games();

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

        // Submitted?
        if(isset($_POST['name']) AND isset($_POST['ip']) AND isset($_POST['port']) AND isset($_POST['password']) AND ctype_digit($_POST['port'])
           AND filter_var($_POST['ip'], FILTER_VALIDATE_IP) AND isset($_POST['game']) AND isset($games[$_POST['game']]))
        {
            // Set name and IP
            $id->name = HTML::entities($_POST['name']);
            $id->ip = HTML::entities($_POST['ip']);

            // Port and password
            $id->port = (int) $_POST['port'];
            $id->password = Security::xss_clean($_POST['password']);

            // Game
            $id->game = $_POST['game'];

            // Save server
            $id->save();

            // Log action
            $this->log_action(__('Updated server: :server', array(':server' => $id->name)));

            // Notice and redirect
            $this->notice(__('Server updated'));
            $this->request->redirect('servers');
        }

        // Title
        $this->title = __('Edit server');

        // View
        $this->view = new View('servers/edit');
        $this->view->server = $id;
        $this->view->games = $games;
    }

    /**
     * Index action (view, add)
     */
    public function action_index()
    {
        // Games
        $games = Gameloader::get_games();

        // Form submitted?
        if(isset($_POST['name']) AND isset($_POST['ip']) AND isset($_POST['port']) AND isset($_POST['password']) AND ctype_digit($_POST['port'])
           AND filter_var($_POST['ip'], FILTER_VALIDATE_IP) AND isset($_POST['game']) AND isset($games[$_POST['game']]))
        {
            // Create new model
            $server = new Model_Server;

            // Set data
            $server->name = HTML::entities($_POST['name']);
            $server->ip = HTML::entities($_POST['ip']);
            $server->port = (int) $_POST['port'];
            $server->password = Security::xss_clean($_POST['password']);
            $server->game = $_POST['game'];

            // Save server
            $server->save();

            // Log action
            $this->log_action(__('Added server: :server', array(':server' => $server->name)));

            // Notice and redirect
            $this->notice(__('Server added'));
            $this->request->redirect('servers');
        }

        // Retrieve all servers
        $servers = ORM::factory('server')->find_all();

        // Set page title
        $this->title = __('Servers management');

        // Set page view
        $this->view = new View('servers/index');

        // Assign server list
        $this->view->servers = $servers;

        // Games
        $this->view->games = $games;
    }

    /**
     * Manage server permissions
     */
    public function action_permissions()
    {
        // Set page title
        $this->title = __('Servers permissions');

        // Variables to store servers and users
        $servers = array();
        $users = array();
        $games = array();

        // Retrieve servers
        foreach(ORM::factory('server')->find_all() as $s)
        {
            $servers[$s->id] = $s->name;
            $games[$s->id] = $s->game;
        }

        // Retrieve users
        foreach(ORM::factory('user')->find_all() as $s)
        {
            $users[$s->id] = $s->username;
        }

        // Submitted
        if(isset($_POST['user_id']) AND isset($_POST['server_id']) AND ctype_digit($_POST['user_id']) AND ctype_digit($_POST['server_id']))
        {
            // Cast
            $server_id = (int) $_POST['server_id'];
            $user_id = (int) $_POST['user_id'];

            // Validate server and user
            if(!isset($servers[$server_id]) OR !isset($users[$user_id]))
            {
                $this->notice(__('Invalid user or server'));
                $this->request->redirect('servers/permissions');
            }

            // Server owned?
            if(Model_Server::is_owned($user_id, $server_id))
            {
                $this->notice(__('Please use edit function to modify user permissions'));
                $this->request->redirect('servers/permissions');
            }

            // Send to model
            Model_Server::add_permissions($user_id, $server_id, $this->permissions($games[$server_id]));

            // Log executed action
            $this->log_action(__('Added permissions to user :user for server: :server', array(
                ':user' => ORM::factory('user', $user_id)->username,
                ':server' => ORM::factory('server', $server_id)->name,
            )));

            // Notice and redirect
			$this->notice(__('Permissions added'));
			$this->request->redirect('servers/permissions');
        }

        // View
        $this->view = new View('servers/permissions');

        // Get permissions
        $this->view->list = Model_Server::get_permissions();

        // Assign servers and users
        $this->view->servers = $servers;
        $this->view->users = $users;
    }

    /**
     * Delete permissions
     *
     * @param	string	$user
     * @param	string	$server
     */
    public function action_permissions_delete($user, $server)
    {
        // Validate ID
        if(!ctype_digit($user) OR !ctype_digit($server))
        {
            throw new Kohana_Exception('Invalid parameter');
        }

        // Get server and user
        $server = ORM::factory('server', (int) $server);
        $user = ORM::factory('user', (int) $user);

        // Loaded?
        if(!$server->loaded() OR !$user->loaded())
        {
            throw new Kohana_Exception('Invalid parameter');
        }

        // Log action
        $this->log_action(__('Removed :user permissions for server: :server', array(
        ':user' => $user->username, ':server' => $server->name)));

        // Delete permissions
        Model_Server::delete_permissions($user->id, $server->id);

        // Notice and redirect
        $this->notice(__('Permissions removed'));
        $this->request->redirect('servers/permissions');
    }

    /**
     * Edit permissions
     *
     * @param	string	$user
     * @param	string	$server
     */
    public function action_permissions_edit($user, $server)
    {
    	// Validate ID
        if(!ctype_digit($user) OR !ctype_digit($server))
        {
            throw new Kohana_Exception('Invalid parameter');
        }

        // Get server and user
		$server = ORM::factory('server', (int) $server);
		$user = ORM::factory('user', (int) $user);

		// Loaded?
		if(!$server->loaded() OR !$user->loaded())
		{
			throw new Kohana_Exception('Invalid parameter');
		}

		// Owned?
		if(!Model_Server::is_owned($user->id, $server->id))
		{
            throw new Kohana_Exception('Invalid parameter');
		}

		// Process POST
		if(isset($_POST['submit']))
		{
            // Edit
            Model_Server::edit_permissions($user->id, $server->id, $this->permissions($server->game));

            // Log edit
			$this->log_action(__('Edited :user permissions for server: :server',
			array(':user' => $user->username, ':server' => $server->name)));

			// Notice and redirect
			$this->notice(__('Permissions saved'));
			$this->request->redirect('servers/permissions');
		}

		// View
		$this->view = new View('servers/permissions_edit');

		// Server and user
        $this->view->server = $server;
        $this->view->user = $user;

        // Retrieve permissions
        $permissions = Model_Server::get_permissions($user->id, $server->id);

        // Assign
        $this->view->permissions = $permissions;

        // Assign to field view
        $this->view->fields = View::factory('servers/fields', array('current' => $permissions, 'fields' => Gameloader::get_permissions($server->game)))
                                  ->render();
    }

    /**
     * Permissions for specific server
     *
     * @param	string	$server
     */
    public function action_permissions_fields($server)
    {
        // Ajax?
        if(!Request::$is_ajax OR !ctype_digit($server))
        {
            exit;
        }

        // Get server
        $server = ORM::factory('server', (int) $server);

        // Invalid server
        if(!$server->loaded())
        {
            exit;
        }

        // Games
        $games = Gameloader::get_games();

        // Valid game?
        if(!isset($games[$server->game]))
        {
            exit;
        }

        // Display
        echo View::factory('servers/fields', array('current' => 0, 'fields' => Gameloader::get_permissions($server->game)))
                                  ->render();

        // Done
        exit;
    }

    /**
     * Set current tab and check permissions
     *
     * @see application/classes/controller/Controller_Main::before()
     */
    public function before()
    {
        // Parent
        parent::before();

        // Set current tab
        $this->tab = 'servers';

        // Check permissions
        $this->do_force_login('servers');
    }

    /**
     * Get permissions bitset
     *
     * @param	string
     * @return	int
     */
    protected function permissions($game = NULL)
    {
        // Bitset
        $bitset = 0;

        // Iterate
        foreach(Gameloader::get_permissions($game) as $k => $v)
        {
            // Isset?
            if(isset($_POST[$k]) AND $_POST[$k] == '1')
            {
                // Add
                $bitset |= $v['bit'];
            }
        }

        // Return bitset
        return $bitset;
    }
}