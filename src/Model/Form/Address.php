<?php

namespace Appacman\Model\Form;

class Address extends FormInput {

	/**
	 * var $latitude
	 */
	private $latitude = '';

	/**
	 * var $longitude
	 */
	private $longitude = '';
	
	public function __construct($info, $id, $table){
        parent::__construct($info, $id, $table);
        
        $position = $this->getPosition();
        if( $position ){
            $this->latitude = $position['latitude'];
            $this->longitude = $position['longitude'];
        }
    }

    protected function getInputHTML($langID = null){
        return $this->inputType('text', $langID) . '
            <div id="map-' . $this->fieldName . '" class="map"></div>
            <input type="hidden" name="latitude-' . $this->fieldName . '" value="'.$this->latitude.'" />
            <input type="hidden" name="longitude-' . $this->fieldName . '" value="'.$this->longitude.'" />
        ';
    }

    protected function hasError($langID = null){
        $postValue = $this->getPostValue($langID);
        $hasHiddenLocation = !empty($_POST['latitude-' . $this->fieldName]) && !empty($_POST['longitude-' . $this->fieldName]);
        $hasLocation = !empty($_POST['latitude']) && !empty($_POST['longitude']);
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }else if( !$hasHiddenLocation && !$hasLocation ){
            return gettext('Asegúrate de haber creado un marcador en el mapa con la dirección correcta.');
        }
        return false;
    }

    public function save($itemID, $langID = null){
    	$lat = $_POST['latitude-' . $this->fieldName];
    	$lng = $_POST['longitude-' . $this->fieldName];
    	
    	if( $lat && $lng ){
			if( $lat != $this->latitude || $lng != $this->longitude ){
				$sql = '
					UPDATE '.$this->table.'
					SET latitude = :latitude, longitude = :longitude
					WHERE id_'.$this->table.' = :id
				';
				$params = array(
					'id'        => array('value' => $itemID,	'type' => \PDO::PARAM_INT),
					'latitude'  => array('value' => $lat,     	'type' => \PDO::PARAM_STR),
					'longitude' => array('value' => $lng,    	'type' => \PDO::PARAM_STR)
				);
				$this->mysql->query($sql, $params);
				if( $this->mysql->getState() ){
					return false;
				}
				return true;
			}
		}
    	return false;
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