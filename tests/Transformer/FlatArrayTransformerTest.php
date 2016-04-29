<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/30/15
 * Time: 1:24 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Test\Serializer\Transformer;

use DateTime;
use NilPortugues\Serializer\DeepCopySerializer;
use NilPortugues\Serializer\Transformer\FlatArrayTransformer;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\Comment;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\Post;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\User;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\ValueObject\CommentId;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\ValueObject\PostId;
use NilPortugues\Test\Serializer\Dummy\ComplexObject\ValueObject\UserId;

class FlatArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $object = $this->getObject();
        $serializer = new DeepCopySerializer(new FlatArrayTransformer());

        $expected = [
            'postId' => 9,
            'title' => 'Hello World',
            'content' => 'Your first post',
            'author.userId' => 1,
            'author.name' => 'Post Author',
            'comments.0.commentId' => 1000,
            'comments.0.dates.created_at' => '2015-07-18T12:13:00+02:00',
            'comments.0.dates.accepted_at' => '2015-07-19T00:00:00+02:00',
            'comments.0.comment' => 'Have no fear, sers, your king is safe.',
            'comments.0.user.userId' => 2,
            'comments.0.user.name' => 'Barristan Selmy',

        ];

        $this->assertEquals($expected, $serializer->serialize($object));
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
            '1.postId' => 9,
            '1.title' => 'Hello World',
            '1.content' => 'Your first post',
            '1.author.userId' => 1,
            '1.author.name' => 'Post Author',
            '1.comments.0.commentId' => 1000,
            '1.comments.0.dates.created_at' => '2015-07-18T12:13:00+02:00',
            '1.comments.0.dates.accepted_at' => '2015-07-19T00:00:00+02:00',
            '1.comments.0.comment' => 'Have no fear, sers, your king is safe.',
            '1.comments.0.user.userId' => 2,
            '1.comments.0.user.name' => 'Barristan Selmy',
        ];

        $this->assertEquals($expected, $serializer->serialize($arrayOfObjects));
    }

    public function testUnserializeWillThrowException()
    {
        $serialize = new DeepCopySerializer(new FlatArrayTransformer());

        $this->setExpectedException("\InvalidArgumentException");
        $serialize->unserialize($serialize->serialize($this->getObject()));
    }
}
