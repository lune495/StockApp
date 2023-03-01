<?php

namespace App\GraphQL\Query;
use App\Models\{LigneApprovisionnement,Approvisionnement,Produit,Outil};
use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Arr;


class ApprovisionnementPaginatedQuery extends Query
{
    protected $attributes = [
        'name' => 'approvisionnementspaginated',
        'description' => ''
    ];

    public function type():type
    {
        return GraphQL::type('approvisionnementspaginated');
    }

    public function args():array
    {
        return
        [
            'id'                       => ['type' => Type::int()],
            'user_id'                  => ['type' => Type::int()],
            'fournisseur_id'           => ['type' => Type::int()],
            'produit_id'               => ['type' => Type::int()],
            'created_at_start'         => ['type' => Type::string()],
            'created_at_end'           => ['type' => Type::string()],
            'page'                     => ['type' => Type::int()],
            'count'                    => ['type' => Type::int()]
        ];
    }

    public function resolve($root, $args)
    {
        $query = Approvisionnement::with('ligne_approvisionnements');
        if (isset($args['id']))
        {
            $query->where('id', $args['id']);
        }
        if (isset($args['user_id']))
        {
            // $query->where('user_id', $args['user_id']);
        }
        if (isset($args['produit_id']))
        {
            $query->whereIn('id', LigneApprovisionnement::where('produit_id', $args['produit_id'])->get(['approvisionnement_id']))->get();
        }
       
        if (isset($args['created_at_start']) && isset($args['created_at_end']) && !empty($args['created_at_start']) && !empty($args['created_at_end']))
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
        if (isset($args['fournisseur_id']))
        {
            $query->whereIn('id', LigneApprovisionnement::where('fournisseur_id', $args['fournisseur_id'])->get(['approvisionnement_id']))->get();
        }


        $count = Arr::get($args, 'count', 20);
        $page  = Arr::get($args, 'page', 1);

        return $query->orderBy('created_at', 'desc')->paginate($count, ['*'], 'page', $page);

    }
}

