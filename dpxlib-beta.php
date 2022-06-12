<?php
/*
Author: Erfan Mola

Constants : dpx_token, dpx_async, dpx_admin, dpx_path, dpx_bot_api_server
*/

$async_data = json_decode($argv[1] ?? '', true);

/* Global Variables */
$dpx_sent_msg_id = null;

if ($async_data[0] ?? '' === 'async') {
    
    define('dpx_token', $async_data[1][2]);

    TelegramAPI($async_data[1][0], $async_data[1][1], true, $async_data[1][2], $async_data[1][3]);

    unset($async_data);

}

function TelegramAPI(string $method, array $content = [], null|bool $sync = null, string|bool $token_bot = null, string|null $bot_api_server = null) : array|string|null {

    if ($token_bot === null) {

        if (defined('dpx_token')) {

            $token_bot = dpx_token;

        }else{

            return 'empty token';

        }

    }

    if ($sync === null) {

        if (defined('dpx_async')) {
        
            $sync = !(dpx_async);

        }else{

            $sync = true;

        }

    }

    if (defined('dpx_bot_api_server')) {

        $bot_api_server = dpx_bot_api_server;

    }else if ($bot_api_server === null) {

        $bot_api_server = 'https://api.telegram.org';

    }

    if (!(is_array($content))) {

        $content = json_decode($content, true);

    }

    foreach ($content as $key => $value) {

        if (is_null($value)) {

            unset($content[$key]);

        }

    }

    if (!($sync)) {

        exec("curl --parallel --parallel-immediate --parallel-max 100 --tcp-fastopen --tcp-nodelay -X POST -H 'Content-type: application/json' -d " . escapeshellarg(json_encode($content)) . " '$bot_api_server/bot$token_bot/$method' -o /dev/null > /dev/null 2>&1 &");

        return null;

    }else{

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$bot_api_server/bot$token_bot/$method");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);

        curl_setopt($ch, CURLOPT_POST, 1);

        $content = http_build_query($content);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    
        $result = curl_exec($ch);
    
        if ($result === false) {
            $result = json_encode(['ok' => false, 'curl_error_code' => curl_errno($ch) , 'curl_error' => curl_error($ch) ]);
        }
    
        curl_close($ch);
    
        return json_decode($result, true);

    }

}

/* Telegram Bot API Methods */

function SendMessage(string|int $chat_id, string|array $text, string|int|null $reply_msg = null, array $content = [], null|bool $sync = null, string|null $token_bot = null) : array|string|null {

    $content['chat_id'] = $chat_id;
    $content['text']    = !(is_string($text)) ? json_encode($text) : $text;
    $content['reply_to_message_id'] = $reply_msg;
    $content['allow_sending_without_reply'] = true;

    $content['disable_web_page_preview'] = $content['disable_web_page_preview'] ?? true;
    $content['parse_mode']               = $content['parse_mode'] ?? 'HTML';

    if (strlen($content['text']) <= 4096) {

        $result = TelegramAPI('sendMessage', $content, $sync, $token_bot);

        global  $dpx_sent_msg_id;

        $dpx_sent_msg_id = $result['result']['message_id'] ?? null;

        return $result;

    }else{

        foreach (str_split($content['text'], 4096) as $chunk) {
            
            SendMessage($chat_id, $chunk, $reply_msg, $content, true, $token_bot);

            usleep(125000);

        }

        return null;

    }
    
}

function SendDocument(string|int $chat_id, string $document, string|array|null $caption = null, string|int|null $reply_msg = null, array $content = [], null|bool $sync = null, string|null $token_bot = null) : array|string|null {

    $content['chat_id'] = $chat_id;
    $content['document'] = $document;
    $content['caption']    = strlen($caption) > 0 ? $caption : '';
    $content['reply_to_message_id'] = $reply_msg;
    $content['allow_sending_without_reply'] = true;

    $content['disable_content_type_detection'] = $content['disable_content_type_detection'] ?? true;
    $content['parse_mode']                     = $content['parse_mode'] ?? 'HTML';

    return TelegramAPI('sendDocument', $content, $sync, $token_bot);

}

function SendPhoto(string|int $chat_id, string $photo, string|array|null $caption = null, string|int|null $reply_msg = null, array $content = [], null|bool $sync = null, string|null $token_bot = null) : array|string|null {

    $content['chat_id'] = $chat_id;
    $content['photo'] = $photo;
    $content['caption']    = strlen($caption) > 0 ? $caption : '';
    $content['reply_to_message_id'] = $reply_msg;
    $content['allow_sending_without_reply'] = true;

    $content['disable_content_type_detection'] = $content['disable_content_type_detection'] ?? true;
    $content['parse_mode']                     = $content['parse_mode'] ?? 'HTML';

    return TelegramAPI('sendPhoto', $content, $sync, $token_bot);

}

