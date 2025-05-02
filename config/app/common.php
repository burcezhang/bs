<?php
// 应用公共文件

use think\facade\Db;

/**
 * @todo 设置超时
 */
function timeLimit()
{
    set_time_limit(0);
    ini_set("memory_limit", "-1");
}

/**
 * @todo 快速实例化数据库
 */
function M($name)
{
    return Db::name($name);
}
