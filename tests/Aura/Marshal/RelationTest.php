<?php
namespace Aura\Marshal;
use Aura\Marshal\Relation\Builder as RelationBuilder;
use Aura\Marshal\Type\Builder as TypeBuilder;

/**
 * Test class for Manager.
 * Generated by PHPUnit on 2011-11-21 at 11:28:20.
 */
class RelationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $type_builder       = new TypeBuilder;
        $relation_builder   = new RelationBuilder;
        $types              = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_types.php';
        
        $this->manager      = new Manager($type_builder, $relation_builder, $types);
        $data               = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_data.php';
        foreach ($this->manager->getTypes() as $type) {
            $obj = $this->manager->{$type}->load($data[$type]);
        }
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    public function testNoRelationship()
    {
        parent::setUp();
        $type_builder     = new TypeBuilder;
        $relation_builder = new RelationBuilder;
        $types            = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_types.php';
        
        $types['posts']['relation_names']['tags']['relationship'] = null;
        
        $this->manager = new Manager($type_builder, $relation_builder, $types);
        $this->setExpectedException('Aura\Marshal\Exception');
        $this->manager->posts;
    }
    
    public function testNoNativeField()
    {
        parent::setUp();
        $type_builder     = new TypeBuilder;
        $relation_builder = new RelationBuilder;
        $types            = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_types.php';
        
        $types['posts']['relation_names']['tags']['native_field'] = null;
        
        $this->manager = new Manager($type_builder, $relation_builder, $types);
        $this->setExpectedException('Aura\Marshal\Exception');
        $this->manager->posts;
    }
    
    public function testNoForeignField()
    {
        parent::setUp();
        $type_builder     = new TypeBuilder;
        $relation_builder = new RelationBuilder;
        $types            = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_types.php';
        
        $types['posts']['relation_names']['tags']['foreign_field'] = null;
        
        $this->manager = new Manager($type_builder, $relation_builder, $types);
        $this->setExpectedException('Aura\Marshal\Exception');
        $this->manager->posts;
    }
    
    public function testNoThroughType()
    {
        parent::setUp();
        $type_builder     = new TypeBuilder;
        $relation_builder = new RelationBuilder;
        $types            = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_types.php';
        
        $types['posts']['relation_names']['tags']['through_type'] = null;
        
        $this->manager = new Manager($type_builder, $relation_builder, $types);
        $this->setExpectedException('Aura\Marshal\Exception');
        $this->manager->posts;
    }
    
    public function testNoThroughNativeField()
    {
        parent::setUp();
        $type_builder     = new TypeBuilder;
        $relation_builder = new RelationBuilder;
        $types            = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_types.php';
        
        $types['posts']['relation_names']['tags']['through_native_field'] = null;
        
        $this->manager = new Manager($type_builder, $relation_builder, $types);
        $this->setExpectedException('Aura\Marshal\Exception');
        $this->manager->posts;
    }
    
    public function testNoThroughForeignField()
    {
        parent::setUp();
        $type_builder     = new TypeBuilder;
        $relation_builder = new RelationBuilder;
        $types            = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_types.php';
        
        $types['posts']['relation_names']['tags']['through_foreign_field'] = null;
        
        $this->manager = new Manager($type_builder, $relation_builder, $types);
        $this->setExpectedException('Aura\Marshal\Exception');
        $this->manager->posts;
    }
    
    public function testBelongsTo()
    {
        $post = $this->manager->posts->getRecord(1);
        $author = $this->manager->posts->getRelated($post, 'authors');
        $this->assertSame('1', $author->id);
        $this->assertSame('Anna', $author->name);
    }
    
    public function testHasOne()
    {
        $post = $this->manager->posts->getRecord(1);
        $meta = $this->manager->posts->getRelated($post, 'metas');
        $this->assertSame('1', $meta->id);
        $this->assertSame('1', $meta->post_id);
        $this->assertSame('meta 1', $meta->data);
    }
    
    public function testHasMany()
    {
        $post = $this->manager->posts->getRecord(5);
        $comments = $this->manager->posts->getRelated($post, 'comments');
        
        $this->assertSame(3, count($comments));
        
        $data  = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_data.php';
        $expect = [
            $data['comments'][3],
            $data['comments'][4],
            $data['comments'][5],
        ];
        
        foreach ($comments as $offset => $comment) {
            $this->assertSame($expect[$offset]['id'], $comment->id);
            $this->assertSame($expect[$offset]['post_id'], $comment->post_id);
            $this->assertSame($expect[$offset]['body'], $comment->body);
        }
    }
    
    public function testHasManyThrough()
    {
        $post = $this->manager->posts->getRecord(3);
        $tags = $this->manager->posts->getRelated($post, 'tags');
        
        $this->assertSame(2, count($tags));
        
        $data  = include __DIR__ . DIRECTORY_SEPARATOR . 'fixture_data.php';
        $expect = [
            $data['tags'][2],
            $data['tags'][0],
        ];
        
        foreach ($tags as $offset => $tag) {
            $this->assertSame($expect[$offset]['id'], $tag->id);
            $this->assertSame($expect[$offset]['name'], $tag->name);
        }
    }
}
