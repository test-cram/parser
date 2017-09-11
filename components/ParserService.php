<?php
namespace app\components;

use app\models\Domain;
use app\models\HtmlSource;
use app\models\SourceInterface;

/**
 * Class ParserService
 * @package app\components
 */
class ParserService extends \yii\base\Component
{
    /**
     * @param string $url
     * @param int $depth
     */
    public function parseUrls($url, $depth = 3)
    {
        /** @var HtmlSource $source */
        $source = \Yii::$container->get('HtmlSource', [$url, $depth]);
        $externalDomains = $this->recursiveParse($source);
        $source->save($externalDomains);
    }

    /**
     * @param SourceInterface $source
     * @return Domain[]
     */
    protected function recursiveParse(SourceInterface $source)
    {
        /** @var Domain[] $externalDomains */
        $externalDomains = $source->getParsedData();
        foreach ($externalDomains as $domain) {
            foreach ($domain->getExternalUrls() as $key => $url) {
                if ($url['depth'] > 0) {
                    $this->parseUrls($url['url'], $url['depth']);
                }
                $domain->setIsExecuted($key);
            }
        }
        return $externalDomains;
    }
}