function SendAnimation(string|int $chat_id, string $animation, string|array|null $caption = null, string|int|null $reply_msg = null, array $content = [], null|bool $sync = null, string|null $token_bot = null) : array|string|null {

    $content['chat_id'] = $chat_id;
    $content['animation'] = $animation;
    $content['caption']    = strlen($caption) > 0 ? $caption : '';
    $content['reply_to_message_id'] = $reply_msg;
    $content['allow_sending_without_reply'] = true;

    $content['disable_content_type_detection'] = $content['disable_content_type_detection'] ?? true;
    $content['parse_mode']                     = $content['parse_mode'] ?? 'HTML';

    return TelegramAPI('sendAnimation', $content, $sync, $token_bot);

}

function SendVideo(string|int $chat_id, string $video, string|array|null $caption = null, string|int|null $reply_msg = null, array $content = [], null|bool $sync = null, string|null $token_bot = null) : array|string|null {

    $content['chat_id'] = $chat_id;
    $content['video'] = $video;
    $content['caption']    = strlen($caption) > 0 ? $caption : '';
    $content['reply_to_message_id'] = $reply_msg;
    $content['allow_sending_without_reply'] = true;

    $content['disable_content_type_detection'] = $content['disable_content_type_detection'] ?? true;
    $content['parse_mode']                     = $content['parse_mode'] ?? 'HTML';

    return TelegramAPI('sendVideo', $content, $sync, $token_bot);

}

function SendVoice(string|int $chat_id, string $voice, string|array|null $caption = null, string|int|null $reply_msg = null, array $content = [], null|bool $sync = null, string|null $token_bot = null) : array|string|null {

    $content['chat_id'] = $chat_id;
    $content['voice'] = $voice;
    $content['caption']    = strlen($caption) > 0 ? $caption : '';
    $content['reply_to_message_id'] = $reply_msg;
    $content['allow_sending_without_reply'] = true;

    $content['disable_content_type_detection'] = $content['disable_content_type_detection'] ?? true;
    $content['parse_mode']                     = $content['parse_mode'] ?? 'HTML';

    return TelegramAPI('sendVoice', $content, $sync, $token_bot);

}

function SendAudio(string|int $chat_id, string $audio, string|array|null $caption = null, string|int|null $reply_msg = null, array $content = [], null|bool $sync = null, string|null $token_bot = null) : array|string|null {

    $content['chat_id'] = $chat_id;
    $content['audio'] = $audio;
    $content['caption']    = strlen($caption) > 0 ? $caption : '';
    $content['reply_to_message_id'] = $reply_msg;
    $content['allow_sending_without_reply'] = true;

    $content['disable_content_type_detection'] = $content['disable_content_type_detection'] ?? true;
    $content['parse_mode']                     = $content['parse_mode'] ?? 'HTML';

    return TelegramAPI('sendAudio', $content, $sync, $token_bot);

}

function DeleteMessage(string|int $chat_id, string|int $msg_id, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('deleteMessage', [
        'chat_id' => $chat_id,
        'message_id' => $msg_id,
    ], $sync, $token_bot);

}

function BanChatMember(string|int $chat_id, string|int $user_id, int $until_date = 0, bool $revoke_messages = true, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('banChatMember', [
        'chat_id'         => $chat_id,
        'user_id'         => $user_id,
        'until_date'      => $until_date,
        'revoke_messages' => $revoke_messages,
    ], $sync, $token_bot);

}

function UnbanChatMember(string|int $chat_id, string|int $user_id, bool $only_if_banned = true, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('unbanChatMember', [
        'chat_id'        => $chat_id,
        'user_id'        => $user_id,
        'only_if_banned' => $only_if_banned,
    ], $sync, $token_bot);

}

function BanChatSenderChat(string|int $chat_id, int $sender_chat_id, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('banChatSenderChat', [
        'chat_id'         => $chat_id,
        'sender_chat_id'  => $sender_chat_id,
    ], $sync, $token_bot);

}

function UnbanChatSenderChat(string|int $chat_id, int $sender_chat_id, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('unbanChatSenderChat', [
        'chat_id'         => $chat_id,
        'sender_chat_id'  => $sender_chat_id,
    ], $sync, $token_bot);

}

function ForwardMessage(string|int $to_chat_id, string|int $from_chat_id, string|int $msg_id, bool $notify = false, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('forwardMessage', [
        'chat_id'      => $to_chat_id,
        'from_chat_id' => $from_chat_id,
        'message_id'   => $msg_id,
        'disable_notification' => !($notify),
    ], $sync, $token_bot);

}

