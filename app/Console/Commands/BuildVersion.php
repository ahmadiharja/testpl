<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use Storage;

class BuildVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build {version} {messages?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rootPath = base_path();
        
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        $archiveFile = storage_path("app/updates/remote_".$this->argument('version').".zip");
        $zip  = new ZipArchive;

        if ($zip->open($archiveFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
           
            // loop through all the files and add them to the archive.
            $cnt = 0;
            foreach ($files as $name => $file) {
                
                if (!$file->isDir()) {
                    
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    
                    if (!$this->isIgnore($relativePath)) {
                        echo $relativePath."\n";
                        // Add current file to archive
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }
            
            // close the archive.
            if ($zip->close()) {
                // archive is now downloadable ...
                //return response()->download($archiveFile, basename($archiveFile))->deleteFileAfterSend(true);
                $laraUpdate = '{
    "version": "'.$this->argument('version').'",
    "archive": "remote_'.$this->argument('version').'.zip",
    "description": "'.$this->argument('messages').'"
}';     
                if (Storage::exists('updates/laraupdater.json')) {
                    $old_versions = Storage::get('updates/laraupdater.json');
                } else {
                    $old_versions = '';
                }

                Storage::put('updates/laraupdater.json', $laraUpdate);
                Storage::append('updates/laraupdater.json.oldversions', $old_versions);

                // update current version
                file_put_contents(base_path().'/version.txt', $this->argument('version'));
            } else {
                throw new Exception("could not close zip file: " . $archive->getStatusString());
            }
        }

        
    }

    private function isIgnore($file) {
        $path = pathinfo($file);
        // ignore the top level files
        if ($path['dirname'] == '.') return true;

        $exclude_ext = array('rar', 'zip', 'log');
        $exclude_dir = array(
            '.', 'storage', 'bootstrap', 'fonts', 
            'installer', 'svg', 'tests', 'updates',
            
        );
        // start with . 
        foreach ($exclude_dir as $dir) {
            if (strpos($file, $dir) === 0) return true;
        }

        // Exclude files with specific extensions
        if (isset($path['extension']) && in_array($path['extension'], $exclude_ext)) {
            return true;
        }

        return false;

        if (in_array($path['extension'], $exclude_ext)) return true;
        return false;
    }
}
