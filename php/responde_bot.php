<?php
$token = '1652202112:AAHyH8NzDJfP4XIMSUXNv-04Se7zORNLnkI';
$website = 'https://api.telegram.org/bot'.$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];

switch($message) {
    case '/start':
        $response = 'Me has iniciado';
        sendMessage($chatId, $response);
        break;
    case '/info':
        $response = 'Hola! Soy @trecno_bot';
        sendMessage($chatId, $response);
        break;
    default:
        $response = 'No te he entendido';
        sendMessage($chatId, $response);
        break;
}

function sendMessage($chatId, $response) {
    $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}
?>