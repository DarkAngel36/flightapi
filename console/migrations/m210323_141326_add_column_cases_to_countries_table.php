<?php

use yii\db\Migration;

/**
 * Class m210323_141326_add_column_cases_to_countries_table
 */
class m210323_141326_add_column_cases_to_countries_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%countries}}', 'cases', $this->string()->null()->comment('Cases'));
	    $this->addColumn('{{%cities}}', 'cases', $this->string()->null()->comment('Cases'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%countries}}', 'cases');
	    $this->dropColumn('{{%cities}}', 'cases');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210323_141326_add_column_cases_to_countries_table cannot be reverted.\n";

        return false;
    }
    */
}
