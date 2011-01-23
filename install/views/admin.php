<div class="wrexbox">
    <h1>Add admin</h1>
    <?php if($message): ?>
    <div class="notice"><?php echo $message ?></div>
    <?php endif; ?>
    <form action="index.php" method="post">
        <div class="group">
            <div class="title">Data</div>
            <div class="content">
            <div>
                <label for="username">Login:</label>
                <input type="text" name="username" id="username" maxlength="20" />
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="text" name="email" id="email" maxlength="70" />
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" />
            </div>
            <div>
                <label for="password2">Confirm password:</label>
                <input type="password" name="password2" id="password2" />
            </div>
            <div>
                <input type="submit" name="submit" id="submit" value="Send" />
            </div>
            </div>
        </div>
    </form>
</div>
