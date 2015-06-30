<?php
/**
*
*/
class Img
{

    public $id;
    public $category;
    public $key;
    public $imghost;
    public $imgpath = 'img';
    
    public function __construct($cate, $id)
    {
        $this->category = $cate;
        $this->id = $id;
        $this->key = Config::get('app.imghostKey');
        $this->imghost = Config::get('app.imghost');
    }

    public function save($newId)
    {
        $params = ['key' => $this->key, 'hash' => $this->id, 'id' => $newId, 'cate' => $this->category];
        $re = $this->fireGetRequest($params, 'save');
        if (!$re->result) {
            throw new Exception("保存图片失败", 2);
        } else {
            $imgs = [];
            if (is_array($re->data) && !empty($re->data)) {
                foreach ($re->data as $key => $value) {
                    $imgs[$key] = $this->imghost.$this->imgpath.$value;
                }
            }
            return $imgs;
        }
    }

    public function getSavedImg($newId, $string = '', $array = false)
    {
        $imgs = $this->save($newId);

        if ($string) {
            $o = explode(',', $string);
            $oldImgs = [];
            foreach ($o as $value) {
                $key = $this->getKey($value);
                $oldImgs[$key] = $value;
            }
            foreach ($imgs as $key => $i) {
                if (array_key_exists($key, $oldImgs)) {
                    unset($oldImgs[$key]);
                }
            }
            if (!empty($odlImgs)) {
                foreach ($oldImgs as $key => $img) {
                    $imgs[$key] = $img;
                }
            }
        }

        if ($array) {
            return $imgs;
        } else {
            return implode(',', $imgs);
        }
    }

    public function getList()
    {
        $params = ['key' => $this->key, 'cate' => $this->category, 'id' => $this->id];
        $re = $this->fireGetRequest($params, 'get');
        if (!$re->result) {
            return false;
        }
        $imgs = [];
        if (is_array($re->data) && !empty($re->data)) {
            foreach ($re->data as $key => $value) {
                $imgs[] = $this->imghost.$this->imgpath.$value;
            }
        }
        return $imgs;
    }

    public function fireGetRequest($params, $op)
    {
        $urlParams = '';
        foreach ($params as $key => $value) {
            $urlParams .= '&'.$key.'='.$value;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->imghost.$op.'.php?'.$urlParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        return json_decode($response);
    }

    public function getKey($filename)
    {
        if (!$tmp = explode(',', $filename)) {
            return false;
        }

        return array_pop($tmp);
    }
}
