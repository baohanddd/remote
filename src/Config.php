<?php
namespace baohan\Remote;


interface Config
{
    /**
     * Example: "http:://local.remote.com"
     *
     * @return string
     */
    public function getHost();

    /**
     * @return string
     */
    public function getPrefix();

    /**
     * Specified value of timeout, unit: second, default: 5.0
     *
     * @return float
     */
    public function getTimeout();

    /**
     * Enable verify via SSL, default is false
     *
     * @return bool
     */
    public function enableVerify();

    /**
     * Enable throw exception when occurs http error, default: false
     *
     * @return bool
     */
    public function enableHttpErrors();

    /**
     * Enable debug mode, default: false
     *
     * @return bool
     */
    public function enableDebug();
}