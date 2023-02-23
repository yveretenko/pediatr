<?php

class ViewHelper
{
    public static function render(array $params=[])
    {
        global $module, $controller, $action;

        extract($params);

        $view_path=APPLICATION_TOP_PATH."/module/$module/view/views/".StringHelper::decamelize($controller).'/'.StringHelper::decamelize($action).'.phtml';

        if (is_file($view_path))
            require_once($view_path);
    }
}