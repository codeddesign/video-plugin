<?php
/*
Plugin Name: VideoPlugin
Plugin URI: none
Description: VideoPlugin System
Version: 0.1
 */

function ad3mediaConfig()
{
    if (file_exists(realpath(__DIR__.'/config.dev.php'))) {
        $config = require 'config.dev.php';
    } else {
        $config = require 'config.php';
    }

    return $config;
}

class VideoPluginController
{
    public function __construct()
    {
        add_action('add_meta_boxes', array(&$this, 'addCustom'));
    }

    /**
     * Hook to add it to admin's main menu.
     */
    public function PayrollPluginController()
    {
        $this->__construct();
    }

    /**
     * Action to add it to admin's main menu.
     */
    public function addCustom()
    {
        add_action('media_buttons', array(&$this, 'printCustomMedia'));
    }

    public function printCustomMedia()
    {
        $config = ad3mediaConfig();

        require 'view/style.html';

        require 'view/sidebar.html';

        require 'view/modal.php';
    }
}

/* Init Plugin */
$l = new VideoPluginController();

function ad3media_short_handler($attr)
{
    $config = ad3mediaConfig();

    $pattern = '<script src="%s/p%s.%s.js"></script>';

    return sprintf($pattern, $config['player_host'], $attr['campaign'], $attr['youtube']);
}

add_shortcode('ad3media', 'ad3media_short_handler');
