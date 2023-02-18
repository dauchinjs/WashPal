<?php

//get token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://80.232.223.8:10101/?token=renew");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
curl_setopt($ch, CURLOPT_USERPWD, "davidsj:burtiuncipari");

$output = curl_exec($ch);

curl_close($ch);

$xml = simplexml_load_string($output);
$token = $xml->asXML();
//echo $token;

$tokenValue = (string)$xml->Value;
$requestDate = (string)$xml->Created;
$requestTime = date('H:i:s', strtotime($requestDate));

//start task
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://80.232.223.8:10101/task");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, "command=begin");

authorization($ch, $tokenValue);

$output = curl_exec($ch);

checkStatus($ch);

curl_close($ch);

//get information
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://80.232.223.8:10101/task");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, "command=getvalue,request={$requestTime}");

authorization($ch, $tokenValue);

echo 'Time, Node, Type, Temperature (°C), Δ' . PHP_EOL;
echo '---------------------------------------------' . PHP_EOL;

$previousTemperatures = [];

$amountOfRequests = 100;

for ($i = 0; $i < $amountOfRequests; $i++) {
    $output = curl_exec($ch);
    $data = json_decode($output, true);

    $node = $data['data']['node'];
    $type = $data['data']['type'];
    $name = $data['data']['name'];
    $fahrenheitTemperature = $data['data']['temp'];
    $celsiusTemperature = number_format(($fahrenheitTemperature - 32) * 5 / 9, 2);

    if ($type === 'unknown') {
        continue;
    }

    $machineID = $node . '_' . $type;

    if (array_key_exists($machineID, $previousTemperatures)) {
        $previousTemperature = $previousTemperatures[$machineID];
        $deltaTemperature = $celsiusTemperature - $previousTemperature;
    } else {
        $deltaTemperature = '';
    }

    $previousTemperatures[$machineID] = $celsiusTemperature;

    echo $requestTime . ', ' . $node . ', ' . $type . ', ' . $celsiusTemperature . ', ' . $deltaTemperature . PHP_EOL;
    echo '---------------------------------------------' . PHP_EOL;
}


curl_close($ch);

//end task
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://80.232.223.8:10101/task");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, "command=end");

authorization($ch, $tokenValue);

$output = curl_exec($ch);

checkStatus($ch);

curl_close($ch);

function authorization($ch, $tokenValue): void
{
    curl_setopt($ch, CURLOPT_USERPWD, "davidsj:burtiuncipari");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: text/plain;charset=utf-8',
        'Authorization: Bearer ' . $tokenValue
    ]);
}

function checkStatus($ch): string
{
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode == 200) {
        return $httpCode . " OK" . PHP_EOL;
    }
    return "Error: " . $httpCode;
}