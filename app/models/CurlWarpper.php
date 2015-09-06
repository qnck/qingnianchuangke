<?php
/**
*
*/
class CurlWarpper extends Eloquent
{
    /**
     * prepare curl connection
     * @author Kydz 2015-06-14
     * @return n/a
     */
    private function initCurl()
    {
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_URL, $this->sendUrl);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
    }
    
    /**
     * execute curl
     * @author Kydz 2015-06-14
     * @return object xml to obj
     */
    private function execCurl()
    {
        $re = curl_exec($this->ch);
        $re = new SimpleXMLElement($re);
        return $re;
    }

    /**
     * set post data
     * @author Kydz 2015-06-14
     * @param  array $data data to be posted
     */
    private function setPostData($data)
    {
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
    }
}
