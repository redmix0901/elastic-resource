<?php

namespace Redmix0901\ElasticResource;

use Redmix0901\ElasticResource\ElasticCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

trait ElasticCollectionTrait
{
    protected static $elasticCollection;

    public static function fromElasticsearch($response, $limit = 0)
    {
        if($response === null){
            $response = [];
        }
        
        /**
         * Kiểm tra nếu $response là 1 collection của model hoặc là 1 model
         * thì sẽ transformers dữ liệu mặc định
         */
        if ($response instanceof Model || $response instanceof Collection) {
            return new static($response);
        }

        /**
         * Khởi tạo elasticsearch collection
         */
        static::setElasticCollection(new ElasticCollection($response));

        if ($limit) {
            return new static(
                static::getElasticCollection()->paginate($limit)
            );
        }

        if (is_a(static::class, ResourceCollection::class, true)) {
            return new static(
                static::getElasticCollection()->all()
            );
        }

        return new static(
            static::getElasticCollection()->first()
        );
    }

    /**
     * Set the elastic collection.
     *
     * @param  ElasticCollection
     * @return void
     */
    public static function setElasticCollection(ElasticCollection $elasticCollection)
    {
        static::$elasticCollection = $elasticCollection;
    }

    /**
     * Get the elastic collection.
     *
     * @return ElasticCollection
     */
    public static function getElasticCollection()
    {
        return static::$elasticCollection;
    }
}
