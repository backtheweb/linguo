<?php
namespace Backtheweb\Linguo\Command;

use Config;

use Illuminate\Console\Command;
use League\Flysystem\Exception;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use \Backtheweb\LinguoUI\Models\Catalog;

class StoreCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linguo:store';
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
        $locale      = Config::get('app.locale');
        $i18nPath    = Config::get('linguo.i18nPath');

        $this->line(sprintf('<info>%s</info> on path <comment>%s</comment>', $locale, $i18nPath));

        $path  = $i18nPath . '/' . $locale;
        $files = glob($path .'/*.{php}', GLOB_BRACE);

        foreach($files as $file){

            $info = pathinfo($file);
            $data = include $file;

            $this->create($info['filename'], $locale, $data);
        }

        return 0;
    }

    protected function create($domain, $locale, $data){

        switch (true){

            case isset($data[$domain]): $format = 'php-array-po';   break;
            case is_array($data):       $format = 'php-array';      break;

            default:

                $format = 'undefined';
        }

        $this->line(sprintf('<info>%s</info> <comment>%s</comment>', $domain, $format ));

        $catalog = Catalog::updateOrCreate([
            'name'          => $domain,
            'locale'        => $locale,
            'source_format' => $format,
        ]);

        switch($format){

            case 'php-array':    $this->extract_array($catalog, $data);      break;
            case 'php-array-po': $this->extract_array_po($catalog, $data);   break;
        }
    }

    protected function extract_array_po(Catalog $catalog, array $source = []){

        $messages = $source[$catalog->name];

        unset($messages['']);

            foreach ($messages as  $key => $arr){

            }

        exit;
    }

    protected function extract_array(Catalog $catalog, array $source = []){

        foreach($source as $k => $v){

            if(is_array($v)){

                $msg = sprintf('<info>%s</info> <comment>%s</comment>', $k, '');
                print_r($msg);

            } else {

                $msg = sprintf('<info>%s</info> <comment>%s</comment>', $k, $v);
                $this->line($msg);
            }
        }
    }
}