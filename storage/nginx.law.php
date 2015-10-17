<?php

$package = 'nginx';

package($package)
    ->install();


if (!in_array(paper('os.distribution'), ['void'])) {
    var_dump(paper('os.distribution'));
    service($package)
        ->enable()
        ->requires(package($package));
}

$wwwFolder = '/var/local/www/';
$nginxConfig = which(paper('os.distribution'), ["freebsd" => '/usr/local/etc/nginx/nginx.conf', '/etc/nginx/nginx.conf']);

file($nginxConfig)
    ->contents(file_get_contents(__DIR__ . '/nginx/nginx.conf'))
    ->requires(package($package))
    ->notify(service($package));

dir($wwwFolder)
    ->recursive();

file($wwwFolder . 'index.html')
    ->contents('success:order')
    ->requires(package($package))
    ->requires(dir($wwwFolder))
    ->requires(file($nginxConfig));

