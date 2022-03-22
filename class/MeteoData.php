<?php
class MeteoData
{
    public function getMeteoData($url_fluxmeteo)
    {
        $handle = fopen($url_fluxmeteo,'r');

        $urlData = file($url_fluxmeteo);

        
        $meteoData = $this->cleanMeteoData($urlData);

        return $meteoData;

    }

    public function cleanMeteoData($urlData)
    {
        $i=0;
        foreach ($urlData as $data)
        {
            
            $data=substr($data,0, -6);
            $data=trim($data);
            if(stripos($data, '<')!==0 AND ($data!==""))
            {
                $meteoLign = explode(';',$data);
                
                $meteoLign["date"] = $meteoLign[0];
                $meteoLign["ville"] = $meteoLign[1];
                $meteoLign["période"] = $meteoLign[2];
                $meteoLign["résumé"] = $meteoLign[3];
                $meteoLign["id_résumé"] = $meteoLign[4];
                $meteoLign["mintemp"] = $meteoLign[5];
                $meteoLign["maxtemp"] = $meteoLign[6];
                $meteoLign["commentaire"] = $meteoLign[7];
                unset($meteoLign[0]);
                unset($meteoLign[1]);
                unset($meteoLign[2]);
                unset($meteoLign[3]);
                unset($meteoLign[4]);
                unset($meteoLign[5]);
                unset($meteoLign[6]);
                unset($meteoLign[7]);
                unset($meteoLign[8]);

                $meteoDatas[$i]= $meteoLign;
                $i++;              
            }     
        }
        return($meteoDatas);
    }

    public function saveMeteoData($meteoDatas)
    {
        $mysqli = new mysqli('localhost', 'root', '', 'projet_meteo');
        $mysqli->set_charset("utf8");
        if($mysqli->connect_errno) 
        {
            echo 'Echec de la connexion' .$mysqli->connect_error;
            exit();
        }
        $i=0;
        foreach($meteoDatas as $meteoData)
        {
            $i++;
            if (!$mysqli->query('INSERT IGNORE INTO meteo_data (id, date, ville, période, résumé, id_résumé, mintemp, maxtemp, commentaire) VALUES ("'.$i.'","'.$meteoData['date'].'","'.$meteoData['ville'].'", "'.$meteoData['période'].'", "'.$meteoData['résumé'].'", "'.$meteoData['id_résumé'].'", "'.$meteoData['mintemp'].'", "'.$meteoData['maxtemp'].'", "'.$meteoData['commentaire'].'")'))
            {
                $msg_error = 'Une erreur est survenue lors de la mise à jour des données dans la base. <br> Message d\'erreur : ' . $mysqli->error;
                $msg_error .= '<br> Aucune information n\'a été enregistrée.';
                echo $msg_error;
            }
            else
            {
                return true;
            }
        }
    }

    public function getj1j2Meteo($today)
    {
   
    $j1 = date("Y-m-d", strtotime("+1 day", strtotime($today)));
    $j2=date("Y-m-d", strtotime("+2 day", strtotime($today)));
    $mysqli = new mysqli('localhost', 'root', '', 'projet_meteo');
    $mysqli->set_charset("utf8");

    if($mysqli->connect_errno) 
    {
        echo 'Echec de la connexion' .$mysqli->connect_error;
        exit();
    }
    
    $result = $mysqli->query('SELECT date, ville, période, résumé, id_résumé, mintemp, maxtemp, commentaire FROM meteo_data WHERE date = "'. $j1 .'" OR date ="'.$j2.'"');
    if (!$result)
    {
        echo 'Une erreur est survenue lors de la récupération des données dans la base. Message d\'erreur : ' . $mysqli->error;
        return false;
    }
    else
    {
        
         while ($row = $result->fetch_array())
        {
            $j1j2_meteoData['date'] = $row['date'];
            $j1j2_meteoData['ville'] = $row['ville'];
            $j1j2_meteoData['période'] = $row['période'];
            $j1j2_meteoData['résumé'] = $row['résumé'];
            $j1j2_meteoDataa['id_résumé'] = $row['id_résumé'];
            $j1j2_meteoData['mintemp'] = $row['mintemp'];
            $j1j2_meteoData['maxtemp'] = $row['maxtemp'];
            $j1j2_meteoData['commentaire'] = $row['commentaire'];
            $j1j2_meteoDatas[] = implode($j1j2_meteoData, ";");
        }
        return  $j1j2_meteoDatas;

    }
    $mysqli->close;
}
}
