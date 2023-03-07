<?php

namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Arr;
use \App\Models\{Proforma,Outil};
use Carbon\Carbon;


class ProformaPaginatedQuery extends Query
{
    protected $attributes = [
        'name'              => 'proformaspaginated',
        'description'       => ''
    ];

    public function type():type
    {
        return GraphQL::type('proformaspaginated');
    }

    public function args():array
    {
        return
        [
            'id'                  => ['type' => Type::int()],
            'numero'              => ['type' => Type::string()],
            'client_id'           => ['type' => Type::int()],
            'user_id'             => ['type' => Type::int()],
            'created_at_start'         => ['type' => Type::string()],
            'created_at_end'           => ['type' => Type::string()],

            'created_at'               => ['type' => Type::string(), 'description' => ''],
            'created_at_fr'            => ['type' => Type::string(), 'description' => ''],
            'updated_at'               => ['type' => Type::string(), 'description' => ''],
            'updated_at_fr'            => ['type' => Type::string(), 'description' => ''],
        
            'page'                          => ['name' => 'page', 'description' => 'The page', 'type' => Type::int() ],
            'count'                         => ['name' => 'count',  'description' => 'The count', 'type' => Type::int() ]
        ];
    }


    public function resolve($root, $args)
    {
        $query = Proforma::query();
        if (isset($args['id']))
        {
            $query->where('id', $args['id']);
        }
        if (isset($args['client_id']))
        {
            $query = $query->where('client_id', $args['client_id']);
        }
        if (isset($args['user_id']))
        {
            // $query = $query->where('user_id', $args['user_id']);
        }
        if (isset($args['numero']))
        {
            $query = $query->where('numero',Outil::getOperateurLikeDB(),'%'.$args['numero'].'%');
        }
        if (isset($args['created_at_start']) && isset($args['created_at_end']))
        {
            $from = $args['created_at_start'];
            $to = $args['created_at_end'];

            // Eventuellement la date fr
            $from = (strpos($from, '/') !== false) ? Carbon::createFromFormat('d/m/Y', $from)->format('Y-m-d') : $from;
            $to = (strpos($to, '/') !== false) ? Carbon::createFromFormat('d/m/Y', $to)->format('Y-m-d') : $to;

            $from = date($from.' 00:00:00');
            $to = date($to.' 23:59:59');
            $query->whereBetween('created_at', array($from, $to));
        }
        $count = Arr::get($args, 'count', 10);
        $page  = Arr::get($args, 'page', 1);

        return $query->orderBy('id', 'desc')->paginate($count, ['*'], 'page', $page);
    }
}

