# PlayerDeviceNameAPI

プレイヤー様の使用端末名を取得致します、PocketMine-MPプラグインにてございます。

## usage
```php
use PlayerDeviceNameAPI\PlayerDeviceNameAPI;
```
### getDevice
指定したプレイヤー様の使用端末名を取得します。  
サーバーにプレイヤー様は存在しない場合、「null」を返します。  
```php
PlayerDeviceNameAPI::getDevice(String);
```
応答例
```
Android: 「Sony,Xperia Z4 Tablet」「Huawei,HUAWEI P20 Lite」「Sony,Xperia Tablet Z」(メーカー名,機種名)
fireOS: 「Fire HD 8 (2020, 第10世代)」「Kindle Fire HD 8.9 (2012, 第2世代)(Wi-Fi)」「Kindle Fire HDX 8.9 (2013, 第3世代)(WAN)」(「機種名」(発売年, 第「世代」世代)「(WI-FI/WAN)」)
IOS: 「iPhone 11 Pro」「iPhone 8 Plus」
windows: [」(空文字)

未知の端末の場合、以下の値を返します。
Android: 「sony sot31」「huawei ane-lx1」「sony sgp311」
fireOS: 「amazon kfonwi」「amazon kfkawi」
IOS: 「iPhone12,8」「iPhone11,6」

サーバーにプレイヤー様は存在しない場合、「null」を返します。
「null」
```
### getDeviceName
指定したプレイヤー様の使用端末名を取得します。  
未知の端末や、ios、Android、FireOS以外の端末の場合(Windowsの場合)、「null」を返します。  
サーバーにプレイヤー様は存在しない場合、「null」を返します。  
```php
PlayerDeviceNameAPI::getDeviceName(String);
```
応答例
```
Android: 「Sony,Xperia Z4 Tablet」「Huawei,HUAWEI P20 Lite」「Sony,Xperia Tablet Z」(メーカー名,機種名)
fireOS: 「Fire HD 8 (2020, 第10世代)」「Kindle Fire HD 8.9 (2012, 第2世代)(Wi-Fi)」「Kindle Fire HDX 8.9 (2013, 第3世代)(WAN)」(「機種名」(発売年, 第「世代」世代)「(WI-FI/WAN)」)
IOS: 「iPhone 11 Pro」「iPhone 8 Plus」
windows: 「null」

未知の端末の場合、以下の値を返します。
「null」

サーバーにプレイヤー様は存在しない場合、「null」を返します。
「null」
```
### getDeviceNamebyDeviceModel
指定致しました、デバイスモデル名より、デバイスの名前を取得します。  
応答例
```
Android:「sony sot31」=>「Sony,Xperia Z4 Tablet」
fireOS:「amazon kfonwi」=>「Fire HD 8 (2020, 第10世代)」
IOS:「iphone12,8」=>「iPhone SE 2nd Generation」
Nintendo Switch:「switch」=>「Nintendo Switch」(1件)
xbox:「xbox_one_s」=>「Xbox One S」(1件)
PlayStation: 「null」
windows: 「null」

未知、未来の端末の場合、以下の値を返します。
「null」
```
### getDeviceOS
指定したプレイヤー様のデバイスOSを数値にて返します。  
  
```
public const UNKNOWN = -1;
public const ANDROID = 1;
public const IOS = 2;
public const OSX = 3;
public const AMAZON = 4;
public const GEAR_VR = 5;
public const HOLOLENS = 6;
public const WINDOWS_10 = 7;
public const WIN32 = 8;
public const DEDICATED = 9;
public const TVOS = 10;
public const PLAYSTATION = 11;
public const NINTENDO = 12;
public const XBOX = 13;
public const WINDOWS_PHONE = 14;
```
プレイヤー様はサーバーに存在しないを場合、「null」を返します。  
応答例  
```
「-1～14」
サーバーにプレイヤー様は存在しない場合、「null」を返します。
「null」
```
```php
PlayerDeviceNameAPI::getDeviceOS($name);
```
## example
```php
use PlayerDeviceNameAPI\PlayerDeviceNameAPI;
```
```php
public function join(PlayerJoinEvent $event){
   var_dump(PlayerDeviceNameAPI::getDevice($event->getPlayer()->getName()));
   var_dump(PlayerDeviceNameAPI::getDeviceName($event->getPlayer()->getName()));
   var_dump(PlayerDeviceNameAPI::getDeviceOS($event->getPlayer()->getName()));
   var_dump(PlayerDeviceNameAPI::getDeviceNamebyDeviceModel(PlayerDeviceNameAPI::getDeviceModel($event->getPlayer()->getName())));
}
```
#### windows
```
string(0) ""
NULL
int(7)
NULL
```
#### android
```
string(22) "Huawei,HUAWEI P20 Lite"
string(22) "Huawei,HUAWEI P20 Lite"
int(1)
string(22) "Huawei,HUAWEI P20 Lite"
```

## 情報ソース様 一覧
### ios (iPhone)
#### 【Swift,Objective-C】iOSデバイスのモデル名を取得する 例:iPhoneXS Maxとか【CocoaPodsもあるよ】   
https://qiita.com/MYamate_jp/items/9f26ad6f78f347ebd629  
  
### Android
#### サポートされているデバイス
https://support.google.com/googleplay/answer/1727131?hl=ja  
http://storage.googleapis.com/play_public/supported_devices.csv  

### Fire OS
以下のサイトに掲載致しましております、データシートのHTML解析の実施により、取得致しました。
https://developer.amazon.com/ja/docs/fire-tablets/ft-device-specifications-fire-models.html  
https://developer.amazon.com/ja/docs/fire-tablets/ft-device-specifications-firehdx-models.html  
https://developer.amazon.com/ja/docs/fire-tablets/ft-device-specifications-firehd-models.html  
