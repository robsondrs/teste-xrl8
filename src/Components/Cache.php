<?php

namespace GetawayFinder\Components;


class Cache 
{
    private static $time = '5 minutes';
    private $baseName = 'default_cache';
    private $folder;

    /**
     * Creates a new object of the class.
     *
     * @param string $baseName The base name of the object. Default is null.
     * @param string $folder The folder path for the object. Default is null.
     * @return void
     */
    public function __construct(string $baseName = null, string $folder = null) 
    {
        if (!is_null($baseName)) $this->baseName = $baseName;
        $this->setFolder(!is_null($folder) ? $folder : sys_get_temp_dir());
    }

    /**
     * Sets the folder for caching.
     *
     * @param string $folder The folder path to set for caching.
     * @return void
     */
    protected function setFolder($folder) 
    {
        // Se a pasta existir, for uma pasta e puder ser escrita
        if (file_exists($folder) && is_dir($folder) && is_writable($folder)) {
            $this->folder = $folder;
        } else {
            trigger_error('Não foi possível acessar a pasta de cache', E_USER_ERROR);
        }
    }

    /**
     * Generates the file location for a given key.
     *
     * @param string $key The key used to generate the file location.
     * @return string The generated file location.
     */
    protected function generateFileLocation($key) 
    {
        return $this->folder . DIRECTORY_SEPARATOR . sha1($this->baseName . '_' . $key) . '.tmp';
    }

    
    /**
     * Creates a cache file with the given key and content.
     *
     * @param string $key The key used to generate the file name.
     * @param mixed $content The content to be written to the cache file.
     * @return bool|int Returns the number of bytes written to the file if successful, or false if an error occurred.
     */
    protected function createCacheFile($key, $content) 
    {
        // Gera o nome do arquivo
        $filename = $this->generateFileLocation($key);
    
        // Cria o arquivo com o conteúdo
        return file_put_contents($filename, $content)
            OR trigger_error('Não foi possível criar o arquivo de cache', E_USER_ERROR);
    }

    /**
     * Sets a value in the cache.
     *
     * @param mixed $key The key of the cache item.
     * @param mixed $content The content to be stored in the cache item.
     * @param int|null $time The expiration time of the cache item. If null, the default expiration time will be used.
     * @return bool True if the cache item was successfully created, false otherwise.
     */
    public function set($key, $content, $time = null) 
    {
        $time = strtotime(!is_null($time) ? $time : self::$time);

        $content = serialize([
            'expires' => $time,
            'content' => $content
        ]);
    
        return $this->createCacheFile($key, $content);
    }

    /**
     * Retrieves the value associated with the given key from the cache.
     *
     * @param string $key The key to retrieve the value for.
     * @return mixed The value associated with the given key, or `false` if the key is not found or the cache has expired.
     */
    public function get($key) 
    {
        $filename = $this->generateFileLocation($key);
        if (file_exists($filename) && is_readable($filename)) {
            $cache = unserialize(file_get_contents($filename));
            if ($cache['expires'] > time()) {
                return $cache['content'];
            }

            unlink($filename);
        }

        return false;
    }
}
