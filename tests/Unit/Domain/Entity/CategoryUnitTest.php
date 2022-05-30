<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Category;
use Core\Domain\Exception\EntityValidationException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Throwable;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new Category(
            name: 'Category name',
            description: 'Category description',
            isActive: true
        );

        $this->assertNotEmpty($category->id());
        $this->assertNotEmpty($category->createdAt());
        $this->assertEquals('Category name', $category->name);
        $this->assertEquals('Category description', $category->description);
        $this->assertTrue($category->isActive);
    }

    public function testActivated()
    {
        $category = new Category(
            name: 'Category name',
            isActive: false,
        );

        $this->assertFalse($category->isActive);

        $category->activate();

        $this->assertTrue($category->isActive);
    }

    public function testDisabled()
    {
        $category = new Category(
            name: 'Category name',
        );

        $this->assertTrue($category->isActive);

        $category->disable();

        $this->assertFalse($category->isActive);
    }

    public function testUpdate()
    {
        $uuid = Uuid::uuid4()->toString();
        $category = new Category(
            id: $uuid,
            name: 'Category name',
            description: 'Category description'
        );

        $category->update(
            name: 'Category new name',
            description: 'Category new description'
        );

        $this->assertEquals($uuid, $category->id);
        $this->assertEquals('Category new name', $category->name);
        $this->assertEquals('Category new description', $category->description);
    }

    public function testExceptionIncorretMinLengthName()
    {
        try {
            new Category(
                name: 'Ca',
                description: 'Category description'
            );

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testExceptionIncorretMaxLengthName()
    {
        try {
            new Category(
                name: random_bytes(256),
                description: 'Category description'
            );

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testExceptionCorrectName()
    {
        try {
            new Category(
                name: 'Category name',
                description: 'Category description'
            );

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertNotInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testExceptionIncorrectDescription()
    {
        try {
            new Category(
                name: 'Category name',
                description: random_bytes(256)
            );

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testExceptionCorrectDescription()
    {
        try {
            new Category(
                name: 'Category name',
                description: 'Category description'
            );

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertNotInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testExceptionNullDescription()
    {
        try {
            new Category(
                name: 'Category name',
            );

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertNotInstanceOf(EntityValidationException::class, $throwable);
        }
    }
}