<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 4.4.2017 г.
 * Time: 21:03 ч.
 */

/**
 * We make our own twig extension for the MarkdownTransformer service that we created
 * We need to add it to the services.yml though
 */

namespace FlorilFlowersBundle\Twig;


use FlorilFlowersBundle\Service\MarkdownTransformer;

class MarkdownExtension extends \Twig_Extension
{
    private $markdownTransformer;

    /**
     * MarkdownExtension constructor.
     */
    public function __construct(MarkdownTransformer $markdownTransformer)
    {
        $this->markdownTransformer = $markdownTransformer;
    }



    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'markdownify',
                array($this, 'parseMarkdown'),
                ['is_safe' =>['html']]
            )
        ];
    }

    public function parseMarkdown($str)
    {
        return $this->markdownTransformer->parse($str);
//        return strtoupper($str);
    }


    public function getName()
    {
        return 'app_markdown';
    }

}