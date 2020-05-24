<?php

/**
 * 日期处理
 *
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

class Dates
{
    /**
     * 计算月有多少天
     *
     * @param $month
     * @param string $year
     * @return int
     */
    public function getMonthDays($month, $year = '')
    {
        $year = !$year ? date("Y") : $year;

        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
     * 得到当前日期+时间
     *
     * @return false|string
     */
    public function curdatetime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 得到当前时间
     *
     * @return false|string
     */
    public function curtime()
    {
        return date('H:i:s');
    }

    /**
     * 得到当前日期
     *
     * @return false|string
     */
    public function curdate()
    {
        return date('Y-m-d');
    }
}