<?php

namespace Backtheweb\Linguo;

use Symfony\Component\Translation\TranslatorInterface;

class Translator implements TranslatorInterface
{
    /** @var \Gettext\Translator  */
    protected $translator;

    /** @var array  */
    protected $translations = [];

    /** @var FileLoader */
    protected $loader = null;

    /** @var string  */
    protected $locale = 'en';

    /** @var   */
    protected $fallback = 'en';

    /**
     * Translator constructor.
     * @param LoaderInterface $loader
     * @param null $locale
     */
    public function __construct(LoaderInterface $loader, $locale)
    {
        $this->setDefaultLocale($this->locale);
        $this->loader     = $loader;
        $this->translator = new \Gettext\Translator();
    }

    /**
     * @param string $msgid
     * @param array $parameters
     * @param string $domain
     * @param null $locale
     * @return mixed|string
     */
    public function trans($msgid, array $parameters = array(), $domain = 'default', $locale = null)
    {

        $string = $this->translator->dgettext($domain, $msgid);
        $string = strtr($string, $parameters);

        return $string;
    }

    public function transChoice($msgid, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        throw new Exceptipon ('transchoice method is not supported');
    }

    /**
     * @param $msgid
     * @param $plural
     * @param int $count
     * @param array $args
     * @param string $domain
     * @param null $locale
     * @return mixed|string
     */
    public function plural($msgid, $plural, $count = 1, Array $args = [], $domain = 'default', $locale = null)
    {
        $string = $this->translator->dngettext($domain, $msgid, $plural, $count);

        if($args){

            $string = strtr($string, $args);

        } else {

            $string = strtr($string, [':count' => $count]);
        }

        return $string;
    }

    /**
     * @param $locale
     */
    protected function loadTranslations($locale, $domain = 'default')
    {

        if(!isset( $this->translations[$domain][$locale])){

            $this->translations[$domain][$locale] =  $this->loader->load($locale, $domain);
        }

        if(count(  $this->translations[$domain][$locale])){

            $this->translator->loadTranslations( $this->translations[$domain][$locale] );
        }
    }

    /**
     * @param $locale
     */
    public function setDefaultLocale($locale)
    {
        app()->setLocale($locale);

        $this->setLocale($locale);
        $this->loadTranslations($locale);
    }

    public function setFallback($fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     * @return FileLoader|LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return Linguo
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

}