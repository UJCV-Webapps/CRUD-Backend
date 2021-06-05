<?php

function createDirectoryIfNotExist($path)
{
    if (!is_dir($path)) {
        mkdir('assets/profiles', 0777);
    }
}
