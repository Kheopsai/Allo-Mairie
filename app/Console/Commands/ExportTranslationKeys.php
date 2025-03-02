<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class ExportTranslationKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transkeys:export {locales?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all unique translation keys from App/View and resources/views to JSON files for specified locales in the lang directory.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $locales = $this->argument('locales');

        if (empty($locales)) {
            $input = $this->ask('Please specify the locales (e.g., ar fr en)');
            $locales = explode(' ', $input);
        }

        $langDir = lang_path();
        $directories = [
            app_path('View'),
            resource_path('views'),
        ];

        // Validate directories
        foreach ($directories as $dir) {
            if (! File::isDirectory($dir)) {
                $this->error("Directory not found: {$dir}");

                return Command::FAILURE;
            }
        }

        $finder = new Finder;
        $finder->files()
            ->in($directories)
            ->name('*.php');

        $translationKeys = [];

        foreach ($finder as $file) {
            $content = File::get($file->getRealPath());
            $this->extractKeys($content, '/trans\([\'"](.+?)[\'"]\)/', $translationKeys);
            $this->extractKeys($content, '/__\([\'"](.+?)[\'"]\)/', $translationKeys);
        }

        if (empty($translationKeys)) {
            $this->info('No translation keys found.');

            return Command::SUCCESS;
        }

        // Create translation structure with empty values
        $translations = array_fill_keys(array_keys($translationKeys), '');

        foreach ($locales as $locale) {
            $filePath = "{$langDir}/{$locale}.json";

            // Ensure language directory exists
            File::ensureDirectoryExists($langDir);

            file_put_contents(
                $filePath,
                json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );

            $this->info("Created translation file: {$filePath}");
        }

        return Command::SUCCESS;
    }

    /**
     * Extract and clean translation keys
     */
    protected function extractKeys(string $content, string $pattern, array &$keys): void
    {
        preg_match_all($pattern, $content, $matches);

        foreach ($matches[1] ?? [] as $key) {
            if (! empty($key)) {
                // Clean escaped quotes and backslashes
                $cleanKey = $this->cleanKey($key);
                $keys[$cleanKey] = true;
            }
        }
    }

    /**
     * Clean translation key from escape characters
     */
    protected function cleanKey(string $key): string
    {
        // Replace escaped single quotes with actual single quotes
        $key = str_replace("\\'", "'", $key);
        // Replace escaped double quotes with actual double quotes
        $key = str_replace('\\"', '"', $key);
        // Remove any remaining backslashes (careful with this)
        // $key = str_replace('\\', '', $key); // Uncomment if needed

        return $key;
    }
}
