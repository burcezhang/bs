<?php

namespace app\backend\model;

use think\Model;

class Banner extends Model
{
    protected $name = 'banner';
    protected $autoWriteTimestamp = true;
    protected $deleteTime = 'deleted_time';
} 