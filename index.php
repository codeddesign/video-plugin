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
        add_meta_box(
            'ad3media_box', // this is HTML id of the box on edit screen
            'Video', // title of the box
            array(&$this, 'printVideoSide'), // function to be called to display the checkboxes, see the function below
            'post', // on which edit screen the box should appear
            'side', // part of page where the box should appear
            'high' // priority of the box
        );
    }

    public function printVideoSide()
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

    return '<script src="'.$config['player_host'].'/p'.$attr['id'].'.js"></script>';
}

add_shortcode('ad3media', 'ad3media_short_handler');