function CopyMessage(string|int $to_chat_id, string|int $from_chat_id, string|int $msg_id, string|int|null $reply_msg = null, array $content = [], null|bool $sync = null, string|null $token_bot = null) : array|string|null {

    $content['chat_id']             = $to_chat_id;
    $content['from_chat_id']        = $from_chat_id;
    $content['message_id']          = $msg_id;
    $content['reply_to_message_id'] = $reply_msg;

    $content['allow_sending_without_reply'] = $content['allow_sending_without_reply'] ?? true;
    $content['parse_mode']                  = $content['parse_mode'] ?? 'HTML';

    return TelegramAPI('copyMessage', $content, $sync, $token_bot);
    
}

function LeaveChat(string|int $chat_id, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('leaveChat', [
        'chat_id' => $chat_id,
    ], $sync, $token_bot);

}

function PinMessage(string|int $chat_id, string|int $msg_id, bool $notify = false, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('pinChatMessage', [
        'chat_id'    => $chat_id,
        'message_id' => $msg_id,
        'disable_notification' => !($notify),
    ], $sync, $token_bot);

}

function UnpinChatMessage(string|int $chat_id, string|int $msg_id, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('unpinChatMessage', [
        'chat_id'    => $chat_id,
        'message_id' => $msg_id,
    ], $sync, $token_bot);

}

function UnpinAllChatMessages(string|int $chat_id, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('unpinAllChatMessages', [
        'chat_id'    => $chat_id,
    ], $sync, $token_bot);

}

function GetChat(string|int $chat_id, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('getChat', [
        'chat_id' => $chat_id,
    ], true, $token_bot)['result'] ?? null;

}

function GetChatAdministrators(string|int $chat_id, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('getChatAdministrators', [
        'chat_id' => $chat_id,
    ], true, $token_bot)['result'] ?? null;

}

function GetChatMemberCount(string|int $chat_id, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('getChatMemberCount', [
        'chat_id' => $chat_id,
    ], true, $token_bot)['result'] ?? null;

}

function GetChatMember(string|int $chat_id, string|int $user_id, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('getChatMember', [
        'chat_id' => $chat_id,
        'user_id' => $user_id,
    ], true, $token_bot)['result'] ?? null;

}

function AnswerCallbackQuery(string|int $callback_query_id, string $text, bool $alert = false, int $cache_time = 0, string $url = '', null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('answerCallbackQuery', [
        'callback_query_id' => $callback_query_id,
        'text'              => $text, 
        'show_alert'        => $alert,
        'url'               => $url,
        'cache_time'        => $cache_time,
    ], $sync, $token_bot);

}

function EditMessageText(null|string|int $chat_id = null, null|int|string $message_id = null, string|array $text, null|int|string $inline_message_id = null, array $content = [], null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    if ($inline_message_id !== null) {

        $content['inline_message_id'] = $inline_message_id;

    }else{

        $content['chat_id'] = $chat_id;
        $content['message_id'] = $message_id;

    }

    $content['text']    = !(is_string($text)) ? json_encode($text) : $text;

    $content['disable_web_page_preview'] = $content['disable_web_page_preview'] ?? true;
    $content['parse_mode']               = $content['parse_mode'] ?? 'HTML';

    return TelegramAPI('editMessageText', $content, $sync, $token_bot);

}

function EditMessageReplyMarkup(null|string|int $chat_id = null, null|int|string $message_id = null, array|string $reply_markup = [], null|int|string $inline_message_id = null, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    $content = [];

    if ($inline_message_id !== null) {

        $content['inline_message_id'] = $inline_message_id;

    }else{

        $content['chat_id'] = $chat_id;
        $content['message_id'] = $message_id;

    }

    $content['reply_markup'] = is_array($reply_markup) ? json_encode($reply_markup) : $reply_markup;

    return TelegramAPI('editMessageReplyMarkup', $content, $sync, $token_bot);

}

function AnswerInlineQuery(string|int $inline_query_id, string|array $results, bool $is_personal = false, int $cache_time = 300, string|int|null $next_offset = null, string|null $switch_pm_text = null, string|null $switch_pm_parameter = null, null|bool $sync = null, null|string $token_bot = null) : array|string|null {

    return TelegramAPI('answerInlineQuery', [
        'inline_query_id'     => $inline_query_id,
        'results'             => is_array($results) ? json_encode($results) : $results, 
        'is_personal'         => $is_personal,
        'cache_time'          => $cache_time,
        'next_offset'         => $next_offset,
        'switch_pm_text'      => $switch_pm_text,
        'switch_pm_parameter' => $switch_pm_parameter,
    ], $sync, $token_bot);

}

