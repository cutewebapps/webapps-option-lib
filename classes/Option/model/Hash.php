<?php

class Option_Hash_Table extends DBx_Table
{
/**
 * database table name
 */
    protected $_name='option_hash';
/**
 * database table primary key
 */
    protected $_primary='opt_id';
}

class Option_Hash_List extends DBx_Table_Rowset
{
}

/**
 * Option_Hash should be a container for UI properties instead of config files
 */
class Option_Hash extends DBx_Table_Row
{
    public static function getClassName() { return 'Option_Hash'; }
    public static function TableClass() { return self::getClassName().'_Table'; }
    public static function Table() { $strClass = self::TableClass();  return new $strClass; }
    public static function TableName() { return self::Table()->getTableName(); }
    public static function FormClass( $name ) { return self::getClassName().'_Form_'.$name; }
    public static function Form( $name ) { $strClass = self::getClassName().'_Form_'.$name; return new $strClass; }

    /**
     * @var Option_Hash_List
     */
    public static $lstCached = null;
    
    /**
     * @return string 
     */
    public function getKey()
    {
        return $this->opt_key;
    }
    /**
     * @return mixed
     */
    public function getValue()
    {
        if ( $this->_isSerialized( $this->opt_value ) ) {
            return unserialize( $this->opt_value );
        }
        return $this->opt_value;
    }

    /**
     *
     * @param mixed $data
     * @return boolean
     */
    protected function _isSerialized( $data ) {
        // if it isn't a string, it isn't serialized
        if ( !is_string( $data ) )
            return false;
        $data = trim( $data );
        if ( 'N;' == $data )
            return true;
        if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
            return false;
        switch ( $badions[1] ) {
            case 'a' :
            case 'O' :
            case 's' :
                if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                    return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                    return true;
                break;
        }
        return false;
    }

    /**
     * @param string $strKey 
     * @param string $strDefault
     * @return string 
     */
    public static function get( $strKey, $strDefault = '')
    {

        if ( isset( self::$lstCached ) && isset( self::$lstCached [ $strKey ] ))
            return self::$lstCached [ $strKey ];

        $listRows = Option_Hash::Table()->fetchAll();
        self::$lstCached = array();
        foreach ( $listRows as $objOption ) {
             self::$lstCached [ $objOption->getKey() ] = $objOption->getValue();
        }

        if ( isset( self::$lstCached ) && isset( self::$lstCached [ $strKey ] ))
            return self::$lstCached [ $strKey ];

        return $strDefault;
    }

    /**
     *
     * @param string $strKey
     * @param string $strDefault
     * @return integer
     */
    public static function getInt( $strKey, $strDefault = 0 )
    {
        return intval( self::get( $strKey, $strDefault ));
    }

    /**
     * @param string $strKey
     * @param string $strValue
     */
    public static function set($strKey, $strValue = '' )
    {
        if ( is_array( $strKey ) ) {
            foreach ( $strKey as $key => $value ) Option_Hash::set( $key, $value );
            return;
        }

        $tbl = Option_Hash::Table();
        $select = $tbl->select()->where( 'opt_key = ?', $strKey );
            
        $objRow = $tbl->fetchRow( $select );
        if ( !is_object( $objRow ) ) {
            $objRow = $tbl->createRow();
            $objRow->opt_key = $strKey;
        }
        if ( !is_string( $strValue ) )
            $objRow->opt_value = serialize( $strValue );
        else
            $objRow->opt_value = $strValue;
        
        $objRow->save( false );
        self::$lstCached [ $strKey ] = $strValue;
    }

    public static function clean( $strPrefix )
    {
        $tbl = Option_Hash::Table();

        $select = $tbl->select()->where( 'opt_key LIKE ?', $strKey.'%' );
        $lstRows = $tbl->fetchAll( $select );
        foreach ( $lstRows as $objRow )
        {
            unset( self::$lstCached [ $objRow->opt_key ] );
            $objRow->delete();
        }
    }

    /**
     * @param string $strKey
     * @param string $strValue
     * @return boolean whether the property was set
     */
    public static function setIfPublic($strKey, $strValue )
    {
        $tbl = Option_Hash::Table();
        $select = $tbl->select()
                ->where( 'opt_key = ?', $strKey )
                ->where( 'opt_is_public = 1');

        $objRow = $tbl->fetchRow( $select );
        if ( is_object( $objRow ) ) {
            $objRow->opt_value = $strValue;
                    $objRow->save();
            self::$lstCached [ $strKey ] = $strValue;
            return true;
        } else {
            return false;
        }
    }
}
