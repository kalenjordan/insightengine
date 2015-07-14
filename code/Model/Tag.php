<?php

class Model_Tag
{
    protected $_userId;

    public function setUserId($userId)
    {
        $this->_userId = $userId;
        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function fetchAll()
    {
        $tags = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', 1)
            ->find_many();

        return $tags;
    }
}