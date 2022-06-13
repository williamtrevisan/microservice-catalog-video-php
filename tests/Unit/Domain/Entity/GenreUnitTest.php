<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Genre;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class GenreUnitTest extends TestCase
{
    public function testAttributes()
    {
        $id = RamseyUuid::uuid4()->toString();
        $date = date('Y-m-d H:i:s');

        $genre = new Genre(
            id: new Uuid($id),
            name: 'Genre name',
            isActive: true,
            createdAt: new DateTime($date),
        );

        $this->assertEquals($id, $genre->id());
        $this->assertEquals('Genre name', $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertEquals($date, $genre->createdAt());
    }

    public function testAttributesCreate()
    {
        $genre = new Genre(
            name: 'Genre name',
        );

        $this->assertNotEmpty($genre->id());
        $this->assertEquals('Genre name', $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertNotEmpty($genre->createdAt());
    }
}
