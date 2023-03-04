<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Client,Outil};
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{

    public function save(Request $request)
    {
        //
         try 
        {
            $errors =null;
            $item = new Client();
            if (!empty($request->id))
            {
                $item = Client::find($request->id);
            }
            if (empty($request->nom_complet))
            {
                $errors = "Renseignez le nom du client";
            }
            $telephone = Outil::enleveEspaces($request->telephone);
            $item->nom_complet = $request->nom_complet;
            $item->email = $request->email;
            $item->adresse = $request->adresse;
            $item->telephone = $telephone;
                if (!isset($errors)) 
                {
                    $item->save();
                    return $item;
                }
                if (isset($errors))
                {
                    throw new \Exception('{"data": null, "errors": "'. $errors .'" }');
                }

        } catch (\Throwable $e) {
                return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
         try {
            return DB::transaction(function () use ($id) {
                $errors = null;
                $data = 0;

               if($id)
                {
                    $client = Client::find($id);
                    if ($client!=null)
                    {
                        if(count($client->ventes) > 0)
                        {
                            $data = 0;
                            $errors = "Ce client est déjà liée à une vente, impossible de le supprimer";
                        }
                        else
                        {
                            $client->delete();
                            $client->forceDelete();
                            $data = 1;
                        }
                    }
                      else
                    {
                        $data = 0;
                        $errors = "client inexistant";
                    }
                }
                else
                {
                    $errors = "Données manquantes";
                }

                if (isset($errors)) {
                    throw new \Exception('{"data": null, "errors": "' . $errors . '" }');
                }
                return response('{"data":' . $data . ', "errors": "' . $errors . '" }')->header('Content-Type', 'application/json');
            });
        } catch (\Exception $e) {
            return response($e->getMessage())->header('Content-Type', 'application/json');
        }
    }
}
