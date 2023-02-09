<?php

namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Models\{Produit,Outil};
class ProduitQuery extends Query
{
    protected $attributes = [
        'name' => 'produits'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Produit'));
    }

    public function args(): array
    {
        return
        [
            'id'                  => ['type' => Type::int()],
            'nom'                 => ['type' => Type::string()],
            'code'                => ['type' => Type::string()],
            'search'              => ['type' => Type::string()],
            'designation'         => ['type' => Type::string(), 'description' => ''],
            'visible_appro'       => ['type' => Type::int(), 'description' => ''],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Produit::query();
        if (isset($args['id']))
        {
            $query = $query->where('id', $args['id']);
        }
        if (isset($args['search']))
        {
            $query = $query->where('designation',Outil::getOperateurLikeDB(),'%'.$args['search'].'%')
                           ->orWhere('code',Outil::getOperateurLikeDB(),'%'.$args['search'].'%');
        }
        if (isset($args['designation']))
        {
            $query = $query->where('designation',Outil::getOperateurLikeDB(),'%'.$args['designation'].'%');
        }
        // if(isset($args['designation']) && isset($args['visible_appro']) && $args['visible_appro'] == 1)
        // {
        //     // $query = $query->join('depots', 'produits.id', '=', 'depots.produit_id')
        //     // ->where('produits.designation',Outil::getOperateurLikeDB(),'%'.$args['designation'].'%')
        //     // ->selectRaw('produits.*');
        //     $query = $query->where('designation',Outil::getOperateurLikeDB(),'%'.$args['designation'].'%');
        // }
        $query->orderBy('id', 'desc');
        $query = $query->get();
        return $query->map(function (Produit $item)
        {
            return
            [
                'id'                      => $item->id,
                'image'                   => $item->image,
                'code'                    => $item->code,
                'designation'             => $item->designation,
                'description'             => $item->description,
                'pa'                      => $item->pa,
                'pv'                      => $item->pv,
                'qte'                     => $item->qte,
                'limite'                  => $item->limite,
                'famille_id'              => $item->famille_id,
                'famille'                 => $item->famille,
                'depots'                  => $item->depots,
                'nbr_produit'             => $item->nbr_produit,
            ];
        });

    }
}
