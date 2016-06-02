<?php

use Illuminate\Database\Seeder;
use TeamTeaTime\Filer\LocalFile;

class LocalFilesHashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = LocalFile::whereNull('hash')->get();
        foreach ($items as $item) {
            $item->hash = $item->generateHash();
            $item->save();
        }
    }
}
