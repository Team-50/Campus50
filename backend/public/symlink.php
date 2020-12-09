<?php

$linkName = 'storage';

$target = '';//isi dengan path yang mengarah ke storage/app/public misalnya kalau di cpanel /home2/team50/Campus50/storage/app/public

symlink($target, $linkName);