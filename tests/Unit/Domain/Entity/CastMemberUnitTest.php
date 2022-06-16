<?php

namespace Domain\Entity;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class CastMemberUnitTest extends TestCase
{
    public function testAttributes()
    {
        $id = RamseyUuid::uuid4()->toString();
        $date = date('Y-m-d H:i:s');

        $castMember = new CastMember(
            name: 'Cast member name',
            type: CastMemberType::Actor,
            id: new Uuid($id),
            createdAt: new DateTime($date),
        );

        $this->assertEquals($id, $castMember->id());
        $this->assertEquals('Cast member name', $castMember->name);
        $this->assertEquals(CastMemberType::Actor, $castMember->type);
        $this->assertEquals($date, $castMember->createdAt());
    }

    public function testAttributesNewEntity()
    {
        $castMember = new CastMember(
            name: 'Cast member name',
            type: CastMemberType::Director,
        );

        $this->assertNotEmpty($castMember->id());
        $this->assertEquals('Cast member name', $castMember->name);
        $this->assertEquals(CastMemberType::Director, $castMember->type);
        $this->assertNotEmpty($castMember->createdAt());
    }

    public function testValidation()
    {
        $this->expectException(EntityValidationException::class);

        new CastMember(name: 'Ca', type: CastMemberType::Director);
    }

    public function testExceptionUpdate()
    {
        $this->expectException(EntityValidationException::class);

        $castMember = new CastMember(
            name: 'Cast member name',
            type: CastMemberType::Director
        );

        $castMember->update(name: random_bytes(256));
    }

    public function testUpdate()
    {
        $castMember = new CastMember(
            name: 'Cast member name',
            type: CastMemberType::Director
        );

        $castMember->update(name: 'Cast member name updated');

        $this->assertEquals('Cast member name updated', $castMember->name);
    }
}
