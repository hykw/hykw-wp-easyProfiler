<?php
  /*
    Plugin Name: HYKW easy Profiler
    Plugin URI: https://github.com/hykw/hykw-wp-easyProfiler
    Description: 簡易プロファイラ
    Author: Hitoshi Hayakawa
    version: 1.0.0
  */

class hykwEasyPF
{
  private $isRunnable;  # FALSEなら何もしない
  private $qs;          # QUERY_STRINGの値

  private $isStart;   # TRUEなら計測開始
  private $startTime; # 開始時間（epoch)
  private $lastTime;  # 前回の時間
  private $lapCounter;  # lap()が呼ばれた回数

  function __construct($qs = '')
  {
    $this->qs = $qs;
    $this->_init();
  }

  private function _init()
  {
    $qs = $this->qs;
    $this->isStart = FALSE;
    $this->lapCounter = 1;

    $this->isRunnable = FALSE;
    if ($qs === '')
      $this->isRunnable = TRUE;
    else {
      # QUERY_STRING と $qs の値が一致しないと実行を許可しない
      if (!isset($_SERVER['QUERY_STRING']))
        return;

      parse_str($_SERVER['QUERY_STRING'], $server_qs);
      if (!isset($server_qs[$qs]))
        return;

      $this->isRunnable = TRUE;
    }
  }

  function start()
  {
    if ($this->isRunnable === FALSE)
      return;

    if ($this->isStart)
      return;

    $this->isStart = TRUE;
    $microtime = microtime(TRUE);
    $this->startTime = $microtime;
    $this->lastTime = $microtime;

    $this->writeLog('');
    $log = "===== [hykwEasyPF] start";
    $this->writeLog($log);
  }

  /**
   * lap 
   * 
   * @param string $msg 出力するメッセージ
   */
  function lap($msg = '')
  {
    if ($this->isStart === FALSE)
      return;

    $now = microtime(TRUE);

    $diff_fromStart = ($now - $this->startTime);
    $diff_fromLast = ($now - $this->lastTime);

    $log = $this->getLogString($msg, $diff_fromLast, $diff_fromStart);
    $this->writeLog($log);

    $this->lastTime = $now;
    $this->lapCounter++;
  }

  function stop()
  {
    if ($this->isStart === FALSE)
      return;

    $log = '==================== end';
    $this->lap();
    $this->writeLog($log);
    $this->isStart = FALSE;

    $this->_init();
  }

  /**
   * getLogString ログに出力する文字列を取得する
   * 
   * @param string $msg 出力するメッセージ
   * @param float $diff_fromLast 前回のlapからの経過時間(ms)
   * @param float $diff_fromStart スタートからの経過時間(ms)
   */
  private function getLogString($msg, $diff_fromLast, $diff_fromStart)
  {
    $log = sprintf('[%d] ', $this->lapCounter);
    $log .= sprintf("elapse(start):%f, elapse(lastLap):%f", $diff_fromStart, $diff_fromLast);

    if ($msg != '')
      $log .= sprintf(' msg:%s ', $msg);

    return $log;
  }

  private function writeLog($log)
  {
    syslog(LOG_INFO, $log);
  }

}
