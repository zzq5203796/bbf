<?php

namespace app;
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */

class Zip
{
    public function __construct() {

    }

    public function index() {
        echo "welcome.";
    }

    public function menu() {
        $this->js();
        $this->css();
    }

    public function js() {
        $data = [
            "js/menu",
            "js/common" => [
                "js/common",

                "js/plug/auto",
                "js/plug/ajax",
                "js/plug/full-window",
                "js/plug/keydown",
                "js/plug/message",
                "js/plug/progress-bar",
                "js/plug/runscroll",
                "js/plug/store",
                "js/plug/string",
                "js/plug/tabs",
            ],
        ];
        foreach ($data as $vo) {
            $this->parse_js($vo, ['time' => 1]);
        }
        echo date("Y-m-d H:i:s") . " success<br/>";
    }

    public function parse_js($file, $option = []) {
        $option = array_merge(['name' => '', 'time' => false, 'check' => false], $option);
        $ext = "js";
        $content = '';
        if (is_array($file)) {
            foreach ($file as $vo) {
                $content .= $this->parse_js($vo, ['write' => false]);
            }
            $file = $file[0];
        } else {
            $content = read("../$file.$ext");
            $content = '/* '.$file.' */; '.$this->parse_js_content($content);
            if ($option['time']) {
                $content = '/* ' . date("Y-m-d H:i:s") . " */\r\n" . $content;
            }
        }
        if (!isset($option['write']) || $option['write']) {
            write("../min/$file.min.$ext", $content);
        }
        return $content;
    }

    public function css() {
        $data = [
            "css/menu"
        ];
        foreach ($data as $vo) {
            parse_css($vo, ['time' => 1]);
        }
        echo date("Y-m-d H:i:s") . " success.<br/>";
    }

    public function index_min() {
        $file = "js/menu";
        $ext = "js";
        $content = read("../$file.$ext");
        $content = JSMin::minify($content);
        write("../min/$file.min.$ext", $content);
        echo "success";
    }

    protected function parse_js_content($js) {
        $h1 = 'http://';
        $s1 = '【:??】';    //标识“http://”,避免将其替换成空
        $h2 = 'https://';
        $s2 = '【s:??】';    //标识“https://”
        //        preg_match_all('#include\("([^"]*)"([^)]*)\);#isU', $js, $arr);
        //        if (isset($arr[1])) {
        //            foreach ($arr[1] as $k => $inc) {
        //                $path = "http://www.xxx.com/";          //这里是你自己的域名路径
        //                $temp = file_get_contents($path . $inc);
        //                $js = str_replace($arr[0][$k], $temp, $js);
        //            }
        //        }
        //        $js = preg_replace('#function include([^}]*)}#isU', '', $js);//include函数体
        $js = preg_replace('#\/\*.*\*\/#isU', '', $js);//块注释
        $js = str_replace($h1, $s1, $js);
        $js = str_replace($h2, $s2, $js);
        $js = preg_replace('#\/\/[^\n]*#', '', $js);//行注释
        $js = str_replace($s1, $h1, $js);
        $js = str_replace($s2, $h2, $js);
        $js = str_replace("\t", "", $js);//tab
        $js = preg_replace('#\s*(=|>=|\?|:|==|\+|\|\||\+=|>|<|\/|\-|,|{|}|;|\(|\))\s*#', '$1', $js);//字符前后多余空格
        $js = str_replace("\t", "", $js);//tab
        $js = str_replace("\r\n", "", $js);//回车
        $js = str_replace("\r", "", $js);//换行
        $js = str_replace("\n", "", $js);//换行
        $js = trim($js, " ");
        return $js;
    }

}

/**
 *  合并压缩css
 * @param $files
 * @param array $option [name|time]
 * @return string
 */
function parse_css($files, $option = []) {
    $path = '../min/';
    $ext = "css";
    $option = array_merge(['name' => '', 'time' => false, 'check' => false], $option);
    $files = is_array($files)?: [$files];
    $filename = $option['name'];
    $filename = empty($filename)? (count($files) == 1? $files[0] . ".min": "$ext/" . md5(implode(',', $files))): "$filename.min";
    $css_url = $path . $filename . ".$ext";
    if ($option['check'] && file_exists($css_url)) {
        return $css_url;
    }
    $content = '';
    foreach ($files as $file) {
        $content .= read("../$file.$ext");
    }
    $content = parse_css_content($content);
    if ($option['time']) {
        $content = '/* ' . date("Y-m-d H:i:s") . " */\r\n" . $content;
    }
    write($css_url, $content);
    return $css_url;
}

