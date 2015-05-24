<?php
/**
 * @link http://hiqdev.com/yii2-hiart
 * @copyright Copyright (c) 2015 HiQDev
 * @license http://hiqdev.com/yii2-hiart/license
 */

namespace hiqdev\hiart;

/**
 * Exception represents an exception that is caused by elasticsearch-related operations.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class HiResException extends \yii\db\Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'HiActiveResource Database Exception';
    }
}
