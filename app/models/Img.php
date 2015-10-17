<?php
/**
*
*/
class Img
{

    public $id;
    public $category;
    
    public function __construct($cate, $id)
    {
        $this->category = $cate;
        $this->id = $id;
    }

    public function save($id)
    {
        $oss = new AliyunOss($this->category, $this->id, $id);
        $re = $oss->save();
        $imgs = Img::attachHost($re);
        return $imgs;
    }

    public function getSavedImg($newId, $string = '', $array = false)
    {
        $imgs = $this->save($newId);
        
        if ($string) {
            $o = explode(',', $string);
            $oldImgs = Img::attachKey($o);
            if (!empty($imgs)) {
                foreach ($imgs as $key => $i) {
                    if (array_key_exists($key, $oldImgs)) {
                        unset($oldImgs[$key]);
                    }
                }
            }
            if (!empty($oldImgs)) {
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
        $oss = new AliyunOss($this->category, '', $this->id);
        $re = $oss->getList();
        $imgs = Img::attachHost($re);
        return $imgs;
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
        if (empty($array)) {
            return $array;
        }
        foreach ($array as $key => $img) {
            if (strpos($key, $needle) !== false) {
                $re[] = $img;
            }
        }
        return $re;
    }

    public static function attachHost($crud = [])
    {
        if (empty($crud)) {
            return [];
        }
        $host = Config::get('app.imghost');
        $array = [];
        foreach ($crud as $key => $img) {
            $array[$key] = $host.$img;
        }

        return $array;
    }
}
