<?php

use yii\db\Migration;

/**
 * Class m210323_140555_add_column_iata_type_to_airports_table
 */
class m210323_140555_add_column_iata_type_to_airports_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%airports}}', 'iata_type', $this->string()->null()->comment('IATA Type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%airports}}', 'iata_type');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210323_140555_add_column_iata_type_to_airports_table cannot be reverted.\n";

        return false;
    }
    */
}
