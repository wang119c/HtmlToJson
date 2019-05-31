<?php
/**
 * Created by PhpStorm.
 * User: huizi
 * Date: 2019/5/28
 * Time: 10:22
 */

namespace app\bmapp\service;

use app\bmapp\exception\ParameterException;
use OSS\Core\OssException;
use OSS\OssClient;

/**
 * 解析 把html 转换成 json
 * Class HtmlToJson
 * @package app\bmapp\service
 */
class HtmlToJson
{
    public $textTag = ['body', "html", "meta", "head", "br"];
    public $textTagOther = ['strong', "span", "b", "h3"];


    /**
     * 转成json 格式
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/28-17:28
     */
    public function toJson($html)
    {
        $dom = new \DOMDocument();
        $libxmlPreviousState = libxml_use_internal_errors(true);
        $dom->loadHTML("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />" . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($libxmlPreviousState);
        $this->elementToObj($dom->documentElement, $ret);
        return $this->arrToJson($ret);
    }

	/**
     * 转成html
     * @param $jsonArr
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/31-16:57
     * @return string
     */
    public function toHtml($jsonArr){
        $html = "";
        foreach ($jsonArr as $key=>$value){
            if($value['type'] == "text"){
                $html .= "<p style='margin-bottom: 5px'>".$value['value']."</p>";
            }
            if( $value['type'] == "image" ){
                $html .= "<img src='".$value['value']."' style='width: 100%'></img>";
            }
        }
        return $html;
    }

    /**
     * 数组转化成json对象
     * @param $array
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/29-11:57
     * @return array
     * @throws ParameterException
     * @throws \think\Exception
     */
    private function arrToJson($array)
    {
        $jsonArr = [];
        foreach ($array as $key => $val) {
            if (
                (isset($val['tag']) && $val['tag'] == "div") ||
                (isset($val['tag']) && $val['tag'] == "p") ||
                (isset($val['tag']) && $val['tag'] == "strong") ||
                (isset($val['tag']) && $val['tag'] == "span") ||
                (isset($val['tag']) && $val['tag'] == "b") ||
                (isset($val['tag']) && $val['tag'] == "h3") ||
                (isset($val['tag']) && $val['tag'] == "ul") ||
                (isset($val['tag']) && $val['tag'] == "li")
            ) {
                if (isset($val['html']) && trim($val['html']) != "") {
                    $jsonArr[$key]['type'] = "text";
                    $jsonArr[$key]['value'] = $val['html'];
                }
            }
            if (isset($val['tag']) && $val['tag'] == "img") {
                if (isset($val['src']) && trim($val['src']) != "") {
                    $jsonArr[$key]['type'] = "image";
                    $jsonArr[$key]['value'] = $this->imgCoverNative($val['src']);
                }
            }
        }
        return $jsonArr;
    }

    /**
     * 转化图片到本地
     * @param $imgUrl
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/29-11:54
     * @return string
     * @throws ParameterException
     * @throws \think\Exception
     */
    public function imgCoverNative($imgUrl,$host="http://127.0.0.1/",$savePath=""){
        if(  $savePath == ""){
            $folder = __DIR__."test/".date("Ymd")."/";
            $fileName = md5($imgUrl).".png";
            $savePath =  $folder . $fileName ; 
        }
        if (
            (strpos($imgUrl, "http://") == false) &&
            (strpos($imgUrl, "https://") == false)
        ) {
            $imgUrl = "http:" . $imgUrl;
        }
        if (
            (strpos($imgUrl, ".jpg") == false ) &&
            (strpos($imgUrl, ".png") == false ) &&
            (strpos($imgUrl, ".jpeg") == false ) &&
            (strpos($imgUrl, ".gif") == false)
        ) {
            $imgUrl = $imgUrl.".png" ;
        }
        $content =  file_get_contents($imgUrl);
        file_put_contents($content);
        return  $host.$savePath ; 
    }


    /**
     * 转化图片到本地（oss）
     * @param $imgUrl
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/29-11:54
     * @return string
     * @throws ParameterException
     * @throws \think\Exception
     */
    // public function imgCoverNative($imgUrl)
    // {
    //     $endpoint = config('oss.endpoint_internal');
    //     $bucket = config('oss.bucket');
    //     $accessId = config('oss.access_id');
    //     $accessSecret = config('oss.access_secret');
    //     $ossHost = config('oss.cdn_host');
    //     try {
    //         if (
    //             (strpos($imgUrl, "http://") == false) &&
    //             (strpos($imgUrl, "https://") == false)
    //         ) {
    //             $imgUrl = "http:" . $imgUrl;
    //         }
    //         if (
    //             (strpos($imgUrl, ".jpg") == false ) &&
    //             (strpos($imgUrl, ".png") == false ) &&
    //             (strpos($imgUrl, ".jpeg") == false ) &&
    //             (strpos($imgUrl, ".gif") == false)
    //         ) {
    //             $imgUrl = $imgUrl.".png" ;
    //         }
    //         $content =  file_get_contents($imgUrl);
    //         $folder = "shuzidao/".date("Ymd")."/";
    //         $fileName = md5($imgUrl).".png";
    //         $object = $folder.$fileName;
    //         $ossClient = new OssClient($accessId, $accessSecret, $endpoint);
    //         $ossClient->putObject($bucket, $object, $content);
    //         return $ossHost.'/'.$object;
    //     } catch (OssException $e) {
    //         throw new ParameterException([
    //             'code' => 20003,
    //             'msg' => '文件转化失败'
    //         ]);
    //     }
    // }

    /**
     * 提取element 数组元素标签
     * @param $element
     * @param $ret
     * Created by PhpStorm.
     * Author:huizi
     * Date: 2019/5/28-17:26
     * @return array
     */
    private function elementToObj($element, &$ret)
    {
        if (!isset($element->tagName)) {
            return $ret;
        }
        $obj = array("tag" => $element->tagName);
        foreach ($element->attributes as $attribute) {
            $obj[$attribute->name] = $attribute->value;
        }
        $noadd = 1;
        foreach ($element->childNodes as $subElement) {
            if ($subElement->nodeType == XML_TEXT_NODE) {
                $obj['html'] = $subElement->wholeText;
                $ret[] = $obj;
                $noadd = 0;
            } else {
                $this->elementToObj($subElement, $ret);
            }
        }
        // 去除无用元素
        if (in_array($obj['tag'], $this->textTag)) {
            $noadd = 0;
        }
        // 去除无用元素
        if (in_array($obj['tag'], $this->textTagOther) && !isset($obj['html'])) {
            $noadd = 0;
        }
        if ($noadd == 1) {
            $ret[] = $obj;
        }
        return $obj;
    }
}