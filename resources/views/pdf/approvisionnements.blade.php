@extends('pdf.layouts.layout-export2')
@section('title', "PDF Facture commande")
@section('content')
    <table style="border: none; border: none;margin-top:2px;font-size: 11px">
        <tr>
            <td style="border: none">
                <p style="font-weight: bold;font-size: 14px">C.I.S SHOWROOM</p>
                <p style="font-size: 11px">Vente de Materiels de Plomberie Sanitaire Tuyauterie</p>
                <p style="font-size: 11px">Electricité Carreaux Luminaire</p>
            </td>
        </tr>
        <tr  style="border: none">
            <td  style="border: none">
                <div style="" >
                    <p  style="text-align:left;line-height:5px"> OUEST FOIRE, TALLY WALLY N°21  </p>
                    <p style="text-align:left;line-height:5px"> +221 77 348 15 82</p>
                    <p style="text-align:left;line-height:5px"> +221 77 597 55 21</p>
                </div>
            </td>
            <td style="border:none;">
                <div style="border-left: 3px solid black">
                    <p style="text-align:left ; margin-left:15px;line-height:5px ">www.cis-immobiliere.sn</p>
                    <p style="text-align:left ; margin-left:15px;line-height:5px ">Instagram:  @cis</p>
                    <p style="text-align:left ; margin-left:15px;line-height:5px ">email:  cisvdn@gmail.com</p>
                </div>
            </td>
            <td style="border:none;"></td>
            <td style="border:none;"></td>
        </tr>
    </table>

    <table style="border: none;font-size: 11px; margin-top:0px">
        <tr  style="border: none">
            <td style="border: none;">
                <div>
                    <p class="badge" style="text-align:left;line-height:15px">Date</p>
                    <p style="text-align:left;line-height:5px">{{ $created_at_fr}}</p>
                    <p style="style=border-left: 2px solid white;border-bottom: 2px solid white"></p>
                    <p style="style=border-left: 2px solid white;border-bottom: 2px solid white"></p>
                </div>
            </td>
            <td style="border: none;"></td><td style="border: none;"></td><td style="border: none;"></td><td style="border: none;"></td><td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none">
                 <div>
                    <p class="badge" style="text-align:left;line-height:15px">Fournisseur</p>
                    <p style="text-align:left;line-height:5px">{{ $fournisseur ? "Nom: " . $fournisseur["nom_complet"] : "FOURNISSEUR DE PASSAGE"}}</p>
                    <p style="text-align:left ;line-height:5px ">{{ $fournisseur ? "Téléphone: " . $fournisseur["telephone"] : " "}}</p>
                    <p style="text-align:left;line-height:5px;text-transform: capitalize "> {{ $fournisseur ? "Adresse: " . $fournisseur["adresse"] : ""}}</p>

                </div>
            </td>
            <td style="border:none;"></td>
        </tr>
    </table>

    <table style="border: none;font-size: 11px; margin-top:30px">
        <tr  style="border: none">
            <td style="border: none; margin-left: 15px">
                <div>
                    <p class="badge" style="text-align:left;line-height:15px">Numero</p>
                    {{-- <p style="text-align:left;line-height:5px">{{ $data[0]['code']}}</p> --}}
                    <p style="text-align:left;line-height:5px">{{$numero ? $numero : "SN0002022FA01"}}</p>
                </div>
            </td>
            <td style="border:none;"></td>
            <td style="border: none; margin-left: 15px">
                <div>
                    <p class="badge" style="text-align:left;line-height:15px">Date</p>
                    <p style="text-align:left;line-height:5px">{{ $created_at_fr}}</p>
                </div>
            </td>
            <td style="border:none;"></td>
        </tr>
    </table>

    <h2 style="margin:0">Facture Achat </h2>
    <p class="badge" style="text-align:left;line-height:15px">{{$type_appro}}</p>
    <br>
    <table class="table table-bordered w-100">
        <tr style="font-size: 1.2em;">
            <th style="border:none"> <p class="badge">Produit</p> </th>
            <th style="border:none"><p class="badge">Qté</p></th>
            <th style="border:none"><p class="badge">P U</p></th>
            <th style="border:none"><p class="badge">Remise</p></th>
            <th style="border:none"><p class="badge">Montant HT</p></th>
        </tr>
    <tbody style="border:none">
        @foreach($ligne_approvisionnements as $ligne_approvisionnement)
            <tr style="padding:0px;">
                <td style="font-size:11px;padding: 2px"> {{$ligne_approvisionnement["produit"]["designation"]}}</td>
                <td style="font-size:11px;padding: 2px"> {{$ligne_approvisionnement["quantity_received"]}}</td>
                <td style="font-size:11px;padding: 2px"> {{$ligne_approvisionnement["produit"]["pa"]}}</td>
                <td style="font-size:11px;padding: 2px"> {{0}}</td>
                <td style="font-size:11px;padding: 2px">{{\App\Models\Outil::formatPrixToMonetaire($ligne_approvisionnement["quantity_received"]*$ligne_approvisionnement["produit"]["pa"], false, false)}}</td>
            </tr>
        @endforeach

        <!--total-->
        <tr>
            <td colspan="1" style="border-left: 2px solid white;border-bottom: 2px solid white"></td>
            <td colspan="2">
                <div>
                    <p class="badge" style="line-height:15px;font-size:9px!important">Total HT</p>
                    <p style="line-height:5px">{{ $montant}}</p>
                </div>
            </td>
            {{-- <td>
                <div>
                    <p class="badge" style="line-height:15px"> Remise</p>
                    <p style="line-height:5px">0</p>
                </div>
            </td> --}}
            {{-- <td>
                <div>
                    <p class="badge" style="line-height:15px">tva</p>
                    <p style="line-height:5px">0</p>
                </div>
            </td> --}}
            <td colspan="2" style="font-weight: bold;font-size: 14px"> 
                <div>
                    <p class="badge">Net a payer</p>
                    <p style="line-height:5px">{{ \App\Models\Outil::formatPrixToMonetaire($montant, false, true)}}</p>
                </div> 
            </td>
            <td style="font-weight: bold;font-size: 14px">  </td>
        </tr>
        <tr>
            <td colspan="2"  style="padding-top : 10px;font-weight: bold;font-size: 11px">Conditions Reglement</td>
            <td style="padding-top : 10px;font-weight: bold;font-size: 11px"> {{$created_at_fr}} </td>
            <td style="padding-top : 10px;font-weight: bold;font-size: 11px"> ESP</td>
            <td style="padding-top : 10px;font-weight: bold;font-size: 11px"> {{\App\Models\Outil::formatPrixToMonetaire($montant, false, true)}} </td>
        </tr>
        
    </tbody>
</table>
@endsection