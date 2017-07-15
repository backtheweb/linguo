<?php
namespace Backtheweb\Linguo\Command;

use Config;

use Illuminate\Console\Command;
use Illuminate\Config\Repository;
use League\Flysystem\Exception;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Backtheweb\Linguo\Translator;

class ParseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linguo:parse';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all files where the argument is used as a lemma';

    /**
     * Folders to seek for missing translations
     *
     * @var  array
     */

    protected $sources = [];
    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $configRepository
     */

    protected $potPathName = null;

    /**
     * @var string
     */
    protected $potFile  = null;

    /**
     * @var string
     */
    protected $i18nPath = null;

    /** @var array  */
    protected $locales = [];

    /**
     * @return int
     * @throws Exception
     */
    public function fire()
    {
        $this->sources     = Config::get('linguo.sources');
        $this->potPathName = Config::get('linguo.i18nPath') . '/catalog.pot';
        $this->i18nPath    = Config::get('linguo.i18nPath');
        $this->locales     = Config::get('linguo.locales');

        $domain            = 'default';
        $translations      = null;
        $sources           = [];

        if(!$this->locales){

            throw new Exception('Locales not defined on config');
        }

        $reader = function($root) use (&$reader) {

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST,
                \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
            );

            $files = [];

            foreach ($iterator as $path => $file) {

                if ($file->isDir()) {

                    $f = $reader($file);

                    $files = array_merge($files, $f);

                } else {

                    $paths[] = (string) $file;
                }
            }

            return $paths;
        };

        if(is_file($this->potPathName)){
            unlink($this->potPathName);
        }

        touch($this->potPathName);

        /*
        \Gettext\Extractors\PhpCode::$functions['gettext']      = '__';
        \Gettext\Extractors\PhpCode::$functions['ngettext']     = 'plural';
        */

        \Gettext\Extractors\PhpCode::$functions['plural']     = 'n__';

        $pot = \Gettext\Translations::fromPoFile($this->potPathName);
        $pot->setDomain($domain);

        foreach($this->sources as $path) {

            $found    = $reader($path);
            $sources  = array_merge($sources, $found);
        }

        foreach($sources as $file){

            //$ext    = pathinfo($file, PATHINFO_EXTENSION);
            $count  = $pot->count();
            $string = file_get_contents($file);
            $type   = null;

            switch(true){

                case preg_match('/.blade.php/', $file):

                    $string = str_replace('{{', '<?php ', $string);
                    $string = str_replace('}}', ' ?>',    $string);

                    \Gettext\Extractors\PhpCode::fromString($string, $pot, $file);

                    $type = 'blade';

                    break;

                case preg_match('/.twig/', $file):

                    $string = str_replace('{{', '<?php ', $string);
                    $string = str_replace('}}', ' ?>',    $string);

                    \Gettext\Extractors\PhpCode::fromString($string, $pot, $file);
                    $type = 'twig';

                    break;

                case preg_match('/.php/', $file):

                    \Gettext\Extractors\PhpCode::fromString($string, $pot, $file);

                    $type = 'php';

                    break;

                default:

                    continue;
            }

            $count = $pot->count() - $count;
            $msg   = sprintf('<comment>%s</comment> %s <info>%s</info>', $file, $count > 0 ? "[$count]" : "<error>[$count]</error>", strtoupper($type));

            $this->line($msg);
        }

        $pot->toPoFile($this->potPathName);

        ////

        $msg = sprintf('<info>Done!</info> <comment>POT created</comment> Found %s translations', $pot->count());
        $this->line($msg);

        foreach($this->locales as $locale){

            $lang   = \Locale::getPrimaryLanguage($locale);
            $base   = $this->i18nPath . '/' . $lang . '/';

            if(!is_dir($base)){
                mkdir($base, '0777', true);
            }

            $poFile = $base .  $domain . '.po';
            $target = $base .  $domain . '.php';

            if(!is_file($poFile)){
                touch($poFile);
            }

            /** @var \Gettext\Translations $po */
            $po = \Gettext\Translations::fromPoFile($poFile);

            $msg = sprintf('%s %s <info>Done!</info>', $domain, $locale);
            $this->line($msg);
            $po->setLanguage($locale);
            $po->setDomain($domain);
            $po->setHeader('Project-Id-Version',        'ntradeshows');
            $po->setHeader('Language-Team',             'backtheweb <translators@backtheweb.com>');
            $po->setHeader('X-Poedit-SourceCharset',    'UTF-8');
            $po->mergeWith($pot, \Gettext\Translations::MERGE_ADD | \Gettext\Translations::MERGE_REMOVE | \Gettext\Translations::MERGE_COMMENTS | \Gettext\Translations::MERGE_REFERENCES | \Gettext\Translations::MERGE_PLURAL);
            $po->toPoFile($poFile);

            // Build

            $po->setDomain($domain);
            \Gettext\Generators\PhpArray::toFile($po, $target);

            $msg = sprintf('<info>Done!</info> Build translations for %s %s', $po->count(), $locale);
            $this->line($msg);
        }

        return 0;
    }
}