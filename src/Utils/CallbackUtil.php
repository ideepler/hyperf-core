<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\Utils;

/**
 * 回调相关方法
 */
class CallbackUtil
{

	// 摘要干扰因子
	private static string $sundryFactor = 'kw4d';

	/**
	 * 回调URL后缀生成
	 * @param mixed $uniqueId 唯一标识.
	 * @param mixed $secretCode 安全码.
	 * @return string
	 */
	public static function segmentGenerate(mixed $uniqueId, mixed $secretCode): string
	{
		// 生成内层业务签名
		$innerSign = md5($uniqueId . $secretCode);
		// 生成外层公共签名
		$outerSign = md5($uniqueId . static::$sundryFactor . $innerSign);
		// 拼装URL
		return '/' . $outerSign . '-' . $innerSign . '/' . $uniqueId;
	}

	/**
	 * 外层签名验证
	 * @param mixed $uniqueId 唯一标识.
	 * @param mixed $innerSign 内层签名.
	 * @param mixed $outerSign 外层签名.
	 * @return bool
	 */
	public static function outerSignCheck(mixed $uniqueId, mixed $innerSign, mixed $outerSign): bool
	{
		// 校验签名
		if(md5($uniqueId . static::$sundryFactor . $innerSign) == $outerSign){
			return true;
		}
		return false;
	}

	/**
	 * 内层签名验证
	 * @param mixed $uniqueId 唯一标识.
	 * @param mixed $secretCode 安全码.
	 * @param mixed $innerSign 内层签名.
	 * @return bool
	 */
	public static function innerSignCheck(mixed $uniqueId, mixed $secretCode, mixed $innerSign): bool
	{
		// 校验签名
		if(md5($uniqueId . $secretCode) == $innerSign){
			return true;
		}
		return false;
	}
}