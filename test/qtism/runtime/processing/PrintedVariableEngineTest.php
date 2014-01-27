<?php
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\MultipleContainer;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Duration;
use qtism\runtime\common\TemplateVariable;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\processing\PrintedVariableEngine;
use qtism\data\content\PrintedVariable;
use qtism\runtime\common\State;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class PrintedVariableEngineTest extends QtiSmTestCase {
	
    /**
     * @param mixed $value
     * @param string $expected
     * @param string $format
     * @param boolean $powerForm
     * @param integer|string $base
     * @param $integer|string $index
     * @param string $delimiter
     * @param string $field
     * @param stribng $mappingIndicator
     * @dataProvider printedVariableProvider
     */
    public function testPrintedVariable($expected, $identifier, State $state, $format = '', $powerForm = false, $base = 10, $index = -1, $delimiter = ';', $field = '', $mappingIndicator = '=') {
        
        $printedVariable = new PrintedVariable($identifier);
        $printedVariable->setFormat($format);
        $printedVariable->setPowerForm($powerForm);
        $printedVariable->setBase($base);
        $printedVariable->setIndex($index);
        $printedVariable->setDelimiter($delimiter);
        $printedVariable->setField($field);
        $printedVariable->setMappingIndicator($mappingIndicator);
        
        $engine = new PrintedVariableEngine($printedVariable);
        $engine->setContext($state);
        $this->assertEquals($expected, $engine->process());
    }
    
    public function printedVariableProvider() {
        $state = new State();
        
        $state->setVariable(new OutcomeVariable('nullValue', Cardinality::SINGLE, BaseType::BOOLEAN, null));
        
        $state->setVariable(new OutcomeVariable('nonEmptyString', Cardinality::SINGLE, BaseType::STRING, 'Non Empty String'));
        $state->setVariable(new OutcomeVariable('emptyString', Cardinality::SINGLE, BaseType::STRING, ''));
        $state->setVariable(new TemplateVariable('positiveInteger', Cardinality::SINGLE, BaseType::INTEGER, 25));
        $state->setVariable(new TemplateVariable('zeroInteger', Cardinality::SINGLE, BaseType::INTEGER, 0));
        $state->setVariable(new TemplateVariable('negativeInteger', Cardinality::SINGLE, BaseType::INTEGER, -25));
        $state->setVariable(new TemplateVariable('positiveFloat', Cardinality::SINGLE, BaseType::FLOAT, 25.3455322345));
        $state->setVariable(new OutcomeVariable('zeroFloat', Cardinality::SINGLE, BaseType::FLOAT, 0.0));
        $state->setVariable(new OutcomeVariable('negativeFloat', Cardinality::SINGLE, BaseType::FLOAT, -53000.0));
        $state->setVariable(new OutcomeVariable('false', Cardinality::SINGLE, BaseType::BOOLEAN, false));
        $state->setVariable(new OutcomeVariable('true', Cardinality::SINGLE, BaseType::BOOLEAN, true));
        $state->setVariable(new OutcomeVariable('URI', Cardinality::SINGLE, BaseType::URI, 'http://qtism.taotesting.com'));
        $state->setVariable(new TemplateVariable('zeroIntOrIdentifier', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, 0));
        $state->setVariable(new TemplateVariable('positiveIntOrIdentifier', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, 25));
        $state->setVariable(new TemplateVariable('zeroIntOrIdentifier', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, 0));
        $state->setVariable(new OutcomeVariable('identifierIntOrIdentifier', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, 'woot'));
        $state->setVariable(new TemplateVariable('negativeIntOrIdentifier', Cardinality::SINGLE, BaseType::INTEGER, -25));
        $state->setVariable(new OutcomeVariable('duration', Cardinality::SINGLE, BaseType::DURATION, new Duration('PT3M26S')));
        $state->setVariable(new OutcomeVariable('pair', Cardinality::SINGLE, BaseType::PAIR, new Pair('A', 'B')));
        $state->setVariable(new OutcomeVariable('directedPair', Cardinality::SINGLE, BaseType::DIRECTED_PAIR, new DirectedPair('B', 'C')));
        $state->setVariable(new OutcomeVariable('identifier', Cardinality::SINGLE, BaseType::IDENTIFIER, 'woot'));
        
        $state->setVariable(new TemplateVariable('multipleInteger', Cardinality::MULTIPLE, BaseType::INTEGER, new MultipleContainer(BaseType::INTEGER, array(10, 20, -1))));
        $state->setVariable(new OutcomeVariable('multipleFloat', Cardinality::MULTIPLE, BaseType::FLOAT, new MultipleContainer(BaseType::FLOAT, array(10.0, 20.0, -1.0))));
        $state->setVariable(new OutcomeVariable('multipleString', Cardinality::MULTIPLE, BaseType::STRING, new MultipleContainer(BaseType::STRING, array('Ta', 'Daaa', 'h', ''))));
        $state->setVariable(new OutcomeVariable('multipleBoolean', Cardinality::MULTIPLE, BaseType::BOOLEAN, new MultipleContainer(BaseType::BOOLEAN, array(true, false, true, true))));
        $state->setVariable(new OutcomeVariable('multipleURI', Cardinality::MULTIPLE, BaseType::URI, new MultipleContainer(BaseType::URI, array('http://www.taotesting.com', 'http://www.rdfabout.com'))));
        $state->setVariable(new OutcomeVariable('multipleIdentifier', Cardinality::MULTIPLE, BaseType::IDENTIFIER, new MultipleContainer(BaseType::IDENTIFIER, array('9thing', 'woot-woot'))));
        $state->setVariable(new TemplateVariable('multipleDuration', Cardinality::MULTIPLE, BaseType::DURATION, new MultipleContainer(BaseType::DURATION, array(new Duration('PT0S'), new Duration('PT3M')))));
        $state->setVariable(new OutcomeVariable('multiplePair', Cardinality::MULTIPLE, BaseType::PAIR, new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'B'), new Pair('C', 'D'), new Pair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('multipleDirectedPair', Cardinality::MULTIPLE, BaseType::DIRECTED_PAIR, new MultipleContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('C', 'D'), new DirectedPair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('multipleIntOrIdentifier', Cardinality::MULTIPLE, BaseType::INT_OR_IDENTIFIER, new MultipleContainer(BaseType::INT_OR_IDENTIFIER, array('woot', 25, 0, -25))));
        
        // -- Ordered containers, no value for the 'index' attribute.
        $state->setVariable(new TemplateVariable('orderedInteger', Cardinality::MULTIPLE, BaseType::INTEGER, new MultipleContainer(BaseType::INTEGER, array(10, 20, -1))));
        $state->setVariable(new OutcomeVariable('orderedFloat', Cardinality::MULTIPLE, BaseType::FLOAT, new MultipleContainer(BaseType::FLOAT, array(10.0, 20.0, -1.0))));
        $state->setVariable(new OutcomeVariable('orderedString', Cardinality::MULTIPLE, BaseType::STRING, new MultipleContainer(BaseType::STRING, array('Ta', 'Daaa', 'h', ''))));
        $state->setVariable(new OutcomeVariable('orderedBoolean', Cardinality::MULTIPLE, BaseType::BOOLEAN, new MultipleContainer(BaseType::BOOLEAN, array(true, false, true, true))));
        $state->setVariable(new OutcomeVariable('orderedURI', Cardinality::MULTIPLE, BaseType::URI, new MultipleContainer(BaseType::URI, array('http://www.taotesting.com', 'http://www.rdfabout.com'))));
        $state->setVariable(new OutcomeVariable('orderedIdentifier', Cardinality::MULTIPLE, BaseType::IDENTIFIER, new MultipleContainer(BaseType::IDENTIFIER, array('9thing', 'woot-woot'))));
        $state->setVariable(new TemplateVariable('orderedDuration', Cardinality::MULTIPLE, BaseType::DURATION, new MultipleContainer(BaseType::DURATION, array(new Duration('PT0S'), new Duration('PT3M')))));
        $state->setVariable(new OutcomeVariable('orderedPair', Cardinality::MULTIPLE, BaseType::PAIR, new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'B'), new Pair('C', 'D'), new Pair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('orderedDirectedPair', Cardinality::MULTIPLE, BaseType::DIRECTED_PAIR, new MultipleContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('C', 'D'), new DirectedPair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('orderedIntOrIdentifier', Cardinality::MULTIPLE, BaseType::INT_OR_IDENTIFIER, new MultipleContainer(BaseType::INT_OR_IDENTIFIER, array('woot', 25, 0, -25))));
        
        // -- Ordered containers, value for the 'index' attribute set.
        $state->setVariable(new TemplateVariable('orderedIndexedInteger', Cardinality::ORDERED, BaseType::INTEGER, new OrderedContainer(BaseType::INTEGER, array(10, 20, -1))));
        // The field is extracted from a variable ref.
        $state->setVariable(new OutcomeVariable('fieldVariableRefInteger', Cardinality::SINGLE, BaseType::INTEGER, 1));
        // Set up a wrong variable reference for 'index' value.
        $state->setVariable(new OutcomeVariable('fieldVariableRefString', Cardinality::SINGLE, BaseType::STRING, 'index'));
        $state->setVariable(new OutcomeVariable('orderedIndexedFloat', Cardinality::ORDERED, BaseType::FLOAT, new OrderedContainer(BaseType::FLOAT, array(10.0, 20.0, -1.0))));
        $state->setVariable(new OutcomeVariable('orderedIndexedString', Cardinality::ORDERED, BaseType::STRING, new OrderedContainer(BaseType::STRING, array('Ta', 'Daaa', 'h', ''))));
        $state->setVariable(new OutcomeVariable('orderedIndexedBoolean', Cardinality::ORDERED, BaseType::BOOLEAN, new OrderedContainer(BaseType::BOOLEAN, array(true, false, true, true))));
        $state->setVariable(new OutcomeVariable('orderedIndexedURI', Cardinality::ORDERED, BaseType::URI, new OrderedContainer(BaseType::URI, array('http://www.taotesting.com', 'http://www.rdfabout.com'))));
        $state->setVariable(new OutcomeVariable('orderedIndexedIdentifier', Cardinality::ORDERED, BaseType::IDENTIFIER, new OrderedContainer(BaseType::IDENTIFIER, array('9thing', 'woot-woot'))));
        $state->setVariable(new TemplateVariable('orderedIndexedDuration', Cardinality::ORDERED, BaseType::DURATION, new OrderedContainer(BaseType::DURATION, array(new Duration('PT0S'), new Duration('PT3M')))));
        $state->setVariable(new OutcomeVariable('orderedIndexedPair', Cardinality::ORDERED, BaseType::PAIR, new OrderedContainer(BaseType::PAIR, array(new Pair('A', 'B'), new Pair('C', 'D'), new Pair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('orderedIndexedDirectedPair', Cardinality::ORDERED, BaseType::DIRECTED_PAIR, new OrderedContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('C', 'D'), new DirectedPair('E', 'F')))));
        $state->setVariable(new OutcomeVariable('orderedIndexedIntOrIdentifier', Cardinality::ORDERED, BaseType::INT_OR_IDENTIFIER, new OrderedContainer(BaseType::INT_OR_IDENTIFIER, array('woot', 25, 0, -25))));
        
        return array(
            array('', 'nonExistingVariable', $state),
            array('', 'nullValue', $state),            
                        
            array('Non Empty String', 'nonEmptyString', $state),
            array('', 'emptyString', $state),
            array('25', 'positiveInteger', $state),
            array('0', 'zeroInteger', $state),
            array('-25', 'negativeInteger', $state),
            array('2.534553e+1', 'positiveFloat', $state),
            array('0.000000e+0', 'zeroFloat', $state),
            array('-5.300000e+4', 'negativeFloat', $state),
            array('false', 'false', $state),
            array('true', 'true', $state),
            array('http://qtism.taotesting.com', 'URI', $state),
            array('25', 'positiveIntOrIdentifier', $state),
            array('0', 'zeroIntOrIdentifier', $state),
            array('-25', 'negativeIntOrIdentifier', $state),
            array('woot', 'identifierIntOrIdentifier', $state),
            array('206', 'duration', $state),
            array('A B', 'pair', $state),
            array('B C', 'directedPair', $state),
            array('woot', 'identifier', $state),
                        
            array('10;20;-1', 'multipleInteger', $state),
            array('1.000000e+1;2.000000e+1;-1.000000e+0', 'multipleFloat', $state),
            array('Ta;Daaa;h;', 'multipleString', $state),
            array('true;false;true;true', 'multipleBoolean', $state),
            array('http://www.taotesting.com;http://www.rdfabout.com', 'multipleURI', $state),
            array('9thing;woot-woot', 'multipleIdentifier', $state),
            array('0;180', 'multipleDuration', $state),
            array('A B;C D;E F', 'multiplePair', $state),
            array('woot;25;0;-25', 'multipleIntOrIdentifier', $state),
                        
            array('10;20;-1', 'multipleInteger', $state),
            array('1.000000e+1;2.000000e+1;-1.000000e+0', 'multipleFloat', $state),
            array('Ta;Daaa;h;', 'multipleString', $state),
            array('true;false;true;true', 'multipleBoolean', $state),
            array('http://www.taotesting.com;http://www.rdfabout.com', 'multipleURI', $state),
            array('9thing;woot-woot', 'multipleIdentifier', $state),
            array('0;180', 'multipleDuration', $state),
            array('A B;C D;E F', 'multiplePair', $state),
            array('woot;25;0;-25', 'multipleIntOrIdentifier', $state),
                        
            array('10', 'orderedIndexedInteger', $state, '', false, 10, 0),
            array('20', 'orderedIndexedInteger', $state, '', false, 10, 1),
            array('-1', 'orderedIndexedInteger', $state, '', false, 10, 2),
                        
            // The index does not exist, the full container content is displayed.
            array('10;20;-1', 'orderedIndexedInteger', $state, '', false, 10, 3),
            // The index is a valid variable reference.
            array('20', 'orderedIndexedInteger', $state, '', false, 10, 'fieldVariableRefInteger'),
            // The index is an unresolvable variable reference, the full container content is displayed.
            array('10;20;-1', 'orderedIndexedInteger', $state, '', false, 10, 'XRef'),
            // The index value is not a string, the full container content is displayed.
            array('10;20;-1', 'orderedIndexedInteger', $state, '', false, 10, 'fieldVariableRefString'),
                 
            array('1.000000e+1', 'orderedIndexedFloat', $state, '', false, 10, 0),
            array('2.000000e+1', 'orderedIndexedFloat', $state, '', false, 10, 1),
            array('-1.000000e+0', 'orderedIndexedFloat', $state, '', false, 10, 2), 
            array('Ta', 'orderedIndexedString', $state, '', false, 10, 0),
            array('Daaa', 'orderedIndexedString', $state, '', false, 10, 1),
            array('h', 'orderedIndexedString', $state, '', false, 10, 2),
            array('', 'orderedIndexedString', $state, '', false, 10, 3),
            array('true', 'orderedIndexedBoolean', $state, '', false, 10, 0),
            array('false', 'orderedIndexedBoolean', $state, '', false, 10, 1),
            array('true', 'orderedIndexedBoolean', $state, '', false, 10, 2),
            array('true', 'orderedIndexedBoolean', $state, '', false, 10, 3),
            array('http://www.taotesting.com', 'orderedIndexedURI', $state, '', false, 10, 0),
            array('http://www.rdfabout.com', 'orderedIndexedURI', $state, '', false, 10, 1),
            array('9thing', 'orderedIndexedIdentifier', $state, '', false, 10, 0),
            array('woot-woot', 'orderedIndexedIdentifier', $state, '', false, 10, 1),
            array('0', 'orderedIndexedDuration', $state, '', false, 10, 0),
            array('180', 'orderedIndexedDuration', $state, '', false, 10, 1),
            array('A B', 'orderedIndexedPair', $state, '', false, 10, 0),
            array('C D', 'orderedIndexedPair', $state, '', false, 10, 1),
            array('E F', 'orderedIndexedPair', $state, '', false, 10, 2),
            array('A B', 'orderedIndexedDirectedPair', $state, '', false, 10, 0),
            array('C D', 'orderedIndexedDirectedPair', $state, '', false, 10, 1),
            array('E F', 'orderedIndexedDirectedPair', $state, '', false, 10, 2),
            array('woot', 'orderedIndexedIntOrIdentifier', $state, '', false, 10, 0),
            array('25', 'orderedIndexedIntOrIdentifier', $state, '', false, 10, 1),
            array('0', 'orderedIndexedIntOrIdentifier', $state, '', false, 10, 2),
            array('-25', 'orderedIndexedIntOrIdentifier', $state, '', false, 10, 3),
        );
    }
}