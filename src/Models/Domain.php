<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Domain extends Model
{
    use HasDateTimeFormatter;

    protected $fillable = ['host','manager_id'];

    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.domains_table'));
    }


    public function manager()
    {
        $managerModel = config('admin.database.users_model');
        return $this->belongsTo($managerModel, 'manager_id');
    }

    public static function fromRequest() : Domain {
        if(request())
            $host = request()->getHost();
        else
           $host = Str::of(config('app.url'))->remove('http://')->remove('https://');

        $domain = self::whereHost($host)->first();

        if(!$domain)
            throw new \Exception('Domain not setup');

        return $domain;
    }
}
