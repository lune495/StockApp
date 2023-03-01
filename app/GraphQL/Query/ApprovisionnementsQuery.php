<?php

namespace App\GraphQL\Query;

use  App\Models\{Approvisionnement,LigneApprovisionnement,Outil};
use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ApprovisionnementsQuery extends Query
{
    protected $attributes = [
        'name' => 'approvisionnements'
    ];

    public function type():type
    {
        return Type::listOf(GraphQL::type('Approvisionnement'));
    }

    public function args():array
    {
        return
        [
            'id'                       => ['type' => Type::int()],
            'user_id'                  => ['type' => Type::int()],
            'produit_id'               => ['type' => Type::int()],
            'fournisseur_id'           => ['type' => Type::int()],
            'created_at_start'         => ['type' => Type::string()],
            'created_at_end'           => ['type' => Type::string()],
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
        if (isset($args['fournisseur_id']))
        {
            $query->whereIn('fournisseur_id',$args['fournisseur_id']);
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

        return $query->map(function (Approvisionnement $item)
        {
            return
            [
                'id'                                => $item->id,
                'numero'                            => $item->numero,
                'statut'                            => $item->statut,
                'user_id'                           => $item->user_id,
                'fournisseur_id'                    => $item->fournisseur_id,
                'montant'                           => $item->montant,
                'qte_total_appro'                   => $item->qte_total_appro,
                'fournisseur'                       => $item->fournisseur,
                'user'                              => $item->user,
                'ligne_approvisionnements'          => $item->ligne_approvisionnements,
                'type_appro'                        => $item->type_appro,
                'created_at'                        => $item->created_at,

                 
                //'deleted_at'                        => empty($item->deleted_at) ? $item->deleted_at : $item->deleted_at->format(Outil::formatdate()),
            ];
        });
    }
}
