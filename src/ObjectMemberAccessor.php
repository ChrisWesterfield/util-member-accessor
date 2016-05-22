<?php

namespace Kassko\Component\MemberAccessor\ObjectMemberAccessor;

/**
 * Access to non public properties and methods.
 */
class ObjectMemberAccessor
{
    /**
     * Get the value of a public or a non public property.
     *
     * @param mixed     $object         An object
     * @param string    $propertyName   The name of the property to get the value
     *
     * @return mixed
     */
    public function getPropertyValue($object, $propertyName)
    {
        $func = function () use ($propertyName) {
            return $this->$propertyName;
        };
        $func = $func->bindTo($object, $object);

        return $func();
    }

    /**
     * Set a value for a public or a non public property.
     *
     * @param mixed     $object         An object
     * @param string    $propertyName   The name of the property to set
     * @param array     $value          The value to set
     *
     * @return mixed
     */
    public function setPropertyValue($object, $propertyName, $value)
    {
        $func = function () use ($propertyName, $value) {
            return $this->$propertyName = $value;
        };
        $func = $func->bindTo($object, $object);

        return $func();
    }

    /**
     * Execute a public or a non public method and return the value it returns.
     *
     * @param mixed     $object         An object
     * @param string    $methodName     The name of the method to access
     * @param array     $params         The method parameters
     *
     * @return mixed
     */
    public function getMethodValue($object, $methodName, array $params = [])
    {
        return $this->execute($object, $methodName, $params);
    }

    /**
     * Execute a public or a non public method.
     *
     * @param mixed     $object         An object
     * @param string    $methodName     The name of the method to execute
     * @param array     $params         The method parameters
     *
     * @return mixed
     */
    public function executeMethod($object, $methodName, array $params = [])
    {
        $func = function () use ($methodName, $params) {

            switch (count($params))
            {
                //Avoid calling the expensive method "call_user_func_array()" for the most common case.
                //@todo When migrated to Php 5.6, go through unpacking $params "...$params" and the code will be cleaner.
                case 0:
                    return $this->$methodName();

                case 1:
                    return $this->$methodName($params[0]);

                case 2:
                    return $this->$methodName($params[0], $params[1]);

                case 3:
                    return $this->$methodName($params[0], $params[1], $params[2]);

                case 4:
                    return $this->$methodName($params[0], $params[1], $params[2], $params[3]);

                case 5:
                    return $this->$methodName($params[0], $params[1], $params[2], $params[3], $params[4]);

                default:
                    return call_user_func_array([$this, $methodName], $params);
            }
        };
        $func = $func->bindTo($object, $object);

        return $func();
    }
}
