<?php

use yii\db\Migration;

/**
 * Class m180927_203958_add_relation
 */
class m180927_203958_add_relation extends Migration
{


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->createIndex('idx_airports_code', '{{%airports}}', 'code');
        $this->createIndex('idx_airports_city_code', '{{%airports}}', 'city_code');
        $this->createIndex('idx_airports_country_code', '{{%airports}}', 'country_code');
    }

    public function down()
    {
        $this->dropIndex('idx_airports_code', '{{%airports}}');
        $this->dropIndex('idx_airports_city_code', '{{%airports}}');
        $this->dropIndex('idx_airports_country_code', '{{%airports}}');
        echo "m180927_203958_add_relation cannot are reverted.\n";

        return true;
    }

}
