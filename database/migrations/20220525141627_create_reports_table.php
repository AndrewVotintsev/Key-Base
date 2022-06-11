<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateReportsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('reports', ['signed' => false]);

        $table->addColumn('key', 'uuid');
        $table->addColumn('room_id', 'integer', ['signed' => false]);
        $table->addForeignKey('room_id', 'rooms', 'id');
        $table->addColumn('staff_id', 'integer', ['signed' => false]);
        $table->addForeignKey('staff_id', 'staff', 'id');
        $table->addColumn('date_of_issue', 'timestamp');
        $table->addColumn('date_return', 'timestamp', ['null' => true]);
        $table->create();
    }
}
