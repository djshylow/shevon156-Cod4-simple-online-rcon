<div class="wrexbox">
    <h1>System config</h1>
    <?php if($message): ?>
    <div class="notice"><?php echo $message ?></div>
    <?php endif; ?>
    <form action="index.php" method="post">
    <div class="group">
        <div class="title">
            Configuration
        </div>
        <div class="content">
            <div>
                <label for="path">Cookie path:<br /><small>Default is usually correct</small></label>
                <input type="text" name="path" id="path" value="<?php echo $path ?>" />
            </div>
            <div>
                <label for="host">Host MySQL:<br /><small>Usually localhost</small></label>
                <input type="text" name="host" id="host" value="localhost" />
            </div>
            <div>
                <label for="user">MySQL user:</label>
                <input type="text" name="user" id="user" value="" />
            </div>
            <div>
                <label for="password">MySQL password:</label>
                <input type="text" name="password" id="password" value="" />
            </div>
            <div>
                <label for="database">MySQL db name:</label>
                <input type="text" name="database" id="database" value="" />
            </div>
            <div>
                <label for="prefix">Prefix:</label>
                <input type="text" name="prefix" id="prefix" value="blackops_" />
            </div>
            <div>
                <input type="submit" name="submit" id="submit" value="Submit" />
            </div>
        </div>
    </div>
    </form>
</div>
