<?php 
   $bot_Token = '918836101:AAGGaH2MIoTjqdhOmRs_34G1Yjgx5VkwgFI';
   $id_Chat = '1087049979';
   $website = 'https://api.telegram.org/bot'.$bot_Token;

   function sendMessage($id, $msj, $website){
    $url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
    file_get_contents($url);
   }
?>
