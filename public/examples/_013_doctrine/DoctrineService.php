<?php
require_once '../../../Doctrine.php';

class DoctrineService {

	public $doctrine;

	function __construct(){
		$this->doctrine = new Doctrine();
	}

	function cities() {
		$cities = $this->doctrine->em->getRepository('YourCitiesEntity')->findAll();
		
		$returned = array();

		foreach($cities as $city){
			$returned[] = $city->getName();
		}

		return $returned;
	}
}