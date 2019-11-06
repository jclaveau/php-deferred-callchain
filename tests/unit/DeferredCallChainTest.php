<?php
namespace JClaveau\Async;

class DeferredCallChainTest extends \AbstractTest
{
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
    public function test_toString_with_string_target()
    {
        $nameRobert = DeferredCallChain::new_("Human")
            ->setName('Muda')
            ->setFirstName('Robert')
            ;

        $this->assertEquals(
            "(new JClaveau\Async\DeferredCallChain('Human'))->setName('Muda')->setFirstName('Robert')",
            (string) $nameRobert
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
            "(new JClaveau\Async\DeferredCallChain( JClaveau\Async\Human#" . spl_object_id($human) . " ))->setName('Muda')->setFirstName('Robert')",
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
            // throw $e;
            // var_dump(get_class($e));
            // var_dump(array_slice($e->getTrace(), 0, 5));
            
            $this->assertEquals(
                 "setColor() is neither a method of JClaveau\Async\Human nor a function",
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

        try {
            $fullName = $defineAge( new Human );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\Exception $e) {            
            $this->assertEquals(
                 "Exception which is not a BadMethodCallException",
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
            $fullName = $defineAge( new Human );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\BadMethodCallException $e) {            
            $this->assertEquals(
                 "BadMethodCallException not thrown from __call",
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
                 "Undefined index: non_existing_entry",
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
            '[{"method":"setName","arguments":["Muda"]},{"method":"setFirstName","arguments":["Robert"]},{"entry":"entry"}]',
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

    /**/
}

class Human
{
    protected $name;
    protected $firstName;
    protected $age;

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

    protected function throwExceptionForTestPurpose()
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
            $this->throwExceptionForTestPurpose();
        }
        else {
            throw new \BadMethodCallException(
                $name . ' is not a method of ' . Human::class
            );
        }
        
        return $this;
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
