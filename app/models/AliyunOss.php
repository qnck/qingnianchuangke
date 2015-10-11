<?php
/**
*
*/
class AliyunOss
{
    private $_oss = null;
    private $_upload_token = '';
    private $_cate = '';
    private $_id = '';
    private $_bucket = 'qnckimg';

    public function __construct($cate)
    {
        $this->_cate = $cate;
        $basePath = base_path();
        require_once($basePath."/vendor/aliyunoss/alioss.class.php");
        require_once($basePath."/vendor/aliyunoss/thirdparty/xml2array.class.php");
        $this->_oss = new ALIOSS();
    }

    public function setToken($token)
    {
        $this->_upload_token = $token;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function upload($token)
    {
        $folder = $this->_cate;
        if (!$token) {
            throw new Exception("需要传入token", 20001);
        } elseif (!$this->_cate) {
            throw new Exception("需要传入cate", 20001);
        } else {
            if (!isset($_FILES)) {
                throw new Exception("没有文件传入", 20001);
            } else {
                foreach ($_FILES as $key => $file) {
                    if ($file['name'] && $file['error'] == 0 && $file['size'] > 0) {
                        // todo enhance with mime type
                        $obj = '_tmp/'.$this->_cate.'/'.$token.'/'.$key;
                        $re = $this->_oss->upload_file_by_file($this->_bucket, $obj, $file['tmp_name']);
                        if (!$re->isOK()) {
                            throw new Exception("图片:".$file['name'].'上传失败', 20001);
                            break;
                        }
                    }
                }
            }
        }
    }

    public function confirm($token)
    {

    }

    public function scan()
    {
        $options = [
            'delimiter' => '/',
            'prefix' => '_tmp/loan'
        ];
        $response = $this->_oss->list_object($this->_bucket, $options);
        if ($response->isOK()) {
            $re = $this->getResponseBodyArray($response);
            var_dump($re);
        } else {
            var_dump($response);
        }
    }

    public function checkDir()
    {
        $dir = '_tmp/'.$this->_cate;
        $this->makeDir($dir);
        $dir = $this->_cate;
        $this->makeDir($dir);
    }

    public function save()
    {

    }

    public function makeDir($dir)
    {
        // chk _tmp
        $re = $this->_oss->get_object($this->_bucket, $dir.'/');
        if (!$re->isOK()) {
            $re = $this->_oss->create_object_dir($this->_bucket, $dir);
            if (!$re->isOK()) {
                throw new Exception("文件夹创建失败", 20001);
            }
        }
        return true;
    }

    public function getResponseBodyArray($response)
    {
        return $re = XML2Array::createArray($response->body);
    }

    public function getExt($file)
    {
        if (strpos($file['name'], '.') !== false) {
            $split = explode('.', $file['name']);
            $ext = array_pop($split);
        } else {
            switch ($file['type']) {
                case 'jpeg':
                case 'image/jpeg':
                    $ext = 'jpg';
                    break;
                case 'gif':
                case 'image/gif':
                    $ext = 'gif';
                    break;
                case 'png':
                case 'image/png':
                    $ext = 'png';
                    break;
                case 'zip':
                case 'application/zip':
                    $ext = 'zip';
                    break;
                case 'audio/wav':
                case 'x-wav':
                    $ext = 'wav';
                    break;
                default:
                    $ext = 'jpg';
                    break;
            }
        }
        return $ext;
    }
}
