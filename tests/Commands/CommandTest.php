<?php

class CommandTest extends TestCase
{
    public function testCrudMaker()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "table")');

         $this->app['Illuminate\Contracts\Console\Kernel']->handle(
             $input = new \Symfony\Component\Console\Input\ArrayInput([
                'command' => 'crudmaker:new',
                '--no-interaction' => true
             ]),
             $output = new \Symfony\Component\Console\Output\BufferedOutput
         );
    }

    public function testCrudTableMaker()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "table")');

         $this->app['Illuminate\Contracts\Console\Kernel']->handle(
             $input = new \Symfony\Component\Console\Input\ArrayInput([
                'command' => 'crudmaker:table',
                '--no-interaction' => true
             ]),
             $output = new \Symfony\Component\Console\Output\BufferedOutput
         );
    }
}
