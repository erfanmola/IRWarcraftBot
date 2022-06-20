<?php

ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/errors.log");

set_time_limit(10);
define('ABSPATH', true);

require_once "functions.php";

if (!(isset($result))) {

    $result = json_decode($argv[1], true);

}

if ($result === null) {
    
    die("Well, That's smart guess, But sry I'm smarter than you (=");

}

if (isset($result['message'])) {

    $chat_id   = $result['message']['chat']['id'] ?? null;
    $chat_type = $result['message']['chat']['type'] ?? null;

    $msg_id    = $result['message']['message_id'] ?? null;
    $message   = FaToEn($result['message']['text'] ?? '');

    $from_id        = $result['message']['from']['id'] ?? null;
    $from_firstname = $result['message']['from']['first_name'] ?? '';
    $from_lastname  = $result['message']['from']['last_name'] ?? '';
    $from_username  = $result['message']['from']['username'] ?? null;
    $from_name      = "$from_firstname $from_lastname";

    $IsInReplyTo    = isset($result['message']['reply_to_message']);
    $IsInReplyToBot = $IsInReplyTo && (int)$result['message']['reply_to_message']['from']['id'] === (int)$botid;

    if ($chat_type === 'private') {
        
        SendMessage($chat_id, json_encode($result));

        require_once __DIR__ . "/sections/private/autoload.php";

    }else if ($chat_type === 'group' || $chat_type === 'supergroup') {
        
        if (in_array($chat_id, $valid_chats)) {
            
            require_once __DIR__ . "/sections/group/autoload.php";

        }else{

            SendMessage($chat_id, "<pre>$chat_id</pre> is not a Valid Chat ! Contact @Eyfan for more info", null, [], true);

            LeaveChat($chat_id);

        }

    }else{

        SendMessage($chat_id, json_encode($result), null, [], true);
        LeaveChat($chat_id);

    }

}