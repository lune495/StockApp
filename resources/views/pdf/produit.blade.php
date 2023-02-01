<html>
    <head>
        <title>
            Produit
        </title>
        <style>
                .text-uppercase
                {
                    text-transform: uppercase;
                }
                .table {
                    width: 100%;
                    margin-bottom: 1rem;
                    background-color: transparent;
                }
                .table th,
                .table td {
                    padding: 0.55rem;
                    vertical-align: top;
                    border-top: 1px solid #e9ecef;
                }
                 .table thead th {
                    background-color: black;
                    vertical-align: bottom;
                    border-bottom: 2px solid #e9ecef;
                    color: #d7d9f2;
                }
                .table tbody + tbody {
                    border-top: 2px solid #e9ecef;
                }
                .table .table {
                    background-color: #fff;
                }
                .table-sm th,
                .table-sm td {
                    padding: 0.3rem;
                }
                .table-bordered {
                    border: none;
                }

                .table-bordered th,
                .table-bordered td {
                    border: none;
                }
                .table-bordered thead th,
                .table-bordered thead td {
                    border-bottom-width: 2px;
                }
                .table-borderless th,
                .table-borderless td,
                .table-borderless thead th,
                .table-borderless tbody + tbody {
                    border: 0;
                }
                .table-striped tbody tr:nth-of-type(odd) {
                    background-color: rgba(0, 0, 0, 0.03);
                }
                td,
                th {
                    border: 1px solid rgb(190, 190, 190);
                    padding: 10px;
                }

                td {
                    text-align: center;
                }

                th[scope="col"] {
                    background-color: #696969;
                    color: #fff;
                }

                th[scope="row"] {
                    background-color: #d7d9f2;
                }
                table {
                    border-collapse: collapse;
                    border: 1px solid rgb(200, 200, 200);
                    letter-spacing: 1px;
                    font-family: sans-serif;
                    font-size: .8rem;
                }
                .text-center {
                    text-align: center;
                }
                .text-left {
                    text-align: left;
                }
                .text-right {
                    text-align: right;
                }
                /** Define now the real margins of every page in the PDF **/
                body {
                    margin-top: 0.2cm;
                    font-weight: 400;
                    background:  #fff;
                    color: black;
                    -webkit-print-color-adjust:  exact;
                }

                /** Define the header rules **/
                .header {
                    position: fixed;
                    height: 1.5cm;
                }

                /** Define the footer rules **/
                .footer {
                    position: fixed;
                    bottom: 0px;
                    height: 2.5cm;
                }
                .badge{
                    padding:.5em 2em !important;
                    color:black;
                    background:#f1f1f1;
                    max-width: 300px !important;
                    border-radius:8px !important;
                    font-size:11px !important;
                }
                .mb-4{
                    margin-bottom: 5em;                }
                .mt-4{
                    margin-top: 2em;                }
        </style>
    </head>
    <body>
        <div style="text-align:center;font-size:30px;">Liste des produits</div>
        <br><br>
        <div style="text-align:center">Date: {{date('d-m-Y')}}</div>
        <br><br>
        <table class="table table-bordered w-100">
            <tr style="">
                <th style="text-align:center;font-size:15px;background-color:rgba(79, 214, 140, 0.445)"><strong>Code</strong></th>
                <th style="text-align:center;font-size:15px;"><strong>Désignation</strong></th>
                <th style="text-align:center;font-size:15px;"><strong>Stock</strong></th>
                <th style="text-align:center;font-size:15px;"><strong>Prix Achat</strong></th>
                <th style="text-align:center;font-size:15px;"><strong>Prix Vente</strong></th>
                <th style="text-align:center;font-size:15px;"><strong>Category</strong></th>
            </tr>
            <tbody>
                {{-- @foreach($taille as $key =>$item)
                    <tr align="center">
                        <td style="">{{$item->nom}}</td>
                        <td style="">{{$item->famille_produit["designation"]}}</td>
                        <td style="">{{$item->description}}</td>
                    </tr>
                @endforeach --}}
                @for ($i = 0; $i < count($data); $i++)
                <tr>
                        <td style="width:100px;text-align:center">{{$data[$i]["code"] }}</td>
                        <td style="width:400px;text-align:center">{{$data[$i]["designation"] }}</td>
                        <td style="width:100px;text-align:center">{{$data[$i]["qte"] ? $data[$i]["qte"] : 0 }}</td>
                        <td style="width:100px;text-align:center">{{$data[$i]["pa"] }}</td>
                        <td style="width:100px;text-align:center">{{$data[$i]["pv"] }}</td>
                        <td style="width:200px;text-align:center">
                            @if(isset($data[$i]["famille"]))
                                {{ $data[$i]["famille"]["nom"] }}
                            @endif
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </body>
</html>
