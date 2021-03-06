<?php
/**
 * This source file is subject to the MIT license that is bundled with this package in the file LICENSE.txt.
 *
 * @package   Soliant\SimpleFM
 * @copyright Copyright (c) 2007-2016 Soliant Consulting, Inc. (http://www.soliantconsulting.com)
 * @author    jsmall@soliantconsulting.com
 */

namespace Soliant\SimpleFM;

use Soliant\SimpleFM\Loader\AbstractLoader;
use Soliant\SimpleFM\Loader\FilePostContents;
use Soliant\SimpleFM\Parser\FmLayoutParser;
use Soliant\SimpleFM\Parser\FmResultSetParser;

class Adapter
{
    /**
     * @var HostConnection
     */
    protected $hostConnection;

    /**
     * @var string
     */
    protected $layoutName = '';

    /**
     * @var string
     */
    protected $commandString = '-findany';

    /**
     * @var array
     */
    protected $commandArray = ['-findany' => ''];

    /**
     * @var string
     */
    protected $uri = FmResultSetParser::GRAMMAR;

    /**
     * @var boolean
     */
    protected $rowsByRecId = false;

    /**
     * @var string
     */
    protected $commandUrlDebug;

    /**
     * @var AbstractLoader
     */
    protected $loader;

    /**
     * @param HostConnection $hostParams
     * @param null $loader
     */
    public function __construct(HostConnection $hostParams)
    {
        $this->hostConnection = $hostParams;

        // Setup default Loader
        $this->setLoader(new FilePostContents());
    }

    /**
     * @return string
     */
    public function getLayoutName()
    {
        return $this->layoutName;
    }

    /**
     * @param $layoutName
     * @return $this
     */
    public function setLayoutName($layoutName)
    {
        $this->layoutName = $layoutName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommandString()
    {
        return $this->commandString;
    }

    /**
     * @return array
     */
    public function getCommandArray()
    {
        return $this->commandArray;
    }

    /**
     * @param $commandString
     * @return $this
     */
    public function setCommandString($commandString)
    {
        $this->commandString = $commandString;
        $this->commandArray = StringUtils::explodeNameValueString($commandString);
        return $this;
    }

    /**
     * @param $commandArray
     * @return $this
     */
    public function setCommandArray($commandArray)
    {
        $this->commandArray = $commandArray;
        $this->commandString = StringUtils::repackCommandString($commandArray);
        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return $this
     */
    public function useLayoutGrammar()
    {
        $this->uri = FmLayoutParser::GRAMMAR;
        return $this;
    }

    /**
     * @return $this
     */
    public function useResultSetGrammar()
    {
        $this->uri = FmResultSetParser::GRAMMAR;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRowsByRecId()
    {
        return (boolean) $this->rowsByRecId;
    }

    /**
     * @param bool $rowsByRecId
     * @return $this
     */
    public function setRowsByRecId($rowsByRecId = false)
    {
        $this->rowsByRecId = (boolean) $rowsByRecId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommandUrlDebug()
    {
        return $this->getLoader()->getCommandUrlDebug();
    }

    /**
     * @return AbstractLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @param AbstractLoader $loader
     * @return $this
     */
    public function setLoader(AbstractLoader $loader)
    {
        $this->loader = $loader;
        $this->loader->setAdapter($this);
        return $this;
    }

    /**
     * @return HostConnection
     */
    public function getHostConnection()
    {
        return $this->hostConnection;
    }

    /**
     * @param HostConnection $hostConnection
     * @return Adapter
     */
    public function setHostConnection($hostConnection)
    {
        $this->hostConnection = $hostConnection;
        return $this;
    }

    /**
     * @return array
     */
    public function execute()
    {
        /**
         * SPL functions that Loaders and Parsers use do not throw errors. The Loader and Parser methods have to be
         * able to handle either case gracefully or throw an Exception in the case of an unhandled error.
         */
        $xml = $this->loader->load();

        if ($this->uri == FmLayoutParser::GRAMMAR) {
            if ($this->loader->hasError()) {
                return $this->loader->getLastErrorResultFmLayout();
            }
            return $this->parseFmpXmlLayout($xml);
        }

        if ($this->loader->hasError()) {
            return $this->loader->getLastErrorResultFmResultSet();
        }
        return $this->parseFmResultSet($xml);
    }

    /**
     * @param $xml
     * @return array|mixed
     */
    protected function parseFmResultSet($xml)
    {
        $parser = new FmResultSetParser($xml);
        $parser->setRowsByRecId($this->getRowsByRecId());
        return $parser->parse($this->getCommandUrlDebug());
    }

    /**
     * @param $xml
     * @return array|mixed
     */
    protected function parseFmpXmlLayout($xml)
    {
        $parser = new FmLayoutParser($xml);
        return $parser->parse($this->getCommandUrlDebug());
    }
}
