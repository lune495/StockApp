<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Produit,Inventaire,User,Outil,LigneInventaire};
use Illuminate\Support\Facades\DB;
use \PDF;


class InventaireController extends Controller
{
    //
    private $queryName = "inventaires";
    public function save(Request $request)
    {
        try {
            return DB::transaction(function () use ($request)
            {
                $errors = null;
                // $fournisseur = null;
                $item = new Inventaire();
                // $user_id = auth('sanctum')->user()->id;
                $qte_total_inventaire = 0;
                if(!(array_key_exists('user_id', $request->all()))){
                    $errors = "Déconnectez-vous et connectez vous à nouveau";
                }
                DB::beginTransaction();
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
                        else if(!isset($detail['quantite_reel']) || !is_numeric($detail['quantite_reel']) || $detail['quantite_reel'] < 1)
                        {
                            $errors = "Veuillez défnir la quantité";
                        }
                        else
                        {

                            $itemDetail = LigneInventaire::where('inventaire_id',$itemId)->where('produit_id', $detail['produit_id'])->first();
                            if ($itemDetail==null)
                            {
                                $itemDetail = new LigneInventaire();
                                $itemDetail->inventaire_id = $itemId;
                                $itemDetail->produit_id = $detail['produit_id'];
                            }
                            $itemDetail->quantite_reel = $detail['quantite_reel'];
                            $itemDetail->quantite_theorique = $getProduit->qte;
                            $saved = $itemDetail->save();
                            if($saved)
                            {
                                $qte_total_inventaire = $qte_total_inventaire + $itemDetail->quantite_reel;
                            }
                        }
                    } 
                    $item->numero = "Inv000{$item->id}";
                    $item->qte_total_inventaire = $qte_total_inventaire;
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
    // public function abortappro($id)
    // {
    //     //
    //     try 
    //     {
    //         $appro = Approvisionnement::find($id);
    //         if($appro && $appro->type_appro == 'BOUTIQUE'){
    //             if($appro->statut == 0)
    //             {
    //                 DB::beginTransaction();
    //                 $ligne_appros = LigneApprovisionnement::where('approvisionnement_id',$appro->id)->get();
    //                 foreach ($ligne_appros as $ligne_appro) 
    //                 {
    //                     $produit = Produit::find($ligne_appro->produit_id);
    //                     if($produit)
    //                     {
    //                         $produit->qte = $produit->qte - $ligne_appro->quantity_received;
    //                         $produit->save();
    //                     }
    //                 }
    //                 $appro->statut = 1;
    //                 if($appro->save())
    //                 {
    //                     DB::commit();
    //                     $id = $appro->id;
    //                     return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
    //                 }
    //             }else{
    //                 return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
    //             } 
    //         }
    //         if($appro && $appro->type_appro == 'DEPOT'){
    //             if($appro->statut == 0)
    //             {
    //                 DB::beginTransaction();
    //                 $ligne_appros = LigneApprovisionnement::where('approvisionnement_id',$appro->id)->get();
    //                 foreach ($ligne_appros as $ligne_appro) 
    //                 {
    //                     $depot = Depot::where('produit_id',$ligne_appro->produit_id);
    //                     if($depot)
    //                     {
    //                         $depot->stock = $depot->stock - $ligne_appro->quantity_received;
    //                         $depot->save();
    //                     }
    //                 }
    //                 $appro->statut = 1;
    //                 if($appro->save())
    //                 {
    //                     DB::commit();
    //                     $id = $appro->id;
    //                     return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
    //                 }
    //             }else{
    //                 return  Outil::redirectgraphql($this->queryName, "id:{$id}", Outil::$queries[$this->queryName]);
    //             } 
    //         }
    //     } catch (exception $e) {
    //         DB::rollback();
    //         return $e->getMessage();
    //     }
    // }
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

        $appro = Inventaire::find($id);
        if($appro!=null)
        {
         $data = Outil::getOneItemWithGraphQl($this->queryName, $id, true);
         $pdf = PDF::loadView("pdf.inventaires", $data);
        return $pdf->stream();
        }
        else
        {
         $data = Outil::getOneItemWithGraphQl($this->queryName, $id, false);
            return view('notfound');
        }
    }
}
