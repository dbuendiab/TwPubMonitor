<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\Response;

class DBController extends AbstractController
{
    private $sql_last_tuit = '
        SELECT a.ID, a.FECHA, a.NUM, b.LOTE, b.TUIT
        FROM (
        SELECT a.ID, a.FECHA, a.NUM
        FROM tuits AS a
        ORDER BY a.FECHA DESC
        LIMIT 3) AS a
        LEFT JOIN libro AS b
        ON a.NUM = b.NUM
        ORDER BY a.NUM ASC;
    ';
    /*
        Nota 1:
        Máquina Cloud9 en Irlanda. Problemas horarios:
        En horario de verano: INTERVAL 2 HOUR (tanto en el campo AHORA como en el WHERE)
        En horario de invierno: INTERVAL 1 HOUR
        Nota 2:
        En la máquina Clouding las horas están sincronizadas, así que INTERVAL 0.
    */
    private $sql_last_lote_agenda = '
        SELECT fecha AS FECHA, lote AS LOTE, DATE_ADD(NOW(), INTERVAL 0 HOUR) AS AHORA
        FROM agenda
        WHERE fecha < DATE_ADD(NOW(), INTERVAL 0 HOUR)
        ORDER BY fecha DESC
        LIMIT 1;
    ';
    
    private $sql_agenda = '
        SELECT DATE_FORMAT(fecha, \'%Y-%m-%d\') AS FECHA,
               DATE_FORMAT(fecha, \'%H:%i\') AS HORA, 
               lote AS LOTE        
               FROM agenda       
               ORDER BY FECHA;
    ';
    
    private $sql_horas_distintas = '
        SELECT DISTINCT DATE_FORMAT(fecha, \'%H:%i\') AS HORA
        FROM agenda
        ORDER BY HORA;
    ';
    
    private $sql_agenda_ampliada = '
    SELECT a.lote AS LOTE, c.id AS ID
    FROM (
        SELECT MAX(a.num) AS num, b.lote 
        FROM tuits AS a 
        INNER JOIN libro AS b 
        ON a.num = b.num 
        GROUP BY b.lote) AS a 
    INNER JOIN tuits AS c 
    ON a.num = c.num;
    ';
    
    private $sql_agenda_ampliada_2 = '
    SELECT DATE_FORMAT(fecha, \'%Y-%m-%d\') AS FECHA,
           DATE_FORMAT(fecha, \'%H:%i\') AS HORA, 
           LOTE, 
           ID
    FROM agenda
    ORDER BY LOTE;
    ';
    
    /**
     * @Route("/db", name="d_b")
     */
    public function index(Connection $connection)
    {
        $tuits = $connection->fetchAll($this->sql_last_tuit);
        $previstos = $this->ultimo_lote_agenda();
        //debug_zval_dump($tuits);
        //debug_zval_dump($previstos);
        
        $sincronizado = (end($tuits)['LOTE'] == end($previstos)['LOTE']);
        //debug_zval_dump($sincronizado);
        //$lotes = serialize($previstos);

        return $this->render('db/index.html.twig', [
                                   'tuits' => $tuits, 
                                   'controller_name' => 'DBController',
                                   'ultimo_lote_agenda' => $previstos, 
                                   'sincronizado' => $sincronizado,
                                   //'lotes' => $lotes,
                             ]
                            );
    }    
    
    private function ultimo_lote_agenda()
    {
        $db_custom_url = getenv('DATABASE_URL');
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $db_custom_url ,);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $lote_ultimo = $conn->fetchAll($this->sql_last_lote_agenda);
        return $lote_ultimo;
    }
/*
    private function agenda_datos() {
        $db_custom_url = getenv('DATABASE_CUSTOM_URL');
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $db_custom_url ,);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $agenda = $conn->fetchAll($this->sql_agenda);
        return $agenda;
    }
*/
    private function agenda_datos() {
        $db_custom_url = getenv('DATABASE_URL');
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $db_custom_url ,);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $agenda = $conn->fetchAll($this->sql_agenda_ampliada_2);
        return $agenda;
    }


    private function horas_distintas() {
        $db_custom_url = getenv('DATABASE_URL');
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $db_custom_url ,);
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $horas = $conn->fetchAll($this->sql_horas_distintas);
        return $horas;
    }

      /**
     * @Route("/pruebadb", name="pruebadb")
     */
    public function prueba(Connection $connection)
    {
        $horas = $this->horas_distintas();
        $columnas = ['FECHA' => '&nbsp;'];
        // Poner las filas de la tabla en horizontal
        foreach($horas as $hora) {
            //print_r($hora);
            $columnas[$hora['HORA']] = '';
        }
        
        // Recuperar la tabla de tuits programados (FECHA, HORA, LOTE)
        $agenda = $this->agenda_datos();
        
        //print_r($agenda[0]['FECHA']);
        // Necesito el día de la semana para la presentación de datos 
        $dia_semana = date('w', strtotime($agenda[0]['FECHA']));
        
        $cuadrante = [];
        $dia_anterior = '';
        foreach($agenda as $tuit) {
            //print_r($tuit);
            $dia = $tuit['FECHA'];
            $hora = $tuit['HORA'];
            $lote = $tuit['LOTE'];
            $id = $tuit['ID'];
            
            // Cuando nuevo día, o primer registro, inicializar un array asociativo con la fecha
            if ($dia_anterior === '') {
                $dia_anterior = $dia;
                $fila = $columnas;
                $fila['FECHA'] = $dia;
                //print_r("INICIALIZACIÓN\n");
                //print_r($fila);
            } else {
                if ($dia_anterior != $dia) {
                    // Guardar este día en el cuadrante y pasar al siguiente
                    $cuadrante[] = $fila;
                    $fila = $columnas;
                    $fila['FECHA'] = $dia;
                    $dia_anterior = $dia;
                    //print_r("CAMBIO DE DÍA\n");
                    //print_r($cuadrante);
                    //print_r("\n");
                } else {
                    //
                }
            }
            
            // Para la hora, la usaremos en el array asociativo en combinación con el lote
            if ($id) {
                $fila[$hora] = "<a href=\"https://twitter.com/statuses/$id\" target=\"_blank\">$lote</a>";
            } else {
                $fila[$hora] = $lote;
            }
            //print_r("NUEVO VALOR DE HORA Y LOTE\n");
            //print_r($fila);
        }
        //print_r($cuadrante);
        if ($fila) {
            $cuadrante[] = $fila;
        }

        return $this->render('db/agenda.html.twig', [
                                   'agenda' => $cuadrante, 
                                   'dia_semana' =>$dia_semana,
                             ]
                            );

        
     }
}
