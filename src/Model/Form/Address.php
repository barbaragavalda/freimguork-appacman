<?php

namespace Appacman\Model\Form;

class Address extends FormInput {

    protected function getInputHTML($langID = null){
        $latitude = $longitude = '';
        $position = $this->getPosition();
        if( $position ){
            $latitude = $position['latitude'];
            $longitude = $position['longitude'];
        }

        return $this->inputType('text', $langID) . '
            <div id="map-' . $this->fieldName . '" class="map"></div>
            <input type="hidden" name="latitude-' . $this->fieldName . '" value="'.$latitude.'" />
            <input type="hidden" name="longitude-' . $this->fieldName . '" value="'.$longitude.'" />
        ';
    }

    protected function hasError($langID = null){
        $postValue = $this->getPostValue($langID);
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }else if( empty($_POST['latitude-' . $this->fieldName]) || empty($_POST['longitude-' . $this->fieldName]) ){
            return gettext('Asegúrate de haber creado un marcador en el mapa con la dirección correcta.');
        }
        return false;
    }

    public function save($itemID, $langID = null){
        $sql = '
            UPDATE '.$this->table.'
            SET latitude = :latitude, longitude = :longitude
            WHERE id_'.$this->table.' = :id
        ';
        $params = array(
            'id'        => array('value' => $itemID,                                    'type' => \PDO::PARAM_INT),
            'latitude'  => array('value' => $_POST['latitude-' . $this->fieldName],     'type' => \PDO::PARAM_STR),
            'longitude' => array('value' => $_POST['longitude-' . $this->fieldName],    'type' => \PDO::PARAM_STR)
        );
        $this->mysql->query($sql, $params);
        if( $this->mysql->rowCount() == 1 ){
            return false;
        }
        return true;
    }

    private function getPosition(){
        $sql = '
            SELECT latitude, longitude
            FROM '.$this->table.'
            WHERE id_'.$this->table.' = :id
        ';
        $params = array(
            'id' => array('value' => $this->id, 'type' => \PDO::PARAM_INT)
        );
        $position = $this->mysql->query($sql, $params);

        if( count($position) ){
            return $position[0];
        }
        return false;
    }

}