<?php
/**
 * FriendFeedMessage class
 * Encapsulates a message to be sent to FriendFeed via the API
 *
 * @package FriendFeed API
 * @author Evan Fribourg <evan@dotevan.com>
 * @copyright 2010. All Rights Reserved.
 * @license see LICENSE provided with this package.
 */

class FriendFeedMessage {
    
    private $message = array();
    private $targets = array();
    
    /**
     * Sets the message body
     * 
     * @param String $body
     * @return FriendFeedMessage Provides a fluent interface
     */
    public function setBody($body) {
        $this->message['body'] = $body;
        return $this;
    }
    
    /**
     * Gets the message body
     * 
     * @return string
     */
    public function getBody() {
        return @$this->message['body'];
    }

    /**
     * Sets the entry_id that this message references
     * 
     * @param string $entry
     * @return FriendFeedMessage Provides a fluent interface
     */
    public function setEntry($entry) {
        $this->message['entry'] = $entry;

        return $this;
    }

    /**
     * Gets the entry_id that this message references.
     * 
     * @return string
     */
    public function getEntry() {
        return @$this->message['entry'];
    }
    
    /**
     * A specific place to post the message. If not specified it will default
     * to the API's current default, which is the user's personal feed. (me)
     * 
     * @param string $target
     * @return FriendFeedMessage Provides a fluent interface
     */
    public function addTarget($target) {
        if(!in_array($target, $this->targets)) {
            array_push($this->targets, $target);
        }

        return $this;
    }
    
    /**
     * retrieves a list of targets
     * @return array
     */
    public function getTargets() {
        if(isset($this->message['to'])) {
            $ret = $this->message['to'];
        } else {
            $ret = array();
        }
        
        return $ret;
    }
    
    /**
     * set/replace the targets entireley with a new list.
     * 
     * @param array $targets
     */
    public function setTargets(array $targets) {
        $this->targets = $targets;
    }

    /**
     * Moves the entry value into the id field.
     * Deals with the same value needed in different names for different calls
     * **sigh**
     *
     * @return FriendFeedMessage Provides a fluent interface
     */
    public function moveEntryToId() {
        $this->message['id'] = @$this->message['entry'];
        unset($this->message['entry']);
        return $this;
    }
    
    /**
     * Returns the message in a JSON encoded string
     * 
     * @return string JSON encoded
     */
    public function toJson() {
        if(count($this->targets) > 0) {
            $this->message['to'] = $this->targets;
        }

        return json_encode($this->message);
    }
    
    /**
     * Returns the message in a JSON encoded string when the object
     * is cast as a string
     * 
     * @return string
     */
    public function __toString() {
        return $this->toJson();
    }
}
