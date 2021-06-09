<?php

function createDirectoryIfNotExist($path)
{
    if (!is_dir($path)) {
        mkdir('assets/profiles', 0777);
    }
}

function fromArrToJSON($result)
{
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $response[] = $row;
    }
    return isset($response) ? $response : [];
}
