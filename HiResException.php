<?php
/**
 * @link http://hiqdev.com/yii2-hiar
 * @copyright Copyright (c) 2015 HiQDev
 * @license http://hiqdev.com/yii2-hiar/license
 */

namespace hiqdev\hiar;

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
