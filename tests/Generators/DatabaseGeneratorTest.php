<?php

use Grafite\CrudMaker\Generators\DatabaseGenerator;

class DatabaseGeneratorTest extends TestCase
{
    protected $generator;
    protected $config;
    protected $artisanMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->generator = new DatabaseGenerator();
        $this->command = Mockery::mock(\Illuminate\Console\Command::class);
        $this->command->shouldReceive('callSilent')->andReturnUsing(function ($command, $data) {
            \Artisan::call($command, $data);
        });
        $this->config = [
            '_path_migrations_' => base_path('database/migrations'),
            'relationships' => 'hasOne|App\Models\Author|author_id',
        ];
    }

    public function testCreateMigrationFail()
    {
        $this->expectException('Exception');

        $this->generator->createMigration($this->config, 'random_string', 'TestTable', 'another_random_string', $this->command);
    }

    public function testCreateMigrationSuccess()
    {
        $this->createMigration();
    }

    public function testCreateMigrationSuccessAlternativeLocation()
    {
        $config = [
            '_path_migrations_' => base_path('alternative_migrations_location'),
        ];

        $this->createMigration('alternative_migrations_location');
        $this->assertCount(1, glob(base_path('alternative_migrations_location').'/*'));
    }

    public function testCreateSchema()
    {
        $migrations = $this->createMigration();
        $schemaForm = $this->generator->createSchema(
            $this->config,
            '',
            'TestTable',
            [],
            'id:bigIncrements,name:string',
            $this->command
        );

        $this->assertStringContainsString('test_tables', file_get_contents($migrations[0]));
        $this->assertStringContainsString('table->bigIncrements(\'id\')', file_get_contents($migrations[0]));

        $this->assertStringContainsString('table->bigIncrements', $schemaForm);
        $this->assertStringContainsString('table->integer(\'author_id\')', $schemaForm);
        $this->assertStringContainsString('table->string(\'name\')', $schemaForm);
    }

    public function testCreateSchemaAlternativeLocation()
    {
        $migrations = $this->createMigration('alternative_migrations_location');

        $schemaForm = $this->generator->createSchema(
            $this->config,
            '',
            'TestTable',
            [],
            'id:bigIncrements,name:string',
            $this->command
        );

        $this->assertStringContainsString('table->bigIncrements', $schemaForm);
        $this->assertStringContainsString('table->string(\'name\')', $schemaForm);
    }

    private function createMigration($location = null)
    {
        if ($location) {
            $this->config = [
                '_path_migrations_' => base_path($location),
            ];
        }

        $migrationWasMade = $this->generator->createMigration($this->config, '', 'TestTable', [], $this->command);
        $migrations = glob($this->config['_path_migrations_'].'/*');

        $this->assertTrue($migrationWasMade);
        $this->assertCount(1, $migrations);

        return $migrations;
    }

    public function tearDown(): void
    {
        parent::tearDown();
        array_map('unlink', glob($this->config['_path_migrations_'].'/*'));
    }
}
