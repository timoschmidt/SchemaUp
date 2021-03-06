<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Timo Schmidt <timo-schmidt@gmx.net>
*  All rights reserved
*
*  This copyright notice MUST APPEAR in all copies of the script!
****************************************************************/

/**
 * Testcase to test the functionallity of the schema class
 * 
 * @package SchemaUp
 * @subpackage Tests\Mocked\Domain\Database
 * @author Timo Schmidt <timo-schmidt@gmx.net>
 */
class Mocked_Domain_Database_TableTestcase extends Mocked_AbstractMockedTestcase {
	
	/**
	 * @var $table Domain_Database_Table_Factory
	 */
	protected $factory;
	
	/**
	 * @return void
	 */
	public function setUp() {
		$this->factory = new Domain_Database_Table_Factory();
	}
	
	/**
	 * Dataprovider with a create table statement,
	 * and the expected number of fields that should
	 * be created, when the schema gets parsed.
	 * 
	 * @return array
	 */
	public function extractFieldsDataProvider() {
		return array(
			array(
					//magento widget table
				'createTableSchema' => 
					"CREATE TABLE `widget` (
						  `widget_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
						  `code` varchar(255) NOT NULL,
						  `type` varchar(255) NOT NULL,
						  `parameters` text,
						  PRIMARY KEY (`widget_id`),
						  KEY `IDX_CODE` (`code`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Preconfigured Widgets' ",
				'expectedNumberOfFields' => 4
			), 
			array(
				'createTableSchema' => "
					CREATE TABLE `coupon_aggregated` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `period` date NOT NULL DEFAULT '0000-00-00',
						  `store_id` smallint(5) unsigned DEFAULT NULL,
						  `order_status` varchar(50) NOT NULL DEFAULT '',
						  `coupon_code` varchar(50) NOT NULL DEFAULT '',
 						 `coupon_uses` int(11) NOT NULL DEFAULT '0',
						  `subtotal_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
						  `discount_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
						  `total_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
						  `subtotal_amount_actual` decimal(12,4) NOT NULL DEFAULT '0.0000',
						  `discount_amount_actual` decimal(12,4) NOT NULL DEFAULT '0.0000',
						  `total_amount_actual` decimal(12,4) NOT NULL DEFAULT '0.0000',
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `UNQ_COUPON_AGGREGATED_PSOC` (`period`,`store_id`,`order_status`,`coupon_code`),
						  KEY `IDX_STORE_ID` (`store_id`),
						  CONSTRAINT `FK_SALESTRULE_COUPON_AGGREGATED_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
					) ENGINE=InnoDB DEFAULT CHARSET=utf8				
				",
				"expectedNumberOfFields" => 12
			),
			array(
				'createTableSchema' => "CREATE TABLE `sys_domain` (
 					 `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `pid` int(11) unsigned NOT NULL DEFAULT '0',
					  `tstamp` int(11) unsigned NOT NULL DEFAULT '0',
					  `crdate` int(11) unsigned NOT NULL DEFAULT '0',
					  `cruser_id` int(11) unsigned NOT NULL DEFAULT '0',
					  `hidden` tinyint(4) unsigned NOT NULL DEFAULT '0',
					  `domainName` varchar(80) NOT NULL,
					  `redirectTo` varchar(120) NOT NULL,
					  `redirectHttpStatusCode` int(4) unsigned NOT NULL DEFAULT '301',
					  `sorting` int(10) unsigned NOT NULL DEFAULT '0',
					  `prepend_params` int(10) NOT NULL DEFAULT '0',
					  `forced` tinyint(3) unsigned NOT NULL DEFAULT '0',
					  PRIMARY KEY (`uid`),
					  KEY `parent` (`pid`),
					  KEY `tx_realurl` (`domainName`,`hidden`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 ",
				"expectedNumberOfFields" => 12
			), 
			array(
				'createTableSchema' => "
					CREATE TABLE `coupon_aggregated` (
						  `subtotal_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
						  CONSTRAINT `FK_SALESTRULE_COUPON_AGGREGATED_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
					) ENGINE=InnoDB DEFAULT CHARSET=utf8				
				",
				"expectedNumberOfFields" => 1
			),
			array(
				'createTableSchema' => "CREATE TABLE `catalog_product_option_type_price` (
					  `option_type_price_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `option_type_id` int(10) unsigned NOT NULL DEFAULT '0',
					  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
					  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
					  `price_type` enum('fixed','percent') NOT NULL DEFAULT 'fixed',
					  PRIMARY KEY (`option_type_price_id`),
					  UNIQUE KEY `UNQ_OPTION_TYPE_STORE` (`option_type_id`,`store_id`),
					  KEY `CATALOG_PRODUCT_OPTION_TYPE_PRICE_OPTION_TYPE` (`option_type_id`),
					  KEY `CATALOG_PRODUCT_OPTION_TYPE_PRICE_STORE` (`store_id`),
					  CONSTRAINT `FK_CATALOG_PRODUCT_OPTION_TYPE_PRICE_OPTION` FOREIGN KEY (`option_type_id`) REFERENCES `catalog_product_option_type_value` (`option_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
					  CONSTRAINT `FK_CATALOG_PRODUCT_OPTION_TYPE_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
				"expextedNumberOfFields" => 5

			)
		);
	}
	
	/**
	 * Testcase to check if a field collection with the expected number of fields
	 * get retrieved.
	 * 
	 * @dataProvider extractFieldsDataProvider
	 * @test
	 */
	public function getFields($createTableSql, $expectedNumberOfFields) {
		$table = $this->factory->createFromSql($createTableSql);	
		$this->assertEquals($table->getFields()->count(),$expectedNumberOfFields);
	}
	
	/**
	 * This testcase is used to test if the table class throws an exception when
	 * an INSERT INTO statement instead of an CREATE TABLE statement gets passed.
	 * 
	 * @test
	 * @expectedException Exception_Parsing_CreateTable
	 */
	public function thowsExceptionForInsertStatement() {
		$statement	= "INSERT INTO `app_de_commandlog` VALUES (4438)";
		$table		= $this->factory->createFromSql($statement);
	}
}
