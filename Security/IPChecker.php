<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Security;

/**
 * @author msmith
 * @created 3/7/13 12:49 PM
 */
class IPChecker
{
    protected $ranges = array();

    public function addFile($filename)
    {
        $this->addJSON(file_get_contents($filename));
    }

    public function addJSON($json)
    {
        foreach(json_decode($json) as $range){
            $this->addIPRange(
                $range->begin,
                $range->end,
                isset($range->expire) ? $range->expire : null,
                isset($range->note) ? $range->note : null
            );
        }
    }

    public function addIP($ip, $expire = null, $note = null)
    {
        $this->addIPRange($ip, $ip, $expire, $note);
    }

    public function addIPRange($begin, $end, $expire = null, $note = null)
    {
        $this->addIPRangeObject(new IPRange($begin, $end, $expire, $note));
    }

    public function addIPRangeObject(IPRange $range)
    {
        $this->ranges[] = $range;
    }

    public function isAuthorized($ip)
    {
        foreach($this->ranges as $range){
            /** @var $range IPRange */
            if($range->isAuthorized($ip)){

                return true;
            }
        }

        return false;
    }
}
