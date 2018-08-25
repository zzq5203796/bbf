<?php
namespace app;

use \libs\CPdo;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */
class Sgz
{

    public function index() {
    	$file = "../Save01.json";
    	$this->run($file);
    }

    public function info(){
    	$data = get_dir_info("runtime/sgz");
    	$extra = [
    		"00_BrotherIds",
    		"00_CloseIds",
    		"00_FatherIds",
    		"00_HatedIds",
    		"00_MotherIds",
    		"00_MarriageGranterId",    		
    		"00_PersonRelationIds",
    		"00_SpouseIds",
    		"00_SuoshuIds",

    		"00_AllEvents",		// 事件
    		"00_Factions", 		// 势力
    		"00_Militaries",	// 编队
    		"00_Persons_GameObjects", //武将 todo
    		"00_ScenarioMap", // 地图 
    		"00_TroopEvents", // 爆发
    		"00_YearTable",   // 历史事件

    		"10_00_Facilities_GameObjects",
    	];
    	$list = [
    		[10000, "10", "00"],
    		[20000, "20", "10"],
    	];
    	$tmp = 0;
    	list($num, $top, $old) = $list[$tmp];
    	// show_msgs($data);
    	foreach ($data as $key => $vo) {
    		$filename = explode(".", $vo['name'])[0];
    		if($vo['size'] > $num 
    			&& substr($filename, 0, 2) == $old 
    			&& !in_array($filename, $extra)){
    			$this->run("sgz/".$vo['name'], $top."_".explode(".", $vo['name'])[0]);
    		}
    	}
    }
    private function run($file = "../Save01.json", $top="00"){
    	$data = read($file);

    	$top.="_";

    	$data = json_decode($data, true);
    	$i=0;
    	foreach ($data as $key => $value) {
    		$str = json_encode($value, JSON_UNESCAPED_UNICODE);
    		if(strlen($str)<20){
    			continue;
    		}
    		if($i==0 && is_numeric($key)){
    			show_msg($top); 
    			show_msg(strlen($str)); 
    			break;
    		}
    		write("sgz/$top$key.json", $str);
    		$i++;
    	}
    }

    public function getstr(){
        $str = "";
        for($i=0; $i<11;$i++){
            for($k=0;$k<7;$k++){
                $str .= ($i*10+$k)." ";
            }
        }
        show_msg($str);
    }
    public function test(){
        show_now();
        $data = $this->getSave(1);

        $key_string = "Persons.GameObjects";
        $set="";

        $keys = explode(".", $key_string);
        foreach ($keys as $key) {
            $data = $data[$key];
        }
        $map = [
            'CalledName'=>"子龙"
        ];
        foreach ($data as $key => &$value) {
            if($this->isEq($map, $value)){
                show_msgs([$value],"table");
            }
        }

    }
    public function getSave($num){

        $file = "C:/Users/Administrator/Documents/WorldOfTheThreeKingdoms/Save/Save0$num.json";
        $myfile = fopen($file, "r");
        if ($myfile === false) {
            show_msg("open fail.");
            return 0;
        }
        $size = filesize($file);
        $content = fread($myfile, $size);
        fclose($myfile);
        return json_decode($content, true);
    }

    public function save($num, $data){
        $content = json_encode($data);
        $file = "C:/Users/Administrator/Documents/WorldOfTheThreeKingdoms/Save/Save0$num.json";
    }

    public function isEq($map, $data){
        $res = false;
        foreach ($map as $key => $vo) {
            $res = true;
            if(!isset($data[$key]) || $vo!=$data[$key]){
                $res = false;
                break;
            }
        }
        return $res;
    }
}