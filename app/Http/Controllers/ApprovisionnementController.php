<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Fournisseur,Produit,Approvisionnement,User,Depot,Outil,LigneApprovisionnement};
use Illuminate\Support\Facades\DB;
use \PDF;


class ApprovisionnementController extends Controller
{
    //
    private $queryName = "approvisionnements";
    public function save(Request $request)
    {
        try {
            return DB::transaction(function () use ($request)
            {
                $errors = null;
                // $fournisseur = null;
                $item = new Approvisionnement();
                // $user_id = auth('sanctum')->user()->id;
                $montant_total_appro = 0;
                $qte_total_appro = 0;
                if (isset($request->fournisseur_id))
                {
                    $fournisseur = Fournisseur::find($request->fournisseur_id);
                }
                // if(!isset($fournisseur))
                // {
                //     $errors = "Ce fournisseur n'existe pas";
                // }
                
                if(!(array_key_exists('user_id', $request->all()))){
                    $errors = "Déconnectez-vous et connectez vous à nouveau";
                }
                DB::beginTransaction();
                $item->fournisseur_id = $request->fournisseur_id;
                $item->user_id = $request->user_id; 
                // $item->user_id = 1; 
                $str_json = json_encode($request->details);
                $details = json_decode($str_json, true);
                if (!isset($errors)) 
                {

                    $item->save();
                    $itemId = $item->id; 
                    foreach ($details as $detail) 
                    {
                        $getProduit = Produit::find($detail['produit_id']);
                        if($getProduit == null )
                        {
                            $errors = "Produit  inexistant";
                        }
                        else if(!isset($detail['quantite']) || !is_numeric($detail['quantite']) || $detail['quantite'] < 1)
                        {
                            $errors = "Veuillez défnir la quantité";
                        }
                        else
                        {

                            $itemDetail = LigneApprovisionnement::where('approvisionnement_id',$itemId)->where('produit_id', $detail['produit_id'])->first();
                            if ($itemDetail==null)
                            {
                                $itemDetail = new LigneApprovisionnement();
                                $itemDetail->approvisionnement_id = $itemId;
                                $itemDetail->produit_id = $detail['produit_id'];
                            }
                            $itemDetail->quantity_received = $detail['quantite'];
                            $saved = $itemDetail->save();
                            if($saved)
                            {
                                $qte_total_appro = $qte_total_appro + $itemDetail->quantity_received;
                                $montant_total_appro = $montant_total_appro  + ($itemDetail->produit->pa * $itemDetail->quantity_received);
                            }
                        }
                        // Appro depot
                        if($request->type_appro == 'DEPOT')
                        {
                            if (isset($detail['produit_id']))
                            {
                                $depot = Depot::where("produit_id",$detail['produit_id'])->get();
                            }
                            if(!$depot->first())
                            {
                                $errors = "Ce produit n'existe pas";
                            }
                            // if (empty($request->quantite))
                            // {
                            //     $errors = "Renseignez la quantite";
                            // }
                            if (!isset($errors)) 
                            {
                                $depot = Depot::find($depot[0]['id']);
                                $depot->stock = $depot->stock + $detail['quantite'];
                                $depot->save();
                            }
                        }
                        if($request->type_appro == 'BOUTIQUE')
                        {
                            if (isset($detail['produit_id']))
                            {
                                $produit = Produit::where("id",$detail['produit_id'])->get();
                            }
                            if(!$item->first())
                            {
                                $errors = "Ce produit n'existe pas";
                            }
                            if (!isset($errors)) 
                            {
                                $produit = Produit::find($produit[0]['id']);
                                $produit->qte = $produit->qte + $detail['quantite'];
                                $produit->save();
                            }
                        }
                    }    
                    if($request->type_appro == 'DEPOT')
                    {
                        $item->type_appro = "DEPOT";
                    }elseif($request->type_appro == 'BOUTIQUE')
                    {
                        $item->type_appro = "BOUTIQUE";
                    }else{
                         $errors = "Veuillez definir le type d'approvisionnement";
                    }
                    $item->numero = "SN0002022FA0{$item->id}";
                    $item->montant = $montant_total_appro;
                    $item->qte_total_appro = $qte_total_appro;
                    $item->save();
                }
                if (isset($errors))
                {
                    throw new \Exception($errors);
                }
                 DB::commit();
                return  Outil::redirectgraphql($this->queryName, "id:{$itemId}", Outil::$queries[$this->queryName]);
            });
        } catch (exception $e) {            
             DB::rollback();
             return $e->getMessage();
        }
        
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function abortappro($id)
    {
        //
        try 
        {
            $appro = Approvisionnement::find($id);
            if($appro && $appro->type_appro == 'BOUTIQUE'){
                if($appro->statut == 0)
                {
                    DB::beginTransaction();
                    $ligne_appros = LigneApprovisionnement::where('approvisionnement_id',$appro->id)->get();
                    foreach ($ligne_appros as $ligne_appro) 
                    {
                        $produit = Produit::find($ligne_appro->produit_id);
                        if($produit)
                        {
                            $produit->qte = $produit->qte - $ligne_appro->quantity_received;
                            $produit->save();
                        }
                    }
                    $appro->statut = 1;
                    if($appro->save())
                    {
                        DB::commit();
                        $id = $appro->id;
                        return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
                    }
                }else{
                    return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
                } 
            }
            if($appro && $appro->type_appro == 'DEPOT'){
                if($appro->statut == 0)
                {
                    DB::beginTransaction();
                    $ligne_appros = LigneApprovisionnement::where('approvisionnement_id',$appro->id)->get();
                    foreach ($ligne_appros as $ligne_appro) 
                    {
                        $depot = Depot::where('produit_id',$ligne_appro->produit_id);
                        if($depot)
                        {
                            $depot->stock = $depot->stock - $ligne_appro->quantity_received;
                            $depot->save();
                        }
                    }
                    $appro->statut = 1;
                    if($appro->save())
                    {
                        DB::commit();
                        $id = $appro->id;
                        return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
                    }
                }else{
                    return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
                } 
            }
        } catch (exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function genereallPDf($id)
    {
        // $pdf = PDF::loadView('pdf.Approvisionnement', [
        //     'items'  => self::getDataForExport(),
        //         ]);
        // $measure = array(0,0,1200,700);
        // return $pdf->setPaper($measure, 'landscape')->stream();

        // $data = Outil::getOneItemWithGraphQl($this->queryName, $id, true);
        // dd($data);
        // $pdf = PDF::loadView("pdf.ventesold", $data);
        // $measure = array(0,0,225.772,650.197);
        // return $pdf->setPaper($measure, 'orientation')->stream();

        $appro = Approvisionnement::find($id);
        if($appro!=null)
        {
         $data = Outil::getOneItemWithGraphQl($this->queryName, $id, true);
         $pdf = PDF::loadView("pdf.approvisionnements", $data);
        return $pdf->stream();
        }
        else
        {
         $data = Outil::getOneItemWithGraphQl($this->queryName, $id, false);
            return view('notfound');
        }
    }
}
