<?php
namespace app\commands;

use app\components\ParserService;
use yii\console\Controller;
use yii\di\Container;

/**
 * Class ParserController
 * @package app\commands
 */
class ParserController extends Controller
{
    /**
     * @var Container
     */
    protected $parser;

    /**
     * ParserController constructor.
     *
     * @param string $id
     * @param \yii\base\Module $module
     * @param array $config
     * @param ParserService $parserService
     */
    public function __construct($id, $module, array $config = [], ParserService $parserService)
    {
        $this->parser = $parserService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @param string $url
     */
    public function actionDomain($url, $depth = 3)
    {
        $this->parser->parseUrls($url, $depth);
    }
}
