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
        if (!$re['result']) {
            throw new Exception("保存图片失败", 2);
        } else {
            $imgs = null;
            if (is_array($re['data']) && !empty($re['data'])) {
                foreach ($re['data'] as $key => $value) {
                    $imgs[$key] = $this->imghost.$this->imgpath.'/'.$this->category.'/'.$newId.'/'.$value;
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
            $oldImgs = null;
            foreach ($o as $value) {
                $key = Img::getKey($value);
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
        if (!$re['result']) {
            return false;
        }
        $imgs = null;
        if (is_array($re['data']) && !empty($re['data'])) {
            foreach ($re['data'] as $key => $value) {
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
        return json_decode($response, true);
    }

    public static function getKey($filename)
    {
        if (!$tmp = explode('.', $filename)) {
            return false;
        }

        return $tmp[0];
    }

    public static function getFileName($url)
    {
        if (strpos($url, '/') === false) {
            return $url;
        }

        $tmp = explode('/', $url);
        $filename = array_pop($tmp);
        return $filename;
    }

    public static function toArray($string)
    {
        $crud = explode(',', $string);
        if (empty($crud)) {
            return [];
        }
        $array = Img::attachKey($crud);
        if (empty($array)) {
            return null;
        }
        return $array;
    }

    public static function attachKey($crud = [])
    {
        if (empty($crud)) {
            return [];
        }

        $array = [];
        foreach ($crud as $img) {
            $name = Img::getFileName($img);
            $key = Img::getKey($name);
            if (!$key) {
                continue;
            }
            $array[$key] = trim($img);
        }

        return $array;
    }

    public static function filterKey($needle, $array = [])
    {
        $re = [];
        foreach ($array as $key => $img) {
            if (strpos($key, $needle) !== false) {
                $re[] = $img;
            }
        }
        return $re;
    }
}
