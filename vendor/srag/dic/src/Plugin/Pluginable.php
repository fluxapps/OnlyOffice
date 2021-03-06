<?php

namespace srag\DIC\OnlyOffice\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\OnlyOffice\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
