<?php

$hostname = "localhost";
$dbname = "sondaggi";
$user = "root";
$port = 3306;
$db = new PDO("mysql:host=$hostname;dbname=$dbname; port=$port", $user, '') or die();
echo "connessione avvenuta <br/>";

$data = json_decode(file_get_contents('php://input'), true);
$first_key = key($data);
$id_postdata;
reset($data);
if ($first_key == 'camp_id_post_data') {        //controllo che è il JSON di inserimento
    $stringaqueryquest = "INSERT INTO questionario(";
    $contquest = 0;
    //INIZIO RIEMPIMENTO TABELLA QUESTIONARIO
    while (key($data) != 'camp_dettagli_traccimaneto') {
        if (key($data) == "camp_id_post_data") {
            $id_postdata = $data[key($data)];
        }
            $stringaqueryquest = $stringaqueryquest . key($data) . ',';
            if (key($data) == "camp_timestamp_inizio"){
             $timestamp=round($data[key($data)]/1000);
            $date=date("Y-m-d H:i:s",$timestamp);
            $valorequest[$contquest] = $date;
            }else{
            $valorequest[$contquest] = $data[key($data)];
            }
            next($data);
            $contquest++;
        
    }
    next($data);
    $timestamp=round($data[key($data)]/1000);
    $date=date("Y-m-d H:i:s",$timestamp);
    $valorequest[$contquest] = $date;
    $contquest++;
    $stringaqueryquest=$stringaqueryquest.key($data).',';
    next($data);
    $valorequest[$contquest] = $data[key($data)];
    $contquest++;
    $stringaqueryquest=$stringaqueryquest.key($data).',';
    next($data);
    $valorequest[$contquest] = $data[key($data)];
    $contquest++;
    $stringaqueryquest=$stringaqueryquest.key($data);
    
    prev($data);
    prev($data);
    prev($data);
    $stringaqueryquest = $stringaqueryquest . ')';
    $stringaqueryquest = $stringaqueryquest . " VALUES(";
    for ($contvaluesquest = 0; $contvaluesquest < 8; $contvaluesquest++) {
       if ($contvaluesquest == 7) {
            $stringaqueryquest = $stringaqueryquest . "'$valorequest[$contvaluesquest]'";
        } else {
            $stringaqueryquest = $stringaqueryquest . "'$valorequest[$contvaluesquest]'" . ',';
        }
    }
    $stringaqueryquest = $stringaqueryquest . ')';
    echo "$stringaqueryquest <br/>";
    $statementq = $db->prepare($stringaqueryquest);
    if (!$statementq->execute()) {
        echo "Query questionario fallita! <br/>";
    } else {
        echo "Query questionario eseguita!<br/>";
    }
    $arraydomande = $data[key($data)];
    $numtotdomande = sizeof($arraydomande);
    $domanda = $arraydomande[0];
    $contdom = 0;
    $valoredom = [];
    $contvaluesdom = 0;
    $i = 0;
    $domandaid = "";
    $stringaqueryrisp = "";
    $valorerisposte = [];
    while ($contdom < $numtotdomande) {
        $stringaquerydom = "INSERT INTO domanda(";
        foreach ($arraydomande[$contdom] as $key => $value) {
            if($key=='camp_singola_domanda_risposta_data_id' || $key=='camp_singola_domanda_risposta_data_text' || $key=='camp_singola_domanda_risposta_data_corretta_bool'|| $key=='camp_singola_domanda_risposta_data_timestamp'){                
            }
           else if ($key == "camp_singola_domanda_parco_risposte") {
                for($z=0;$z<4;$z++){
                    next($arraydomande[$contdom]);
                }
                $stringaquerydom = $stringaquerydom .key($arraydomande[$contdom]). ',';
                $valoredom[$contvaluesdom] = $arraydomande[$contdom][key($arraydomande[$contdom])];
                $contvaluesdom++;
                next($arraydomande[$contdom]);
                $stringaquerydom = $stringaquerydom .key($arraydomande[$contdom]). ',';
                $valoredom[$contvaluesdom] = $arraydomande[$contdom][key($arraydomande[$contdom])];
                $contvaluesdom++;
                next($arraydomande[$contdom]);
                $stringaquerydom = $stringaquerydom .key($arraydomande[$contdom]). ',';
                $valoredom[$contvaluesdom] = $arraydomande[$contdom][key($arraydomande[$contdom])];
                $contvaluesdom++;
                next($arraydomande[$contdom]);
                $stringaquerydom = $stringaquerydom .key($arraydomande[$contdom]). ',';
                 $timestamp=round(($arraydomande[$contdom][key($arraydomande[$contdom])])/1000);
                 $date=date("Y-m-d H:i:s",$timestamp);
                 $valoredom[$contvaluesdom] = $date;
                 $contvaluesdom++;
                for($z=0;$z<8;$z++)
                {
                    prev($arraydomande[$contdom]);
                }
                $stringaquerydom = $stringaquerydom . "fk_questionarioid" . ') VALUES(';
                $dimensionedomanda = count($valoredom);
                for ($i = 0; $i < $dimensionedomanda; $i++) {
                    if ($i == 5) {
                        $stringaquerydom = $stringaquerydom . $valoredom[$i] . ',';
                    }  else{
                        $stringaquerydom = $stringaquerydom . "'$valoredom[$i]'" . ',';
                    }
                }
                $stringaquerydom = $stringaquerydom . "'$id_postdata'" . ')';
                $statementd = $db->prepare($stringaquerydom);
                if (!$statementd->execute()) {
                    echo "Query domanda fallita! <br/>";
                    echo $stringaquerydom;
                } else {
                    echo "Query domanda eseguita! <br/>";
                }
                $stringaqueryrisp = "INSERT INTO parco_risposte(";
                $arrayrisp = $arraydomande[$contdom][$key];
                $contatore = 0;
                $arrayrisposte = $arrayrisp[0];
                foreach ($arrayrisposte as $chiaverisp => $valorerisp) {
                    $stringaqueryrisp = $stringaqueryrisp . "$chiaverisp" . ',';
                    $valorerisposte[$contatore] = $valorerisp;
                    $contatore++;
                }
                $stringaqueryrisp = $stringaqueryrisp . "fk_domandaid," . "fk_domandaquestionario";
                $stringaqueryrisp = $stringaqueryrisp . ') VALUES(';
                $dimensionearray = sizeof($valorerisposte);
                for ($cont = 0; $cont < $dimensionearray; $cont++) {
                    if ($cont == 2 || $cont == 5 || $cont == 8 || $cont == 11) {
                        $stringaqueryrisp = $stringaqueryrisp . $valorerisposte[$cont] . ',';
                    } else {
                        $stringaqueryrisp = $stringaqueryrisp . "'$valorerisposte[$cont]'" . ',';
                    }
                }
                $stringaqueryrisp = $stringaqueryrisp . "'$domandaid'" . ',' . "'$id_postdata'" . ")";
                $statementr = $db->prepare($stringaqueryrisp);
                if (!$statementr->execute()) {
                    echo "Query risposta fallita! <br/>";
                    echo $stringaqueryrisp;
                } else {
                    echo "Query risposta eseguita! <br/>";
                }
                
                $contdom++;
                $contvaluesdom = 0;
            } 
            else {
                if ($key == "camp_singola_domanda_id") {
                    $domandaid = $arraydomande[$contdom][$key];
                    $valoredom[$contvaluesdom] = $arraydomande[$contdom][$key];
                    $stringaquerydom = $stringaquerydom . $key . ',';
                    $contvaluesdom++;
                }
                else if($key=="camp_singola_domanda_inizio_timestamp"){
                    $stringaquerydom = $stringaquerydom . $key . ',';
                    $timestamp=round(($arraydomande[$contdom][$key])/1000);
                    $date=date("Y-m-d H:i:s",$timestamp);
                    $valoredom[$contvaluesdom] = $date;
                    $contvaluesdom++;
                }
                else {
                    
                    $stringaquerydom = $stringaquerydom . $key . ',';
                    $valoredom[$contvaluesdom] = $arraydomande[$contdom][$key];
                    $contvaluesdom++;
                    
                }
            } //FINE ELSE 
        }
    } //FINE WHILE
}