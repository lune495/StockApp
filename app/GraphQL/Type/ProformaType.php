<?php
namespace App\GraphQL\Type;

use App\Models\{Proforma,ProformaProduit,Outil};
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Carbon\Carbon;
 
class ProformaType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'Proforma',
        'description'   => ''
    ];

    public function fields(): array
    {
       return
            [ 
                'id'                        => ['type' => Type::id(), 'description' => ''],
                'numero'                    => ['type' => Type::string()],
                'montant'                   => ['type' => Type::string()],
                'montant_avec_remise'       => ['type' => Type::float()],
                'remise_total'              => ['type' => Type::float()],
                'montant_ht'                => ['type' => Type::float()],
                'montant_ttc'               => ['type' => Type::float()],
                'montant_taxe'              => ['type' => Type::float()],
                'qte'                       => ['type' => Type::int()],
                'taxe'                      => ['type' => GraphQL::type('Taxe')],
                'client_id'                 => ['type' => Type::int()],
                'client'                    => ['type' => GraphQL::type('Client')],
                'user_id'                   => ['type' => Type::int()],
                'user'                      => ['type' => GraphQL::type('User')],
                'proforma_produits'         => ['type' => Type::listOf(GraphQL::type('ProformaProduit')), 'description' => ''],
                'created_at'                => ['type' => Type::string()],
                'created_at_fr'             => ['type' => Type::string()],
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
    protected function resolveQteField($root, array $args)
    {
        
        return $root['qte']==null ? 0 : $root['qte'];
    }

    protected function resolveMontantHtField($root, $args)
    {
        return $root['montant'];
    }
    protected function resolveMontantAvecRemiseField($root, $args)
    {
        $proformaprdts = ProformaProduit::where('proforma_id',$root['id'])->get();
        $montant_total_proforma = 0;
        foreach($proformaprdts as $proformaprdt){
            $montant_remise = ((($proformaprdt->prix_vente * $proformaprdt->qte)*$proformaprdt->remise)/100);
            // dd($montant_remise);
            $montant_total_proforma = $montant_total_proforma + (($proformaprdt->prix_vente * $proformaprdt->qte)-$montant_remise);
        }
        return round($montant_total_proforma);
    }
    protected function resolveRemiseTotalField($root, $args)
    {
        //  $proformaprdts = ProformaProduit::where('proforma_id',$root['id'])->get();
        //  $remise_total = 0;
        //  $cpt = 0;
        //  foreach($proformaprdts as $proformaprdt){
        //     $cpt++;
        //     $remise_total = $remise_total + $proformaprdt->remise;
        // }
        // return  round($remise_total/$cpt);
        return 0;
    }
    protected function resolveMontantTtcField($root, $args)
    {
        $proformaprdts = ProformaProduit::where('proforma_id',$root['id'])->get();
        $montant_total_proforma = 0;
        foreach($proformaprdts as $proformaprdt){
            $montant_remise = ((($proformaprdt->prix_vente * $proformaprdt->qte)*$proformaprdt->remise)/100);
            // dd($montant_remise);
            $montant_total_proforma = $montant_total_proforma + (($proformaprdt->prix_vente * $proformaprdt->qte)-$montant_remise);
        }
        $montant_ttc = $root['taxe'] ? $montant_total_proforma + (($montant_total_proforma * $root['taxe']['value'])/100) : 0;
        return isset($root['taxe']) ? round($montant_ttc) : 0;
    }
    protected function resolveMontantTaxeField($root, $args)
    {
        $proformaprdts = ProformaProduit::where('proforma_id',$root['id'])->get();
        $montant_total_proforma = 0;
        foreach($proformaprdts as $proformaprdt){
            $montant_remise = ((($proformaprdt->prix_vente * $proformaprdt->qte)*$proformaprdt->remise)/100);
            // dd($montant_remise);
            $montant_total_proforma = $montant_total_proforma + (($proformaprdt->prix_vente * $proformaprdt->qte)-$montant_remise);
        }
        $montant_remise = $root['taxe'] ? ($montant_total_proforma * $root['taxe']['value'])/100 : 0;
        return $montant_remise;
    }
}