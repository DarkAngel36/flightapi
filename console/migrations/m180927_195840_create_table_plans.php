<?php

use yii\db\Migration;

/**
 * Class m180927_195840_create_table_plans
 */
class m180927_195840_create_table_plans extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%planes}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string()->notNull(),
            'name' => $this->string(),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_plans_code', '{{%planes}}', 'code');
    }

    public function down()
    {
        $this->dropIndex('idx_plans_code', '{{%planes}}');
        $this->dropTable('{{%planes}}');
        echo "m180927_195840_create_table_plans cannot be reverted.\n";

        return true;
    }

}
