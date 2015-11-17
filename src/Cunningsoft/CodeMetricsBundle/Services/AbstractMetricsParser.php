<?php

namespace Cunningsoft\CodeMetricsBundle\Services;

use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractMetricsParser
{
    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param string $type
     *
     * @return \DOMElement
     */
    public function getNodes($type)
    {
        $content = file_get_contents($this->cacheDir . '/metrics/' . $this->getMetricsFilename());
        $crawler = new Crawler($content);
        $nodes = array();
        foreach ($crawler->filter($type) as $element) {
            $nodes[] = $element;
        }

        return $nodes;
    }

    /**
     * @return string
     */
    abstract protected function getMetricsFilename();
}
