<?php

use Swiftmade\FEL\Helper;
use Swiftmade\FEL\FormulaLanguage;

class HelpersTest extends TestCase
{
    public function testEnumerateHelper()
    {
        $helper = new Helper();
        // Enumerate string
        $result = $helper->enumerate("apple");
        $this->assertEquals(["apple"], $result);
        // Enumerate regular arrays
        $result = $helper->enumerate(["bar", "foo", "baz"]);
        $this->assertEquals(["bar", "foo", "baz"], $result);
        // Enumerate associative arrays
        $result = $helper->enumerate(["name" => "John", "last_name" => "Doe"]);
        $this->assertEquals([["name" => "John", "last_name" => "Doe"]], $result);
    }

    public function testReplaceHelper()
    {
        $subject = 'abc';
        $dictionary = [
            'a' => 'bar',
            'b' => 'baz',
            'c' => 'foo'
        ];

        $helper = new Helper();
        $result = $helper->replace($dictionary, $subject);
        $this->assertEquals('barbazfoo', $result);
    }
}      