<?php

namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Arr;
use \App\Models\{Proforma,Outil};

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
        $count = Arr::get($args, 'count', 10);
        $page  = Arr::get($args, 'page', 1);

        return $query->orderBy('id', 'desc')->paginate($count, ['*'], 'page', $page);
    }
}

