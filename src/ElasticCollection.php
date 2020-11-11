<?php

namespace Redmix0901\ElasticResource;

use App\Entities\ElasticModel;
use App\Helpers\ElasticsearchPaginator as Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ElasticCollection
{
    protected $items;

    protected $response;

    protected $instance;

    /**
     * @param array $response
     * @param $instance
     */
    public function __construct($response, $instance = null)
    {
        $this->response = $response;
        $this->instance = new ElasticModel;
        $this->items = $this->elasticToModel();
    }


    /**
     * Builds a list of models from Elasticsearch
     * results.
     *
     * @return array
     */
    public function elasticToModel()
    {
        return $this->instance->newFromElastisearch($this->response);
    }

    /**
     * Total number of hits.
     *
     * @return string
     */
    public function total()
    {
        return $this->hits()['total']['value'] ?? 0;
    }

    /**
     * hits.
     *
     * @return array
     */
    public function hits()
    {
        return $this->response['hits'] ?? [];
    }

    /**
     * Max score of the results.
     *
     * @return string
     */
    public function maxScore()
    {
        return $this->response['hits']['max_score'] ?? 0;
    }

    /**
     * Time in ms it took to run the query.
     *
     * @return string
     */
    public function took()
    {
        return $this->response['took'];
    }

    /**
     * Wheather the query timed out, or not.
     *
     * @return bool
     */
    public function timedOut()
    {
        return $this->response['timed_out'];
    }

    /**
     * Shards information.
     *
     * @param null|string $key
     * @return array|string
     */
    public function shards($key = null)
    {
        $shards = $this->response['_shards'];
        if ($key and isset($shards[$key])) {
            return $shards[$key];
        }
        return $shards;
    }

    /**
     * Aggregations information.
     *
     * @return collect
     */
    public function aggregations()
    {
        if (isset($this->response['aggregations'])) {
            return collect($this->response['aggregations'])->map(function ($item, $key) {
                return $item['buckets'];
            });
        }

        return collect();
    }

    /**
     * Get first items
     *
     *
     * @return Collection
     */
    public function first()
    {
        if (empty($this->items->first())) {
            throw (new ModelNotFoundException)->setModel(get_class($this->instance));
        }

        return $this->items->first();
    }

    /**
     * Get all items
     *
     *
     * @return Collection
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Paginate Collection
     *
     * @param int $pageLimit
     *
     * @return Paginator
     */
    public function paginate($pageLimit = 10)
    {
        $page = Paginator::resolveCurrentPage() ?: 1;
 
        return new Paginator($this->items, $this->hits(), $this->total(), $pageLimit, $page, ['path' => Paginator::resolveCurrentPath()]);
    }
}
