<?php

if (preg_match("/race\s?(undead|orc|human|random|night\s?elf)/i", $message, $Race) && !($IsInReplyTo)) {

    $Race = strtolower(str_replace(' ', '', $Race[1]));

    UserCreateIfNotExist($from_id);

    $conn->query("UPDATE `Users` SET `race` = '$Race' WHERE `user_id` = '$from_id'");

    $emoji = $races[$Race]['emoji']['circle'];

    $Race = ucfirst($Race);

    SendMessage($chat_id, "$emoji Your Race is set to <b>$Race</b> $emoji", $msg_id);

}