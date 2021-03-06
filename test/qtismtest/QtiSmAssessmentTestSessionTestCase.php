<?php
namespace qtismtest;

use qtismtest\QtiSmTestCase;
use qtism\runtime\tests\SessionManager;
use qtism\data\storage\xml\XmlCompactDocument;

abstract class QtiSmAssessmentTestSessionTestCase extends QtiSmTestCase {
    
	public function setUp() {
	    parent::setUp();
	}
	
	public function tearDown() {
	    parent::tearDown();
	}
	
	protected static function instantiate($url) {
	    $doc = new XmlCompactDocument();
	    $doc->load($url);
	     
	    $manager = new SessionManager();
	    return $manager->createAssessmentTestSession($doc->getDocumentComponent());
	}
}