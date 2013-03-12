<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Security;

/**
 * @author msmith
 * @created 3/7/13 12:50 PM
 */
class IPRange
{
    protected $begin, $end, $expire, $note;

    function __construct($begin, $end, $expire = null, $note = null)
    {
        if(!$beginLong = ip2long($begin)){
            throw new \InvalidArgumentException(sprintf('Invalid beginning ip "%s"', $begin));
        }

        if(!$endLong = ip2long($end)){
            throw new \InvalidArgumentException(sprintf('Invalid ending ip "%s"', $end));
        }

        $expireTS = null;
        if($expire){
            if(!$expireTS = strtotime($expire)){
                throw new \InvalidArgumentException(sprintf('Invalid expire date time "%s"', $end));
            }
        }

        $this->begin = $beginLong;
        $this->end = $endLong;
        $this->expire = $expireTS;
        $this->note = $note;
    }

    public function isAuthorized($ip)
    {
        $long = ip2long($ip);
        if(!$long){
            throw new \InvalidArgumentException(sprintf('Invalid ip "%s"', $ip));
        }

        if($long >= $this->begin && $long <= $this->end){
            if(!$this->expire || $this->expire >= time()){

                return true;
            }
        }

        return false;
    }
}
