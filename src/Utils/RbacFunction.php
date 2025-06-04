<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\Utils;

use Ideepler\HyperfCore\Exception\BusinessException;
use Ideepler\HyperfCore\Exception\InvalidArgumentException;

/**
 * Rbac通用方法
 */
class RbacFunction
{
	/**
	 * 返回权限唯一值.
	 * @param array $requestParams 请求相关参数.
	 * @return string
	 */
	public static function getRequestHash(array $requestParams): string
	{
		// 校验参数
		if(empty($requestParams['method'])){
			throw new InvalidArgumentException(10016, ['method']);
		}
		if(empty($requestParams['route'])){
			throw new InvalidArgumentException(10016, ['route']);
		}

		return md5($requestParams['method'].$requestParams['route']);
	}

	/**
	 * 判断权限集合中是否拥有某项权限
	 * @param array $permissionItem 待验证的权限项
	 * @param array $permissionList 当前拥有的权限
	 * @return bool
	 */
	public static function checkPermission(array $permissionItem, array $permissionList): bool
	{

		// 待校验权限为空，则视为通过
		if(empty($permissionItem)){
			return true;
		}

		// 如果权限组不存在，则视为没有权限
		if(empty($permissionList['rule_access'][$permissionItem['access_group_id']])){
			return false;
		}

		// 校验是否有授权
		if(($permissionItem['access_val'] & $permissionList['rule_access'][$permissionItem['access_group_id']]) > 0){
			return true;
		}

		return false;

	}

	/**
	 * 向权限集合中授予或追加权限
	 * @param array $grantList 授予权限的项列表
	 * @param array $permissionList 当前拥有的权限
	 * @return array
	 */
	public static function grantPermission(array $grantList, array $permissionList): array
	{

		// 授权为空，直接返回
		if(empty($grantList)){
			return $permissionList;
		}

		// 遍历授权
		foreach ($grantList as $grantItem){

			// 对操作权限进行赋值
			$ruleAccessVal = $permissionList['rule_access'][$grantItem['access_group_id']] ?? 0;
			$permissionList['rule_access'][$grantItem['access_group_id']] = $ruleAccessVal | $grantItem['access_val'];

			// 对按钮权限进行赋值存储
			if($grantItem['btn_access_val'] != 0){
				$permissionList['btn_access'][$grantItem['menu_id']] = $grantItem['btn_access_val'] ?? 0;
			}

		}

		return $permissionList;
	}

	/**
	 * 从权限集合中取消或移除权限
	 * @param array $cancelList 取消权限的项列表
	 * @param array $permissionList 当前拥有的权限
	 * @return array
	 */
	public static function cancelPermission(array $cancelList, array $permissionList): array
	{
		// 取消权限为空，直接返回
		if(empty($cancelList)){
			return $permissionList;
		}

		// 遍历取消授权
		foreach ($cancelList as $grantItem){

			// 对操作权限进行收回
			if(!empty($permissionList['rule_access'][$grantItem['access_group_id']])){
				$ruleAccessVal = $permissionList['rule_access'][$grantItem['access_group_id']];
				$ruleAccessVal = $ruleAccessVal ^ ($ruleAccessVal & $grantItem['access_val']);
				if($ruleAccessVal != 0){
					$permissionList['rule_access'][$grantItem['access_group_id']] = $ruleAccessVal;
				}else{
					unset($permissionList['rule_access'][$grantItem['access_group_id']]);
				}
			}

			// 对按钮权限进行消除
			if(isset($permissionList['btn_access'][$grantItem['menu_id']])){
				unset($permissionList['btn_access'][$grantItem['menu_id']]);
			}

		}

		return $permissionList;

	}

	/**
	 * 将允许权限合并到当前权限
	 * @param array $allowPermissionList 允许权限列表
	 * @param array $permissionList 当前拥有的权限
	 * @return array
	 */
	public static function mergeAllowPermission(array $allowPermissionList, array $permissionList): array
	{
		// 欲合并权限为空，直接返回
		if(empty($allowPermissionList)){
			return $permissionList;
		}

		// 遍历合并操作权限
		if(!empty($allowPermissionList['rule_access'])){
			foreach ($allowPermissionList['rule_access'] as $key => $val){
				// 对操作权限进行操作
				$ruleAccessVal = $permissionList['rule_access'][$key] ?? 0;
				$permissionList['rule_access'][$key] = $ruleAccessVal | $val;
			}
		}

		// 遍历合并按钮权限
		if(!empty($allowPermissionList['btn_access'])){
			foreach ($allowPermissionList['btn_access'] as $key => $val){
				// 对按钮权限进行操作
				$ruleAccessVal = $permissionList['btn_access'][$key] ?? 0;
				$permissionList['btn_access'][$key] = $ruleAccessVal | $val;
			}
		}

		return $permissionList;

	}

	/**
	 * 将拒绝权限合并到当前权限
	 * @param array $denyPermissionList 角色权限列表
	 * @param array $permissionList 当前拥有的权限
	 * @return array
	 */
	public static function mergeDenyPermission(array $denyPermissionList, array $permissionList): array
	{
		// 欲合并权限为空，直接返回
		if(empty($denyPermissionList)){
			return $permissionList;
		}

		// 遍历移除操作权限
		if(!empty($denyPermissionList['rule_access'])){
			foreach ($denyPermissionList['rule_access'] as $key => $val){
				// 对操作权限进行操作
				$ruleAccessVal = $permissionList['rule_access'][$key] ?? 0;
				$permissionList['rule_access'][$key] = $ruleAccessVal ^ ($ruleAccessVal & $val);
			}
		}

		// 遍历移除按钮权限
		if(!empty($denyPermissionList['btn_access'])){
			foreach ($denyPermissionList['btn_access'] as $key => $val){
				// 对按钮权限进行操作
				$ruleAccessVal = $permissionList['btn_access'][$key] ?? 0;
				$permissionList['btn_access'][$key] = $ruleAccessVal ^ ($ruleAccessVal & $val);
			}
		}

		return $permissionList;

	}

}
