<?php

use yii\db\Migration;

/**
 * Class m180925_111044_create_table_airports
 */
class m180925_111044_create_table_airports extends Migration
{


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%airports}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'time_zone' => $this->string()->notNull(),
            'name_translations' => $this->text(),
            'country_code' => $this->string(3),
            'city_code' => $this->string(3),
            'code' => $this->string(3),
            'flightable' => $this->boolean(),
            'coordinates' => $this->text(),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%airports}}');
        echo "m180925_111044_create_table_airports cannot are reverted.\n";

        return true;
    }

}
