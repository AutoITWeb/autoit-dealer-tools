<?php
// phpcs:ignoreFile -- this is not a core file

namespace Biltorvet\Controller;

/**
 * @see     https://www.tutdepot.com/simple-template-system/
 *
 * Class TemplateController
 * @package Biltorvet\Controller
 */
class TemplateController
{
    /**
     * @var array
     */
    private $globals;

    /**
     * @var string
     */
    private $templateUri;

    public function __construct()
    {
        $this->templateUri = PLUGIN_ROOT . 'templates/';
    }

    /**
     * @param  string $key
     * @param  null   $value
     * @return TemplateController
     */
    public function global_vars(string $key, $value = null): self
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->global_vars($k, $v);
            }
        } else {
            $this->globals[ $key ] = $value;
        }

        return $this;
    }

    /**
     * @param  string $tpl
     * @param  array  $arr
     * @param  bool   $return
     * @return bool|false|string
     */
    public function load(string $tpl, array $arr = array(), bool $return = false)
    {
        if (file_exists($this->templateUri . $tpl)) {
            $templatePath = $this->templateUri . $tpl;

            foreach ($arr as $key => $value) {
                $$key = $value;
            }
            unset($arr);

            ob_start();
            include $templatePath;
            $template = ob_get_contents();
            ob_end_clean();

            if ($return == false) {
                echo $template;
            } else {
                return $template;
            }
        } else {
            return false;
        }
    }
}
