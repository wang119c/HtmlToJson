 # HtmlToJson
 把html 代码转换成数组，适合小程序的展示，同时里面有把远程资源转换成本地图片(可做参考，如果有不懂可以联系:  wx:[wanghui119c]  共同进步)
> html to json 用法
```
 $html = '<div id="LeftTool" class="LeftTool"></div>
<!--内容-->
<div class="content-article">
  <!--导语-->
  <p class="one-p"><img src="//inews.gtimg.com/newsapp_bt/0/7676817611/1000" class="content-picture">
  </p>
  <p class="one-p">中国人民银行日前发布的《2019年一季度小额贷款公司统计数据报告》显示，截至2019年3月末，全国共有小额贷款公司7967家；贷款余额9272亿元，一季度减少273亿元。实际上，去年以来，小贷公司的数量和贷款余额均逐步下降。</p>
  <p class="one-p">有人说，小贷行业自2015年至今一直处于萎缩状态，2015年是行业的“分水岭”，在此之前，快速增长，短短四年间，贷款余额从不足2000亿元扩张至9000亿元；在此之后，久久横盘，陷入瓶颈。</p>
  <div id="Status"></div>
</div>';
(new HtmlToJson())->toJson($html);
结果:
array(27) {
  [5] => array(2) {
    ["type"] => string(5) "image"
    ["value"] => string(46) "//inews.gtimg.com/newsapp_bt/0/7676817611/1000"
  }
  [8] => array(2) {
    ["type"] => string(4) "text"
    ["value"] => string(296) "中国人民银行日前发布的《2019年一季度小额贷款公司统计数据报告》显示，截至2019年3月末，全国共有小额贷款公司7967家；贷款余额9272亿元，一季度减少273亿元。实际上，去年以来，小贷公司的数量和贷款余额均逐步下降。"
  }
  [10] => array(2) {
    ["type"] => string(4) "text"
    ["value"] => string(250) "有人说，小贷行业自2015年至今一直处于萎缩状态，2015年是行业的“分水岭”，在此之前，快速增长，短短四年间，贷款余额从不足2000亿元扩张至9000亿元；在此之后，久久横盘，陷入瓶颈。"
  }
}

```

> 远程图片转成本地图片用法

```
$imgUrl = "//inews.gtimg.com/newsapp_bt/0/7676817611/1000";
(new HtmlToJson())->imgCoverNative($imgUrl);
```

> json2html
```
(new HtmlToJson())->toHtml($json); (准确的说应该是数组)
```