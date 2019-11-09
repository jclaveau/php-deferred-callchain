<?php
namespace JClaveau\Async;

class DeferredCallChainTest extends \AbstractTest
{
    /**
     */
    public function test_later()
    {
        $nameRobert = later(Human::class)
            ->setName('Muda')
            ->setFirstName('Robert')
            ;

        $this->assertEquals(
            "(new JClaveau\Async\DeferredCallChain(".var_export(Human::class, true)."))->setName('Muda')->setFirstName('Robert')",
            (string) $nameRobert
        );
    }

    /**
     */
    public function test_toString()
    {
        $nameRobert = (new DeferredCallChain)
            ->setName('Muda')
            ->setFirstName('Robert')
            ;

        $this->assertEquals(
            "(new JClaveau\Async\DeferredCallChain)->setName('Muda')->setFirstName('Robert')",
            (string) $nameRobert
        );
    }

    /**
     */
    public function test_toString_too_long()
    {
        $target = new Human;
        
        $nameRobert = (new DeferredCallChain)
            ->setName('A_name_longer_than_25_chars')
            ->setFirstName($target)
            ;

        $this->assertEquals(
            "(new JClaveau\Async\DeferredCallChain)->setName(A_name_lon ... n_25_chars)->setFirstName(JClaveau\Async\Human #".spl_object_id($target).")",
            $nameRobert->toString([
                'max_parameter_length' => 25,
                'short_objects' => false,
            ])
        );
        
        $this->assertEquals(
            "(new JClaveau\Async\DeferredCallChain)->setName('A_name_longer_than_25_chars')->setFirstName(JClaveau\Async\Human::__set_state(array(
   'name' => NULL,
   'firstName' => NULL,
   'age' => NULL,
)))",
            $nameRobert->toString([
                'max_parameter_length' => 512,
                'short_objects' => false,
            ])
        );
    }

    /**
     */
    public function test_toString_with_string_target()
    {
        $nameRobert = DeferredCallChain::new_(Human::class)
            ->setName('Muda')
            ->setFirstName('Robert')
            ;

        $this->assertEquals(
            "(new JClaveau\Async\DeferredCallChain(".var_export(Human::class, true)."))->setName('Muda')->setFirstName('Robert')",
            (string) $nameRobert
        );
        
        // Check that the toString result is valid PHP code
        eval('$nameRobert2 = ' . $nameRobert . ';');
        $this->assertEquals(
            "(new JClaveau\Async\DeferredCallChain(".var_export(Human::class, true)."))->setName('Muda')->setFirstName('Robert')",
            (string) $nameRobert2
        );
    }

    /**
     */
    public function test_toString_with_instance_target()
    {
        $human = new Human;
        
        $nameRobert = DeferredCallChain::new_($human)
            ->setName('Muda')
            ->setFirstName('Robert')
            ;

        $this->assertEquals(
            "(new JClaveau\Async\DeferredCallChain( JClaveau\Async\Human #" . spl_object_id($human) . " ))->setName('Muda')->setFirstName('Robert')",
            (string) $nameRobert
        );
    }

    /**
     */
    public function test_invoke()
    {
        $nameRobert = (new DeferredCallChain)
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ;

        $mySubjectIMissedBefore = new Human;
        $fullName = $nameRobert( $mySubjectIMissedBefore );

        $this->assertEquals(
            "Robert Muda",
            $fullName
        );
    }

    /**
     */
    public function test_invoke_with_predefined_target()
    {
        $mySubject = new Human;
        
        $nameRobert = (new DeferredCallChain($mySubject))
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ;

        $fullName = $nameRobert();

        $this->assertEquals(
            "Robert Muda",
            $fullName
        );
    }

    /**
     */
    public function test_invoke_with_predefined_target_and_a_new_target()
    {
        $mySubject = new Human;
        
        $nameRobert = (new DeferredCallChain($mySubject))
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ;


        try {
            $fullName = $nameRobert(new Human);
            $this->assertFalse(true, "an exception has not been thrown");
        }
        catch (\JClaveau\Async\Exceptions\TargetAlreadyDefinedException $e) {
            $this->assertTrue(true, "exception thrown as expected");
        }
    }

    /**
     */
    public function test_invoke_with_target_class()
    {
        $nameRobert = (new DeferredCallChain( Human::class ))
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ;

        $mySubjectIMissedBefore = new Human;
        $fullName = $nameRobert( $mySubjectIMissedBefore );

        $this->assertEquals(
            "Robert Muda",
            $fullName
        );
    }

    /**
     */
    public function test_invoke_with_target_class_without_namespace()
    {
        $nameRobert = (new DeferredCallChain("Human"))
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ;

        $mySubjectIMissedBefore = new Human;

        try {
            $fullName = $nameRobert( $mySubjectIMissedBefore );
            $this->assertFalse(true, "an exception has not been thrown");
        }
        catch (\JClaveau\Async\Exceptions\UndefinedTargetClassException $e) {
            $this->assertTrue(true, "exception thrown as expected");
        }
    }

    /**
     */
    public function test_invoke_with_wrong_target_class()
    {
        $nameRobert = (new DeferredCallChain('\stdClass'))
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ;

        $mySubjectIMissedBefore = new Human;

        try {
            $fullName = $nameRobert( $mySubjectIMissedBefore );
            $this->assertFalse(true, "an exception has not been thrown");
        }
        catch (\JClaveau\Async\Exceptions\BadTargetClassException $e) {
            $this->assertTrue(true, "exception thrown as expected");
        }
    }

    /**
     */
    public function test_invoke_with_target_type()
    {
        $nameRobert = (new DeferredCallChain("object"))
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ;

        $mySubjectIMissedBefore = new Human;
        $fullName = $nameRobert( $mySubjectIMissedBefore );

        $this->assertEquals(
            "Robert Muda",
            $fullName
        );
    }

    /**
     */
    public function test_invoke_with_wrong_target_type()
    {
        $nameRobert = (new DeferredCallChain("string"))
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ;

        $mySubjectIMissedBefore = new Human;

        try {
            $fullName = $nameRobert( $mySubjectIMissedBefore );
            $this->assertFalse(true, "an exception has not been thrown");
        }
        catch (\JClaveau\Async\Exceptions\BadTargetTypeException $e) {
            $this->assertTrue(true, "exception thrown as expected");
        }
    }

    /**
     */
    public function test_invoke_with_interface()
    {
        $getCount = (new DeferredCallChain("\Countable"))
            ->count()
            ;

        $this->assertTrue( is_int($getCount(new CountableClass)) );
    }

    /**
     */
    public function test_invoke_with_missing_target_interface()
    {
        $getCount = (new DeferredCallChain("\Traversable"))
            ->count()
            ;

        $myCountableIMissedBefore = new CountableClass;

        try {
            $count = $getCount( $myCountableIMissedBefore );
            $this->assertFalse(true, "an exception has not been thrown");
        }
        catch (\JClaveau\Async\Exceptions\BadTargetInterfaceException $e) {
            $this->assertTrue(true, "exception thrown as expected");
        }
    }

    /**
     */
    public function test_call_missing_method()
    {
        $defineAge = (new DeferredCallChain)
            ->setColor("green") // fuck racism ;)
            ;

        $mySubjectIMissedBefore = new Human;

        try {
            $fullName = $defineAge( $mySubjectIMissedBefore );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\BadMethodCallException $e) {            
            $this->assertEquals(
                 "setColor() is neither a method of JClaveau\Async\Human nor a function"
                 ."\nWhen applying (new " . DeferredCallChain::class . '( ' . Human::class . ' #' . spl_object_id($mySubjectIMissedBefore). ' ))->setColor(\'green\')'
                 . " called in " . __FILE__ . ":" . (__LINE__ - 13),
                $e->getMessage()
            );
        }
    }

    /**
     */
    public function test_call_of_magic_method__call()
    {
        $defineAge = (new DeferredCallChain)
            ->setAge(23)
            ;

        $mySubjectIMissedBefore = new Human;
        $fullName = $defineAge( $mySubjectIMissedBefore );
        
        $this->assertEquals(23, $mySubjectIMissedBefore->getAge());
    }

    /**
     */
    public function test_exception_trown_from__call_magic_method()
    {
        $defineAge = (new DeferredCallChain)
            ->setGender('female')
            ;

        $somebody = new Human;
        try {
            $fullName = $defineAge( $somebody );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\Exception $e) {            
            $this->assertEquals(
                 "Exception which is not a BadMethodCallException"
                 ."\nWhen applying (new " . DeferredCallChain::class . '( ' . Human::class . ' #' . spl_object_id($somebody). ' ))'
                 .'->setGender(\'female\')'
                 . " called in " . __FILE__ . ":" . (__LINE__ - 13),
                $e->getMessage()
            );
        }
    }

    /**
     * @see https://github.com/jclaveau/php-deferred-callchain/issues/9
     */
    public function test_call_static_method()
    {
        $getMaxAge = (new DeferredCallChain)
            ->getMaxAge(); // getMaxAge is static
        
        $mySubjectIMissedBefore = new Human;
        $max_age = $getMaxAge( $mySubjectIMissedBefore );
        
        $this->assertEquals(125, $max_age);
        
        $max_age = $getMaxAge( Human::class );
        $this->assertEquals(125, $max_age);
    }

    /**
     */
    public function test_call_from__callStatic_magic_method()
    {
        $defineMaxAge = (new DeferredCallChain)
            ->setMaxAge(200)
            ->getMaxAge()
            ;

        $max_age = $defineMaxAge( new Human );
        
        $this->assertEquals(200, $max_age);
        
        $defineMaxAge = (new DeferredCallChain)
            ->setMaxAge(250)
            ->getMaxAge()
            ;

        $max_age = $defineMaxAge( Human::class );
        
        $this->assertEquals(250, $max_age);
    }

    /**
     */
    public function test_exception_trown_from__callStatic_magic_method()
    {
        $defineMaxAge = (new DeferredCallChain)
            ->setExistingColors('green', 'blue', 'orange')
            ;

        $somebody = new Human;        
        try {
            $max_age = $defineMaxAge( $somebody );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\Exception $e) {            
            $this->assertEquals(
                 "Exception which is not a BadMethodCallException"
                 ."\nWhen applying (new " . DeferredCallChain::class . '( ' . Human::class . ' #' . spl_object_id($somebody). ' ))'
                 .'->setExistingColors(\'green\', \'blue\', \'orange\')'
                 . " called in " . __FILE__ . ":" . (__LINE__ - 13),
                $e->getMessage()
            );
        }
    }

    /**
     */
    public function test_usage_BadMethodCallException_trown_from__callStatic_magic_method()
    {
        $defineMaxAge = (new DeferredCallChain)
            ->setPopulationCount(8000000000)
            ;

        $somebody = new Human;        
        try {
            $max_age = $defineMaxAge( $somebody );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\Exception $e) {            
            $this->assertEquals(
                 "BadMethodCallException not thrown from __callStatic"
                 ."\nWhen applying (new " . DeferredCallChain::class . '( ' . Human::class . ' #' . spl_object_id($somebody). ' ))'
                 .'->setPopulationCount(8000000000)'
                 . " called in " . __FILE__ . ":" . (__LINE__ - 13),
                $e->getMessage()
            );
        }
    }

    /**
     */
    public function test_exception_trown_under__call_magic_method()
    {
        $defineAge = (new DeferredCallChain)
            ->setGender2('female')
            ;

        try {
            $somebody = new Human;
            $fullName = $defineAge( $somebody );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\BadMethodCallException $e) {            
            $this->assertEquals(
                 "BadMethodCallException not thrown from __call"
                 ."\nWhen applying (new " . DeferredCallChain::class . '( ' . Human::class . ' #' . spl_object_id($somebody). ' ))'
                 .'->setGender2(\'female\')'
                 . " called in " . __FILE__ . ":" . (__LINE__ - 13),
                $e->getMessage()
            );
        }
    }

    /**
     */
    public function test_invoke_entry()
    {
        $getName = (new DeferredCallChain)
            ['name']
            ;

        $robert = ['name' => 'Muda', 'firstname' => 'Robert'];

        $name = $getName( $robert );

        $this->assertEquals(
            "Muda",
            $name
        );
    }

    /**
     */
    public function test_call_missing_entry()
    {
        $getName = (new DeferredCallChain)
            ['non_existing_entry']
            ;

        $robert = ['name' => 'Muda', 'firstname' => 'Robert'];

        try {
            $name = $getName( $robert );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\Exception $e) {
            $this->assertEquals(
                 "Undefined index: non_existing_entry"
                 ."\nWhen applying (new " . DeferredCallChain::class . '(' . var_export($robert, true). '))'
                 .'[\'non_existing_entry\']'
                 . " called in " . __FILE__ . ":" . (__LINE__ - 14),
                $e->getMessage()
            );
        }
    }

    /**
     */
    public function test_toString_extended()
    {
        $nameRobert = LaterHuman::new_()
            ->setName('Muda')
            ['entry']
            ->setFirstName('Robert')
            ;

        $this->assertEquals(
            "(new JClaveau\Async\LaterHuman)->setName('Muda')['entry']->setFirstName('Robert')",
            (string) $nameRobert
        );
    }

    /**
     * @see https://stackoverflow.com/questions/5525795/does-javascript-guarantee-object-property-order
     */
    public function test_jsonSerialize()
    {
        $nameRobert = LaterHuman::new_()
            ->setName('Muda')
            ->setFirstName('Robert')
            ['entry']
            ;

        $this->assertEquals(
            '[{"method":"setName","arguments":["Muda"],"file":' . json_encode(__FILE__) . ',"line":' . (__LINE__ - 6) . '},'
            .'{"method":"setFirstName","arguments":["Robert"],"file":' . json_encode(__FILE__) . ',"line":' . (__LINE__ - 6) . '},'
            .'{"entry":"entry","file":' . json_encode(__FILE__) . ',"line":' . (__LINE__ - 6) . '}]',
            json_encode($nameRobert)
        );
    }

    /**
     */
    public function test_ArrayAccess_unimplemented_offsetExists()
    {
        try {
            $nameRobert = LaterHuman::new_();
            isset($nameRobert['property_that_could_not_be_tested']);
            $this->assertTrue(false, "An exception must have been thrown here");
        }
        catch (\Exception $e) {
            $this->assertEquals(
                "not implemented",
                $e->getMessage()
            );
        }
    }

    /**
     */
    public function test_ArrayAccess_unimplemented_offsetUnset()
    {
        try {
            $nameRobert = LaterHuman::new_();
            unset($nameRobert['property_that_could_not_be_unset']);
            $this->assertTrue(false, "An exception must have been thrown here");
        }
        catch (\Exception $e) {
            $this->assertEquals(
                "not implemented",
                $e->getMessage()
            );
        }
    }

    /**
     */
    public function test_ArrayAccess_unimplemented_offsetSet()
    {
        try {
            $nameRobert = LaterHuman::new_();
            $nameRobert['property_that_could_not_be_set'] = 'plop';
            $this->assertTrue(false, "An exception must have been thrown here");
        }
        catch (\Exception $e) {
            $this->assertEquals(
                "not implemented",
                $e->getMessage()
            );
        }
    }

    /**
     */
    public function test_function_call()
    {
        $nameRobertUppercase = LaterHuman::new_()
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ->strtoupper()
            ;
            
        $this->assertEquals('ROBERT MUDA', $nameRobertUppercase(new Human));
    }

    /**
     */
    public function test_function_call_with_placeholder()
    {
        $nameRobertUppercase = LaterHuman::new_()
            ->setName('Muda')
            ->setFirstName('Robert')
            ->getFullName()
            ->explode(' ', '$$')
            ;
            
        $this->assertEquals(['Robert', 'Muda'], $nameRobertUppercase(new Human));
    }

    /**
     */
    public function test_usage_exception()
    {
        $nameRobertUppercase = DeferredCallChain::new_()
            ->setName('Muda')
            ->setFirstName('Robert')
            ->throwExceptionForTestPurpose()
            ->setAge(87)
            ;
            
        try {
            $robert = new Human;
            $nameRobertUppercase($robert);
            $this->assertTrue(false, "An exception must have been thrown here");
        }
        catch (\Exception $e) {
            $this->assertEquals(
                "An exception has been thrown by some user code"
                 ."\nWhen applying (new " . DeferredCallChain::class . '( ' . Human::class . ' #' . spl_object_id($robert). ' ))'
                 .'->setName(\'Muda\')->setFirstName(\'Robert\')->throwExceptionForTestPurpose()'
                 . " called in " . __FILE__ . ":" . (__LINE__ - 14),
                $e->getMessage()
            );
        }
    }

    /**/
}

