<?php

file("/home/eater/do-thing")
    ->contents("bash ./do-other-thing;")
    ->requires(file("/home/eater/do-other-thing"));

file("/home/eater/do-other-thing")
    ->source("http://eoc.io/dotfiles");