function parse_css_content($content) {
    $content = preg_replace('#\/\*.*\*\/#isU', '', $content);//清除块注释;
    //    $content = str_replace(["\r\n", "\n", "\t"], '', $content); //清除换行符 制表符 空格

    $content = preg_replace('#\s*(:|;|{|}|,)\s*#', '$1', $content);//字符前后多余空格
    return $content;
}

/**
 *  合并压缩js
 */
function parse_script($urls) {
    $url = md5(implode(',', $urls));
    $path = FCPATH . '/static/parse/';
    $js_url = $path . $url . '.js';
    if (!file_exists($js_url)) {
        if (!file_exists($path))
            mkdir($path, 0777);
        load_qy_lib('JavaScriptPacker');
        $js_content = '';
        foreach ($urls as $url) {
            $append_content = @file_get_contents($url) . "\r\n";
            $packer = new JavaScriptPacker($append_content);
            $append_content = $packer->_basicCompression($append_content);
            $js_content .= $append_content;
        }
        @file_put_contents($js_url, $js_content);
    }
    $js_url = str_replace(FCPATH, '', $js_url);
    return $js_url;
}

class JSMin
{
    const ORD_LF = 10;
    const ORD_SPACE = 32;
    const ACTION_KEEP_A = 1;
    const ACTION_DELETE_A = 2;
    const ACTION_DELETE_A_B = 3;
    protected $a = '';
    protected $b = '';
    protected $input = '';
    protected $inputIndex = 0;
    protected $inputLength = 0;
    protected $lookAhead = null;
    protected $output = '';
    // -- Public Static Methods --------------------------------------------------

    /**
     * Minify Javascript
     *
     * @uses __construct()
     * @uses min()
     * @param string $js Javascript to be minified
     * @return string
     */
    public static function minify($js) {
        $jsmin = new JSMin($js);
        return $jsmin->min();
    }
    // -- Public Instance Methods ------------------------------------------------

    /**
     * Constructor
     *
     * @param string $input Javascript to be minified
     */
    public function __construct($input) {
        $this->input = str_replace("\r\n", "\n", $input);
        $this->inputLength = strlen($this->input);
    }
    // -- Protected Instance Methods ---------------------------------------------

