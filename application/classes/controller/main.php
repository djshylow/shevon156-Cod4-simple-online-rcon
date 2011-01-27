<?php defined('SYSPATH') or die('No direct script access.');
define('SERVER_KICK', 1);
define('SERVER_BAN', 2);
define('SERVER_TEMP_BAN', 4);
define('SERVER_MESSAGE', 8);
define('SERVER_USER_LOG', 16);
define('SERVER_PLAYLIST', 32);
abstract class Controller_Main extends Controller {

    protected $layout = 'layout/backend';
    protected $title = '';
    
    /**
     * @var View
     */
    protected $view = NULL;

    protected $db = NULL;
    protected $session = NULL;
    protected $auth = NULL;
    protected $user = NULL;
    protected $tab = 'rcon';

    protected function log_action($action)
    {
        DB::insert('logs', array('user_id', 'date', 'content'))->values(array(
        $this->user->id, date('Y-m-d H:i:s'), $action
        ))->execute();
    }

	public function before()
    {
        $this->db = Database::instance();
        $this->session = Session::instance();
        $this->auth = Auth::instance();
        $this->user = $this->auth->get_user();
    }
    
    public function after()
    {
        $layout = new View($this->layout);

        $layout->title = $this->title;
        $layout->notice = $this->session->get_once('rcon_notice');
        $layout->content = $this->view->render();
        $layout->tab = $this->tab;

        echo $layout->render();
    }

    protected function do_force_login($role = 'login')
    {
        if(!$this->auth->logged_in($role))
        {
            if(!$this->user)
            {
                $this->request->redirect('login/index');
            }
            else
            {
                $this->notice(__('No permissions'));
                $this->request->redirect('dashboard/index');
            }
        }
    }

    protected function notice($notice)
    {
        $this->session->set('rcon_notice', $notice);
    }
}