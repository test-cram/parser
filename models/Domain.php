<?php
namespace app\models;

use yii\base\Model;

/**
 * Class Domain
 * @package app\models
 */
class Domain extends Model
{
    /**
     * @var array
     */
    protected $externalUrls = [];

    /**
     * @var int
     */
    protected $depth;

    /**
     * Domain constructor.
     * @param int $depth
     * @param array $config
     */
    public function __construct($depth, array $config = [])
    {
        $this->depth = $depth;
        parent::__construct($config);
    }

    /**
     * @param $url
     */
    public function addUrl($url)
    {
        foreach ($this->externalUrls as &$externalUrl) {
            if ($externalUrl['url'] === $url) {
                $externalUrl['count']++;
                return;
            }
        }
        $this->externalUrls[] = [
            'url' => $url,
            'count' => 1,
            'depth' => $this->depth,
            'startedAt' => microtime(true),
            'executionTime' => null
        ];
    }

    /**
     * @param int $key
     */
    public function setIsExecuted($key)
    {
        if (isset($this->externalUrls[$key])) {
            $this->externalUrls[$key]['executionTime'] = microtime(true) - $this->externalUrls[$key]['startedAt'];
        }
    }

    /**
     * @return array
     */
    public function getExternalUrls(): array
    {
        return $this->externalUrls;
    }
}