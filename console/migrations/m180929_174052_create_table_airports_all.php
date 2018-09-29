<?php

use yii\db\Migration;

/**
 * Class m180929_174052_create_table_airports_all
 */
class m180929_174052_create_table_airports_all extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%all_airports}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(5)->notNull(),
            'name' => $this->string()->notNull(),

        ], $tableOptions);

        $this->createIndex('idx_all_airports_code', '{{%all_airports}}', 'code');
    }

    public function down()
    {
        $this->dropIndex('idx_all_airports_code', '{{%all_airports}}');
        $this->dropTable('{{%all_airports}}');
        echo "m180929_174052_create_table_airports_all cannot are reverted.\n";

        return truw;
    }

}
