<?php
/**
 * FriendFeed API Class for PHP5
 *
 * This aims to be a simplistic interface to the FriendFeed API
 * using PHP 5.
 *
 * @package FriendFeed API
 * @author Evan Fribourg <evan@dotevan.com>
 * @copyright 2010. All Rights Reserved.
 * @license see LICENSE provided with this package.
 */

require_once (dirname(__FILE__) . '/FriendFeedMessage.class.php');

class FriendFeed {
    
    /**
     * Base url for the API
     */
    const API_BASE_URL = 'http://friendfeed-api.com/v2/';
    
    /**
     * Constants corresponding to the FF API v2 Options
     */
    const VALIDATE     = 'validate';
    const ENTRY        = 'entry';
    const ENTRY_DELETE = 'entry/delete';
    const COMMENT      = 'comment';
    const LIKE         = 'like';
    
    /**
     * FriendFeed username
     *
     * @var String
     */
    private $user;
    /**
     * FriendFeed API key
     *
     * @var String
     */
    private $api_key;
    
    /**
     * holds the curl information from the last curl request
     */
    private $response;
    private $info;
    
    public function __construct($user, $api_key) {
        $this->user = $user;
        $this->api_key = $api_key;
    }
    
    public function getInfo() {
        return @$this->info;
    }
    
    public function getResponse() {
        return @$this->response;
    }
    
    /**
     * Sends a VALIDATE request to the FriendFeed API
     * @return stdClass
     * @link http://friendfeed.com/api/documentation#validate
     */
    public function validate() {
        return $this->request(self::VALIDATE, new FriendFeedMessage());
    }
    
    /**
     * Posts an entry corresponding to the FriendFeedMessage
     * 
     * @param FriendFeedMessage $message
     * @return stdClass
     * @link http://friendfeed.com/api/documentation#write_entry
     */
    public function postEntry(FriendFeedMessage $message) {
        return $this->request(self::ENTRY, $message);
    }

    /**
     * Deletes an entry
     * 
     * @param FriendFeedMessage $message
     * @return stdClass
     */
    public function deleteEntry(FriendFeedMessage $message) {
        $message->moveEntryToId();
        return $this->request(self::ENTRY_DELETE, $message);
    }

    /**
     * Creates a comment against an entry
     * 
     * @param FriendFeedMessage $comment
     * @return stdClass
     * @link http://friendfeed.com/api/documentation#write_comment
     */
    public function postComment(FriendFeedMessage $comment) {
        return $this->request(self::COMMENT, $comment);
    }

    /**
     * Likes a specific entry
     * 
     * @param FriendFeedMessage $like
     * @return stdClass
     * @link http://friendfeed.com/api/documentation#write_like
     */
    public function like(FriendFeedMessage $like) {
        return $this->request(self::LIKE, $like);
    }

    /**
     * Submits the FriendFeedMessage to FriendFeed
     *
     * @param string $request_type
     * @param FriendFeedMessage $message
     * @return stdClass
     *
     */
    private function request($request_type, FriendFeedMessage $message) {
        $url = self::API_BASE_URL . $request_type;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if($this->user && $this->api_key) {
            curl_setopt($curl, CURLOPT_USERPWD, $this->user . ":" . $this->api_key);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }
        if($request_type != self::VALIDATE) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "json={$message}");
        }

        $this->response = curl_exec($curl);
        $this->info = curl_getinfo($curl);
        curl_close($curl);
        
        if($this->info['http_code'] != 200) {
            return null;
        }
        
        return json_decode($this->response);
    }

}
