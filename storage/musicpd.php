<?php

package('musicpd', [
    "emerge/use" => "vorbis"
])->install();

service('musicpd')
    ->enable()
    ->requires(package('musicpd'));
