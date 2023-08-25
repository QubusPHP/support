<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use DateTime;
use Qubus\Support\Serializer\XmlSerializer;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Post;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\User;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Comment;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;
use ReflectionException;

class XmlSerializerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testSerialization()
    {
        $object = $this->getObject();
        $serializer = new XmlSerializer();
        $serializedObject = $serializer->serialize($object);

        Assert::assertEquals($object, $serializer->unserialize($serializedObject));
    }

    private function getObject(): Post
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

    /**
     * @throws ReflectionException
     */
    public function testArraySerialization()
    {
        $arrayOfObjects = [$this->getObject(), $this->getObject()];
        $serializer = new XmlSerializer();
        $serializedObject = $serializer->serialize($arrayOfObjects);

        Assert::assertEquals($arrayOfObjects, $serializer->unserialize($serializedObject));
    }
}
