<?php

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Transformer;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use DateTime;
use Qubus\Exception\Data\TypeException;
use Qubus\Support\Serializer\DeepCopySerializer;
use Qubus\Support\Serializer\Transformer\FlatArrayTransformer;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Post;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\User;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Comment;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;
use ReflectionException;

class FlatArrayTransformerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testSerialization()
    {
        $object = $this->getObject();
        $serializer = new DeepCopySerializer(new FlatArrayTransformer());

        $expected = [
            '0.postId' => 9,
            '0.title' => 'Hello World',
            '0.content' => 'Your first post',
            '0.author.userId' => 1,
            '0.author.name' => 'Post Author',
            '0.comments.0.commentId' => 1000,
            '0.comments.0.dates.created_at' => '2015-07-18T12:13:00+02:00',
            '0.comments.0.dates.accepted_at' => '2015-07-19T00:00:00+02:00',
            '0.comments.0.comment' => 'Have no fear, sers, your king is safe.',
            '0.comments.0.user.userId' => 2,
            '0.comments.0.user.name' => 'Barristan Selmy',

        ];

        Assert::assertEquals($expected, $serializer->serialize($object));
    }

    /**
     * @return Post
     */
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
                        'created_at' => (new DateTime('2015/07/18 12:13:00+02:00'))->format('c'),
                        'accepted_at' => (new DateTime('2015/07/19 00:00:00+02:00'))->format('c'),
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
        $serializer = new DeepCopySerializer(new FlatArrayTransformer());

        $expected = [
            '0.0.postId' => 9,
            '0.0.title' => 'Hello World',
            '0.0.content' => 'Your first post',
            '0.0.author.userId' => 1,
            '0.0.author.name' => 'Post Author',
            '0.0.comments.0.commentId' => 1000,
            '0.0.comments.0.dates.created_at' => '2015-07-18T12:13:00+02:00',
            '0.0.comments.0.dates.accepted_at' => '2015-07-19T00:00:00+02:00',
            '0.0.comments.0.comment' => 'Have no fear, sers, your king is safe.',
            '0.0.comments.0.user.userId' => 2,
            '0.0.comments.0.user.name' => 'Barristan Selmy',
            '0.1.postId' => 9,
            '0.1.title' => 'Hello World',
            '0.1.content' => 'Your first post',
            '0.1.author.userId' => 1,
            '0.1.author.name' => 'Post Author',
            '0.1.comments.0.commentId' => 1000,
            '0.1.comments.0.dates.created_at' => '2015-07-18T12:13:00+02:00',
            '0.1.comments.0.dates.accepted_at' => '2015-07-19T00:00:00+02:00',
            '0.1.comments.0.comment' => 'Have no fear, sers, your king is safe.',
            '0.1.comments.0.user.userId' => 2,
            '0.1.comments.0.user.name' => 'Barristan Selmy',
        ];

        Assert::assertEquals($expected, $serializer->serialize($arrayOfObjects));
    }

    /**
     * @throws ReflectionException
     */
    public function testUnserializeWillThrowException()
    {
        $serialize = new DeepCopySerializer(new FlatArrayTransformer());

        $this->expectException(TypeException::class);
        $serialize->unserialize($serialize->serialize($this->getObject()));
    }
}
