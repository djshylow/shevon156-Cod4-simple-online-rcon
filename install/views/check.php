<div class="wrexbox">
    <h1>Install check</h1>
    <div class="group">
        <div class="title">
            Server
        </div>
        <div class="content">
            <div>
                <label>PHP:</label>
                <?php echo $server['php'] ?>
                <div class="msg">
                    PHP 5.2.x or newer required
                </div>
            </div>
            <div>
                <label>UTF-8 PCRE support:</label>
                <?php echo $server['pcre'] ?>
            </div>
            <div>
                <label>iconv:</label>
                <?php echo $server['iconv'] ?>
            </div>
            <div>
                <label>MySQL:</label>
                <?php echo $server['mysql'] ?>
                <div class="msg">mysql</div>
            </div>
            <div>
                <label>register_globals:</label>
                <?php echo $server['globals'] ?>
                <div class="msg">
                    Disable (recommended)
                </div>
            </div>
            <div>
                <label>Magic Quotes GPC:</label>
                <?php echo $server['magic_quotes'] ?>
                <div class="msg">Disable (recommended)</div>
            </div>
        </div>
    </div>
    <div class="group">
        <div class="title">
            CHMOD
        </div>
        <div class="content">
            <?php foreach($files as $f): ?>
            <div>
                <label><?php echo $f['file'] ?></label>
                <?php if($f['writeable']): ?><span style="color: green">Yes</span>
                <?php else: ?>
                <span style="color: red">No</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <p style="text-align: right">
        <?php if($success): ?>
            <a href="index.php?nextstep=1" class="button">Next</a>
        <?php endif; ?>
    </p>
</div>
