<?php
class InstallerModuleUpgrade extends InstallerModule {

    /**
     * Third step: Database setup
     */
    protected function databaseSetup()
    {
        $config = require SYSTEM_PATH.'application/config/database.php';

        if(!is_array($config))
        {
            throw new Exception;
        }

        $config = $config['default'];

        $db = mysql_connect($config['connection']['hostname'], $config['connection']['username'], $config['connection']['password']) or die('Cannot connect');

        if(!@mysql_select_db($config['connection']['database'], $db))
        {
            die('Cannot select database');
        }

        // Set database
        $this->installer->setDb($db);

        // Retrieve old permissions
        $permissions = array();
        $query = mysql_query("SELECT * FROM ".$config['table_prefix']."servers_users");

        while($row = mysql_fetch_assoc($query))
        {
            $permission = 0;

            if($row['can_kick'] == '1') $permission |= 1;
            if($row['can_ban'] == '1') $permission |= 2;
            if($row['can_temp_ban'] == '1') $permission |= 4;
            if($row['can_messages'] == '1') $permission |= 8;

            if($permission == 15)
            {
                $permission |= 16;
            }

            $permissions[] = array('server_id' => (int) $row['server_id'], 'user_id' => (int) $row['user_id'], 'permissions' => $permission);
        }


        // Execute
        $this->installer->executeScheme('update.sql', $config['table_prefix']);

        // Remove old permissions
        mysql_query("DELETE FROM ".$config['table_prefix']."servers_users");

        // Create new
        foreach($permissions as $perm)
        {
            mysql_query("INSERT INTO ".$config['table_prefix']."servers_users (user_id, server_id, permissions)
                         VALUES (".$perm['user_id'].", ".$perm['server_id'].", ".$perm['permissions'].")");
        }

        // Next step
        $_SESSION['step'] = 2;

        // Parse view
        $this->installer->parseView('database', array('file' => 'upgrade.php'));
    }

    /**
     * Finalization
     */
    protected function finalStep()
    {
        // Lock installer
        $this->installer->lockInstaller();

        // View
        $this->installer->parseView('finish_upgrade');
    }

    /**
     * Get step list
     *
     * @return array
     */
    public function getStepList()
    {
        return array(
            1 => 'Database',
            2 => 'Finish'
        );
    }

    /**
     * Handle current step
     */
    public function handle()
    {
        switch($this->currentStep)
        {
            case 2:
            	$this->finalStep();
                break;

            default:
            	$this->databaseSetup();
                break;
        }
    }
}