    /**
     * Action -- do something! What to do is determined by the $command argument.
     *
     * action treats a string as a single character. Wow!
     * action recognizes a regular expression if it is preceded by ( or , or =.
     *
     * @uses next()
     * @uses get()
     * @throws JSMinException If parser errors are found:
     *     - Unterminated string literal
     *     - Unterminated regular expression set in regex literal
     *     - Unterminated regular expression literal
     * @param int $command One of class constants:
     *   ACTION_KEEP_A   Output A. Copy B to A. Get the next B.
     *   ACTION_DELETE_A  Copy B to A. Get the next B. (Delete A).
     *   ACTION_DELETE_A_B Get the next B. (Delete B).
     */
    protected function action($command) {
        switch ($command) {
            case self::ACTION_KEEP_A:
                $this->output .= $this->a;
            case self::ACTION_DELETE_A:
                $this->a = $this->b;
                if ($this->a === "'" || $this->a === '"') {
                    for (; ;) {
                        $this->output .= $this->a;
                        $this->a = $this->get();
                        if ($this->a === $this->b) {
                            break;
                        }
                        if (ord($this->a) <= self::ORD_LF) {
                            throw new JSMinException('Unterminated string literal.');
                        }
                        if ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a = $this->get();
                        }
                    }
                }
            case self::ACTION_DELETE_A_B:
                $this->b = $this->next();
                if ($this->b === '/' && (
                        $this->a === '(' || $this->a === ',' || $this->a === '=' ||
                        $this->a === ':' || $this->a === '[' || $this->a === '!' ||
                        $this->a === '&' || $this->a === '|' || $this->a === '?' ||
                        $this->a === '{' || $this->a === '}' || $this->a === ';' ||
                        $this->a === "\n")) {
                    $this->output .= $this->a . $this->b;
                    for (; ;) {
                        $this->a = $this->get();
                        if ($this->a === '[') {
                            /*
                             inside a regex [...] set, which MAY contain a '/' itself. Example: mootools Form.Validator near line 460:
                              return Form.Validator.getValidator('IsEmpty').test(element) || (/^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i).test(element.get('value'));
                            */
                            for (; ;) {
                                $this->output .= $this->a;
                                $this->a = $this->get();
                                if ($this->a === ']') {
                                    break;
                                } elseif ($this->a === '\\') {
                                    $this->output .= $this->a;
                                    $this->a = $this->get();
                                } elseif (ord($this->a) <= self::ORD_LF) {
                                    throw new JSMinException('Unterminated regular expression set in regex literal.');
                                }
                            }
                        } elseif ($this->a === '/') {
                            break;
                        } elseif ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a = $this->get();
                        } elseif (ord($this->a) <= self::ORD_LF) {
                            throw new JSMinException('Unterminated regular expression literal.');
                        }
                        $this->output .= $this->a;
                    }
                    $this->b = $this->next();
                }
        }
    }

    /**
     * Get next char. Convert ctrl char to space.
     *
     * @return string|null
     */
    protected function get() {
        $c = $this->lookAhead;
        $this->lookAhead = null;
        if ($c === null) {
            if ($this->inputIndex < $this->inputLength) {
                $c = substr($this->input, $this->inputIndex, 1);
                $this->inputIndex += 1;
            } else {
                $c = null;
            }
        }
        if ($c === "\r") {
            return "\n";
        }
        if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
            return $c;
        }
        return ' ';
    }

    /**
     * Is $c a letter, digit, underscore, dollar sign, or non-ASCII character.
     *
     * @return bool
     */
    protected function isAlphaNum($c) {
        return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
    }

    /**
     * Perform minification, return result
     *
     * @uses action()
     * @uses isAlphaNum()
     * @uses get()
     * @uses peek()
     * @return string
     */
    protected function min() {
        if (0 == strncmp($this->peek(), "\xef", 1)) {
            $this->get();
            $this->get();
            $this->get();
        }
        $this->a = "\n";
        $this->action(self::ACTION_DELETE_A_B);
        while ($this->a !== null) {
            switch ($this->a) {
                case ' ':
                    if ($this->isAlphaNum($this->b)) {
                        $this->action(self::ACTION_KEEP_A);
                    } else {
                        $this->action(self::ACTION_DELETE_A);
                    }
                    break;
                case "\n":
                    switch ($this->b) {
                        case '{':
                        case '[':
                        case '(':
                        case '+':
                        case '-':
                        case '!':
                        case '~':
                            $this->action(self::ACTION_KEEP_A);
                            break;
                        case ' ':
                            $this->action(self::ACTION_DELETE_A_B);
                            break;
                        default:
                            if ($this->isAlphaNum($this->b)) {
                                $this->action(self::ACTION_KEEP_A);
                            } else {
                                $this->action(self::ACTION_DELETE_A);
                            }
                    }
                    break;
                default:
                    switch ($this->b) {
                        case ' ':
                            if ($this->isAlphaNum($this->a)) {
                                $this->action(self::ACTION_KEEP_A);
                                break;
                            }
                            $this->action(self::ACTION_DELETE_A_B);
                            break;
                        case "\n":
                            switch ($this->a) {
                                case '}':
                                case ']':
                                case ')':
                                case '+':
                                case '-':
                                case '"':
                                case "'":
                                    $this->action(self::ACTION_KEEP_A);
                                    break;
                                default:
                                    if ($this->isAlphaNum($this->a)) {
                                        $this->action(self::ACTION_KEEP_A);
                                    } else {
                                        $this->action(self::ACTION_DELETE_A_B);
                                    }
                            }
                            break;
                        default:
                            $this->action(self::ACTION_KEEP_A);
                            break;
                    }
            }
        }
        return $this->output;
    }

    /**
     * Get the next character, skipping over comments. peek() is used to see
     * if a '/' is followed by a '/' or '*'.
     *
     * @uses get()
     * @uses peek()
     * @throws JSMinException On unterminated comment.
     * @return string
     */
    protected function next() {
        $c = $this->get();
        if ($c === '/') {
            switch ($this->peek()) {
                case '/':
                    for (; ;) {
                        $c = $this->get();
                        if (ord($c) <= self::ORD_LF) {
                            return $c;
                        }
                    }
                case '*':
                    $this->get();
                    for (; ;) {
                        switch ($this->get()) {
                            case '*':
                                if ($this->peek() === '/') {
                                    $this->get();
                                    return ' ';
                                }
                                break;
                            case null:
                                throw new JSMinException('Unterminated comment.');
                        }
                    }
                default:
                    return $c;
            }
        }
        return $c;
    }

    /**
     * Get next char. If is ctrl character, translate to a space or newline.
     *
     * @uses get()
     * @return string|null
     */
    protected function peek() {
        $this->lookAhead = $this->get();
        return $this->lookAhead;
    }
}
