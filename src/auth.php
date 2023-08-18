<?php

require_once ('../vendor/autoload.php');

function getAccessToken() {
    $client = new Google_Client();
    $client->setAuthConfig('access_token.json');
    $client->addScope(Google_Service_Drive::DRIVE);

    $accessToken = json_decode(file_get_contents('access_token.json'), true);
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        file_put_contents('access_token.json', json_encode($client->getAccessToken()));
        $accessToken = $client->getAccessToken();
    }

    return $accessToken['access_token'];
}

function checkAccessToken() {
    if (file_exists('access_token.json')) {
        $accessToken = json_decode(file_get_contents('access_token.json'), true);

        if (!$accessToken || !isset($accessToken['access_token'])) {
            $accessToken = getAccessToken();
        }
    } else {
        $accessToken = getAccessToken();
    }

    return $accessToken['access_token'];
}

?>