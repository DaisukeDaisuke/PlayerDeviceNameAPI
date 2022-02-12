<?php

namespace PlayerDeviceNameAPI;

class DataEnty{
	/** @var array<string, string> */
	public $list;

	/**
	 * @param array<string, string> $list
	 */
	public function __construct(array $list){
		$this->list = $list;
	}

	public function get(string $model) : string{
		return $this->list[$model];
	}

	/**
	 * @return array<string, string>
	 */
	public function getAll() : array{
		return $this->list;
	}
}