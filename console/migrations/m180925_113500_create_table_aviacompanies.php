<?php

use yii\db\Migration;

/**
 * Class m180925_113500_create_table_aviacompanies
 */
class m180925_113500_create_table_aviacompanies extends Migration
{



    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%aviacompanies}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string()->notNull(),
            'name' => $this->string(),
            'name_translations' => $this->text(),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%aviacompanies}}');

        echo "m180925_113500_create_table_aviacompanies cannot are reverted.\n";

        return true;
    }

}
