<?php
function uuid7()
{
    static $last_timestamp = 0;
    $unixts_ms = intval(microtime(true) * 1000);
    if ($last_timestamp >= $unixts_ms) {
        $unixts_ms = $last_timestamp + 1;
    }
    $last_timestamp = $unixts_ms;
    $data = random_bytes(10);
    $data[0] = chr((ord($data[0]) & 0x0f) | 0x70); // set version
    $data[2] = chr((ord($data[2]) & 0x3f) | 0x80); // set variant
    return vsprintf(
        '%s%s-%s-%s-%s-%s%s%s',
        str_split(
            str_pad(dechex($unixts_ms), 12, '0', \STR_PAD_LEFT) .
                bin2hex($data),
            4
        )
    );
}


?>