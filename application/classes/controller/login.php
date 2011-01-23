<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Controller_Main {

    public function action_index()
    {
        // Logged already?
        if($this->auth->logged_in())
        {
            $this->request->redirect('dashboard/index');
        }

        // Login form
        if(isset($_POST['username']) AND isset($_POST['password']))
        {
            // Try to login user
            $result = $this->auth->login(Security::xss_clean($_POST['username']), Security::xss_clean($_POST['password']), TRUE);

            // Success?
            if($result)
            {
                $this->notice(__('Login successful'));
                $this->request->redirect('dashboard/index');
            }
            else
            {
                $this->notice(__('Invalid username or password'));
            }
        }

        // Page title and views
        $this->title = __('Login to panel');
        $this->layout = 'layout/frontend';
        $this->view = new View('login');
    }

    public function action_out()
    {
        // Guests can't logout
        $this->do_force_login();

        // Logout
        $this->auth->logout(TRUE, TRUE);

        // Redirect
        $this->request->redirect('login/index');
    }
}