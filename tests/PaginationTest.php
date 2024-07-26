<?php
namespace Maxizdev\Pagination\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Maxizdev\Utils\Pagination;

class PaginationTest extends KernelTestCase
{
    public function testRunOfPaginationEmpty(): void
    {
        $pagination = new Pagination();
        
        $this -> assertIsObject($pagination);
        $this -> assertEquals(0, $pagination -> getTotal());
        $this -> assertEquals(0, $pagination -> getLimit());
        $this -> assertEquals(0, $pagination -> getPages());
        $this -> assertEquals(0, $pagination -> getCurrent());
    }

    public function testRunOfPaginationWithParams(): void
    {        
        $pagination = new Pagination(110, 20);

        $this -> assertEquals(110, $pagination -> getTotal());
        $this -> assertEquals(20, $pagination -> getLimit());
        $this -> assertNotSame(0, $pagination -> getPages());
        $this -> assertNotSame(0, $pagination -> getCurrent());
    }

    public function testCountPages(): void
    {        
        $pagination = new Pagination();
        $pagination -> run(21, 5);
        
        $this -> assertEquals(5, $pagination -> getPages());
        $this -> assertEquals(1, $pagination -> getCurrent());
    }

    public function testGetCurrentPage(): void
    {
        $_GET['page'] = 3;
        $pagination = new Pagination(23, 5);

        $this -> assertEquals(3, $pagination -> getCurrent());
    }

    public function testGetUrlParams(): void
    {
        $_GET['page'] = 3;
        $pagination = new Pagination(23, 5);

        $this -> assertEquals('page=3', $pagination -> getUrlParams());
        
        $url = '/catalog/new';
        $url = $pagination -> addUrlParams($url);
        $this -> assertEquals('/catalog/new?page=3', $url, $pagination -> getUrlParams());

        $url = '/catalog/new?test=hello';
        $url = $pagination -> addUrlParams($url);
        $this -> assertEquals('/catalog/new?test=hello&page=3', $url, $pagination -> getUrlParams());
    }

    public function testHasPages(): void
    {
        $_GET['page'] = 3;
        $pagination = new Pagination();
        $this -> assertFalse($pagination -> hasPages());

        $pagination -> run(3, 5);
        $this -> assertFalse($pagination -> hasPages());

        $pagination -> run(23, 5);
        $this -> assertTrue($pagination -> hasPages());
    }

    public function testGetDisplayData()
    {
        $_GET['page'] = 3;
        $pagination = new Pagination(23, 3);

        $data = $pagination -> getDisplayData();
        $this -> assertIsArray($data);
        $this -> assertArrayHasKey('prev_link', $data);
        $this -> assertArrayHasKey('next_link', $data);
        $this -> assertArrayHasKey('active', $data);
        $this -> assertArrayHasKey('pages', $data);
    }

    public function testGetDisplayDataPages()
    {
        $_GET['page'] = 3;
        $pagination = new Pagination(23, 3);

        $data = $pagination -> getDisplayData();
        $this -> assertCount(8, $data['pages']);
    }

    public function testTemplate()
    {
        $_GET['page'] = 3;
        $pagination = new Pagination(23, 3);
        $pagination -> setPath('/catalog/promo');
        
        self :: bootKernel();
        $container = self :: getContainer();

        $html = $container -> get('twig') -> render('parts/pagination.html.twig', [
            'pagination' => $pagination
        ]);
        
        $this -> assertStringContainsString('<div class="pagination">', $html);

        $number = substr_count($html, '<a href="/catalog/promo?page=');
        $this -> assertEquals(9, $number);
    }
}