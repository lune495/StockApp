<?php

namespace App\GraphQL\Query; 
use App\Models\{Vente,VenteProduit,Outil};
use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;



class VentePaginatedQuery extends Query
{
    protected $attributes = [
        'name' => 'ventespaginated',
        'description' => ''
    ];

    public function type():type
    {
        return GraphQL::type('ventespaginated');
    }

    public function args():array
    {
        return
        [
            'id'                       => ['type' => Type::int()],
            'client_id'                => ['type' => Type::int()],
            'user_id'                  => ['type' => Type::int()],
            'produit_id'               => ['type' => Type::int()],
            'reference'                => ['type' => Type::string()],
            'created_at_start'         => ['type' => Type::string()],
            'created_at_end'           => ['type' => Type::string()],

            'created_at'               => ['type' => Type::string(), 'description' => ''],
            'created_at_fr'            => ['type' => Type::string(), 'description' => ''],
            'updated_at'               => ['type' => Type::string(), 'description' => ''],
            'updated_at_fr'            => ['type' => Type::string(), 'description' => ''],
            'page'                     => ['type' => Type::int()],
            'count'                    => ['type' => Type::int()]
        ];
    }

    public function resolve($root, $args)
    {
        $query = Vente::with('vente_produits');
        if (isset($args['id']))
        {
            $query->where('id', $args['id']);
        }
        if (isset($args['client_id']))
        {
            $query->where('client_id', $args['client_id']);
        }
         if (isset($args['user_id']))
        {
            // $query->where('user_id', $args['user_id']);
        }
        if (isset($args['reference']))
        {
            $query->where('numero',Outil::getOperateurLikeDB(),'%'.$args['reference'].'%');
        }
        if(isset($args['produit_id']))
        {
            $query->whereIn('id', VenteProduit::where('produit_id', $args['produit_id'])->get(['vente_id']))->get();
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
        

        $query->orderBy('created_at','desc');
        $count = Arr::get($args, 'count', 20);
        $page  = Arr::get($args, 'page', 1);

        return $query->orderBy('created_at', 'desc')->paginate($count, ['*'], 'page', $page);

    }
}

