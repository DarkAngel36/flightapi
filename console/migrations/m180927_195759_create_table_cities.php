<?php

use yii\db\Migration;

/**
 * Class m180927_195759_create_table_cities
 */
class m180927_195759_create_table_cities extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cities}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string()->notNull(),
            'name' => $this->string(),
            'coordinates' => $this->text(),
            'time_zone' => $this->string(),
            'name_translations' => $this->text(),
            'country_code' => $this->string(5),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_cities_code', '{{%cities}}', 'code');
        $this->createIndex('idx_cities_country_code', '{{%cities}}', 'country_code');
    }

    public function down()
    {
        $this->dropIndex('idx_cities_code', '{{%cities}}');
        $this->dropIndex('idx_cities_country_code', '{{%cities}}');
        $this->dropTable('{{%cities}}');
        echo "m180927_195759_create_table_cities cannot are reverted.\n";

        return true;
    }

}
