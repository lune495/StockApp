<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User,Approvisionnement,Produit,LigneApprovisionnement};
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = array();
        // array_push($users,array("name" => "Admin" , "email" => "Admin" ,"password" => "cis2023","role_id" => "1"));
        // array_push($users,array("name" => "Alioune" , "email" => "cis.showroom@gmail.com" ,"password" => "aliounecis2023","role_id" => "1"));
        // array_push($users,array("name" => "Moussa" , "email" => "moussa@gmail.com" ,"password" => "moussacis2023","role_id" => "1"));
        // array_push($users,array("name" => "Moussa" , "email" => "cis.showroom@gmail.com" ,"password" => "moussacis2023","role_id" => "1"));
        // foreach ($users as $user) {
        //     $alluser = User::all();
        //     $test = 0;
        //     foreach ($alluser as $value) {
        //       if($user['name'] == $value->name)
        //         {
        //           $test = 1;
        //         }
        //     }
        //       if($test == 0){
        //         $newuser = new User();
        //         $newuser->name = $user['name'];
        //         $newuser->email = $user['email'];
        //         $newuser->password = $user['password'];
        //         $newuser->role_id = $user['role_id'];
                
        //         $newuser->email = $user['email'];
        //         if (!isset($newuser->id))
        //         {
        //             $newuser->password = bcrypt($user['password']);
        //         }
        //         $newuser->save();
        //       }
        // }

        $appros = Approvisionnement::whereNull('type_appro')->get();
        foreach ($appros as $appro) {

            $ligne_appros = LigneApprovisionnement::where("approvisionnement_id",$appro->id)->get();
            foreach ($ligne_appros as $ligne_appro) {
                $produit = Produit::find($ligne_appro->produit_id);
                $produit->qte = $produit->qte + $ligne_appro->quantity_received;
                $produit->save();
            }
        }

    }
}
