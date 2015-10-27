<?php

user('henk')
    ->shell('/usr/local/bin/bash')
    ->comment("Henk haaiennaaier")
    ->password('$2a$12$Oc4ml11g0aKt7PWOqSOEReEKe45akXYvKuitVO22mFblNGY5h5fm.')
    ->groups([ 'wheel' ]);
