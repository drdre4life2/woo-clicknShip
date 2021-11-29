<?php
/**
 * @package Thefirst
 */
namespace Inc\Base;

class SettingsLinks
{
    protected $plugin;

    public function __construct()
    {
        $this->plugin = PLUGIN;
    }
    public function register()
    {
        
        add_action("plugin_action_links_$this->plugin", array($this, 'settings_link'));
    }

    public function settings_link($links)
    {
        $setting_link = '<a href="admin.php?page=myfirst-plugin"> Settings</a>';
        array_push($links, $setting_link);
        return $links;

    }

}
