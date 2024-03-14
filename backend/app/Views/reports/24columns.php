<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>24 Column Report</title>
    <style type="text/css">
        /* @page {
            margin: 0;
        }
        * { padding: 0; margin: 0; } */
        @font-face {
            font-family: "source_sans_proregular";           
            src: local("Source Sans Pro"), url("fonts/sourcesans/sourcesanspro-regular-webfont.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;

        }        
        body{
            font-family: "source_sans_proregular", Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;            
        }
    </style>
</head>

<body>
  <div class="container mt-1" style="margin-top:-25px;">

    <div style="text-align:left;">
        <span style="font-size: 9pt;font-weight: bold;">VIKING'S PAWNSHOP</span><br/>
        <span style="font-size: 9pt;"><i>Vireli Building, Del Pilar Street, Cabanatuan City</i></span> <br/>
        <span style="font-size: 9pt;"><i>TIN#160-963-246</i></span> <br/><br/>
        <span style="font-size: 12pt;"><?= $generatedDate ?></span> <br/>
    </div>

    <div style="text-align:center;">
        <span style="font-size: 10pt; font-weight: bold;">( 24 COLUMN )</span><br/>
        <span style="font-size: 10pt; font-weight: bold;">SUMMARY REPORT</span> <br/>
    </div>

    <?php 

        // echo '<pre>';
        // print_r($result);
    ?>
    <div style="border: 1px solid black; border-bottom: 3px solid black; padding: 3px; margin-top: 10px; margin-bottom: 10px;"></div>
    <table style="width: 100%; border-collapse: collapse; border-bottom:0px !important; font-size: 9pt;">
        <tr style="border-bottom: 1px solid black;">
            <?php
                foreach ($columns as $key => $value) {
                    $cols = '';
                    
                    $cols .= '<td style=" padding: 5px; text-align: center;">';
                    $cols .= $value['label'];
                    $cols .= '</td>';
                    
                    echo $cols;
                }
            ?>
        </tr>
        <!-- data -->
        <?php foreach($list as $row): ?>
            <tr>
                <td style="text-align: center;"><?= $row['no'] ?></td>
                <td><?= $row['pawnerName'] ?></td>
                <td style="text-align: center;"><?= $row['pawnTicket'] ?></td>
                <td style="text-align: center;"><?= $row['serviceCharge'] ?></td>
                <td style="text-align: center;"><?= $row['principal'] ?></td>
                <td><?= $row['redeemed'] ?></td>
                <td><?= $row['canceled'] ?></td>
                <td><?= $row['spoiled'] ?></td>
                <td><?= $row['address'] ?></td>
                <td>
                    <!-- let str = `${el.qty} ${el.unit.value} ${el.type.value}, ${el.description || 'N/A'}, ${el.weight}/${el.property} with ${el.remarks || ''}`; -->
                    <?php foreach($row['items'] as $itm): ?>
                        <span>
                            <?= $itm->qty ." ". $itm->unit->value ." ". $itm->type->value .", ". $itm->description .", ". $itm->weight ."/". $itm->property ." WITH ". $itm->remarks ?>
                        </span>
                    <?php endforeach ?> 
                </td>
            </tr>
        <?php endforeach ?>
    </table>
  </div>
</body>

</html>