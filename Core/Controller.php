<?php

namespace Core;

class Controller{

    /**
     * Render the specified view with embedded datas stored in $data
     * @param string $view The name of the view to call. The views are stored in the Views folder. The name "my.view" must correspond to the file Views/my/view.php.
     * It's possible to call a partial view from the view file by using the directive "@render(view.name);"
     * @param array(default=[]) $data The datas needed for the view.
     * @return string the content of the view. It's used for recursive rendering.
     */
    static public function render(string $view, array $data = []) : string{
        $view = trim($view, "/");

        $view = str_replace(["//", "..", "."], ["", "", "/"], $view);
        $view = str_replace(".", "/", $view);

        $filename = BASENAME.'/Views/'.$view.'.php';
        unset($view);
        $output = "";
        if(is_file($filename)){
            ob_start();
            require_once($filename);
            $output = ob_get_contents();
            $output = preg_replace_callback("#@render\((.+)\);#", function($elem) use($data){
                return static::render($elem[1]);
            }, $output);
            ob_end_clean();
        }
        unset($filename);
        echo($output);
        return $output;
    }
};
?>
