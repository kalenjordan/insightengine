<?php

class Model_Cron
{
    protected $_username;
    protected $_tagToProcess;

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
        $orm = ORM::for_table('insightengine_users');
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
        $orm = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', $user->getUserId())
            ->order_by_asc('updated_at');

        if ($this->getTagToProcess()) {
            $orm->where_equal('tag', $this->getTagToProcess());
        }

        $tags = $orm->find_many();

        foreach ($tags as $tagRecord) {
            $tag = new Model_Tag($tagRecord);
            $this->_processTag($user, $tag);
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

        if (! $tag->getSubject()) {
            $tag->processSubjectLine();
            $log->log("Processed tag subject line " . $tag->getTag() . ": " . $tag->getSubject());
        }
    }
}