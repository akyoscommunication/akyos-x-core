<?php

namespace Akyos\Core\Wrappers;

use GuzzleHttp\Psr7\Query;
use WP_Query;

const ASC = 'ASC';
const DESC = 'DESC';

class QueryBuilder
{

    private string $post_type;
    private int $limit;
    private string $orderBy;
    private string $order;
    private $offset;
    private string $research = "";
    private array $where = [];
    private array $taxonomy = [];
    private string $relation = 'AND';
    private $category;
    private string $paged;

    private function __construct($post_type)
    {
        $this->post_type = $post_type;
        $this->limit = -1;
        $this->orderBy = 'date';
        $this->order = ASC;
        $this->paged = 1;
        $this->offset = null;
        $this->category = null;
    }

    public static function make(string $post_type): QueryBuilder
    {
        if (!post_type_exists($post_type)) {
            wp_die('Unable to query ' . $post_type . ' because it does not exists.');
        }
        return new QueryBuilder($post_type);
    }

    public function all(): array
    {
        return QueryBuilder::make($this->post_type)->limit(-1)->get();
    }

    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    public function page(int $page): QueryBuilder {
        $this->paged = $page;
        return $this;
    }

    public function research(string $research)
    {
        $this->research = $research;
    }

    public function orderBy(string $value, string $order): QueryBuilder
    {
        if (!in_array($order, [ASC, DESC])) {
            wp_die('Unable to order by ' . $order . ' because it is not a valid order.');
        }
        $this->orderBy = $value;
        $this->order = $order;
        return $this;
    }

    public function offset(int $offset): QueryBuilder
    {
        $this->offset = $offset;
        return $this;
    }

    public function where(string $key, $value, string $operator): QueryBuilder
    {
        $this->where[] = [
            'type' => gettype($value),
            'key' => $key,
            'value' => $value,
            'operator' => $operator,
        ];
        return $this;
    }

    public function orWhere(string $key, $value, string $operator): QueryBuilder
    {
        $this->where[] = [
            'type' => gettype($value),
            'key' => $key,
            'value' => $value,
            'operator' => $operator,
        ];
        $this->relation = 'OR';
        return $this;
    }

    public function andWhere(string $key, $value, string $operator): QueryBuilder
    {
        $this->where[] = [
            'type' => gettype($value),
            'key' => $key,
            'value' => $value,
            'operator' => $operator,
        ];
        $this->relation = 'AND';
        return $this;
    }

    public function category(string $category): QueryBuilder
    {
        $this->category = $category;
        return $this;
    }

    public function taxonomy(string $taxonomy, mixed $terms): QueryBuilder
    {
        if (!taxonomy_exists($taxonomy)) {
            wp_die('Unable to query ' . $taxonomy . ' because it does not exists.');
        }
        $this->taxonomy[] = [
            'taxonomy' => $taxonomy,
            'terms' => $terms,
        ];
        return $this;
    }

    public function get($returnFormat = 'array')
    {
        $args = [
            'post_type' => $this->post_type,
            'posts_per_page' => $this->limit,
            'orderby' => $this->orderBy,
            'order' => $this->order,
            'offset' => $this->offset ?: ($this->paged-1) * $this->limit,
            'paged' => $this->paged
        ];
        if (!empty($this->research)) {
            $args['s'] = $this->research;
        }
        if($this->category) {
            $args['cat'] = $this->category;
        }
        if (count($this->where) > 0) {
            $args['meta_query'] = ['relation' => $this->relation];
            foreach ($this->where as $where) {
                $args['meta_query'][] = [
                    'key' => $where['key'],
                    'value' => $where['value'],
                    'compare' => $where['operator'],
                ];
            }
        }
        if (count($this->taxonomy) > 0) {
            $args['tax_query'] = ['relation' => 'AND'];
            foreach ($this->taxonomy as $taxonomy) {
                $args['tax_query'][] = [
                    'taxonomy' => $taxonomy['taxonomy'],
                    'terms' => $taxonomy['terms'],
                ];
            }
        }
        $query = (new WP_Query($args));

        if($returnFormat === 'query') {
            return $query;
        }
        if ($returnFormat === 'obj') {
            return objectify($query->posts);
        } else {
            return $query->posts;
        }
    }

}
