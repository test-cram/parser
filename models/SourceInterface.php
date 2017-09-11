<?php
namespace app\models;

/**
 * Interface SourceInterface
 * @package app\models
 */
interface SourceInterface
{
    /**
     * Loads the source
     *
     * @return void
     */
    public function load();

    /**
     * Parse data from source
     *
     * @return void
     */
    public function parse();

    /**
     * Save the result
     *
     * @return void
     */
    public function save();

    /**
     * Returns parsed data
     *
     * @return array|string
     */
    public function getParsedData();
}