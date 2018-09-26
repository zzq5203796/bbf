<?php

namespace app;

use \libs\CPdo;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */
class Database
{
    protected $PDO;

    public function __construct($file, $config, $type = 'export') {
        $this->PDO = new CPdo();
        $this->file = $file;
        $this->config = $config;
    }

    public function index() {
        $tables = $this->PDO->getValueBySelfCreateSql("SHOW TABLE STATUS;");
        $data = [
            'Name' => '表名',
            'Engine' => '表类型',
//            'Version' => '',
//            'Row_format' => '行格式',
            'Rows' => '行数',
            'Auto_increment' => '自动递增',
//            'Avg_row_length' => '平均数据长度',
            'Data_length' => '数据长度',
//            'Max_data_length' => '',
//            'Index_length' => '',
//            'Data_free' => '',
            'Collation' => '字符',
//            'Create_time' => '创建时间',
            'Update_time' => '更新时间',
//            'Check_time' => '检查时间',
//            'Checksum' => '检查和',
//            'Create_options' => '创建选项',
            'Comment' => '备注',
        ];
        show_table($tables,$data);
    }

    public function backup() {
        $table = 'webs';
        $start = 0;
        $res = $this->_backup($table, $start);
        show_table(['表' => $table, '执行' => $res[0], '总计' => $res[1]]);
    }

    public function _backup($table = '', $start = 0) {
        $limit = 1000;
        // 备份表结构
        if (0 == $start) {
            $result = $this->PDO->getValueBySelfCreateSql("SHOW CREATE TABLE `$table`");
            $result = array_map('array_change_key_case', $result);

            $sql = "\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "-- Table structure for `$table`\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= trim($result[0]['create table']) . ";\n\n";
            if (false === $this->write($table, $sql)) {
                return false;
            }
        }

        // 数据总数
        $result = $this->PDO->getValueBySelfCreateSql("SELECT COUNT(*) AS count FROM `$table`");
        $count = $result['0']['count'];

        $num = 0;
        //备份表数据
        if ($count) {
            // 写入数据注释
            if (0 == $start) {
                $sql = "-- -----------------------------\n";
                $sql .= "-- Records of `$table`\n";
                $sql .= "-- -----------------------------\n";
                $this->write('data/' . $table, $sql);
            }

            // 备份数据记录
            $result = $this->PDO->getValueBySelfCreateSql("SELECT * FROM `$table` LIMIT {$start}, $limit");
            foreach ($result as $row) {
                $row = array_map('addslashes', $row);
                $sql = "INSERT INTO `$table` VALUES ('" . str_replace(["\r", "\n"], ['\r', '\n'], implode("', '", $row)) . "');\n";
                $num++;
                if (false === $this->write('data/' . $table, $sql, "a")) {
                    return [$start + $num, $count];
                }
            }

            //还有更多数据
            if ($count > $start + $limit) {
                return [$start + $limit, $count];
            }
        }


        // 备份下一表
        return [$start + $num, $count];
    }

    private function write($name, $sql = '', $mode = "w") {
        write("../data/sql/" . $name . ".sql", $sql, $mode);
        return true;

        //        $size = strlen($sql);
        // 由于压缩原因，无法计算出压缩后的长度，这里假设压缩率为50%，
        // 一般情况压缩率都会高于50%；
        //        $size = $this->config['compress'] ? $size / 2 : $size;
        //        $this->open($size);
        //        return $this->config['compress'] ? @gzwrite($this->fp, $sql) : @fwrite($this->fp, $sql);
    }

    /**
     * 打开一个卷，用于写入数据
     * @param integer $size 写入数据的大小
     */
    private function open($size = 0) {
        if ($this->fp) {
            $this->size += $size;
            if ($this->size > $this->config['part']) {
                $this->config['compress']? @gzclose($this->fp): @fclose($this->fp);
                $this->fp = null;
                $this->file['part']++;
                session('backup_file', $this->file);
                $this->create();
            }
        } else {
            $backup_path = $this->config['path'];
            $filename = "{$backup_path}{$this->file['name']}-{$this->file['part']}.sql";
            if ($this->config['compress']) {
                $filename = "{$filename}.gz";
                $this->fp = @gzopen($filename, "a{$this->config['level']}");
            } else {
                $this->fp = @fopen($filename, 'a');
            }
            $this->size = filesize($filename) + $size;
        }
    }

    /**
     * 导入数据
     * @param integer $start 起始位置
     * @return array|bool|int
     */
    public function import($start = 0) {
        if ($this->config['compress']) {
            $gz = gzopen($this->file[1], 'r');
            $size = 0;
        } else {
            $size = filesize($this->file[1]);
            $gz = fopen($this->file[1], 'r');
        }

        $sql = '';
        if ($start) {
            $this->config['compress']? gzseek($gz, $start): fseek($gz, $start);
        }

        for ($i = 0; $i < 1000; $i++) {
            $sql .= $this->config['compress']? gzgets($gz): fgets($gz);
            if (preg_match('/.*;$/', trim($sql))) {
                if (false !== Db::execute($sql)) {
                    $start += strlen($sql);
                } else {
                    return false;
                }
                $sql = '';
            } elseif ($this->config['compress']? gzeof($gz): feof($gz)) {
                return 0;
            }
        }

        return [$start, $size];
    }

}