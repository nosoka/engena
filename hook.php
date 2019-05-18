<?php
    $output = shell_exec("git reset --hard origin/master 2>&1");
    echo "<pre> {$output} </pre>";
    $git_output = shell_exec('git pull');
    echo "<pre>$git_output</pre>";
    $output = shell_exec('php -d allow_url_fopen=On /usr/home/zayinr/bin/composer update');
    echo "<pre>$output</pre>";
    $output = shell_exec('php artisan migrate');
    echo "<pre>$output</pre>";
