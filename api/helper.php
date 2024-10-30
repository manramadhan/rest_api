<?php
/**
 * Fungsi untuk generate respon API
 * 
 * @param array $data
 * @param int $code
 * @return json
 */
function response($data, $code)
{
    http_response_code($code);
    header('Content-Type: application/json');
    return json_encode($data);
}