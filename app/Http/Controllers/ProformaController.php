<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Proforma,ProformaProduit,User,Outil,produit};
use Illuminate\Support\Facades\DB;
use \PDF;

class ProformaController extends Controller
{
    // 
    private $queryName = "proformas";
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        try 
        {
                $errors =null;
                $item = new Proforma();
                // $user_id = auth('sanctum')->user()->id;
                $montant_total_proforma = 0;
                $qte_total_proforma = 0;
                if (!empty($request->id))
                {
                    $item = Proforma::find($request->id);
                }
                    // if (empty($request->client_id))
                    // {
                    //     $errors = "Renseignez le client";
                    // }
                
                    DB::beginTransaction();
                    $item->client_id = $request->client_id;
                    $item->user_id = $request->user_id;
                    $str_json = json_encode($request->details);
                    $details = json_decode($str_json, true);
                        if (!isset($errors)) 
                        {
                            $item->save();
                            foreach ($details as $detail) 
                            {
                                $produit = Produit::find($detail['produit_id']);
                                if (!isset($produit)) {
                                $errors = "Produit inexistant";
                                }
                                if (empty($detail['prix_vente']))
                                {
                                    $errors = "Renseignez le prix unitaire du produit : {$produit->designation}";
                                }
                                        // $oldDetails = ProformaProduit::where('proforma_id',$item->id)->get();
                                        // Outil::Checkdetail($oldDetails, $details, ProformaProduit::class, ['produit_id']);
                                        $proformaprdt = new ProformaProduit(); 
                                        $proformaprdt->produit_id = $detail['produit_id'];
                                        $proformaprdt->proforma_id  = $item->id;
                                        $proformaprdt->qte = $detail['quantite'];
                                        $proformaprdt->prix_vente = $detail['prix_vente'];
                                        $saved = $proformaprdt->save();
                                        if($saved)
                                        {
                                            $qte_total_proforma = $qte_total_proforma + $proformaprdt->qte;
                                            $montant_total_proforma = $montant_total_proforma  + ($detail['prix_vente'] * $proformaprdt->qte);
                                            // $produit->save();
                                        }
                            }
                        }
                        if (!isset($errors)) 
                        { 
                            $tva = !(array_key_exists('tva', $request->all())) ? false : Taxe::first();
                            if($tva!= false && $tva->value != null){
                               $item->taxe_id = $tva->id;
                            }
                            $item->montant = $montant_total_proforma;
                            $item->qte = $qte_total_proforma;
                            $item->numero = "PR-CIS22100{$item->id}";
                            $item->save();
                            $id = $item->id;
                            DB::commit();
                            return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
                        }
                        if (isset($errors))
                        {
                            throw new \Exception($errors);
                        }

        } catch (exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        

    }

    public function generatePDF($id)
    {
        $proforma = Proforma::find($id);
        if($proforma != null)
        {
         $data = Outil::getOneItemWithGraphQl($this->queryName, $id, true);
            // dd($data);   
            $pdf = PDF::loadView("pdf.devis", $data);
            // $measure = array(0,0,225.772,650.197);
            // return $pdf->setPaper($measure, 'orientation')->stream();
            return $pdf->stream();
        }else{
         $data = Outil::getOneItemWithGraphQl($this->queryName, $id, false);
            return view('notfound');
        }
    }
}