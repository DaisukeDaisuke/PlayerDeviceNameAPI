<?php

namespace PlayerDeviceNameAPI;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

//use PlayerDeviceNameAPI\PlayerDeviceNameAPI;

class PlayerDeviceNameAPI extends PluginBase implements Listener{
	/**
	 * 情報ソース様 一覧
	 * ios (iPhone)
	 * 【Swift,Objective-C】iOSデバイスのモデル名を取得する 例:iPhoneXS Maxとか【CocoaPodsもあるよ】
	 * https://qiita.com/takkyun/items/814aa45beee422a5f0c6
	 *
	 * Android
	 * (GooglePlay上にて)サポートされているデバイス
	 * https://support.google.com/googleplay/answer/1727131?hl=ja
	 * =========
	 * http://storage.googleapis.com/play_public/supported_devices.csv
	 *
	 * Fire OS
	 * 以下のサイトに掲載致しましております、データシートのHTML解析の実施により、取得致しました。
	 * https://developer.amazon.com/ja/docs/fire-tablets/ft-device-specifications-fire-models.html
	 * https://developer.amazon.com/ja/docs/fire-tablets/ft-device-specifications-firehdx-models.html
	 * https://developer.amazon.com/ja/docs/fire-tablets/ft-device-specifications-firehd-models.html
	 */

	const DEVICE_OS = 0;
	const DEVICE_MODEL = 1;

	public static $androidDeviceList = [];
	public static $iPhoneDeviceList = [];
	public static $fireosDeviceList = [];

	public static $deviceModel = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		self::$androidDeviceList = json_decode(gzdecode(file_get_contents($this->getResourceFolder()."android.bin")), true);
		self::$iPhoneDeviceList = json_decode(file_get_contents($this->getResourceFolder()."iPhone.json"), true);
		self::$fireosDeviceList = json_decode(file_get_contents($this->getResourceFolder()."fireos.json"), true);
	}

	public function getResourceFolder(): string{
		return $this->getFile()."resources".DIRECTORY_SEPARATOR;
	}

	public function DataPacketReceive(DataPacketReceiveEvent $event){
		if(!$event->getPacket() instanceof LoginPacket) return;
		$packet = $event->getPacket();
		self::$deviceModel[TextFormat::clean($packet->username)] = [
			self::DEVICE_OS => $packet->clientData["DeviceOS"],
			self::DEVICE_MODEL => $packet->clientData["DeviceModel"]
		];
	}

	/**
	 * 指定したプレイヤー様のデバイスOSを数値にて返します。
	 * プレイヤー様はサーバーに存在しないを場合、「null」を返します。
	 * 内部的に使用致します。
	 *
	 * @param String $name
	 * @return int|null
	 */
	public static function getDeviceOS(string $name): ?int{
		return self::$deviceModel[$name][self::DEVICE_OS] ?? null;
	}

	/**
	 * デバイスモデルを返します。
	 * プレイヤー様はサーバーに存在しないを場合、「null」を返します。
	 * 多くの場合、以下のような形式にて返します。
	 *「メーカー名 端末型番」
	 *
	 * Android: 「sony sot31」「huawei ane-lx1」「sony sgp311」
	 * fireOS: 「amazon kfonwi」「amazon kfkawi」
	 * IOS: 「iPhone12,8」「iPhone11,6」
	 * windows: 「mousecomputer co.,ltd. z170-s01」
	 *
	 * @param String $name
	 * @return String|null
	 */
	public static function getDeviceModel(string $name): ?string{
		return self::$deviceModel[$name][self::DEVICE_MODEL] ?? null;
	}

	/**
	 * デバイスの名前を取得を取得します。
	 * デバイスはwindowsの場合や、デバイスの名前を取得できない場合、「null」を返します。
	 * windowsのデバイス名も取得したい場合、代わりに「getDevice」関数の使用もご検討願います。
	 * 以下のような形式にて返します。
	 * 「メーカー名,機種名」
	 *「Sony,Xperia Z4 Tablet」「Huawei,HUAWEI P20 Lite」「Sony,Xperia Tablet Z」
	 *
	 * @param String $name
	 * @return String|null
	 */
	public static function getDeviceName(string $name): ?string{
		$deviceModel = mb_strtolower(self::getDeviceModel($name));
		switch(self::getDeviceOS($name)){
			case DeviceOS::ANDROID:
				return self::$androidDeviceList[$deviceModel] ?? null;
			case DeviceOS::IOS:
				return self::$iPhoneDeviceList[$deviceModel] ?? null;
			case DeviceOS::AMAZON:
				return self::$fireosDeviceList[$deviceModel] ?? null;
			default:
				return null;
		}
	}

	/**
	 * デバイスの名前を取得を取得します。
	 * サーバーにプレイヤー様は存在しない場合、「null」を返します。
	 *
	 * Android: 「Sony,Xperia Z4 Tablet」「Huawei,HUAWEI P20 Lite」「Sony,Xperia Tablet Z」
	 * fireOS: 「Fire HD 8 (2020, 第10世代)」「Kindle Fire HD 8.9 (2012, 第2世代)(Wi-Fi)」
	 * IOS: 「iPhone 11 Pro」「iPhone 8 Plus」
	 *
	 * windowsの場合、「マザーボードの製造先(コンビューターの製造メーカー名) マザーボードの番型」 を返します。
	 * 例:「mousecomputer co.,ltd. z170-s01」
	 *
	 * 未来の端末の場合、以下の値を返します。
	 * Android: 「sony sot31」「huawei ane-lx1」「sony sgp311」
	 * fireOS: 「amazon kfonwi」「amazon kfkawi」
	 * IOS: 「iPhone12,8」「iPhone11,6」「iPhone 3GS」
	 *
	 * @param String $name
	 * @return String|null
	 */
	public static function getDevice(string $name): ?string{
		return self::getDeviceName($name) ?? self::getDeviceModel($name) ?? null;
	}

	/**
	 * @return array
	 */
	public static function getAndroidDeviceList(): array{
		return self::$androidDeviceList;
	}

	/**
	 * @return array
	 */
	public static function getIPhoneDeviceList(): array{
		return self::$iPhoneDeviceList;
	}

	/**
	 * @return array
	 */
	public static function getFireosDeviceList(): array{
		return self::$fireosDeviceList;
	}
}
