<?php

class Model_Cron
{
    protected $_username;
    protected $_tagToProcess;
    protected $_badTags = array();

    public function __construct()
    {
        $configJsonFile = dirname(dirname(dirname(__FILE__))) . "/etc/local.json";
        $json = file_get_contents($configJsonFile);
        $configArray = json_decode($json, true);

        $this->_data = $configArray;
    }

    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function setTagToProcess($tag)
    {
        $this->_tagToProcess = $tag;
        return $this;
    }

    public function getTagToProcess()
    {
        return $this->_tagToProcess;
    }

    public function run()
    {
        $orm = ORM::for_table('insightengine_users')
            ->where_equal('is_active', 1);
        
        if ($this->getUsername()) {
            $orm->where_equal('username', $this->getUsername());
        }

        $users = $orm->find_many();
        foreach ($users as $userRecord) {
            $user = new Model_User($userRecord);
            $this->_runForUser($user);
        }
    }

    /**
     * @param $user Model_User
     */
    protected function _runForUser($user)
    {
        $log = new Model_Log();
        $log->log("Processing user " . $user->getUsername());

        $this->_badTags = array();

        $orm = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', $user->getUserId())
            ->where_equal('is_active', 1)
            ->order_by_asc('updated_at');

        if ($this->getTagToProcess()) {
            $orm->where_equal('tag', $this->getTagToProcess());
        }

        $tags = $orm->find_many();

        foreach ($tags as $tagRecord) {
            $tag = new Model_Tag($tagRecord);
            $this->_processTag($user, $tag);
        }

        if (!empty($this->_badTags)) {
            $this->_notifyBadTags($user);
        }
    }

    /**
     * @param $user Model_User
     * @param $tag Model_Tag
     */
    protected function _processTag($user, $tag)
    {
        $log = new Model_Log();

        $session = new Model_SessionForCron();
        $session->setKey($user->getMandrillApiKey());
        $tag->setSession($session)->processTag();

        $biggestGap = $tag->getBiggestGap();
        $log->log("Processed tag " . $tag->getTag() . ": biggest gap is $biggestGap");

        if ($tag->lastSentStatus($tag->getTagRecord()) == 'bad') {
            $this->_badTags[] = $tag;
        }

        if (! $tag->getSubject()) {
            $tag->processSubjectLine();
            $log->log("Processed tag subject line " . $tag->getTag() . ": " . $tag->getSubject());
        }
    }

    /**
     * @param $user Model_User
     */
    protected function _notifyBadTags($user)
    {
        $username = $user->getUsername();
        $message = "User: $username \r\n\r\n";

        /** @var $tag Model_Tag */
        foreach ($this->_badTags as $tag) {
            $message .= "Tag: " . $tag->getTag() . ": " . $tag->getSummary($tag->getTagRecord());
        }

        $log = new Model_Log();
        $log->log("Notifying $username of bad tags");

        mail("kalen@magemail.co", "InsightEngine Alert for $username", $message, "From: cron@magemail.co");

        return $this;
    }
}