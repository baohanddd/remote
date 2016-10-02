<?php
namespace baohan\Remote\Config;

use baohan\Remote\Config;

abstract class API implements Config
{
    /**
     * Specified value of timeout, unit: second, default: 5.0
     *
     * @return float
     */
    public function getTimeout()
    {
        return 5.0;
    }

    /**
     * Enable verify via SSL, default is false
     *
     * @return bool
     */
    public function enableVerify()
    {
        return false;
    }

    /**
     * Enable throw exception when occurs http error, default: false
     *
     * @return bool
     */
    public function enableHttpErrors()
    {
        return false;
    }

    /**
     * Enable debug mode, default: false
     *
     * @return bool
     */
    public function enableDebug()
    {
        return false;
    }
}