<?php
/**
 * @package Thefirst
 */

namespace Inc\Base;

class Enqueue
{
    Public function register(){
        add_action( 'admin_enqueue_scripts', array($this, 'enqueue'));
    }

    public function enqueue()
        {
            wp_enqueue_style('mypluginstyle', PLUGIN_URL.('assets/style.css'));
            wp_enqueue_script('mypluginscript', PLUGIN_URL.('assets/first.js'));
    
        }

}