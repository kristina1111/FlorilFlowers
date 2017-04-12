<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 4.4.2017 г.
 * Time: 18:49 ч.
 */

namespace FlorilFlowersBundle\Service;


use Doctrine\Common\Cache\Cache;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{

    private $markdownParser;

    private $cache;

    /**
     * MarkdownTransformer constructor.
     */
    public function __construct(MarkdownParserInterface $markdownParser, Cache $cache)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;
    }

    public function parse($str)
    {
        //instantiate cache service
        $cache = $this->cache;

        //create encrypted key of the string that we want to cache
        $key = md5($str);

        //check if this key exists in the cache we already have
        if($cache->contains($key)){
            // if yes, we don't need to encrypt it again and create cache
            // we only return the cache
            return $cache->fetch($key);
        }

        // if we don't have this key, we need to cache this string

//        sleep(1); // for test reasons only

        $str = $this->markdownParser->transformMarkdown($str);

        $cache->save($key, $str);

        return $str;
    }
}