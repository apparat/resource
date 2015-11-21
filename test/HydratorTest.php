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

use Apparat\Resource\Framework\Hydrator\CommonMarkHydrator;
use Apparat\Resource\Framework\Hydrator\TextHydrator;
use Apparat\Resource\Framework\Part\TextPart;
use Apparat\Resource\Model\Hydrator\Hydrator;
use Apparat\Resource\Model\Hydrator\HydratorFactory;
use Apparat\Resource\Model\Hydrator\InvalidArgumentException;
use Apparat\Resource\Model\Hydrator\RuntimeException;
use Apparat\Resource\Model\Part\OutOfBoundsException;
use Apparat\Resource\Model\Part\Part;


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
	 * @return Part File part
	 */
	public function hydrate($data)
	{
		if (!empty($GLOBALS['mockAggregateClass'])) {
			$this->_aggregateClass = self::class;
		}

		$aggregate = parent::hydrate(null);
		foreach (explode('|', $data) as $part => $str) {
			if (!empty($GLOBALS['mockOccurrenceNumber'])) {
				$aggregate->assign(0, $str, $part);
			} elseif(!empty($GLOBALS['mockAssignmentPartIdentifier'])) {
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
				return parent::_dehydrateOccurrence(array_combine(array_map(function($name) {
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
class SequenceHydrator extends \Apparat\Resource\Model\Hydrator\SequenceHydrator
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
class ChoiceHydrator extends \Apparat\Resource\Model\Hydrator\ChoiceHydrator
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
		HydratorFactory::build([[1, 2], SequenceHydrator::class, true]);
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
		HydratorFactory::build([[1, 2], SequenceHydrator::class, 0]);
	}

	/**
	 * Test invalid multipart hydrator occurrences minimum
	 *
	 * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1447021191
	 */
	public function testInvalidMultipartHydratorMinimumOccurrences()
	{
		HydratorFactory::build([[1, 2], SequenceHydrator::class, 0, 1]);
	}

	/**
	 * Test invalid multipart hydrator occurrences maximum
	 *
	 * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1447021211
	 */
	public function testInvalidMultipartHydratorMaximumOccurrences()
	{
		HydratorFactory::build([[1, 2], SequenceHydrator::class, 2, 1]);
	}

	/**
	 * Test an invalid multipart hydrator class
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1447868909
	 */
	public function testInvalidMultipartSubhydratorClass()
	{
		HydratorFactory::build([[\stdClass::class, \stdClass::class], SequenceHydrator::class, 1, 1]);
	}

	/**
	 * Test multipart hydrator
	 */
	public function testMultipartHydrator()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
			1,
			1
		]);
		$this->assertInstanceOf(SequenceHydrator::class, $sequenceHydrator);
	}

	/**
	 * Test multipart hydrator name
	 */
	public function testMultipartHydratorName()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
			1,
			1
		]);
		$this->assertEquals(Hydrator::STANDARD, $sequenceHydrator->getName());
	}

	/**
	 * Test multipart hydrator self reference
	 */
	public function testMultipartHydratorSelf()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			['a' => TextHydrator::class, 'b' => CommonMarkHydrator::class],
			SequenceHydrator::class,
			1,
			1
		]);
		$this->assertEquals($sequenceHydrator, $sequenceHydrator->getSub([]));
	}

	/**
	 * Test multipart hydrator with too few part identifiers
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1448056671
	 */
	public function testMultipartHydratorTooFewPartIdentifiers()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			['a' => TextHydrator::class, 'b' => CommonMarkHydrator::class],
			SequenceHydrator::class,
			1,
			1
		]);
		$sequenceHydrator->getSub(['c']);
	}

	/**
	 * Test multipart hydrator unknown subpart path
	 *
	 * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1447876475
	 */
	public function testMultipartHydratorUnknownSub()
	{
		$sequenceHydrator = HydratorFactory::build([
			['a' => TextHydrator::class, 'b' => CommonMarkHydrator::class],
			SequenceHydrator::class,
			1,
			1
		]);
		$sequenceHydrator->getSub([0, 'c']);
	}

	/**
	 * Test multipart hydrator invalid subpart path
	 *
	 * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1447365624
	 */
	public function testMultipartHydratorInvalidSub()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			['a' => TextHydrator::class, 'b' => CommonMarkHydrator::class],
			SequenceHydrator::class,
			1,
			1
		]);
		$sequenceHydrator->getSub([0, 'a', 0, 'b', 0, 'c']);
	}

	/**
	 * Test multipart hydrator dehydration with an invalid part
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1448107001
	 */
	public function testMultipartHydratorDehydrationOfInvalidPart()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
			1,
			1
		]);
		$sequenceHydrator->dehydrate(new TextPart());
	}

	/**
	 * Test multipart hydrator dehydration with an invalid part
	 *
	 * @expectedException RuntimeException
	 * @expectedExceptionCode 1448112964
	 */
	public function testMultipartHydratorDehydrationWithInvalidOccurrenceDehydration()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
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
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
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
	 * @expectedException \Apparat\Resource\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108316
	 */
	public function testMultipartHydratorSequenceEmptyOccurrence()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
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
	 * @expectedException \Apparat\Resource\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108444
	 */
	public function testMultipartHydratorSequenceInvalidSubhydratorName()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
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
	 * @expectedException \Apparat\Resource\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108849
	 */
	public function testMultipartHydratorSequenceInvalidPartInstance()
	{
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
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
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
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
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
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
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
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
		/** @var SequenceHydrator $sequenceHydrator */
		$sequenceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			SequenceHydrator::class,
			1,
			1
		]);
		$GLOBALS['mockAssignmentPartIdentifier'] = true;
		$sequenceHydrator->hydrate('one|two');
		unset($GLOBALS['mockAssignmentPartIdentifier']);
	}

	/**
	 * Test choice dehydration with empty occurrence
	 *
	 * @expectedException \Apparat\Resource\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108316
	 */
	public function testMultipartHydratorChoiceEmptyOccurrence()
	{
		/** @var ChoiceHydrator $choiceHydrator */
		$choiceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			ChoiceHydrator::class,
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
	 * @expectedException \Apparat\Resource\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108444
	 */
	public function testMultipartHydratorChoiceInvalidSubhydratorName()
	{
		/** @var ChoiceHydrator $choiceHydrator */
		$choiceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			ChoiceHydrator::class,
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
	 * @expectedException \Apparat\Resource\Model\Hydrator\SkippedOccurrenceDehydrationException
	 * @expectedExceptionCode 1448108849
	 */
	public function testMultipartHydratorChoiceInvalidPartInstance()
	{
		/** @var ChoiceHydrator $choiceHydrator */
		$choiceHydrator = HydratorFactory::build([
			[TextHydrator::class, TextHydrator::class],
			ChoiceHydrator::class,
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
		HydratorFactory::build([[Hydrator::STANDARD => \stdClass::class]]);
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
		$textHydrator = HydratorFactory::build([[Hydrator::STANDARD => TextHydrator::class]]);
		$this->assertInstanceOf(TextHydrator::class, $textHydrator);
	}

	/**
	 * Test the text hydrator name
	 */
	public function testTextHydratorName()
	{
		$textHydrator = HydratorFactory::build([[Hydrator::STANDARD => TextHydrator::class]]);
		$this->assertEquals(Hydrator::STANDARD, $textHydrator->getName());
	}

	/**
	 * Test subhydrators on a single part hydrator
	 *
	 * @expectedException \Apparat\Resource\Model\Part\InvalidArgumentException
	 * @expectedExceptionCode 1447365624
	 */
	public function testTextHydratorSub()
	{
		$textHydrator = HydratorFactory::build([[Hydrator::STANDARD => TextHydrator::class]]);
		$textHydrator->getSub(['a', 'b', 'c']);
	}
}