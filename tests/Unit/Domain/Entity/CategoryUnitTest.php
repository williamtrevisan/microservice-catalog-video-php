<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new Category(
            id: 'CategoryId',
            name: 'Category name',
            description: 'Category description',
            isActive: true
        );

        $this->assertEquals('Category name', $category->name);
        $this->assertEquals('Category description', $category->description);
        $this->assertTrue($category->isActive);
    }
}