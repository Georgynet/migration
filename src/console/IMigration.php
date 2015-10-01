<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 01.10.15
 * Time: 13:21
 */

namespace Sllite\console;

/**
 * Интерфейс миграции.
 */
interface IMigration
{
    /**
     * Метод возвращающий запрос, выполняемый на применение миграции.
     * @return mixed
     */
    public function up();

    /**
     * Метод возвращающий запрос, для отмены миграции.
     * @return mixed
     */
    public function down();
}
