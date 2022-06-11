<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStaffTable extends AbstractMigration
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
        $table = $this->table('staff', ['signed' => false]);

        $table->addColumn('full_name', 'char', ['limit' => 255]);
        $table->addColumn('position_id', 'integer', ['signed' => false]);
        $table->addForeignKey('position_id', 'positions', 'id');
        $table->create();
    }
}
