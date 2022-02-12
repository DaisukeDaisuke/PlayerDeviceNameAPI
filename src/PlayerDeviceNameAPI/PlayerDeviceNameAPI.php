<?php

namespace PlayerDeviceNameAPI;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
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

	public DeviceNames $deviceNames;

	public static $deviceModel = [];

	public function onLoad() : void{
		$this->deviceNames = new DeviceNames($this->getResourceFolder());
	}

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function getResourceFolder(): string{
		return $this->getFile()."resources".DIRECTORY_SEPARATOR;
	}

	/**
	 * @priority LOW
	 *
	 * @param PlayerLoginEvent $event
	 * @return void
	 */
	public function DataPacketReceive(PlayerLoginEvent $event){
		$packet = $event->getPlayer();
		$extraData = $packet->getPlayerInfo()->getExtraData();
		self::$deviceModel[$packet->getName()] = [
			self::DEVICE_OS => $extraData["DeviceOS"] ?? "",
			self::DEVICE_MODEL => $extraData["DeviceModel"] ?? DeviceOS::UNKNOWN,
		];
	}

	/**
	 * @priority HIGHEST
	 *
	 * @param PlayerQuitEvent $event
	 * @return void
	 */
	public function PlayerQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		unset(self::$deviceModel[$player->getName()]);
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
