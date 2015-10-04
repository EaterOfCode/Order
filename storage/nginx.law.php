<?php

$package = 'nginx';

package($package)
    ->install();

service($package)
    ->enable()
    ->requires(package($package));

$wwwFolder = which(
    paper('os.distribution'),
    [
        "freebsd" => '/usr/local/www/nginx/',
        '/usr/share/nginx/html/'
    ]
);

file($wwwFolder . 'index.html')
    ->contents('Hello world! <3 Order')
    ->requires(package($package));
