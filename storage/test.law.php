<?php

file("/home/test/do-thing")
    ->contents("bash ./do-other-thing;")
    ->requires(file("/home/test/do-other-thing"))
    ->requires(file("/home/yes"));

file("/home/test/do-other-thing")
    ->source("http://eoc.io/dotfiles")
    ->requires(file("/home/test/do-other-thing"));
