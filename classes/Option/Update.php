<?php

class Option_Update extends App_Update
{
    const VERSION = '0.1.0';
    public static function getClassName() { return 'Option_Update'; }
    public static function TableClass() { return self::getClassName().'_Table'; }
    public static function Table() { $strClass = self::TableClass();  return new $strClass; }
    public static function TableName() { return self::Table()->getTableName(); }

    public function update()
    {
        if ( $this->isVersionBelow( '0.1.0' ) ) {
            $this->_install();
        }
        $this->save( self::VERSION );
    }
    /**
     * @return array
     */
    public static function getTables()
    {
        return array(
            Option_Hash::TableName(),
        );
    }

    protected function _install()
    {
        if (!$this->getDbAdapterRead()->hasTable('option_hash')) {
            Sys_Io::out('Creating Option Hash');
            
            $this->getDbAdapterWrite()->addTableSql('option_hash', '
                  `opt_id`          int(11)   NOT NULL AUTO_INCREMENT,
                  `opt_key`         char(64)  NOT NULL,
                  `opt_is_public`   int(2)    NOT NULL DEFAULT \'0\',
                  `opt_value`       text      NOT NULL DEFAULT \'\',
                  KEY `i_opt_key` (`opt_key`) ', 'opt_id' );
        }
    }

}
