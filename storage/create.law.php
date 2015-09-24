<?php

package('ack')->install();

file('/home/eater/touch')
    ->contents(time())
    ->requires(package('ack'));
