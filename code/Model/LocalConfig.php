<?php

class Model_LocalConfig
{
    protected $_data = array();
    protected $_databaseAdapter;

    public function __construct()
    {
        $configJsonFile = dirname(dirname(dirname(__FILE__))) . "/etc/local.json";
        $json = file_get_contents($configJsonFile);
        $configArray = json_decode($json, true);

        $this->_data = $configArray;
    }

    public function get($key)
    {
        return (isset($this->_data[$key]) ? $this->_data[$key] : null);
    }

    public function configureDatabase()
    {
        $host = $this->get('database_host');
        $databaseName = $this->get('database_name');
        $databaseUser = $this->get('database_user');
        $databasePassword = $this->get('database_password');

        ORM::configure("mysql:host=$host;dbname=$databaseName");
        ORM::configure('username', $databaseUser);
        ORM::configure('password', $databasePassword);

        ORM::configure('id_column_overrides', array(
            'insightengine_tags' => 'tag_id',
        ));
    }

    public function getBaseUrl() { return $this->get('base_url'); }
    public function getBaseRoute() { return $this->get('base_route'); }
    public function getHideExceptions() { return $this->get('hide_exceptions'); }
    public function useHttpsOnly() { return $this->get('https_only'); }
}