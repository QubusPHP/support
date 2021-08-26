<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Transformer;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use DateTime;
use Qubus\Exception\Data\TypeException;
use stdClass;
use Qubus\Support\Serializer\DeepCopySerializer;
use Qubus\Support\Serializer\Transformer\JsonTransformer;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Post;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\User;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Comment;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;

class JsonTransformerTest extends TestCase
{
    public function testSerializationObjectWithArrayOfOneAttribute()
    {
        $object = new stdClass();
        $object->payload = ['userId' => 1];

        $serializer = new DeepCopySerializer(new JsonTransformer());

        $expected = <<<STRING
{
    "payload": {
        "userId": 1
    }
}
STRING;

        Assert::assertEquals($expected, $serializer->serialize($object));
    }

    public function testSerializationObjectWithObjectOfOneAttribute()
    {
        $internal = new stdClass();
        $internal->userId = 1;

        $object = new stdClass();
        $object->payload = $internal;

        $serializer = new DeepCopySerializer(new JsonTransformer());

        $expected = <<<STRING
{
    "payload": {
        "userId": 1
    }
}
STRING;

        Assert::assertEquals($expected, $serializer->serialize($object));
    }

    public function testSerialization()
    {
        $object = $this->getObject();
        $serializer = new DeepCopySerializer(new JsonTransformer());

        $expected = <<<STRING
{
    "postId": 9,
    "title": "Hello World",
    "content": "Your first post",
    "author": {
        "userId": 1,
        "name": "Post Author"
    },
    "comments": [
        {
            "commentId": 1000,
            "dates": {
                "created_at": "2015-07-18T12:13:00+02:00",
                "accepted_at": "2015-07-19T00:00:00+02:00"
            },
            "comment": "Have no fear, sers, your king is safe.",
            "user": {
                "userId": 2,
                "name": "Barristan Selmy"
            }
        }
    ]
}
STRING;

        Assert::assertEquals($expected, $serializer->serialize($object));
    }

    /**
     * @return Post
     */
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
                        'created_at' => (new DateTime('2015/07/18 12:13:00+02:00'))->format('c'),
                        'accepted_at' => (new DateTime('2015/07/19 00:00:00+02:00'))->format('c'),
                    ]
                ),
            ]
        );
    }

    public function testArraySerialization()
    {
        $arrayOfObjects = [$this->getObject(), $this->getObject()];
        $serializer = new DeepCopySerializer(new JsonTransformer());

        $expected = <<<STRING
[
    {
        "postId": 9,
        "title": "Hello World",
        "content": "Your first post",
        "author": {
            "userId": 1,
            "name": "Post Author"
        },
        "comments": [
            {
                "commentId": 1000,
                "dates": {
                    "created_at": "2015-07-18T12:13:00+02:00",
                    "accepted_at": "2015-07-19T00:00:00+02:00"
                },
                "comment": "Have no fear, sers, your king is safe.",
                "user": {
                    "userId": 2,
                    "name": "Barristan Selmy"
                }
            }
        ]
    },
    {
        "postId": 9,
        "title": "Hello World",
        "content": "Your first post",
        "author": {
            "userId": 1,
            "name": "Post Author"
        },
        "comments": [
            {
                "commentId": 1000,
                "dates": {
                    "created_at": "2015-07-18T12:13:00+02:00",
                    "accepted_at": "2015-07-19T00:00:00+02:00"
                },
                "comment": "Have no fear, sers, your king is safe.",
                "user": {
                    "userId": 2,
                    "name": "Barristan Selmy"
                }
            }
        ]
    }
]
STRING;

        Assert::assertEquals($expected, $serializer->serialize($arrayOfObjects));
    }

    public function testUnserializeWillThrowException()
    {
        $serialize = new DeepCopySerializer(new JsonTransformer());

        $this->expectException(TypeException::class);
        $serialize->unserialize($serialize->serialize($this->getObject()));
    }
}
