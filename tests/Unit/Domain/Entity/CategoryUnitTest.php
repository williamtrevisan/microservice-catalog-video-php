<?php

namespace Tests\Unit\Domain\Entity;

use PHPUnit\Framework\TestCase;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new Category(
            name: 'Category name',
            description: 'Category description',
            isActive: true
        );

        $this->assertEquals('Category name', $category->name);
        $this->assertEquals('Category description', $category->description);
        $this->assertTrue($category->isActive);
    }
}