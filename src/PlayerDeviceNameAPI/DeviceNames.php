<?php

namespace PlayerDeviceNameAPI;

class DeviceNames{
	/** @var self */
	private static $instance;

	public static function getInstance() : self{
		if(!isset(self::$instance)){
			throw new \RuntimeException("PlayerDeviceNameAPI: DeviceNames::getInstance() cannot be invoke from onload");
		}
		return self::$instance;
	}

	/**
	 * ane-lx2j => Huawei,HUAWEI P20 Lite
	 * @var array<string, string>
	 */
	public static $androidDeviceList = [];
	/**
	 * amazon kfonwi => Fire HD 8 (2020, 第10世代)
	 * @var array<string, string>
	 */
	public static $iPhoneDeviceList = [];
	/**
	 * iPhone11,6 => iPhone XS Max
	 * @var array<string, string>
	 */
	public static $fireosDeviceList = [];

	public function __construct(string $resourceFolder){
		self::$androidDeviceList = json_decode(gzdecode(file_get_contents($resourceFolder."android.bin")), true);
		self::$iPhoneDeviceList = json_decode(file_get_contents($resourceFolder."iPhone_trim.json"), true);
		self::$fireosDeviceList = json_decode(file_get_contents($resourceFolder."fireos.json"), true);
	}

	public function get() : void{
		
	}
	
	public static function read() : string{

	}

	public static function readCompressed() : string{

	}
}
