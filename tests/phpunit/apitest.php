<?php

use PlayerDeviceNameAPI\PlayerDeviceNameAPI as api;
use PHPUnit\Framework\TestCase;
use pocketmine\network\mcpe\protocol\types\DeviceOS;

class apitest extends TestCase{
	public function test(){
		api::loaddatas();

		api::$deviceModel["androiduser"] = [
			api::DEVICE_OS => DeviceOS::ANDROID,
			api::DEVICE_MODEL => "huawei ane-lx1",
		];
		api::$deviceModel["winuser"] = [
			api::DEVICE_OS => DeviceOS::WINDOWS_10,
			api::DEVICE_MODEL => "",
		];
		api::$deviceModel["iosuser"] = [
			api::DEVICE_OS => DeviceOS::IOS,
			api::DEVICE_MODEL => "iPhone12,8",
		];
		api::$deviceModel["switchuser"] = [
			api::DEVICE_OS => DeviceOS::NINTENDO,
			api::DEVICE_MODEL => "Switch",
		];
		api::$deviceModel["xboxuser"] = [
			api::DEVICE_OS => DeviceOS::XBOX,
			api::DEVICE_MODEL => "xbox_one_s",
		];

		//getDeviceOS
		self::assertEquals(DeviceOS::ANDROID,api::getDeviceOS("androiduser"));
		self::assertEquals(DeviceOS::WINDOWS_10,api::getDeviceOS("winuser"));
		self::assertEquals(DeviceOS::IOS,api::getDeviceOS("iosuser"));
		self::assertEquals(DeviceOS::NINTENDO,api::getDeviceOS("switchuser"));
		self::assertEquals(DeviceOS::XBOX,api::getDeviceOS("xboxuser"));
		self::assertEquals(null,api::getDeviceOS("unknownuser"));

		//getDeviceModel
		self::assertEquals("huawei ane-lx1",api::getDeviceModel("androiduser"));
		self::assertEquals("",api::getDeviceModel("winuser"));
		self::assertEquals("iPhone12,8",api::getDeviceModel("iosuser"));
		self::assertEquals("Switch",api::getDeviceModel("switchuser"));
		self::assertEquals("xbox_one_s",api::getDeviceModel("xboxuser"));
		self::assertEquals(null,api::getDeviceModel("unknownuser"));

		//getDeviceName
		self::assertEquals("Huawei,HUAWEI P20 Lite",api::getDeviceName("androiduser"));
		self::assertEquals(null,api::getDeviceName("winuser"));
		self::assertEquals("iPhone SE 2nd Generation",api::getDeviceName("iosuser"));
		self::assertEquals(null,api::getDeviceName("switchuser"));
		self::assertEquals(null,api::getDeviceName("xboxuser"));
		self::assertEquals(null,api::getDeviceName("unknownuser"));

		//getDevice
		self::assertEquals("Huawei,HUAWEI P20 Lite",api::getDevice("androiduser"));
		self::assertEquals("",api::getDevice("winuser"));
		self::assertEquals("iPhone SE 2nd Generation",api::getDevice("iosuser"));
		self::assertEquals(null,api::getDevice("switchuser"));
		self::assertEquals(null,api::getDevice("xboxuser"));
		self::assertEquals(null,api::getDevice("unknownuser"));

		//getDeviceNamebyDeviceModel
		self::assertEquals("Huawei,HUAWEI P20 Lite",api::getDeviceNamebyDeviceModel("huawei ane-lx1"));
		self::assertEquals(null,api::getDeviceNamebyDeviceModel(""));
		self::assertEquals("iPhone SE 2nd Generation",api::getDeviceNamebyDeviceModel("iPhone12,8"));
		self::assertEquals(null,api::getDeviceNamebyDeviceModel("Switch"));
		self::assertEquals(null,api::getDeviceNamebyDeviceModel("xbox_one_s"));
		self::assertEquals(null,api::getDeviceNamebyDeviceModel("unknown"));
	}
}