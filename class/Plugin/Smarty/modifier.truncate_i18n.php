<?php
/**
 *  smarty modifier:文字列切り詰め処理(i18n対応)
 *
 *  sample:
 *  <code>
 *  {"日本語です"|truncate_i18n:5:"..."}
 *  </code>
 *  <code>
 *  日本...
 *  </code>
 *
 *  @param  int     $len        最大文字幅
 *  @param  string  $postfix    末尾に付加する文字列
 */
function smarty_modifier_truncate_i18n($string, $len = 80, $postfix = "...")
{
    return mb_strimwidth($string, 0, $len, $postfix);
}
