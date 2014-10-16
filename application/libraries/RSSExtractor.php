<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(APPPATH . 'third_party/autoloader.php');
require_once(APPPATH . 'third_party/Readability.inc.php');
require_once(APPPATH . 'libraries/GetImageSize.php');

Class RSSExtractor {

    public $articles = array();
    public $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function extract_images() {

        // function completeImageUrl($matches) {
        //     array_walk()
        // }

        foreach($this->articles as &$article) {
            $article['thumbnail'] = array();
            $link = parse_url($article['link']);
            $host = $link['host'];
            $path = dirname($link['path']);
            $html_fragment = html_entity_decode($article['content'], ENT_QUOTES);
            // TODO: 通过 preg_replace_callback 直接将不全的 url 补全
            // $html_fragment = preg_replace_callback('/<img(?:.+?)src="(.+?)"/i', 'completeImageUrl', $html_fragment);
            if(preg_match_all('/<img(?:.+?)src="(.+?)"/i', $html_fragment, $matches)) {
                foreach ($matches[1] as $image) {
                    if(stripos($image, "http") === false) {
                        $image = 'http://' . $host . $path . '/' . $image;
                    }
                    $imageSize = new ImageSize($image);
                    $imageSize->get();
                    if($imageSize->size >= 30000) {
                        array_push($article['thumbnail'], $image);
                    }
                }
            }
        }
    }

    // TODO: 基于相关性对文章进行过滤，并且与队伍、比赛及赛事相关联 (Larry)
    public function test_relevancy() {
        // 匹配一次获得的分数，标题权重远高于正文
        $weight = array(
            'content' => 1,
            'title' => 50
        );
        // 关键字个数出现得越多，分数获得的加成就越多，标题和正文获得相同的加成，暂定为 1.5
        // 例如：
        //   只出现一个则 $score * $coverage_factor ^ $coverage_rate
        //   出现两个则 $score * ($coverage_factor ^ $coverage_rate)
        //   依次类推
        $coverage_factor = 1.5;
        // 查询关键字，个数不限，应保存在某项赛事的数据库内
        $keywords = explode(',', '世界杯,巴西,2014');
        foreach($this->articles as $key => $article) {
            $title = $article['title'];
            $content = $article['content'];
            $length = strlen($content);
            $scores = array(
                'title' => 0,
                'content' => 0
            );
            $coverage_rate_content = $coverage_rate_title = 0;
            foreach($keywords as $keyword) {
                $score_content = substr_count($content, $keyword) * $weight['content'];
                $coverage_rate_content += $score_content > 0 ? 1 : 0;
                $scores['content'] += $score_content * $weight['content'];

                $score_title = substr_count($title, $keyword) * $weight['content'];
                $coverage_rate_title += $score_title > 0 ? 1 : 0;
                $scores['title'] += $score_title * $weight['title'];
            }
            $scores['content'] = $scores['content'] * pow($coverage_factor, $coverage_rate_content);
            $scores['title'] = $scores['title'] * pow($coverage_factor, $coverage_rate_title);
            $this->articles[$key]['score'] = $scores['content'] + $scores['title'];
            // var_dump($this->articles[$key]['title'], $scores['title'] + $scores['content']);
        }
    }

    public function get_content() {
        if(count($this->articles) <= 0)
            return false;
        $mh = curl_multi_init();
        $ch = array();
        foreach($this->articles as $article) {
            $link = $article['link'];
            $ch[$link] = curl_init();
            curl_setopt($ch[$link], CURLOPT_URL, $link);
            curl_setopt($ch[$link], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $ch[$link]);
        }

        do { $n=curl_multi_exec($mh,$active); } while ($active);
        
        foreach ($ch as $link => $conn) {
            $html = curl_multi_getcontent($conn);
            try {
                $encoding = mb_detect_encoding($html, array('ASCII','GB2312','GBK','UTF-8'), true);
            } catch(Exception $error) {
                $encoding = null;
            }
            if($encoding != null) {
                $readability = new Readability($html, $encoding);
                $article = $readability->getContent();
                $content = $article['content'];
                $content = htmlentities(preg_replace('/\r|\n/', '', $content), ENT_QUOTES);
                $this->articles[$link]['content'] = $content;
            } else {
                $this->articles[$link]['content'] = '';
            }
            curl_close($conn);
        }
        $this->test_relevancy();
        $this->extract_images();
        return $this->articles;
    }

    public function rss($link) {
        $feed = new SimplePie();
        $feed->enable_cache(false);
        $feed->set_feed_url($link);
        $feed->init();
        $feed->handle_content_type();
        $items = $feed->get_items();
        foreach($items as $key => $item) {
            $this->articles[$item->get_link()] = array( 
                'title'  => $item->get_title(),
                'link'   => $item->get_link(),
                'source' => $item->get_author()->get_email(),
                'date'   => strtotime($item->get_date())
            );
        }
    }

}