<?php
if(IS_WIN){
    $hosts_file = 'C:\Windows\System32\drivers\etc\hosts';
}else{
    $hosts_file = "/etc/hosts";
}
define('HOST_FILE', $hosts_file);
$hm = new HostManage(HOST_FILE);
$env = $argv[1];
$domain = input("domain", "");
$newdomain = default_empty_value(input("newdomain", ""), '', $domain);
$type = input("type", "");
$ip = input("ip", "");

if($domain && $type == 'delete'){
    $hm->delGroup($domain);
}elseif($domain && $type=="add"){
    $hm->addGroup($domain, $ip, $newdomain);
}

class HostManage {
    // hosts 文件路径
    protected $file;
    // hosts 记录数组
    protected $hosts = array();
    // 配置文件路径，默认为 __FILE__ . '.ini';
    protected $configFile;
    // 从 ini 配置文件读取出来的配置数组
    protected $config = array();
    // 配置文件里面需要配置的域名
    protected $domain = array();
    // 配置文件获取的 ip 数据
    protected $ip = array();

    protected $change = false;
    protected $auth = false;

    public function __construct($file, $config_file = null) {
        $this->auth = auth($file);
        if(!$this->auth){
            show_msg( "【无法找到文件】" . $file. "<br />", 1, 0);
            die();
        }
        $this->file = $file;
        if ($config_file) {
            $this->configFile = $config_file;
        } else {
            $this->configFile = root_dir() . '/runtime/hosts.ini';
        }
        $this->initHosts()
            ->initCfg();
    }
    public function __destruct() {
        $this->write();
        $hosts = $this->allHosts();
        if(IS_AJAX){
            return $hosts;
        }elseif($this->auth && !IS_CLI){
            if(empty($this->auth['w'])){
                show_msg( "hosts 文件无【写入】权限....<br />" . $this->file. "<br />", 1, 0);
            }else{
                show_msg( "hosts 文件编辑 <br />", 1, 0);
            }
            view("hosts-form", $hosts);
        }
    }
    public function initHosts() {
        $lines = file($this->file);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $item = preg_split('/\s+/', $line);
            if($line[0] == '#' && !empty($this->hosts[$item[1]])){
                continue;
            }
            $this->hosts[$item[1]] = $item[0];
        }
        return $this;
    }
    public function initCfg() {
        if (! file_exists($this->configFile)) {
            $this->config = ['domain' => [], 'ip' => []];
        } else {
            $this->config = (parse_ini_file($this->configFile, true));
        }
        $this->domain = array_keys($this->config['domain']);
        $this->ip = $this->config['ip'];
        return $this;
    }
    public function allHosts() {
        return $this->hosts;
    }
    /**
     * 删除配置文件里域的 hosts
     */
    public function delGroup($domain) {
        $this->delRecord($domain);
    }

    /**
     * 将域配置为指定 ip
     * @param $env
     * @return \HostManage
     */
    public function addGroup($domain, $ip="127.0.0.1", $newdomain) {
        $this->addRecord($domain, $ip, $newdomain);
        return $this;
    }

    /**
     * 添加一条 host 记录
     * @param $ip
     * @param $domain
     * @return HostManage
     */
    function addRecord($domain, $ip, $newdomain) {
        $this->change = true;
        $this->hosts[$domain] = $ip;
        $data = [];
        foreach ($this->hosts as $key => $value) {
            $k = $domain == $key? $newdomain: $key;
            $data[$k] = $value;
        }
        $this->hosts = $data;
        return $this;
    }

    /**
     * 删除一条 host 记录
     * @param $domain
     * @return HostManage
     */
    function delRecord($domain) {
        unset($this->hosts[$domain]);
        return $this;
    }
    /**
     * 写入 host 文件
     */
    public function write() {
        if(empty($this->auth['w'])){
            return ;
        }
        $str = '';
        foreach ($this->hosts as $domain => $ip) {
            $str .= $ip . "\t" . $domain . PHP_EOL;
        }
        // file_put_contents($this->file, $str);
        return $this;
    }
}