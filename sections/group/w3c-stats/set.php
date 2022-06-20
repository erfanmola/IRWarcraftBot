<?php

if (preg_match("/set\s?(\w+\#\d+)/i", $message, $BattleTag) && !($IsInReplyTo)) {

    $BattleTag = $BattleTag[1];

    UserCreateIfNotExist($from_id);

    $conn->query("UPDATE `Users` SET `battle_tag` = '$BattleTag' WHERE `user_id` = '$from_id'");

    SendMessage($chat_id, "🔷 <b>$BattleTag</b> has been set as your Battle Tag 🔷", $msg_id);

}