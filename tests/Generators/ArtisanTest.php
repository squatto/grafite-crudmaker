<?php


class ArtisanTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->destinationDir = __DIR__.'/../../vendor/orchestra/testbench-core/laravel';

        if (!is_dir($this->destinationDir.'/routes')) {
            mkdir($this->destinationDir.'/routes');
        }

        file_put_contents($this->destinationDir.'/routes/web.php', "<?php\n\n");

        $this->artisan('crudmaker:new', [
            'table' => 'books',
            '--migration' => true,
            '--api' => true,
            '--ui' => 'bootstrap',
            '--schema' => 'id:bigIncrements,name:string(200),price:decimal(10,4),ibsn:integer|unsigned|references(\'id\')|on(\'products\')|onDelete(\'restrict\')',
        ]);
    }

    public function testApi()
    {
        $file = $this->destinationDir.'/app/Http/Controllers/Api/BooksController.php';
        $this->assertFileExists($file);
        $contents = file_get_contents($file);
        $this->assertStringContainsString('class BooksController extends Controller', $contents);
    }

    public function testController()
    {
        $file = $this->destinationDir.'/app/Http/Controllers/BooksController.php';
        $this->assertFileExists($file);
        $contents = file_get_contents($file);
        $this->assertStringContainsString('class BooksController extends Controller', $contents);
    }

    public function testModels()
    {
        $file = $this->destinationDir.'/app/Models/Book.php';
        $this->assertFileExists($file);
        $contents = file_get_contents($file);
        $this->assertStringContainsString('class Book extends Model', $contents);
    }

    public function testSchema()
    {
        $files = glob($this->destinationDir.'/database/migrations/*_create_books_table.php');
        $this->assertFileExists($files[0]);
        $contents = file_get_contents($files[0]);
        $this->assertStringContainsString('$table->decimal(\'price\',10,4);', $contents);
        $this->assertStringContainsString('$table->integer(\'ibsn\')->unsigned()->references(\'id\')->on(\'products\')->onDelete(\'restrict\');', $contents);
    }

    public function testRequest()
    {
        $fileA = $this->destinationDir.'/app/Http/Requests/BookCreateRequest.php';
        $fileB = $this->destinationDir.'/app/Http/Requests/BookUpdateRequest.php';
        $this->assertFileExists($fileA);
        $this->assertFileExists($fileB);
        $contentsA = file_get_contents($fileA);
        $contentsB = file_get_contents($fileB);
        $this->assertStringContainsString('class BookCreateRequest extends FormRequest', $contentsA);
        $this->assertStringContainsString('class BookUpdateRequest extends FormRequest', $contentsB);
    }

    public function testService()
    {
        $file = $this->destinationDir.'/app/Services/BookService.php';
        $this->assertFileExists($file);
        $contents = file_get_contents($file);
        $this->assertStringContainsString('class BookService', $contents);
    }

    public function testRoutes()
    {
        $file = $this->destinationDir.'/routes/web.php';
        $contents = file_get_contents($file);

        $this->assertStringContainsString('BooksController', $contents);
        $this->assertStringContainsString('\'as\' => \'books.search\'', $contents);
        $this->assertStringContainsString('\'uses\' => \'BooksController@search\'', $contents);
    }

    public function testViews()
    {
        $fileA = $this->destinationDir.'/resources/views/books/index.blade.php';
        $contentsA = file_get_contents($fileA);
        $this->assertFileExists($fileA);
        $this->assertStringContainsString('$books', $contentsA);

        $fileB = $this->destinationDir.'/resources/views/books/edit.blade.php';
        $contentsB = file_get_contents($fileB);
        $this->assertFileExists($fileB);
        $this->assertStringContainsString('$book', $contentsB);
    }

    public function testTest()
    {
        $fileA = $this->destinationDir.'/tests/Feature/BookAcceptanceTest.php';
        $contentsA = file_get_contents($fileA);
        $this->assertFileExists($fileA);
        $this->assertStringContainsString('class BookAcceptanceTest', $contentsA);

        $fileB = $this->destinationDir.'/tests/Unit/BookServiceTest.php';
        $contentsB = file_get_contents($fileB);
        $this->assertFileExists($fileB);
        $this->assertStringContainsString('class BookServiceTest', $contentsB);
    }

    public function testFactory()
    {
        $file = $this->destinationDir.'/database/factories/bookFactory.php';
        $contents = file_get_contents($file);

        $this->assertStringContainsString('Book::class', $contents);
        $this->assertStringContainsString('$factory->define(', $contents);
    }

    public function tearDown(): void
    {
        $files = glob($this->destinationDir.'/database/migrations/*_create_books_table.php');
        @unlink($files[0]);
    }
}
