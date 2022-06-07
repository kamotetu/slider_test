<?php

if (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    echo '404';
    die();
}
if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
    header('HTTP/1.1 500 Internal Server Bad Request');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => 'ERROR', 'code' => 500)));
}

if (!validate($_POST)) {
    echo '505';
    die();
}

$range_type = h($_POST['range_type']);
$from = h($_POST['from']);
$to = h($_POST['to']);

const ITEMS = [
    'itemA' => [
        'length' => 30,
        'height' => 10,
    ],
    'itemB' => [
        'length' => 24,
        'height' => 100,
    ],
    'itemC' => [
        'length' => 58,
        'height' => 49,
    ],
    'itemD' => [
        'length' => 79,
        'height' => 82,
    ],
];

$response = [];
$response['items'] = [];
$response['length']['min'] = 0;
$response['length']['max'] = 0;
$response['height']['min'] = 0;
$response['height']['max'] = 0;
$length_min = 0;
$length_max = 0;
$height_min = 0;
$height_max = 0;
$first = true;
foreach (ITEMS as $item_name => $item) {
    $search_keyword = 'length';
    if ($range_type === '2') {
        $search_keyword = 'height';
    }
    if (
        $from <= $item[$search_keyword] &&
        $to >= $item[$search_keyword]
    ) {
        $response['items'][$item_name] = $item;
        if ($first) {
            $length_min = $item['length'];
            $length_max = $item['length'];
            $height_min = $item['height'];
            $height_max = $item['height'];
            $first = false;
        }
        if ($length_min > $item['length']) {
            $length_min = $item['length'];
        }
        if ($length_max < $item['length']) {
            $length_max = $item['length'];
        }
        if ($height_min > $item['height']) {
            $height_min = $item['height'];
        }
        if ($height_max < $item['height']) {
            $height_max = $item['height'];
        }
    }
    $response['length']['min'] = $length_min;
    $response['length']['max'] = $length_max;
    $response['height']['min'] = $height_min;
    $response['height']['max'] = $height_max;
}
header("Content-type: application/json; charset=UTF-8");
echo json_encode($response);
exit;

function validate(array $params): bool
{
    $params_keys = [
        'range_type',
        'from',
        'to',
    ];
    if (count($params) !== 3) {
        return false;
    }
    foreach ($params_keys as $params_key) {
        if (!array_key_exists($params_key, $params)) {
            return false;
        }
    }
    return true;
}

function h(string $value): string
{
    return htmlspecialchars($value);
}
