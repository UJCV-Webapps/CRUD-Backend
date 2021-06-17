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

function base64ToImage($base64_string, $output_file)
{
    // open the output file for writing
    $ifp = fopen($output_file, 'wb');

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode(',', $base64_string);

    // we could add validation here with ensuring count( $data ) > 1
    fwrite($ifp, base64_decode($data[1]));

    // clean up the file resource
    fclose($ifp);

    return $output_file;
}
