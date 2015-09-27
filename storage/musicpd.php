<?php

package('mpd', [
    "emerge/use" => "vorbis"
])->install();

service('mpd')
    ->enable()
    ->requires(package('mpd'));
