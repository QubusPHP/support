<?php

/**
 * Qubus\Support
 *
 * @link       https://github.com/QubusPHP/support
 * @copyright  2020 Joshua Parker <josh@joshuaparker.blog>
 * @copyright  2015 Nil Portugués Calderó
 * @license    https://opensource.org/licenses/mit-license.php MIT License
 *
 * @since      1.0.0
 */

declare(strict_types=1);

namespace Qubus\Tests\Support\Serializer\Transformer;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use DateTime;
use Qubus\Exception\Data\TypeException;
use Qubus\Support\Serializer\DeepCopySerializer;
use Qubus\Support\Serializer\Transformer\XmlTransformer;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Post;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\PostId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\User;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\UserId;
use Qubus\Tests\Support\Serializer\Dummy\Complex\Comment;
use Qubus\Tests\Support\Serializer\Dummy\Complex\ValueObjects\CommentId;

class XmlTransformerTest extends TestCase
{
    public function testSerialization()
    {
        $object = $this->getObject();
        $serializer = new DeepCopySerializer(new XmlTransformer());
        $xml = $serializer->serialize($object);

        $expected = <<<STRING
<?xml version="1.0" encoding="UTF-8"?>
<data>
  <postId type="integer">9</postId>
  <title type="string">Hello World</title>
  <content type="string">Your first post</content>
  <author>
    <userId type="integer">1</userId>
    <name type="string">Post Author</name>
  </author>
  <comments>
    <sequential-item>
      <commentId type="integer">1000</commentId>
      <dates>
        <created_at type="string">2015-07-18T12:13:00+02:00</created_at>
        <accepted_at type="string">2015-07-19T00:00:00+02:00</accepted_at>
      </dates>
      <comment type="string">Have no fear, sers, your king is safe.</comment>
      <user>
        <userId type="integer">2</userId>
        <name type="string">Barristan Selmy</name>
      </user>
    </sequential-item>
  </comments>
</data>\n
STRING;

        Assert::assertEquals($expected, $xml);
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
        $serializer = new DeepCopySerializer(new XmlTransformer());

        $expected = <<<STRING
<?xml version="1.0" encoding="UTF-8"?>
<data>
  <sequential-item>
    <postId type="integer">9</postId>
    <title type="string">Hello World</title>
    <content type="string">Your first post</content>
    <author>
      <userId type="integer">1</userId>
      <name type="string">Post Author</name>
    </author>
    <comments>
      <sequential-item>
        <commentId type="integer">1000</commentId>
        <dates>
          <created_at type="string">2015-07-18T12:13:00+02:00</created_at>
          <accepted_at type="string">2015-07-19T00:00:00+02:00</accepted_at>
        </dates>
        <comment type="string">Have no fear, sers, your king is safe.</comment>
        <user>
          <userId type="integer">2</userId>
          <name type="string">Barristan Selmy</name>
        </user>
      </sequential-item>
    </comments>
  </sequential-item>
  <sequential-item>
    <postId type="integer">9</postId>
    <title type="string">Hello World</title>
    <content type="string">Your first post</content>
    <author>
      <userId type="integer">1</userId>
      <name type="string">Post Author</name>
    </author>
    <comments>
      <sequential-item>
        <commentId type="integer">1000</commentId>
        <dates>
          <created_at type="string">2015-07-18T12:13:00+02:00</created_at>
          <accepted_at type="string">2015-07-19T00:00:00+02:00</accepted_at>
        </dates>
        <comment type="string">Have no fear, sers, your king is safe.</comment>
        <user>
          <userId type="integer">2</userId>
          <name type="string">Barristan Selmy</name>
        </user>
      </sequential-item>
    </comments>
  </sequential-item>
</data>\n
STRING;

        Assert::assertEquals($expected, $serializer->serialize($arrayOfObjects));
    }

    public function testUnserializeWillThrowException()
    {
        $serialize = new DeepCopySerializer(new XmlTransformer());

        $this->expectException(TypeException::class);
        $serialize->unserialize($serialize->serialize($this->getObject()));
    }
}
