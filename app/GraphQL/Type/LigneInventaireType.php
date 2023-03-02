<?php

namespace App\GraphQL\Type;

use  App\Models\{LigneInventaire,Outil};
use Carbon\Carbon;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class LigneInventaireType extends GraphQLType
{

    protected $attributes =
    [
        'name' => 'LigneInventaire',
        'description' => ''
    ];

    public function fields():array
    {
        return
        [
            'id'                                                        => [ 'type' => Type::int(), 'description' => ''],
            'inventaire_id'                                             => [ 'type' => Type::int(), 'description' => ''],
            'produit_id'                                                => [ 'type' => Type::int(), 'description' => ''],
            'quantite_reel'                                             => [ 'type' => Type::int(), 'description' => ''],
            'quantite_theorique'                                        => [ 'type' => Type::int(), 'description' => ''],
            'inventaire'                                                => [ 'type' => GraphQL::type('Inventaire'), 'description' => ''],
            'produit'                                                   => [ 'type' => GraphQL::type('Produit'), 'description' => ''],
            'created_at'                                                => [ 'type' => Type::string(), 'description' => ''],
            'created_at_fr'                                             => [ 'type' => Type::string(), 'description' => ''],
            'updated_at'                                                => [ 'type' => Type::string(), 'description' => ''],
            'updated_at_fr'                                             => [ 'type' => Type::string(), 'description' => ''],
        ];
    }
    /*************** Pour les dates ***************/
    protected function resolveCreatedAtField($root, $args)
    {
        if (!isset($root['created_at']))
        {
            $date_at = $root->created_at;
        }
        else
        {
            $date_at = is_string($root['created_at']) ? $root['created_at'] : $root['created_at']->format(Outil::formatdate());
        }
        return $date_at;
    }

    protected function resolveCreatedAtFrField($root, $args)
    {
        if (!isset($root['created_at']))
        {
            $created_at = $root->created_at;
        }
        else
        {
            $created_at = $root['created_at'];
        }
        return Carbon::parse($created_at)->format('d/m/Y H:i:s');
    }

    protected function resolveUpdatedAtField($root, $args)
    {
        if (!isset($root['updated_at']))
        {
            $date_at = $root->updated_at;
        }
        else
        {
            $date_at = is_string($root['updated_at']) ? $root['updated_at'] : $root['updated_at']->format(Outil::formatdate());
        }
        return $date_at;
    }

    protected function resolveUpdatedAtFrField($root, $args)
    {
        if (!isset($root['created_at']))
        {
            $date_at = $root->created_at;
        }
        else
        {
            $date_at = $root['created_at'];
        }
        return Carbon::parse($date_at)->format('d/m/Y H:i:s');
    }
    /*************** /Pour les dates ***************/

}
