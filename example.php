<?php

require_once('./FriendFeed.class.php');

$ff = new FriendFeed('dotevan', include 'api_key.inc');

if($ff->validate()) {
    $message = new FriendFeedMessage();
    $message->setBody('This is a message from the FriendFeed class.');
    if(($entryData = $ff->postEntry($message))) {
        echo "I created the entry!\n";
        $message = new FriendFeedMessage();
        $message->setEntry($entryData->id)->setBody('This is the comment that I am adding!');
        if(($commentData = $ff->postComment($message))) {
            echo "I created the comment!\n";
        } else {
            echo "I was unable to create the comment.\n";
        }

        $message = new FriendFeedMessage();
        $message->setEntry($entryData->id);
        if($ff->deleteEntry($message)) {
            echo "I deleted the entry!\n";
        }
    } else {
        echo "I was unable to create the entry.\n";
    }
} else {
    echo "Unable to validate the user via the api key.\n";
}

echo "Done!\n";