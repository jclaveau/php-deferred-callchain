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
            ->setAge(23)
            ;

        $mySubjectIMissedBefore = new Human;

        try {
            $fullName = $defineAge( $mySubjectIMissedBefore );
            $this->assertTrue(false, 'An exception should have been thrown here');
        }
        catch (\Exception $e) {
            $this->assertEquals(
                 "call_user_func_array() expects parameter 1 to be a valid callback, "
                ."class 'JClaveau\Async\Human' does not have a method 'setAge'",
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

    /**/
}

class Human
{
    protected $name;
    protected $firstName;

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
