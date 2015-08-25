<?php
/**
 * @link http://hiqdev.com/yii2-hiart
 * @copyright Copyright (c) 2015 HiQDev
 * @license http://hiqdev.com/yii2-hiart/license
 */

namespace hiqdev\hiart;

class HiArtException extends \yii\db\Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'HiPanel active record exception';
    }
}
