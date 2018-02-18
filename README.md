# Bitfinex-API-V2-PHP
This project is designed to help you make your own PHP projects that interact with the [Bitfinex APIv2](https://docs.bitfinex.com/v2/reference) (Beta).

#### Getting started
```php
use apis\BitfinexClientV2;
$bitfinex_client = new BitfinexClientV2("<api_key>","<secret>");
```
You can get ```<api_key>``` and ```<secret>``` [using this page](https://www.bitfinex.com/api). If you need only public endpoints, you can use ```null``` for both.


#### Platform Status
Get the current status of the platform
```php
$platform_status = $bitfinex_client->get_platform_status();
```
<details>
 <summary>View Response</summary>

```
array (size=1)
  0 => int 1
```
1=operative, 0=maintenance

</details>

#### Get Tickers
The ticker is a high level overview of the state of the market. It shows you the current best bid and ask, as well as the last trade price. It also includes information such as daily volume and how much the price has moved over the last day.
```php
$tickers = $bitfinex_client->get_tickers(array('tBTCUSD', 'tLTCUSD', 'fUSD', ...));
```
You need to pass market symbols as parameters. Note: The symbols are different from symbols in v1. Use ```t<PAIR>``` for trading pairs (e.g. tBTCUSD), and ```f<CUR>``` for funding currencies (e.g. fUSD).
<details>
 <summary>View Response</summary>

```
array (size=3)
  0 => 
    array (size=11)
      0 => string 'tBTCUSD' (length=7)
      1 => int 10828
      2 => float 65.78772132
      3 => int 10829
      4 => float 120.09462879
      5 => int 157
      6 => float 0.0147
      7 => int 10828
      8 => float 72942.39401639
      9 => int 11250
      10 => int 10122
  1 => 
    array (size=11)
      0 => string 'tLTCUSD' (length=7)
      1 => float 220.68
      2 => float 1130.0676649
      3 => float 221.45
      4 => float 786.94620261
      5 => float -5.03
      6 => float -0.0222
      7 => float 221.46
      8 => float 321863.71924875
      9 => float 231.54
      10 => int 211
  2 => 
    array (size=14)
      0 => string 'fUSD' (length=4)
      1 => float 0.00037043
      2 => float 0.000285
      3 => int 30
      4 => float 2544856.0906782
      5 => float 0.00025357
      6 => int 2
      7 => float 202335.31417382
      8 => float 1.0E-5
      9 => float 0.04
      10 => float 0.00026
      11 => float 297334459.71289
      12 => int 0
      13 => int 0
```
</details>

#### Get Tickers with formatting
As the standard response is only array index, there is a function to get the formatted array (with keys).
```php
$tickers = $bitfinex_client->get_tickers_formatted(array('tBTCUSD', 'tLTCUSD', 'fUSD', ...));
```
<details>
 <summary>View Response</summary>

```
array (size=3)
  0 => 
    array (size=13)
      'ticker_type' => string 'trading' (length=7)
      'symbol' => string 'tBTCUSD' (length=7)
      'market' => string 'BTCUSD' (length=6)
      'bid' => int 10746
      'bid_size' => float 71.54374822
      'ask' => int 10747
      'ask_size' => float 35.53756893
      'daily_change' => float 34.9846636
      'daily_change_perc' => float 0.0033
      'last_price' => int 10747
      'volume' => float 72963.65501843
      'hight' => int 11250
      'low' => int 10122
  1 => 
    array (size=13)
      'ticker_type' => string 'trading' (length=7)
      'symbol' => string 'tLTCUSD' (length=7)
      'market' => string 'LTCUSD' (length=6)
      'bid' => float 218.08
      'bid_size' => float 545.91393065
      'ask' => float 218.19
      'ask_size' => float 808.06812952
      'daily_change' => float -9.12
      'daily_change_perc' => float -0.0402
      'last_price' => float 218.01
      'volume' => float 324392.37833162
      'hight' => float 231.54
      'low' => int 211
  2 => 
    array (size=15)
      'ticker_type' => string 'funding' (length=7)
      'symbol' => string 'fUSD' (length=4)
      'market' => string 'USD' (length=3)
      'bid' => float 0.00037031
      'bid_size' => float 0.000285
      'bid_period' => int 30
      'ask' => float 2544270.1706782
      'ask_size' => float 0.00024922
      'ask_period' => int 2
      'daily_change' => float 735563.22827726
      'daily_change_perc' => float -0.00010644
      'last_price' => float -0.2973
      'volume' => float 0.00025156
      'hight' => float 295514706.44457
      'low' => int 0
```
</details>

TODO: add more public endpoints.