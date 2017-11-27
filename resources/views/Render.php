<?php

class Render
{

    private $index;

    private $ignore = [];

    public function __construct($templates_folder)
    {
        if (!array_key_exists($templates_folder, Config::$ASSETS)) {
            $templates_folder = 'WEB';
        }
        $main_templates_folder = Config::$ASSETS[$templates_folder];
        $common_templates_folder = Config::$ASSETS['COMMON'];

        $this->renderToArray(Config::$TEMPLATES['MAIN'], $main_templates_folder);
        $this->renderToArray(Config::$TEMPLATES['COMMON'], $common_templates_folder);
    }

    /**
     * Renders an array of template files located in the $folder provided. Saves them to objects internal array
     * @param $templates
     * @param $folder
     */
    private function renderToArray($templates, $folder)
    {
        foreach ($templates as $template => $file) {
            $this->index[$template] = new Template($folder . $file);
        }
    }

    /**
     * Rendering recipe for Index - $this->index[PLACEHOLDER] - if applicable
     * @param $id
     */
    public function Index($id)
    {
        
    }

    /**
     * Merges an array of saved templates to a string
     * @return string
     */
    public function Content()
    {
        $html = '';
        foreach ($this->index as $template_name => $template_content) {
            if (in_array($template_name, $this->ignore)) {
                continue;
            }
            $html .= $template_content;
        }

        return $html;
    }
}
