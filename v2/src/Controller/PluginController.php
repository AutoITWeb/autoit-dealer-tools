<?php

namespace Biltorvet\Controller;

use Biltorvet\Utility\Callbacks;
use Dotenv\Dotenv;
use Exception;

class PluginController
{
    /**
     * @var Dotenv
     */
    private $dotenv;

    /**
     * @var Callbacks
     */
    private $callbacks;

    public function __construct()
    {
        try {
            $this->loadEnv();

            $this->callbacks = new Callbacks();
            $this->actions();
            $this->filters();
            $this->shortcodes();
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * Actions.
     *
     * @see https://developer.wordpress.org/plugins/hooks/actions/
     */
    private function actions()
    {
        if (WP_DEBUG) {
            add_action('admin_menu', array($this->callbacks, 'debug_page_menu'));
        }
    }

    /**
     * Filters.
     *
     * @see https://developer.wordpress.org/plugins/hooks/filters/
     */
    private function filters()
    {
        add_filter('wp_mail', array($this->callbacks, 'sendLead'));
    }

    /**
     * Shortcodes.
     *
     * @see https://codex.wordpress.org/Function_Reference/add_shortcode
     */
    private function shortcodes()
    {
        add_shortcode('bdt_get_vehicles', [$this->callbacks, 'get_vehicles_shortcode']);
        add_shortcode('bdt_get_vehicles_by_status_code', [$this->callbacks, 'get_vehicles_by_status_code_shortcode']);
        add_shortcode('bdt_get_vehicles_by_type', [$this->callbacks, 'get_vehicles_by_type_shortcode']);
    }

    /**
     * @throws Exception $e
     */
    private function loadEnv()
    {
        $this->dotenv = Dotenv::create(PLUGIN_ROOT);
        $this->dotenv->load();

        $this->dotenv->required('API_URL')->notEmpty();
    }
}
