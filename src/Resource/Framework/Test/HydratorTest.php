<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat_<Package>
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace ApparatTest;

use Apparat\Resource\Domain\Factory\HydratorFactory;
use Apparat\Resource\Domain\Model\Hydrator\HydratorInterface;
use Apparat\Resource\Domain\Model\Hydrator\InvalidArgumentException;
use Apparat\Resource\Domain\Model\Hydrator\RuntimeException;
use Apparat\Resource\Domain\Model\Part\AbstractPartAggregate;
use Apparat\Resource\Domain\Model\Part\OutOfBoundsException;
use Apparat\Resource\Domain\Model\Part\PartAggregateInterface;
use Apparat\Resource\Framework\Hydrator\TextHydrator;
use Apparat\Resource\Framework\Part\TextPart;


/**
 * Mock methods for multipart hydrators
 *
 * @package ApparatTest
 */
trait AggregateHydratorMocks
{
	/**
	 * Translate data to a file part
	 *
	 * @param string $data Part data
	 * @return PartAggregateInterface Resource part
	 */
	public function hydrate($data)
	{
		if (!empty($GLOBALS['mockAggregateClass'])) {
			$this->_aggregateClass = self::class;
		}

		/** @var AbstractPartAggregate $aggregate */
		$aggregate = parent::hydrate(null);
		foreach (explode('|', $data) as $part => $str) {
			if (!empty($GLOBALS['mockOccurrenceNumber'])) {
				$aggregate->assign(0, $str, $part);
			} elseif (!empty($GLOBALS['mockAssignmentPartIdentifier'])) {
				$aggregate->assign("_$part", $str, 0);
			} else {
				$aggregate->assign("$part", $str);
			}
		}
		return $aggregate;
	}

	/**
	 * Dehydrate a single occurrence
	 *
	 * @param array $occurrence Occurrence
	 * @return string Dehydrated occurrence
	 */
	protected function _dehydrateOccurrence(array $occurrence)
	{

		// If the default validation should be used
		if (empty($GLOBALS['mockOccurrenceDehydration'])) {

			// If an empty occurrence shall be tested
			if (!empty($GLOBALS['mockEmptyOccurrence'])) {
				return parent::_dehydrateOccurrence([]);

				// If an invalid subhydrator name should be tested
			} elseif (!empty($GLOBALS['mockSubhydratorName'])) {
				return parent::_dehydrateOccurrence(array_combine(array_map(function ($name) {
					return '_'.$name.'_';
				}, array_keys($occurrence)),
					array_values($occurrence)));

				// If an invalid part instance should be tested
			} elseif (!empty($GLOBALS['mockPartInstance'])) {
				return parent::_dehydrateOccurrence(array_fill_keys(array_keys($occurrence), null));

				// Else: Regular processing
			} else {
				return parent::_dehydrateOccurrence($occurrence);
			}
			// Else return a mock result
		} else {
			return [];
		}
	}

	/**
	 * Validate the parameters accepted by this hydrator
	 *
	 * By default, a multipart parameter accepts exactly two parameters:
	 * - the minimum number of occurrences of the contained part aggregate
	 * - the maximum number of occurrences of the contained part aggregate
	 *
	 * @param array $parameters Parameters
	 * @return boolean Parameters are valid
	 */
	static function validateParameters(...$parameters)
	{

		// If the default validation should be used
		if (empty($GLOBALS['mockValidateParameters'])) {
			return parent::validateParameters(...$parameters);

			// Else return a mock result
		} else {
			return false;
		}
	}
}

/**
 * Sequence hydrator mock
 *
 * @package ApparatTest
 */
class AbstractSequenceHydrator extends \Apparat\Resource\Domain\Model\Hydrator\AbstractSequenceHydrator
{
	/**
	 * Use multipart hydrator mock methods
	 */
	use AggregateHydratorMocks;
}

/**
 * Choice hydrator mock
 *
 * @package ApparatTest
 */
class AbstractChoiceHydrator extends \Apparat\Resource\Domain\Model\Hydrator\AbstractChoiceHydrator
{
	/**
	 * Use multipart hydrator mock methods
	 */
	use AggregateHydratorMocks;
}

/**
 * Hydrator tests
 *
 * @package ApparatTest
 */
class HydratorTest extends TestBase
{

	/**
	 * Test an invalid hydrator configuraton
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447019565
	 */
	public function testInvalidHydratorConfig()
	{
		HydratorFactory::build([]);
	}

	/**
	 * Test an invalid hydrator content model configuraton
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447020287
	 */
	public function testInvalidHydratorContentModel()
	{
		HydratorFactory::build([[]]);
	}

