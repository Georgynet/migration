<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 11:18
 */

return '<?php' . "\n" . '

use Sllite\console\IMigration;

class ' . $migrationName . ' implements IMigration
{
    public function up()
    {
        ' . (!empty($upSql) ? $upSql : '' ) . '
    }

    public function down()
    {
        ' . (!empty($downSql) ? $downSql : '' ) . '
    }
}
';
