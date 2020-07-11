<?php
$host = 'localhost';
$username = '';
$password = '';
$db_name = '';
$store_id = 0;

$mysqli = new mysqli($host, $username, $password, $db_name);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$res = $mysqli->query('SELECT DISTINCT entity_id FROM catalog_product_entity_varchar');
while ( $row = $res->fetch_assoc() ) {
    echo '<p>'.$row['entity_id'].': ';
    $attribute_ids = [];

    $product_info = $mysqli->query('SELECT * FROM catalog_product_entity_varchar WHERE store_id = '.$store_id.' AND entity_id = '. $row['entity_id']);

    while ( $info_row = $product_info->fetch_assoc() ){
        echo $info_row['attribute_id']. '-';
        if( !isset($attribute_ids[$info_row['attribute_id']]) ) {
            $attribute_ids[$info_row['attribute_id']] = [
                    'attribute_id' => (int) $info_row['attribute_id'],
                    'value_id' => (int) $info_row['value_id']
                ];
            echo '.';
        }else{
            if( (int) $info_row['value_id'] > (int) $attribute_ids[$info_row['attribute_id']]['value_id']){
                $old_value_id = (int) $attribute_ids[$info_row['attribute_id']]['value_id'];
                $attribute_ids[$info_row['attribute_id']]['value_id'] = (int) $info_row['value_id'];
                $mysqli->query('DELETE FROM catalog_product_entity_varchar WHERE value_id = '.$old_value_id);
                echo '=';
            }else{
                $mysqli->query('DELETE FROM catalog_product_entity_varchar WHERE value_id = '.$info_row['value_id']);
                echo '-'.$info_row['attribute_id'].'-';
            }

        }

    }

    echo '</p>';

}
