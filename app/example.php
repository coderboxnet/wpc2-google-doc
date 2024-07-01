<?php
error_reporting(E_ALL);

session_start();

// include your composer dependencies
require_once '../vendor/autoload.php';

/************************************************
 * The redirect URI is to the current page, e.g:
 * http://localhost:8080/simple-file-upload.php
 ************************************************/
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$client = new Google\Client();
$client->setAuthConfig(dirname(__FILE__) . '/assets/credentials.json');
$client->setRedirectUri($redirect_uri);
$client->addScope(Google\Service\Drive::DRIVE);
$service = new Google\Service\Drive($client);

// add "?logout" to the URL to remove a token from the session
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['upload_token']);
}



/************************************************
 * If we have a code back from the OAuth 2.0 flow,
 * we need to exchange that with the
 * Google\Client::fetchAccessTokenWithAuthCode()
 * function. We store the resultant access token
 * bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code'], $_SESSION['code_verifier']);
        $client->setAccessToken($token);

        // store in the session also
        $_SESSION['upload_token'] = $token;

        // redirect back to the example
        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
    } catch (\Throwable $th) {
        var_dump($th);
    }

}

// set the access token as part of the client
if (!empty($_SESSION['upload_token'])) {
    try {
        $client->setAccessToken($_SESSION['upload_token']);
        if ($client->isAccessTokenExpired()) {
            unset($_SESSION['upload_token']);
        } else {
            // Now lets try and send the metadata as well using multipart!
            $file = new Google\Service\Drive\DriveFile();
            $file->setName("DemoFile.txt");
            $result = $service->files->create(
                $file,
                [
                    'data' => "Sample content goes here",
                    'mimeType' => 'text/plain'
                ]
            );
            var_dump($result);
        }
    } catch (\Throwable $th) {
        var_dump($th);
    }

} else {
    try {
        $_SESSION['code_verifier'] = $client->getOAuth2Service()->generateCodeVerifier();
        $authUrl = $client->createAuthUrl();
        header("Location: {$authUrl}");
    } catch (\Throwable $th) {
        var_dump($th);
    }

}
