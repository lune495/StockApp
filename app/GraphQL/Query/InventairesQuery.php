<?php

namespace App\GraphQL\Query;

use  App\Models\{Inventaire,LigneInventaire,Outil};
use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InventairesQuery extends Query
{
    protected $attributes = [
        'name' => 'inventaires'
    ];

    public function type():type
    {
        return Type::listOf(GraphQL::type('Inventaire'));
    }

    public function args():array
    {
        return
        [
            'id'                       => ['type' => Type::int()],
            'user_id'                  => ['type' => Type::int()],
            'produit_id'               => ['type' => Type::int()],
            'created_at_start'         => ['type' => Type::string()],
            'created_at_end'           => ['type' => Type::string()],
        ];
    }

    public function resolve($root, $args)
    {
        $query = Inventaire::with('ligne_inventaires');
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
            $query->whereIn('id', LigneInventaire::where('produit_id', $args['produit_id'])->get(['inventaire_id']))->get();
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

        $query = $query->get();

        return $query->map(function (Inventaire $item)
        {
            return
            [
                'id'                                => $item->id,
                'numero'                            => $item->numero,
                'qte_total_inventaire'              => $item->qte_total_inventaire,
                'user_id'                           => $item->user_id,
                'user'                              => $item->user,
                'ligne_inventaires'                 => $item->ligne_inventaires,
                'created_at'                        => $item->created_at,

                 
                //'deleted_at'                        => empty($item->deleted_at) ? $item->deleted_at : $item->deleted_at->format(Outil::formatdate()),
            ];
        });
    }
}
