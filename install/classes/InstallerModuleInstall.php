<?php
class InstallerModuleInstall extends InstallerModule {

    /**
     * Fourth step: Add administrator
     */
    protected function addAdministrator()
    {
        $db = mysql_connect($_SESSION['host'], $_SESSION['user'], $_SESSION['password']) or die('Cannot connect');

        if(!@mysql_select_db($_SESSION['database'], $db))
        {
            die('Cannot select database');
        }

        // Set
        $this->installer->setDb($db);

        // Message
        $message = '';

        // Submitted?
        if(!empty($_POST['submit']) AND !empty($_POST['username']) AND !empty($_POST['password']) AND !empty($_POST['password2']) AND !empty($_POST['email']))
        {
            // Match?
            if($_POST['password'] == $_POST['password2'])
            {
                $this->installer->addUser($_POST['username'], $_POST['password'], $_POST['email']);
                $this->installer->nextStep();
            }
            else
            {
                $message = 'Password does not match';
            }
        }

        // View
        $this->installer->parseView('admin', array('message' => $message));
    }

    /**
     * Second step: Basic configuration (URL and database)
     */
    protected function basicConfig()
    {
        // Message
        $message = '';

        // Submitted?
        if(!empty($_POST['submit']) AND !empty($_POST['path']) AND !empty($_POST['host'])
           AND !empty($_POST['user']) AND !empty($_POST['database']))
        {
            if(empty($_POST['prefix']))
            {
                $dbPref = 'blackops_';
            }
            else
            {
                $dbPref = htmlspecialchars($_POST['prefix']);
            }

            $db = @mysql_connect($_POST['host'], $_POST['user'], isset($_POST['password']) ? $_POST['password'] : '');

            if(!$db)
            {
                $error = mysql_error();
            }
            else
            {
                if(!@mysql_select_db($_POST['database'], $db))
                {
                    $error = 'Cannot select database';
                }
                else
                {
                    $error = '';
                }
            }

            if($error)
            {
                $message = $error;
            }
            else
            {
                // Continue
                try {
                    $this->installer->modifyConfig('app', array('cookie_path' => $_POST['path']));

                    $this->installer->modifyConfig('database', array('default' => array(
                    		'type'       => 'mysql',
		'connection' => array(
			'hostname'   => $_POST['host'],
			'database'   => $_POST['database'],
			'username'   => $_POST['user'],
			'password'   => isset($_POST['password']) ? $_POST['password'] : '',
			'persistent' => FALSE,
		),
		'table_prefix' => $dbPref,
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => FALSE,
                    )));
                }
                catch(Exception $e)
                {
                    exit('Error: '.$e->getMessage());
                }

                // Set session data
                $_SESSION['host'] = $_POST['host'];
                $_SESSION['user'] = $_POST['user'];
                $_SESSION['password'] = isset($_POST['password']) ? $_POST['password'] : '';
                $_SESSION['database'] = $_POST['database'];
                $_SESSION['prefix'] = $dbPref;

                // Next step
                $this->installer->nextStep();
            }
        }

        // Path
        $path = substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - 17);

        // Parse view
        $this->installer->parseView('config', array('path' => $path, 'message' => $message));
    }

    /**
     * Third step: Database setup
     */
    protected function databaseSetup()
    {
        $db = mysql_connect($_SESSION['host'], $_SESSION['user'], $_SESSION['password']) or die('Cannot connect');

        if(!@mysql_select_db($_SESSION['database'], $db))
        {
            die('Cannot select database');
        }

        // Charset
        mysql_query('ALTER DATABASE `'.$_SESSION['database'].'` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci', $db);

        // Set database
        $this->installer->setDb($db);

        // Execute
        $this->installer->executeScheme('schema.sql', $_SESSION['prefix']);

        // Next step
        $_SESSION['step'] = 4;

        // Parse view
        $this->installer->parseView('database', array('file' => 'index.php'));
    }

    /**
     * Finalization
     */
    protected function finalStep()
    {
        // Lock installer
        $this->installer->lockInstaller();

        // View
        $this->installer->parseView('finish');
    }

    /**
     * Get step list
     *
     * @return array
     */
    public function getStepList()
    {
        return array(
            1 => 'Checks',
            2 => 'Config',
            3 => 'Database',
            4 => 'Account setup',
            5 => 'Finish'
        );
    }

    /**
     * Handle current step
     */
    public function handle()
    {
        switch($this->currentStep)
        {
            case 1:
            	$this->preinstallCheck();
                break;

            case 2:
            	$this->basicConfig();
                break;

            case 3:
            	$this->databaseSetup();
                break;

            case 4:
            	$this->addAdministrator();
                break;

            case 5:
            	$this->finalStep();
                break;
        }
    }

    /**
     * First step: preinstallation check(CHMOD and requirements)
     */
    protected function preinstallCheck()
    {
        // Everything OK?
        $success = TRUE;

        // Server config
        $server = array();

        // PHP version
        if(version_compare(PHP_VERSION, '5.2.3', '>='))
        {
            $server['php'] = '<span style="color: green">'.PHP_VERSION.'</span>';
        }
        else
        {
            $success = FALSE;
            $server['php'] = '<span style="color: red">'.PHP_VERSION.'</span>';
        }

        // UTF-8
        if(!preg_match('/^.$/u', 'ñ'))
        {
            $success = FALSE;
            $server['pcre'] = '<span style="color: red">No</span>';
        }
        else
        {
            $server['pcre'] = '<span style="color: green">Yes</span>';
        }

        // Iconv
        if(!extension_loaded('iconv'))
        {
            $success = FALSE;
            $server['iconv'] = '<span style="color: red">No</span>';
        }
        else
        {
            $server['iconv'] = '<span style="color: green">Yes</span>';
        }

        // MySQL
        if(!function_exists('mysql_connect'))
        {
            $success = FALSE;
            $server['mysql'] = '<span style="color: red">No</span>';
        }
        else
        {
            $server['mysql'] = '<span style="color: green">Yes</span>';
        }

        // Register globals
        if(ini_get('register_globals'))
        {
            $server['globals'] = 'On';
        }
        else
        {
            $server['globals'] = '<span style="color: green">Off</span>';
        }

        // Magic quotes
        if(get_magic_quotes_gpc())
        {
            $server['magic_quotes'] = 'On';
        }
        else
        {
            $server['magic_quotes'] = '<span style="color: green">Off</span>';
        }

        // CHMOD check
        $chmod = $this->installer->checkWrite(array
        (
            'application/cache',
            'application/config/app.php',
            'application/config/database.php',
        ));

        // Success?
        if(!$chmod['success'] AND substr(PHP_OS, 0, 3) != 'WIN')
        {
            $success = FALSE;
        }

        // Next step?
        if($_GET['nextstep'] == '1' AND $success)
        {
            $this->installer->nextStep();
        }

        // Render view
        $this->installer->parseView('check', array('success' => $success, 'server' => $server, 'files' => $chmod['files']));
    }
}
