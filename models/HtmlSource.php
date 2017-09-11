<?php
namespace app\models;

use Yii;

/**
 * Class HtmlSource
 * @package app\models
 */
class HtmlSource implements SourceInterface
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var Domain[]
     */
    protected $externalDomains = [];

    /**
     * @var int
     */
    protected $depth;


    /**
     * HtmlSource constructor.
     * @param $url
     * @param int $depth
     */
    public function __construct($url, $depth = 3)
    {
        $this->depth = $depth;
        $this->url = $this->validateUrl($url);
        $this->domain = $this->getDomainFromUrl($url);
    }

    /**
     * Loads the external page
     * @return void
     */
    public function load()
    {
        if (!empty($this->source)) {
            return;
        }
        if ($ch = @curl_init()) {
            @curl_setopt($ch, CURLOPT_URL, $this->url);
            @curl_setopt($ch, CURLOPT_HEADER, false);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $this->source = @curl_exec($ch);
            @curl_close($ch);
        }
    }

    /**
     * Returns urls found on the page
     *
     * @return array
     */
    protected function getUrls()
    {
        if (preg_match_all("/\<a.*?href=\"(https?[^\"]*)\"/", $this->source, $matches)) {
            return $matches[1];
        }
        return [];
    }

    /**
     * @return Domain[]
     */
    public function getParsedData()
    {
        if (empty($this->externalDomains)) {
            $this->parse();
        }
        return $this->externalDomains;
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        if (empty($this->source)) {
            $this->load();
        }
        $urls = $this->getUrls();
        foreach ($urls as $url) {
            $url = $this->validateUrl($url);
            if ($this->getDomainFromUrl($url) === $this->domain) {
                continue;
            }
            $this->addUrlToDomain($url);
        }
    }

    /**
     * @param string $url
     */
    protected function addUrlToDomain($url)
    {
        $domainName = $this->getDomainFromUrl($url);
        if (!isset($this->externalDomains[$domainName])) {
            $this->externalDomains[$domainName] = Yii::createObject(Domain::class, [$this->depth - 1]);
        }
        $this->externalDomains[$domainName]->addUrl($url);
    }

    /**
     * @inheritdoc
     */
    public function save(array $externalDomains = [])
    {
        if (empty($externalDomains)) {
            return;
        }
        $this->externalDomains = $externalDomains;
        $this->writeOutput();
    }

    /**
     * @param string $url
     * @return string
     */
    protected function getDomainFromUrl($url)
    {
        $result = parse_url($url);
        if (isset($result['host'])) {
            return $result['host'];
        }
        return '';
    }

    /**
     * @param string $url
     * @return string
     */
    protected function validateUrl($url)
    {
        $url = str_replace('www.', '', trim($url, '/'));
        if (preg_match('/^http/', $url)) {
            return $url;
        }
        return 'http://' . $url;
    }

    /**
     * Save output to file
     * TODO: Add Output interface
     * @return void
     */
    protected function writeOutput()
    {
        $output = Yii::$app->view->renderFile(Yii::getAlias('@app/views/parser/domain.php'), [
            'domains' => $this->externalDomains,
        ]);
        $url = Yii::getAlias('@app/runtime/logs/') . date('Y-m-d_H-i-s_') . $this->domain . '.html';
        file_put_contents($url, $output);
    }
}