	/**
	 * Test an invalid subhydrator name
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447364401
	 */
	public function testInvalidSubhydratorNae()
	{
		HydratorFactory::build([['~' => true]]);
	}

	/**
	 * Test a missing multipart hydrator
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447107537
	 */
	public function testMissingMultipartHydrator()
	{
		HydratorFactory::build([[1, 2]]);
	}

	/**
	 * Test an empty multipart hydrator class
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447107792
	 */
	public function testEmptyMultipartHydratorClass()
	{
		HydratorFactory::build([[1, 2], '']);
	}

	/**
	 * Test an invalid multipart hydrator class
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447107792
	 */
	public function testInvalidMultipartHydratorClass()
	{
		HydratorFactory::build([[1, 2], \stdClass::class]);
	}

	/**
	 * Test invalid multipart hydrator parameters
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447109790
	 */
	public function testInvalidMultipartHydratorParameters()
	{
		$GLOBALS['mockValidateParameters'] = true;
		HydratorFactory::build([[1, 2], AbstractSequenceHydrator::class, true]);
		unset($GLOBALS['mockValidateParameters']);
	}

	/**
	 * Test too few multipart hydrator parameters
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447866302
	 */
	public function testTooFewMultipartHydratorParameters()
	{
		HydratorFactory::build([[1, 2], AbstractSequenceHydrator::class, 0]);
	}

	/**
	 * Test invalid multipart hydrator occurrences minimum
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1447021191
	 */
	public function testInvalidMultipartHydratorMinimumOccurrences()
	{
		HydratorFactory::build([[1, 2], AbstractSequenceHydrator::class, 0, 1]);
	}

	/**
	 * Test invalid multipart hydrator occurrences maximum
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1447021211
	 */
	public function testInvalidMultipartHydratorMaximumOccurrences()
	{
		HydratorFactory::build([[1, 2], AbstractSequenceHydrator::class, 2, 1]);
	}

	/**
	 * Test an invalid multipart hydrator class
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447868909
	 */
	public function testInvalidMultipartSubhydratorClass()
	{
		HydratorFactory::build([[\stdClass::class, \stdClass::class], AbstractSequenceHydrator::class, 1, 1]);
	}

