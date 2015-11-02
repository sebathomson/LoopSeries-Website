<?php
namespace LoopAnime\AppBundle\Tests\Entity;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use LoopAnime\AppBundle\Entity\BaseEntity;

class BaseEntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \LoopAnime\ApiBundle\Exceptions\ResponseNotFoundException
     */
    public function testGetResponse()
    {
        $entity = new BaseEntity();
        $entity->getResponse();
    }

    public function testGetSerialized()
    {
        $entity = new TestEntity();
        $data = $entity->serialize();
        $this->assertEquals('{"id":"1","name":"luis"}', $data);
    }

    public function testGetSerializedContext()
    {
        $entity = new TestEntity();
        $data = $entity->serialize("update");
        $this->assertEquals('{"id":"1"}', $data);
    }

    public function testSerializedArray()
    {
        $entity = new TestEntity();
        $data = $entity->serialize("update", true);
        $this->assertEquals(['id' => 1], $data);
    }

}

class TestEntityResponse
{
    /**
     * @Type("string")
     * @Groups({"update"})
     */
    public $id = 1;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    public $name = "luis";
}

class TestEntity extends BaseEntity
{

    public function getResponse()
    {
        return new TestEntityResponse();
    }

}