<?php

namespace Redmix0901\ElasticResource\Entities;

use Illuminate\Database\Eloquent\Model;
use Redmix0901\ElasticResource\BuildFromElasticsearch;

class ElasticModel extends Model
{
    use BuildFromElasticsearch;

    protected $fillable = ['id'];
}
