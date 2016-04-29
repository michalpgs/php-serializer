<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/30/15
 * Time: 1:03 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Test\Serializer\Transformer;

use DateTime;
use NilPortugues\Serializer\DeepCopySerializer;
use NilPortugues\Serializer\Transformer\XmlTransformer;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\Comment;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\Post;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\User;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\ValueObject\CommentId;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\ValueObject\PostId;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\ValueObject\UserId;

class XmlTransformerTest extends \PHPUnit_Framework_TestCase
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
</data>

STRING;

        $this->assertEquals($expected, $xml);
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
</data>

STRING;

        $this->assertEquals($expected, $serializer->serialize($arrayOfObjects));
    }

    public function testUnserializeWillThrowException()
    {
        $serialize = new DeepCopySerializer(new XmlTransformer());

        $this->setExpectedException("InvalidArgumentException");
        $serialize->unserialize($serialize->serialize($this->getObject()));
    }
}
