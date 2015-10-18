<?php

user('henk')
    ->shell('/usr/local/bin/bash')
    ->comment("Henk haaiennaaier")
    ->password(hash('sha256', 'haaien'))
    ->groups(['sudo', 'wheel']);