	/**
	 * Test multipart hydrator
	 */
	public function testMultipartHydrator()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$this->assertInstanceOf(AbstractSequenceHydrator::class, $sequenceHydrator);
	}

	/**
	 * Test multipart hydrator name
	 */
	public function testMultipartHydratorName()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$this->assertEquals(HydratorInterface::STANDARD, $sequenceHydrator->getName());
	}

	/**
	 * Test multipart hydrator dehydration with an invalid part
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1448107001
	 */
	public function testMultipartHydratorDehydrationOfInvalidPart()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$sequenceHydrator->dehydrate(new TextPart('', new TextHydrator('name')));
	}

	/**
	 * Test multipart hydrator dehydration with an invalid part
	 *
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 1448112964
	 */
	public function testMultipartHydratorDehydrationWithInvalidOccurrenceDehydration()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$sequence = $sequenceHydrator->hydrate('one|two');
		$GLOBALS['mockOccurrenceDehydration'] = true;
		$sequenceHydrator->dehydrate($sequence);
		unset($GLOBALS['mockOccurrenceDehydration']);
	}

	/**
	 * Test sequence hydration with invalid aggregate class
	 *
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 1447887703
	 */
	public function testMultipartHydratorInvalidAggregateClass()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$GLOBALS['mockAggregateClass'] = true;
		$sequenceHydrator->hydrate('one|two');
		unset($GLOBALS['mockAggregateClass']);
	}

	/**
	 * Test sequence dehydration with empty occurrence
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108316
	 */
	public function testMultipartHydratorSequenceEmptyOccurrence()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$sequence = $sequenceHydrator->hydrate('one|two');
		$GLOBALS['mockEmptyOccurrence'] = true;
		$sequenceHydrator->dehydrate($sequence);
		unset($GLOBALS['mockEmptyOccurrence']);
	}

	/**
	 * Test sequence dehydration with unknown subhydrator name
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108444
	 */
	public function testMultipartHydratorSequenceInvalidSubhydratorName()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$sequence = $sequenceHydrator->hydrate('one|two');
		$GLOBALS['mockSubhydratorName'] = true;
		$sequenceHydrator->dehydrate($sequence);
		unset($GLOBALS['mockSubhydratorName']);
	}

	/**
	 * Test sequence dehydration with invalid part instance
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108849
	 */
	public function testMultipartHydratorSequenceInvalidPartInstance()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$sequence = $sequenceHydrator->hydrate('one|two');
		$GLOBALS['mockPartInstance'] = true;
		$sequenceHydrator->dehydrate($sequence);
		unset($GLOBALS['mockPartInstance']);
	}

	/**
	 * Test sequence part count
	 */
	public function testMultipartHydratorSequenceCount()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$this->assertEquals(1, count($sequenceHydrator->hydrate('one|two')));
	}

	/**
	 * Test invalid occurrences number
	 *
	 * @expectedException OutOfBoundsException
	 * @expectedExceptionCode 1447976806
	 */
	public function testMultipartHydratorSequenceInvalidOccurrenceNumber()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$GLOBALS['mockOccurrenceNumber'] = true;
		$sequenceHydrator->hydrate('one|two');
		unset($GLOBALS['mockOccurrenceNumber']);
	}

	/**
	 * Test sequence serialization
	 */
	public function testMultipartHydratorSequenceSerialization()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$sequence = $sequenceHydrator->hydrate('one|two');
		$this->assertEquals('onetwo', strval($sequence));
	}

	/**
	 * Test invalid assignment part identifier
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447364401
	 */
	public function testMultipartHydratorSequenceInvalidAssignmentPartIdentifier()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$GLOBALS['mockAssignmentPartIdentifier'] = true;
		$sequenceHydrator->hydrate('one|two');
		unset($GLOBALS['mockAssignmentPartIdentifier']);
	}

	/**
	 * Test sequence unknown delegate method
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1448225222
	 */
	public function testMultipartHydratorSequenceUnknownMethod()
	{
		/** @var AbstractSequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractSequenceHydrator::class,
			1,
			1
		]);
		$sequence = $sequenceHydrator->hydrate('one|two');
		$sequence->delegate('unknownMethod', [], []);
	}

	/**
	 * Test choice dehydration with empty occurrence
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108316
	 */
	public function testMultipartHydratorChoiceEmptyOccurrence()
	{
		/** @var AbstractChoiceHydrator $choiceHydrator */
		$choiceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractChoiceHydrator::class,
			1,
			1
		]);
		$choice = $choiceHydrator->hydrate('one');
		$GLOBALS['mockEmptyOccurrence'] = true;
		$choiceHydrator->dehydrate($choice);
		unset($GLOBALS['mockEmptyOccurrence']);
	}

	/**
	 * Test choice dehydration with unknown subhydrator name
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108444
	 */
	public function testMultipartHydratorChoiceInvalidSubhydratorName()
	{
		/** @var AbstractChoiceHydrator $choiceHydrator */
		$choiceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractChoiceHydrator::class,
			1,
			1
		]);
		$choice = $choiceHydrator->hydrate('one');
		$GLOBALS['mockSubhydratorName'] = true;
		$choiceHydrator->dehydrate($choice);
		unset($GLOBALS['mockSubhydratorName']);
	}

	/**
	 * Test choice dehydration with invalid part instance
	 *
	 * @expectedException \Apparat\Resource\Domain\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108849
	 */
	public function testMultipartHydratorChoiceInvalidPartInstance()
	{
		/** @var AbstractChoiceHydrator $choiceHydrator */
		$choiceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			AbstractChoiceHydrator::class,
			1,
			1
		]);
		$choice = $choiceHydrator->hydrate('one');
		$GLOBALS['mockPartInstance'] = true;
		$choiceHydrator->dehydrate($choice);
		unset($GLOBALS['mockPartInstance']);
	}

	/**
	 * Test an invalid singlepart hydrator class
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447110065
	 */
	public function testInvalidSinglepartHydratorClass()
	{
		HydratorFactory::build([[HydratorInterface::STANDARD => \stdClass::class]]);
	}

	/**
	 * Test the text hydrator (short form)
	 */
	public function testTextHydratorShort()
	{
		$textHydrator = HydratorFactory::build([TextHydrator::class]);
		$this->assertInstanceOf(TextHydrator::class, $textHydrator);
	}

	/**
	 * Test the text hydrator (verbose form)
	 */
	public function testTextHydratorVerbose()
	{
		$textHydrator = HydratorFactory::build([[HydratorInterface::STANDARD => TextHydrator::class]]);
		$this->assertInstanceOf(TextHydrator::class, $textHydrator);
	}

	/**
	 * Test the text hydrator name
	 */
	public function testTextHydratorName()
	{
		$textHydrator = HydratorFactory::build([[HydratorInterface::STANDARD => TextHydrator::class]]);
		$this->assertEquals(HydratorInterface::STANDARD, $textHydrator->getName());
	}
}