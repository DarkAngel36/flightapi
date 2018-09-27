<?php

use yii\db\Migration;

/**
 * Class m180927_200951_create_table_countries
 */
class m180927_200951_create_table_countries extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%countries}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(5)->notNull(),
            'name' => $this->string()->notNull(),
            'currency' => $this->string(5),
            'name_translations' => $this->text(),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_countries_code', '{{%countries}}', 'code');
    }

    public function down()
    {
        $this->dropIndex('idx_countries_code', '{{%countries}}');
        $this->dropTable('{{%countries}}');
        echo "m180927_200951_create_table_countries cannot are reverted.\n";

        return true;
    }
}
