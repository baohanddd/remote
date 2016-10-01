<?php
namespace baohan\Remote;


interface Config
{
    /**
     * @return string
     */
    public function getHost();

    /**
     * @return string
     */
    public function getPrefix();
}