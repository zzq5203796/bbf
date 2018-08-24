<?php
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/8/23
 * Time: 15:05
 */

namespace app;


class Db
{

    public function cmd() {
        $data = [
            "show create table table_name;",
            "select * from information_schema.columns where table_schema = 'db_name'", //#所在表字段
        ];
    }
}