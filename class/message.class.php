<?php
/*******************************************************************************
 * File: message.class.php
 *
 * Desc: A Message object that handles data about a message, such as who
 * posted it or what keywords it contains, if any.
 *
 * Date: 2018-05-19
 ******************************************************************************/
class Message
{
    // Message id
    private $messageId;

    // Poster username
    private $username;

    // The message itself
    private $message;

    // The date and time for the post
    private $date;

    // string[] for keywords for the post
    private $keywords;

    /**
     * Message constructor. Sets data members and gets keywords for post.
     * @param $id int The id of the post.
     * @param $user string Username of the postee.
     * @param $msg string The message.
     * @param $dt string Date of the message.
     */
    public function __construct($id, $user, $msg, $dt)
    {
        // Sets data members
        $this->messageId = $id;
        $this->username = $user;
        $this->message = $msg;

        // Formats date to Y-m-d
        $this->date = new DateTime($dt);
        $this->date = $this->date->format("Y-m-d (H:i:s)");

        // Asks for keywords for post
        $this->keywords = $this->getKeywordsForPost();
    }

    /**
     * Gets the keywords for this post from the database.
     * @return string[] Keywords for the post, if any.
     */
    private function getKeywordsForPost(){
        // Array of strings
        $keywords = [];

        $query = "SELECT keyword.keyword FROM securitylab.keyword WHERE message_id = $1;";
        $param = array($this->messageId);

        $db = Database::getInstance();
        $result = $db->doParamQuery($query, $param);

        if ($result != null && $result != false){

            while ($row = pg_fetch_row($result)){
                $keywords[] = $row[0];
            }

            pg_free_result($result);
        }

        return $keywords;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string[]
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}