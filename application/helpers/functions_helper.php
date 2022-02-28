<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Evaluar
 */
function getDateNow($text) {
    return date($text);
}

/**
 * Fecha por defecto -1 un día
 */
function getDateDefault($text) {
    return date($text, strtotime('-1 day'));
}

function getDatePrevious($text, $num) {
    return date($text, strtotime('-'.$num.' day'));
}


/**
 * Evalua Sesión activa
 */
function checkSession() {
    $return = false;
    if(isset($_SESSION['user_id'])) {
        $return = true;
    }
    return $return;
}

function appName() {
    return 'Opensoft Cloud Companion';
}

/**
 * Formato de fecha para centralizador
 */
function formatDateCentralizer($str,$type) {
    $pre = '';
    if ($type == 1) {
        $sep = '/';
    } else if ($type == 2) {
        $sep = '-';
    } else if ($type == 3) {
        $sep = '/';
        $pre = '-';
    }
    return implode($pre, array_reverse(explode($sep, $str)));
}

/**
 * Convertir unidad de medida
 * @param type Int 0: ltr. a gal.; 1: M3 a gal.
 * @param co float
 * @return float
 */
function converterUM($data) {
    if($data['type'] == 0) {
        // return $data['co'] / 3.7853;//11620307 - GLP
        return $data['co'] / 1;//11620307 - GLP
    } else if($data['type'] == 1) {
        return $data['co'] / 3.15;//11620308 - GNV
    } else {
        return $data['co'];
    }
}

function getUncompressData($url) {
    $old = ini_set('default_socket_timeout', 40);//considerar menos(inicial 120)
    //$old = ini_set('default_socket_timeout', 5);
    $fh = fopen($url, 'rb');
    if ($fh === FALSE) {
        log_message('Error', 'Error al conectarse a '.$url);
        return FALSE;
    } else {
        log_message('Error', '$fh '.$fh);
    }
    $res = '';
    while (!feof($fh)) {
        $res .= fread($fh, 8192);
    }
    fclose($fh);
    
    // error_log("Original");
    // error_log($res);
    
    $descomprimido = gzuncompress($res);
    
    // error_log("Descomprimido");
    // error_log($descomprimido);
    
    if($descomprimido === false){
        return explode("\n", $res);
    }
    return explode("\n", $descomprimido);
}

function array_sort($array, $on, $order = SORT_ASC) {
    $new_array = array();
    $sortable_array = array();

    if(count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
    return $new_array;
}

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

function subval_sort($a,$subkey,$or) {
    foreach($a as $k=>$v) {
        $b[$k] = strtolower($v[$subkey]);
    }
    if($or == 'ASC') {
        asort($b);
    } else {
        arsort($b);
    }
    
    foreach($b as $key=>$val) {
        $c[] = $a[$key];
    }
    return $c;
}

function getMemory($params) {
    return array(
        'message' => $params[0],
        'memory' => memory_get_usage()
    );
}

function getDescriptionTypeStation($ts, $isReverse = false) {
    $result = '';
    if(!$isReverse) {
        if($ts == 0 || $ts == 3 || $ts == 4 || $ts == 6 || $ts == 7 || $ts == 8) {
            $result = 'C';
        } else if($ts == 1 || $ts == 5) {
            $result = 'M';
        } else if($ts == 2) {
            $result = 'MP';
        }
    } else {
        if($ts == 'C') {
            $result = 0;
        } else if($ts == 'M') {
            $result = 1;
        } else if($ts == 'MP') {
            $result = 2;
        }
    }
    return $result;
}

function amountPercentage($data) {
    if($data['num1'] == 0 && $data['num2'] == 0) {
        return 0;
    } else {
        return ((($data['num1']*100)/$data['num2']) - 100);
    }
}

function var_log($data, $ispre = true) {
    if($ispre) {
        echo '<hr><pre>';
        var_dump($data);
        echo '</pre><hr><br>';
    } else {
        echo '<hr>';
        var_dump($data);
        echo '<hr><br>';
    }
}