<?php

namespace Redmix0901\ElasticResource;

use Redmix0901\ElasticResource\ElasticCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

trait ElasticCollectionTrait
{
    protected static $elasticCollection;

    public static function fromElasticsearch($response, $instance, $limit = 0)
    {

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
        static::setElasticCollection(new ElasticCollection($response, $instance));

        if ($limit) {
            return new static(
                static::getElasticCollection()->paginate($limit)
            );
        }

        if (static::class instanceof ResourceCollection) {
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
