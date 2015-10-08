<?php

$package = 'nginx';

package($package)
    ->install();

service($package)
    ->enable()
    ->requires(package($package));

$wwwFolder = '/var/local/www/';
$nginxConfig = which(paper('os.distrobution'), ["freebsd" => '/usr/local/etc/nginx/nginx.conf', '/etc/nginx/nginx.conf']);

file($nginxConfig)
    ->contents(file_get_contents(__DIR__ . '/nginx/nginx.conf'))
    ->requires(package($package));

file($wwwFolder . 'index.html')
    ->contents('Hello world! <3 Order')
    ->requires(package($package))
    ->requires(file($nginxConfig));