/* Telegram Bot API Methods */



/* Helper Methods */

function ReportToAdmin(string|array $text, null|bool $sync = null) : bool {
    
    if (defined('dpx_admin')) {

        if (is_array(dpx_admin)) {

            foreach (array_unique(dpx_admin) as $uid) {

                SendMessage($uid, $text, null, [], $sync);

            }

        }else{

            SendMessage(dpx_admin, $text, null, [], $sync);

        }

        return true;

    }else{
        
        return false;

    }

}

function IsUserMemberOf(string|int $user_id, string|int $chat_id, null|string $token_bot = null) : bool {

    $chat_member = GetChatMember($chat_id, $user_id, $token_bot);

    if ($chat_member === null) {

        return false;

    }else{

        return isset($chat_member['status']) && $chat_member['status'] !== 'left' && $chat_member['status'] !== 'kicked';

    }

}

function IsUserAdminOf(string|int $user_id, string|int $chat_id, null|string $token_bot = null) : bool {
    
    foreach (GetChatAdministrators($chat_id, $token_bot) as $admin) {

        if ((string)$admin['user']['id'] === (string)$user_id) {

            return true;

        }

    }

    return false;

}

function CheckUserRemainingSponsors(string|int $user_id, array $sponsors, null|string $token_bot = null, string|null $bot_api_server = null) : array {

    if ($token_bot === null) {

        if (defined('dpx_token')) {

            $token_bot = dpx_token;

        }else{

            return 'empty token';

        }

    }

    $mh = curl_multi_init();
   
    $CurlHandles = [];

    foreach ($sponsors as $sponsor) {

        if (defined('dpx_bot_api_server')) {

            $bot_api_server = dpx_bot_api_server;
    
        }else if ($bot_api_server === null) {
    
            $bot_api_server = 'https://api.telegram.org';
    
        }

        $url = "$bot_api_server/bot$token_bot/getChatMember";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
    
        curl_setopt($ch, CURLOPT_POST, 1);
    
        $content = [
            'chat_id' => $sponsor['chat_id'],
            'user_id' => $user_id,
        ];
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

        $CurlHandles[] = $ch;
        curl_multi_add_handle($mh, $ch);

    }
   
    $active = null;

    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);

    while ($active && $mrc == CURLM_OK) {

        if (curl_multi_select($mh) != -1) {

            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        }

    }
   
    foreach ($CurlHandles as $key => $ch) {
        
        $chat_member = json_decode(curl_multi_getcontent($ch), true)['result'] ?? [];
        
        curl_multi_remove_handle($mh, $ch);

        if (isset($chat_member['status']) && $chat_member['status'] !== 'left' && $chat_member['status'] !== 'kicked') {

            unset($sponsors[$key]);

        }

    }

    curl_multi_close($mh); 

    return array_values($sponsors);

}

function MentionUserByID(string|int $user_id, string $text) : string {
    return "<a href='tg://user?id=$user_id'>$text</a>";
}

function IsUserFlooding(string|int $user_id, int $flood_limit_count = 10, int $flood_limit_time = 120, string|int $stack = 'global', null|string $token_bot = null) : bool|string|null {

    if ($token_bot === null) {

        if (defined('dpx_token')) {

            $token_bot = dpx_token;

        }else{

            return 'empty token';

        }

    }

    $stack = md5($token_bot . $stack);

    $file = sys_get_temp_dir() . "/$stack";

    if (!(file_exists($file))) {

        file_put_contents($file, '[]');
        chmod($file, 0777);

        $data = [];

    }

    $i = 0;

    while (0644 === (fileperms($file) & 0777) && $i < 20) {

        usleep(12500);
        $i++;

    }

    chmod($file, 0644);

    $data = file_get_contents($file);
    $data = json_decode($data, true);

    $now = time();
    $now = $now - ($now % 60);

    if ($data === null || $data === false) {

        $data = [$now, []];

    }else if (is_array($data) && count($data) < 2) {

        $data = [$now, []];

    }

    if (($data[0] + $flood_limit_time) <= $now) {

        $data[1] = [];

    }

    $count = $data[1][$user_id] ?? 0;
    $count++;
    $data[1][$user_id] = $count;

    $data[0] = $now;

    if ((int)$count <= (int)((int)$flood_limit_count + 1)) {

        file_put_contents($file, json_encode($data));

    }

    chmod($file, 0777);

    if ((int)$count === (int)$flood_limit_count) {

        return true;

    }else if ((int)$count > (int)$flood_limit_count) {

        return null;

    }else{

        return false;

    }

}