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
    public function test_toString_extended()
    {
        $nameRobert = LaterHuman::new_()
            ->setName('Muda')
            ->setFirstName('Robert')
            ;

        $this->assertEquals(
            "(new JClaveau\Async\LaterHuman)->setName('Muda')->setFirstName('Robert')",
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
            ;

        $this->assertEquals(
            '{"setName":["Muda"],"setFirstName":["Robert"]}',
            json_encode($nameRobert)
        );
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
