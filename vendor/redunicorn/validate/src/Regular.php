<?php
/**
 * 正则类
 *
 * User: Jay
 * Date: 2018/7/3
 */

namespace RedUnicorn\Validate;


class Regular
{
    /**
     * 正则 邮箱
     */
    const REG_EMAIL = '/^[^\@]+@.*\.[a-z]{2,6}$/i';

    /**
     * 正则 手机号码
     */
    const REG_MOBILE = '/^1[2345789]{1}\d{9}$/';

    /**
     * 正则 WX号
     */
    const REG_WX_NUMBER = '/^[a-zA-Z0-9_-]{5,19}$/';

    /**
     * 正则 座机号码
     */
    const REG_TELEPHONE = '/(^0\d{2,}-\d{1,}$)|(^\d{2,}-\d{2,}-\d{1,}$)/';

    /**
     * 正则 QQ
     */
    const REG_QQ = '/^[1-9]\d{4,10}$/';

    /**
     * 正则 价格:2位小数
     */
    const REG_PRICE = '/^[0-9]+(.[0-9]{1,2})?$/';

    /**
     * 正则 密码
     */
    const REG_PASSWORD = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{5,18}$/';

    /**
     * 正则 名称
     */
    const REG_NAME = '/^[0-9a-zA-Zxa0-xff_]$/';

    /**
     * 正则 经度
     */
    const REG_LNG = '/^-?([1]?[1-7][1-9]|[1]?[1-8][0]|[1-9]?[0-9])\.{1}\d{1,6}$/';

    /**
     * 正则 维度
     */
    const REG_LAT = '/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}$/';

    /**
     * 正则 银行卡
     */
    const REG_BANK_CARD    = '/^\d{16,21}$/';

    /**
     * 正则 联系方式 (手机号以及座机号验证正则)
     */
    const REG_CONTACT_WAY = '/^((^0\d{2,}-\d{1,}$)|(^\d{2,}-\d{2,}-\d{1,}$))|(1[23456789]{1}\d{9})$/';

    /**
     * 正则 邮政编码
     */
    const REG_POSTCODE = '/^[1-9][0-9]{5}$/';

    /**
     * 正则 身份证号码
     */
    const REG_ID_CARD_NO = '/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/';

    /**
     * 正则 mac地址
     */
    const REG_MAC = '/^([A-Fa-f0-9]{2}[-,:]){5}[A-Fa-f0-9]{2}$/';

    /**
     * 正则 整数
     */
    const REG_INTEGER = '/^-?\\d+$/';
}