<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Zabbix_curl{

	protected $CI;
	protected $user = "zabbixt";
	protected $password = "123456";
	protected $url = "http://zbx.huazhu.com/api_jsonrpc.php";
	protected $headers;
	protected $auth;

	public function __construct()
    {
        $this->CI =& get_instance();

        $this->headers[] = 'Content-Type: application/json';

        $json_array ['jsonrpc'] = "2.0";
		$json_array ['method'] = "user.authenticate";
		$json_array ['params']['user'] = $this->user;
		$json_array ['params']['password'] = $this->password;
		$json_array ['auth'] = NULL;
		$json_array ['id'] = 0;

		$post_data = json_encode($json_array);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
	    curl_setopt($ch, CURLOPT_URL,$this->url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_POST,1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    $auth_code_json = curl_exec($ch);

	    $auth_code_array = json_decode($auth_code_json,true);

	    $this->auth = $auth_code_array['result'];
    }

    protected function zabbix_curl_device($method,$params)
	{
		$post_json_array ['jsonrpc'] = "2.0";
		$post_json_array ['method'] = $method;
		$post_json_array ['params'] = $params;
		$post_json_array ['auth'] = $this->auth;
		$post_json_array ['id'] = 1;

		$post_data = json_encode($post_json_array);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
	    curl_setopt($ch, CURLOPT_URL,$this->url);
	    curl_setopt($ch, CURLOPT_TIMEOUT,0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_POST,1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    $json_result_array = json_decode(curl_exec($ch) , TRUE);

	    return $json_result_array['result'];
	}

	public function get_history($itemids_array = NULL,$time_from = 0,$time_till = 0)
	{
	    $method = "history.get";

	    $params['history'] = 0;
	    $params['itemids'] = $itemids_array;
	    $params['output'] = 'extend';
	    $params['time_from'] = $time_from;
	    $params['time_till'] = $time_till;

	    return $this->zabbix_curl_device($method,$params);
	}

	public function get_item($hostids_array = NULL , $groupids_array = NULL)
	{
	    $method = "item.get";

	    $params['history'] = 0;
	    $params['hostids'] = $hostids_array;
	    $params['groupids'] = $groupids_array;
	    
	    $params['output'] = 'extend';

	    return $this->zabbix_curl_device($method,$params);
	}

	public function get_item_from_name($name)
	{
	    $method = "item.get";

	    $params['history'] = 0;
	    $params['filter'] = array('name' => $name);
	    $params['output'] = 'extend';

	    return $this->zabbix_curl_device($method,$params);
	}

	public function get_item_from_group($group = NULL)
	{
	    $method = "item.get";

	    $params['history'] = 0;
	    $params['group'] = $group;
	    $params['output'] = 'extend';

	    return $this->zabbix_curl_device($method,$params);
	}

	public function get_hostgroup()
	{
		$method = "hostgroup.get";

	    $params['output'] = 'extend';

	    return $this->zabbix_curl_device($method,$params);
	}

	public function get_hostid($groupids_array = NULL)
	{
	    $method = "host.get";

	    $params['groupids'] = $groupids_array;
	    $params['output'] = 'extend';

	    return $this->zabbix_curl_device($method,$params);
	}

	public function get_group_name($groupids = NULL)
	{
		$method = "hostgroup.get";

		$params['groupids'] = $groupids;
	    $params['output'] = 'extend';

	    $result = $this->zabbix_curl_device($method,$params);
	    return $result[0]['name'];
	}

	public function get_host_name($hostids = NULL)
	{
	    $method = "host.get";

	    $params['hostids'] = $hostids;
	    $params['output'] = 'extend';

	    $result = $this->zabbix_curl_device($method,$params);
	    return $result[0]['name'];
	}

	public function get_item_name($itemids = NULL)
	{
	    $method = "item.get";

	    $params['itemids'] = $itemids;
	    $params['output'] = 'extend';

	    $result = $this->zabbix_curl_device($method,$params);
	    return $result[0]['name'];
	}
}