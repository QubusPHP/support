<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use DateTime;
use SplFixedArray;
use stdClass;
use Qubus\Support\Serializer\DeepCopySerializer;
use Qubus\Support\Serializer\Strategy\NullStrategy;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Post;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\User;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Comment;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;
use Qubus\Tests\Support\Serializer\Dummy\Simple\ChildOfSplFixedArray;

class DeepCopySerializerTest extends TestCase
{
    public function testSerialization()
    {
        $object = $this->getObject();
        $serializer = new DeepCopySerializer(new NullStrategy());
        $serializedObject = $serializer->serialize($object);

        Assert::assertEquals($object, $serializer->unserialize($serializedObject));
    }

    private function getObject()
    {
        return new Post(
            new PostId(9),
            'Hello World',
            'Your first post',
            new User(
                new UserId(1),
                'Post Author'
            ),
            [
                new Comment(
                    new CommentId(1000),
                    'Have no fear, sers, your king is safe.',
                    new User(new UserId(2), 'Barristan Selmy'),
                    [
                        'created_at' => (new DateTime('2015/07/18 12:13:00'))->format('c'),
                        'accepted_at' => (new DateTime('2015/07/19 00:00:00'))->format('c'),
                    ]
                ),
            ]
        );
    }

    public function testArraySerialization()
    {
        $arrayOfObjects = [$this->getObject(), $this->getObject()];
        $serializer = new DeepCopySerializer(new NullStrategy());
        $serializedObject = $serializer->serialize($arrayOfObjects);

        Assert::assertEquals($arrayOfObjects, $serializer->unserialize($serializedObject));
    }

    public function testObjectStorageCopyDuringSerialization()
    {
        $post = $this->getObject();

        $stdClass = new stdClass();
        $stdClass->first = $post;
        $stdClass->second = $post;

        $serializer = new DeepCopySerializer(new NullStrategy());
        $serializedObject = $serializer->serialize($stdClass);

        Assert::assertEquals($stdClass, $serializer->unserialize($serializedObject));
    }

    public function testSplFixedArraySerialization()
    {
        $splFixedArray = new SplFixedArray(3);
        $splFixedArray[0] = 1;
        $splFixedArray[1] = 2;
        $splFixedArray[2] = 3;

        $serializer = new DeepCopySerializer(new NullStrategy());
        $serializedObject = $serializer->serialize($splFixedArray);

        Assert::assertEquals($splFixedArray, $serializer->unserialize($serializedObject));
    }

    public function testSplFixedArrayChildSerialization()
    {
        $splFixedArray = new ChildOfSplFixedArray(3);
        $splFixedArray[0] = 1;
        $splFixedArray[1] = 2;
        $splFixedArray[2] = 3;

        $serializer = new DeepCopySerializer(new NullStrategy());
        $serializedObject = $serializer->serialize($splFixedArray);

        Assert::assertEquals($splFixedArray, $serializer->unserialize($serializedObject));
    }
}
