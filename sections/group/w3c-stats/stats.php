<?php

if ($message === 'hahaha') {

    $battle_tag = GetUserDataBy('battle_tag', 'user_id', $from_id);

    if ($battle_tag) {
    
        SendMessage($chat_id, W3CAPI("/players/" . urlencode($battle_tag)), $msg_id);

    }

}