class Human
{
    protected $name;
    protected $firstName;
    protected $age;
    protected static $maxAge=125;

    public static function getMaxAge()
    {
        return self::$maxAge;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->name;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function throwExceptionForTestPurpose()
    {
        throw new \Exception("An exception has been thrown by some user code");
    }

    protected function throwBadMethodCallExceptionDuringSetAge2()
    {
        throw new \BadMethodCallException("BadMethodCallException not thrown from __call");
    }

    public function __call($name, array $arguments)
    {
        if ($name == 'setAge') {
            $this->age = $arguments[0];
        }
        elseif ($name == 'setGender') {
            throw new \Exception("Exception which is not a BadMethodCallException");
        }
        elseif ($name == 'setGender2') {
            $this->throwBadMethodCallExceptionDuringSetAge2();
        }
        else {
            throw new \BadMethodCallException(
                $name . ' is not a method of ' . Human::class
            );
        }
        
        return $this;
    }

    public static function __callStatic($name, array $arguments)
    {
        if ($name == 'setMaxAge') {
            self::$maxAge = $arguments[0];
            return get_called_class();
        }
        elseif ($name == 'setExistingColors') {
            throw new \Exception("Exception which is not a BadMethodCallException");
        }
        elseif ($name == 'setPopulationCount') {
            self::throwBadMethodCallExceptionDuringsetPopulationCount();
        }
        else {
            throw new \BadMethodCallException(
                $name . ' is not a method of ' . Human::class
            );
        }
    }

    protected static function throwBadMethodCallExceptionDuringsetPopulationCount()
    {
        throw new \BadMethodCallException("BadMethodCallException not thrown from __callStatic");
    }

}

class LaterHuman extends DeferredCallChain
{
}

class CountableClass implements \Countable
{
    public function count()
    {
        return rand(0, 100);
    }
}
