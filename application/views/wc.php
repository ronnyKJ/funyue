<?php $base_url = $this->config->item('base_url');?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="apple-mobile-web-app-capable" content="yes" />

    <link rel="apple-touch-icon" href="/public/modules/wc/img/icon-114.png">
    <link rel="apple-touch-icon" sizes="57x57" href="/public/modules/wc/img/icon-57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/public/modules/wc/img/icon-72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/public/modules/wc/img/icon-114.png">

    <title>FUN阅世界杯2014</title>

    <!-- style start -->
    <link rel="stylesheet" href="<?php echo $base_url;?>public/modules/wc/css/idangerous.swiper.css">
    <link rel="stylesheet" href="<?php echo $base_url;?>public/common/css/normalize.css">
    <link rel="stylesheet" href="<?php echo $base_url;?>public/modules/wc/css/main.css">
    <!-- style end -->
    <script type="text/javascript" name="baidu-tc-cerfication" src="http://apps.bdimg.com/cloudaapi/lightapp.js#3f51a0ac8b83d6c6a218facf56c9b5f6"></script>
    <script type="text/javascript">window.bd && bd._qdc && bd._qdc.init({app_id: 'afadec3d91ee644c942094fc'});</script>
</head>

<script id="date-info-tpl" type="text/x-jquery-tmpl">
    <div class="datetime" {{if isToday&&isCover}}{{else}}style="height: 46px;line-height: 46px;"{{/if}} >
        <span class="date">${dateStr}</span>{{if isToday&&isCover}}<span class="time">日报 更新于${updateTime}</span>{{/if}}
    </div>
    <div class="calendar"></div>
</script>

<script id="news-article-tpl" type="text/x-jquery-tmpl">
    <div class="swiper-container news-page">
        <div class="swiper-wrapper news-page-wrapper">
            <div class="swiper-slide cover page">
                <div class="top">
                    <div class="alpha"></div>
                    {{tmpl '#date-info-tpl'}}
                    <div class="picture" style="background-image:url(${cover});{{if from == 'lebo'}}height:60%{{/if}}">
                        <div class="white-mask"></div>
                        {{if from == "lebo"}}
                        <input class="lebo-src" type="hidden" value="${media_mod}" width="320" />
                        <div class="muplayer">
                            <div class="mucontrol play" data-id="${id}"></div>
                            <div class="progress"><span class="red">00:00</span><span class="gray">/00:00</span></div>
                        </div>
                        {{/if}}
                    </div>

                    <div class="content">
                        <div class="type line" style="color: ${typeColor}"><b class="ball" style="background-color: ${typeColor}">${indexStr}</b>${typeStr}</div>
                        <div class="title line">${title}</div>
                        <div class="resource line">
                            <span class="source">来源: <a href="${source_link}" target="_blank">${source}</a></span>
                            <span class="view">已阅读: ${views}</span>
                        </div>
                        <div class="tip">往下滑动阅读正文</div>
                    </div>
                </div>

                <div class="bottom">
                    {{html content}}
                    {{if ad_link }}
                        <a class="ad" target="_blank" href="${ad_link}">
                            <img height="100%" src="${ad_img}" />
                        </a>
                    {{/if}}
                </div>
            </div>
        </div>
    </div>
</script>

<script id="cover-tpl" type="text/x-jquery-tmpl">
    <div class="alpha"></div>
    <div class="alpha b-alpha"></div>
    {{tmpl '#date-info-tpl'}}
    <div class="day-count">
        <div class="logo"></div>
        <div class="right">
            <span class="title">
                {{if begin}}2014世界杯{{else}}世界杯倒计时{{/if}}
            </span>
            <div class="count">
                <span class="char">{{if begin}}第{{/if}}</span><span class="num">${remain[0]}</span><span class="num">${remain[1]}</span><span class="char">天</span>
            </div>
        </div>
    </div>
    <div class="start"><img src="http://m.baidu.com/static/search/ala/fy-read.gif" /><span>滑动阅读</span></div>
</script>

<script id="month-mark-tpl" type="text/x-jquery-tmpl">
    <div>
        <li class="month-mark" month="${month}"><span>${month}月</span></li>
    </div>
</script>

<script id="common-date-tpl" type="text/x-jquery-tmpl">
<div>
    <li class="date ${cls}" date="${dateStr}" count="${count}">
        <div class="date">${date}<span class="ri">日</span></div>
        {{if count > 0}}
        <div class="info">
            <div class="progress">共${count}篇</div>
        </div>
        {{/if}}
    </li>
</div>
</script>
<script id="future-date-tpl" type="text/x-jquery-tmpl">
    <div>
        <li class="future">
            <div class="date">${date}<span class="ri">日</span></div>
        </li>
    </div>
</script>


<script id="calendar-tpl" type="text/x-jquery-tmpl">
    <div class="calendar-month"><span>月</span></div>
    <div class="swiper-container calendar-swiper">
        <div class="swiper-wrapper calendar-wrapper">
            <div class="swiper-slide calendar-slide">
                <ul class="calendar-list">

                </ul>
            </div>
        </div>
    </div>
</script>
<script id="share-tpl" type="text/x-jquery-tmpl">
    <div class="share">
        <div class="up" news-id="${newsId}"></div>
        <div class="down" news-id="${newsId}"></div>
        <div class="share-btn weibo"></div>
    </div>
</script>
<body>
<div class="start-page"></div>
<div class="container">
    <div class="swiper-container horizon">
        <div class="swiper-wrapper endless-swiper-wrapper">
            
        </div>
    </div>
</div>
</body>
<script>
var _hmt = _hmt || [];
var startDate = <?php echo $startDate;?>;
var openDate = <?php echo $openDate;?>;
var latestDate = '<?php echo $latestDate;?>';
var newsUrl = "<?php echo isset( $mod ) && $mod == 'preview' ? '/admin/preview/news/tmp/' : '/wc/news/' ?>";
var countInfo = <?php echo json_encode( $countInfo );?>;
</script>

<!-- script start -->
<script type="text/javascript" src="<?php echo $base_url;?>public/vendor/jquery/jquery.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>public/vendor/hammerjs/dist/jquery.hammer.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>public/vendor/jquery-tmpl/jquery.tmpl.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>public/vendor/swiper/dist/idangerous.swiper-2.4-zkj.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>public/vendor/router/router.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>public/vendor/muplayer/player.min.js"></script>

<script type="text/javascript" src="<?php echo $base_url;?>public/modules/wc/js/data.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>public/modules/wc/js/EndlessSlider.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>public/modules/wc/js/App.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>public/modules/wc/js/main.js"></script>
<!-- script end -->

<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F2ff7fce22200e9eabac437d59b296501' type='text/javascript'%3E%3C/script%3E"));
</script>

</html>