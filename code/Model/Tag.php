<?php

class Model_Tag
{
    protected $_userId;

    /** @var ORM */
    protected $_tagModel;

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
            ->order_by_asc('biggest_gap_last_30_days')
            ->find_many();

        return $tags;
    }

    public function loadByTag($userId, $tag)
    {
        $tag = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', $userId)
            ->where_equal('tag', $tag)
            ->find_one();

        if (! $tag) {
            throw new Exception("Wasn't able to find tag: $tag");
        }

        $this->_tagModel = $tag;
        return $this;
    }

    public function loadByTagId($userId, $tagId)
    {
        $tag = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', $userId)
            ->where_equal('tag_id', $tagId)
            ->find_one();

        if (! $tag) {
            throw new Exception("Wasn't able to find tag: $tag");
        }

        $this->_tagModel = $tag;
        return $this;
    }

    public function getTag()
    {
        if (! isset($this->_tagModel['tag'])) {
            throw new Exception("Couldn't find tag in tag data");
        }

        return $this->_tagModel['tag'];
    }

    public function getBiggestGap()
    {
        if (! isset($this->_tagModel['biggest_gap_last_30_days'])) {
            throw new Exception("Couldn't find tag in tag data");
        }

        return $this->_tagModel['biggest_gap_last_30_days'];
    }

    public function getLastSent()
    {
        if (! isset($this->_tagModel['last_sent'])) {
            throw new Exception("Couldn't find last_sent in tag data");
        }

        return $this->_tagModel['last_sent'];
    }

    public function getSubject()
    {
        if (! isset($this->_tagModel['tag_subject'])) {
            throw new Exception("Couldn't find tag_subject in tag data");
        }

        return $this->_tagModel['tag_subject'];
    }

    public function getSendCount30Days()
    {
        if (! isset($this->_tagModel['send_count_30_days'])) {
            throw new Exception("Couldn't find send_count_30_days in tag data");
        }

        return $this->_tagModel['send_count_30_days'];
    }

    public function processTag()
    {
        if (! isset($this->_tagModel)) {
            throw new Exception("Tag data hasn't been loaded yet");
        }

        $session = new Model_Session();

        $mandrill = new Model_Mandrill();
        $mandrill->setKey($session->getKey());

        $timeSeries = $mandrill->fetchTagTimeSeries($this->getTag());
        $sentCount30Days = $mandrill->getSentInLast30Days($this->getTag());

        $lastSent = $this->_getLastSent($timeSeries);
        $biggestGap = $this->_getBiggestGap($timeSeries);

        $this->_tagModel->set('last_sent', $lastSent)
            ->set_expr('updated_at', 'NOW()')
            ->set('biggest_gap_last_30_days', $biggestGap)
            ->set('send_count_30_days', $sentCount30Days);

        // This has to happen after the sets above b/c it depends on them (ugh).
        $defaultToActive = $this->defaultToActive($this->_tagModel);

        $this->_tagModel->set('is_active', $defaultToActive)
            ->save();
    }

    public function processSubjectLine()
    {
        if (! isset($this->_tagModel)) {
            throw new Exception("Tag data hasn't been loaded yet");
        }

        $session = new Model_Session();

        $mandrill = new Model_Mandrill();
        $mandrill->setKey($session->getKey());

        try {
            $lastMessage = $mandrill->fetchLastMessage($this->getTag());
            $subject = (isset($lastMessage['subject']) ? $lastMessage['subject'] : "Not found");
        } catch (Exception $e) {
            $subject = null;
        }

        $this->_tagModel->set('tag_subject', $subject)
            ->save();
    }

    protected function _getLastSent($timeSeries)
    {
        $first = $timeSeries[0]['time'];
        return $first;
    }

    protected function _getBiggestGap($timeSeries)
    {
        $lastHourlyData = null;
        $maxDifference = 0;

        foreach ($timeSeries as $hourlyData) {
            if ($lastHourlyData) {
                $lastTime = new \Carbon\Carbon($lastHourlyData['time']);
                $thisTime = new \Carbon\Carbon($hourlyData['time']);
                if ($thisTime->diffInDays(\Carbon\Carbon::now()) > 30) {
                    break;
                }

                $difference = $thisTime->diffInHours($lastTime);
                $maxDifference = max($difference, $maxDifference);

            }

            $lastHourlyData = $hourlyData;
        }

        return $maxDifference;
    }

    public function formatBiggestGap($biggestGapInHours)
    {
        if ($biggestGapInHours > 48) {
            return round($biggestGapInHours / 48) . "d";
        }

        return $biggestGapInHours . "h";
    }

    public function formatLastSent($lastSent)
    {
        $lastSent = new \Carbon\Carbon($lastSent);
        $lastSentHoursAgo = $lastSent->diffInHours();

        return ($lastSentHoursAgo > 48) ? round($lastSentHoursAgo / 48) . "d" : $lastSentHoursAgo . "h";
    }

    /**
     * @param $tag ORM
     * @return string
     */
    public function lastSentStatus($tag)
    {
        $biggestGap = $tag['biggest_gap_last_30_days'];
        $lastSent = new \Carbon\Carbon($tag['last_sent']);
        $lastSentHoursAgo = $lastSent->diffInHours();

        return ($lastSentHoursAgo > $biggestGap) ? "bad" : "good";
    }

    /**
     * @param $tag ORM
     * @return bool
     */
    public function defaultToActive()
    {
        if ($this->getSendCount30Days() < 10) {
            return false;
        }

        return true;
    }
}