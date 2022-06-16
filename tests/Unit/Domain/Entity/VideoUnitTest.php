<?php

namespace Domain\Entity;

use Core\Domain\Entity\Video;
use Core\Domain\Exception\NotificationException;
use Core\Domain\Enum\{MediaStatus, Rating};
use Core\Domain\ValueObject\{Image, Media, Uuid};
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class VideoUnitTest extends TestCase
{
    public function testShouldBeAbleToInstantiateAVideoEntityWithAllAtributes()
    {
        $id = RamseyUuid::uuid4()->toString();
        $date = date('Y-m-d H:i:s');

        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
            id: new Uuid($id),
            published: true,
            createdAt: new DateTime($date),
        );

        $this->assertEquals($id, $video->id());
        $this->assertEquals('Video title', $video->title);
        $this->assertEquals('Video description', $video->description);
        $this->assertEquals(2001, $video->yearLaunched);
        $this->assertEquals(190, $video->duration);
        $this->assertTrue($video->opened);
        $this->assertEquals(Rating::Rate12, $video->rating);
        $this->assertTrue($video->published);
        $this->assertEquals($date, $video->createdAt());
    }

    public function testShouldDefineDefaultValuesFromOptionalAttributes()
    {
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
        );

        $this->assertNotEmpty($video->id());
        $this->assertFalse($video->published);
        $this->assertNull($video->thumbFile());
        $this->assertNull($video->thumbHalfFile());
        $this->assertNull($video->bannerFile());
        $this->assertNull($video->trailerFile());
        $this->assertNull($video->videoFile());
    }

    public function testShouldBeAbleToAddCastMemberToVideo()
    {
        $castMemberId = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
        );

        $video->addCastMember(castMemberId: $castMemberId);

        $this->assertCount(1, $video->castMembersId);
        $this->assertEquals([$castMemberId], $video->castMembersId);
    }

    public function testShouldBeAbleToRemoveCastMemberFromVideo()
    {
        $castMemberId1 = RamseyUuid::uuid4()->toString();
        $castMemberId2 = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
        );
        $video->addCastMember(castMemberId: $castMemberId1);
        $video->addCastMember(castMemberId: $castMemberId2);

        $video->removeCastMember(castMemberId: $castMemberId1);
        $video->removeCastMember(castMemberId: $castMemberId2);

        $this->assertCount(0, $video->castMembersId);
        $this->assertEquals([], $video->castMembersId);
    }

    public function testShouldBeAbleToAddCategoryToVideo()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
        );

        $video->addCategory(categoryId: $categoryId);

        $this->assertCount(1, $video->categoriesId);
        $this->assertEquals([$categoryId], $video->categoriesId);
    }

    public function testShouldBeAbleToRemoveCategoryFromVideo()
    {
        $categoryId1 = RamseyUuid::uuid4()->toString();
        $categoryId2 = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
        );
        $video->addCategory(categoryId: $categoryId1);
        $video->addCategory(categoryId: $categoryId2);

        $video->removeCategory(categoryId: $categoryId1);

        $this->assertCount(1, $video->categoriesId);
        $this->assertEquals([1 => $categoryId2], $video->categoriesId);
    }

    public function testShouldBeAbleToAddGenreToVideo()
    {
        $genreId = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
        );

        $video->addGenre(genreId: $genreId);

        $this->assertCount(1, $video->genresId);
        $this->assertEquals([$genreId], $video->genresId);
    }

    public function testShouldBeAbleToRemoveGenreFromVideo()
    {
        $genreId1 = RamseyUuid::uuid4()->toString();
        $genreId2 = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
        );
        $video->addGenre(genreId: $genreId1);
        $video->addGenre(genreId: $genreId2);

        $video->removeGenre(genreId: $genreId2);

        $this->assertCount(1, $video->genresId);
        $this->assertEquals([$genreId1], $video->genresId);
    }

    public function testShouldBeAbleToIncludeAThumbFileToVideo()
    {
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
            thumbFile: new Image(filePath: 'fakepath/thumbfile/test.png'),
        );

        $this->assertNotNull($video->thumbFile());
        $this->assertInstanceOf(Image::class, $video->thumbFile());
        $this->assertEquals(
            'fakepath/thumbfile/test.png',
            $video->thumbFile()?->filePath()
        );
    }

    public function testShouldBeAbleToIncludeAThumbHalfToVideo()
    {
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
            thumbHalfFile: new Image(filePath: 'fakepath/thumbhalf/test.png'),
        );

        $this->assertNotNull($video->thumbHalfFile());
        $this->assertInstanceOf(Image::class, $video->thumbHalfFile());
        $this->assertEquals(
            'fakepath/thumbhalf/test.png',
            $video->thumbHalfFile()->filePath()
        );
    }

    public function testShouldBeAbleToIncludeABannerToVideo()
    {
        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
            bannerFile: new Image(filePath: 'fakepath/banner-file/test.png'),
        );

        $this->assertNotNull($video->bannerFile());
        $this->assertInstanceOf(Image::class, $video->bannerFile());
        $this->assertEquals(
            'fakepath/banner-file/test.png',
            $video->bannerFile()->filePath()
        );
    }

    public function testShouldBeAbleToIncludeATrailerMediaToVideo()
    {
        $trailerFileMedia = new Media(
            filePath: 'fakepath/trailer-file/test.mp4',
            status: MediaStatus::Pending,
            encodedFilePath: 'fakepath/encoded.extension',
        );

        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
            trailerFile: $trailerFileMedia,
        );

        $this->assertNotNull($video->trailerFile());
        $this->assertInstanceOf(Media::class, $video->trailerFile());
        $this->assertEquals(
            'fakepath/trailer-file/test.mp4',
            $video->trailerFile()->filePath
        );
    }

    public function testShouldBeAbleToIncludeAVideoMediaToVideo()
    {
        $videoFileMedia = new Media(
            filePath: 'fakepath/video-file/test.mp4',
            status: MediaStatus::Complete,
        );

        $video = new Video(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
            videoFile: $videoFileMedia,
        );

        $this->assertNotNull($video->videoFile());
        $this->assertInstanceOf(Media::class, $video->videoFile());
        $this->assertEquals(
            'fakepath/video-file/test.mp4',
            $video->videoFile()->filePath
        );
    }

    public function testShouldThrowAnExceptionWithIsInvalidTitleLength()
    {
        $this->expectException(NotificationException::class);

        new Video(
            title: 'Vi',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: Rating::Rate12,
        );
    }
}
