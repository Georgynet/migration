<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 11:18
 */

return '<?php

use Sllite\console\IMigration;

class ' . $migrationName . ' implements IMigration
{
    public function up()
    {
        // up migration
    }

    public function down()
    {
        // down migration
    }
}
';
