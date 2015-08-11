<?php
namespace Mooti\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use \Mooti\Xizlr\Core;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider routerSucceedsData
     */
    public function routerSucceeds($httpMethod, $uri, $postVars, $resourceName, $resourceMethod, $resourceArguments)
    {
        $router = new \Mooti\Xizlr\Core\Http\Router($httpMethod, $uri, $postVars);
        $this->assertEquals($resourceName, $router->getResourceName());
        $this->assertEquals($resourceMethod, $router->getResourceMethod());
        $this->assertEquals($resourceArguments, $router->getResourceArguments());
    }

    public function routerSucceedsData()
    {
        return array(
            array('get', '/1.0/users/', array(), 'user', 'getUsers', array('version' => '1.0')),
            array('get', '/1.0/users/?groupId=12345', array(), 'user', 'getUsers', array('version' => '1.0', 'filter' => array('groupId' => array('is' => '12345')))),
            array('get', '/1.0/users/?not-groupId=67890', array(), 'user', 'getUsers', array('version' => '1.0', 'filter' => array('groupId' => array('isNot' => '67890')))),
            array('get', '/1.0/users/?groupId[]=123&groupId[]=456&not-groupId[]=789', array(), 'user', 'getUsers', array('version' => '1.0', 'filter' => array('groupId' => array('is' => array('123', '456'), 'isNot' => array('789'))))),
            array('get', '/1.0/users/?max-age=35&min-age=25', array(), 'user', 'getUsers', array('version' => '1.0', 'filter' => array('age' => array('greaterEqual' => '25', 'lessEqual' => '35')))),
            array('get', '/1.0/users/123', array(), 'user', 'getUser', array('version' => '1.0', 'userId' => '123')),
            array('get', '/1.0/users/123/addresses/', array(), 'address', 'getAddresses', array('version' => '1.0', 'filter' => array('userId' => array('is' => '123')))),

            array('post', '/1.0/users/', array(), 'user', 'addUser', array('version' => '1.0', 'data' => array())),
            array('post', '/1.0/users/', array('name' => 'Ken'), 'user', 'addUser', array('version' => '1.0', 'data' => array('name' => 'Ken'))),
            array('post', '/1.0/users/?groupId=12345', array('name' => 'Ken', 'groupId' => '1111'), 'user', 'addUsers', array('version' => '1.0', 'data' => array('groupId' => '12345', 'name' => 'Ken'))),
            array('post', '/1.0/users/123', array('name' => 'Ken'), 'user', 'editUser', array('version' => '1.0', 'userId' => '123', 'data' => array('name' => 'Ken'))),
            array('post', '/1.0/users/123?groupId=12345', array('name' => 'Ken'), 'user', 'editUser', array('version' => '1.0', 'userId' => 123, 'data' => array('groupId' => '12345', 'name' => 'Ken'))),
            array('post', '/1.0/users/123/addresses/', array('name' => 'Ken'), 'address', 'addAddress', array('version' => '1.0', 'data' => array('userId' => '123', 'name' => 'Ken'))),
            array('post', '/1.0/users/123/addresses/456', array('name' => 'Ken'), 'address', 'editAddress', array('version' => '1.0', 'addressId' => '456', 'data' => array('userId' => '123', 'name' => 'Ken'))),

            array('delete', '/1.0/users/123', array(), 'user', 'deleteUser', array('version' => '1.0', 'userId' => '123')),

            array('head', '/1.0/users/123', array(), 'user', 'userExists', array('version' => '1.0', 'userId' => '123')),
        );
    }

    public function routerFailssData()
    {
        return array(
            array('get', ''), //empty
            array('get', '/'), //not enough info
            array('get', '/1.0/'), //not enough info
            array('get', '/1.0/user'), //singular
            array('get', '/1.0/user/'), //singular
            array('get', '/1.0/users/12345/addresses/456'), //trying to target a specific child need to use /1.0/addresses/456 instead
            array('get', '/1.0/users/12345/address') //singular
        );
    }
}

