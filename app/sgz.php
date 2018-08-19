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
}