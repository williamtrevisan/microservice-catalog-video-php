<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class GenreUnitTest extends TestCase
{
    public function testAttributes()
    {
        $id = RamseyUuid::uuid4()->toString();
        $date = date('Y-m-d H:i:s');

        $genre = new Genre(
            name: 'Genre name',
            id: new Uuid($id),
            isActive: true,
            createdAt: new DateTime($date),
        );

        $this->assertEquals($id, $genre->id());
        $this->assertEquals('Genre name', $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertEquals($date, $genre->createdAt());
        $this->assertIsArray($genre->categoriesId);
        $this->assertCount(0, $genre->categoriesId);
        $this->assertEquals([], $genre->categoriesId);
    }

    public function testAttributesCreate()
    {
        $genre = new Genre(name: 'Genre name');

        $this->assertNotEmpty($genre->id());
        $this->assertEquals('Genre name', $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertNotEmpty($genre->createdAt());
    }

    public function testDeactivate()
    {
        $genre = new Genre(name: 'Genre name');

        $genre->deactivate();

        $this->assertFalse($genre->isActive);
    }

    public function testActivate()
    {
        $genre = new Genre(name: 'Genre name', isActive: false);

        $genre->activate();

        $this->assertTrue($genre->isActive);
    }

    public function testUpdate()
    {
        $genre = new Genre(name: 'Genre name');

        $genre->update(name: 'Genre name updated');

        $this->assertEquals('Genre name updated', $genre->name);
    }

    public function testEntityExceptions()
    {
        $this->expectException(EntityValidationException::class);

        new Genre(name: 'Ge');
    }

    public function testEntityUpdateException()
    {
        $this->expectException(EntityValidationException::class);

        $genre = new Genre(name: 'Genre name');

        $genre->update(name: Str::random(256));
    }

    public function testAddCategoryToGenre()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $genre = new Genre(name: 'Genre name');

        $genre->addCategory(categoryId: $categoryId);
        $genre->addCategory(categoryId: $categoryId);

        $this->assertIsArray($genre->categoriesId);
        $this->assertCount(2, $genre->categoriesId);
        $this->assertEquals([$categoryId, $categoryId], $genre->categoriesId);
    }

    public function testAddCategoryWhenInstantiate()
    {
        $categoryId = RamseyUuid::uuid4()->toString();

        $genre = new Genre(
            name: 'Genre name',
            categoriesId: [$categoryId, $categoryId]
        );

        $this->assertIsArray($genre->categoriesId);
        $this->assertCount(2, $genre->categoriesId);
        $this->assertEquals([$categoryId, $categoryId], $genre->categoriesId);
    }

    public function testRemoveCategoryFromGenre()
    {
        $categoryId1 = RamseyUuid::uuid4()->toString();
        $categoryId2 = RamseyUuid::uuid4()->toString();
        $genre = new Genre(
            name: 'Genre name',
            categoriesId: [$categoryId1, $categoryId2]
        );

        $genre->removeCategory(categoryId: $categoryId1);

        $this->assertCount(1, $genre->categoriesId);
        $this->assertEquals([1 => $categoryId2], $genre->categoriesId);
    }
}
