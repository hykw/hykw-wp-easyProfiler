簡易的なプロファイル機能を提供するWordPressプラグイン
----------
経過時間をsyslogに出力します。

# 使い方

```php
$obj = new hykwEasyPF();
# $obj = new hykwEasyPF('PF');    # QUERY_STRING指定

$obj->start();    # 計測開始
$obj->lap();       # ラップタイムを出力(メッセージ省略)
$obj->lap('message1');       # ラップタイムを出力(メッセージ指定)
$obj->lap('message2');       # ラップタイムを出力(メッセージ指定)
$obj->stop();    # 計測終了
```

$obj = new hykwEasyPF('PF');
のように引数を指定した場合、QUERY_STRINGにその文字列が無い限り、実行しません(例：http://example.jp/?PF )
同一環境を複数人で実行している場合や、本番環境で計測する場合などに使うことを想定しています。

