<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */

namespace qtism\data\storage\xml\marshalling;

use qtism\data\QtiComponent;
use qtism\data\state\CorrectResponse;
use qtism\data\state\Value;
use qtism\data\state\ValueCollection;
use qtism\common\enums\BaseType;
use \DOMElement;
use \InvalidArgumentException;

/**
 * Marshalling/Unmarshalling implementation for correctResponse.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class CorrectResponseMarshaller extends Marshaller
{
    /**
     * The baseType of values.
     *
     * @var integer
     */
    private $baseType = -1;

    public function setBaseType($baseType = -1)
    {
        if (in_array($baseType, BaseType::asArray()) || $baseType == -1) {
            $this->baseType = $baseType;
        } else {
            $msg = "The baseType argument must be a value from the BaseType enumeration.";
            throw new InvalidArgumentException($msg);
        }
    }

    /**
	 * Get the base type of inner values.
	 *
	 * @return integer
	 */
    public function getBaseType()
    {
        return $this->baseType;
    }

    /**
	 * Create a new CorrectResponseMarshaller object.
	 *
	 * @param string $version The QTI version number on which the Marshaller operates e.g. '2.1'.
	 * @param integer $baseType The base type of the Variable referencing this CorrectResponse.
	 */
    public function __construct($version, $baseType = -1)
    {
        parent::__construct($version);
        $this->setBaseType($baseType);
    }

    /**
	 * Marshall a CorrectResponse object into a DOMElement object.
	 *
	 * @param \qtism\data\QtiComponent $component A CorrectResponse object.
	 * @return \DOMElement The according DOMElement object.
	 */
    protected function marshall(QtiComponent $component)
    {
        $element = static::getDOMCradle()->createElement($component->getQtiClassName());

        $interpretation = $component->getInterpretation();
        if (!empty($interpretation)) {
            self::setDOMElementAttribute($element, 'interpretation', $interpretation);
        }

        // A CorrectResponse contains 1..* Value objects
        foreach ($component->getValues() as $value) {
            $valueMarshaller = $this->getMarshallerFactory()->createMarshaller($value, array($this->getBaseType()));
            $valueElement = $valueMarshaller->marshall($value);
            $element->appendChild($valueElement);
        }

        return $element;
    }

    /**
	 * Unmarshall a DOMElement object corresponding to a QTI correctResponse element.
	 *
	 * @param \DOMElement $element A DOMElement object.
	 * @return \qtism\data\QtiComponent A CorrectResponse object.
	 * @throws \qtism\data\storage\xml\marshalling\UnmarshallingException If the DOMElement object cannot be unmarshalled in a valid CorrectResponse object.
	 */
    protected function unmarshall(DOMElement $element)
    {
        $interpretation = static::getDOMElementAttributeAs($element, 'interpretation', 'string');
        $interpretation = (empty($interpretation)) ? '' : $interpretation;

        // Retrieve the values ...
        $values = new ValueCollection();
        $valueElements = $element->getElementsByTagName('value');

        if ($valueElements->length > 0) {
            for ($i = 0; $i < $valueElements->length; $i++) {
                $valueMarshaller = $this->getMarshallerFactory()->createMarshaller($valueElements->item($i), array($this->getBaseType()));
                $values[] = $valueMarshaller->unmarshall($valueElements->item($i));
            }

            $object = new CorrectResponse($values, $interpretation);

            return $object;
        } else {
            $msg = "A 'correctResponse' QTI element must contain at least one 'value' QTI element.";
            throw new UnmarshallingException($msg, $element);
        }

    }

    /**
	 * @see \qtism\data\storage\xml\marshalling\Marshaller::getExpectedQtiClassName()
	 */
    public function getExpectedQtiClassName()
    {
        return 'correctResponse';
    }
}
