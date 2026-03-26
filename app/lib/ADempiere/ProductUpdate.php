<?php
/**
 * Bean for updating "product update" to ADempiere via web service
 * 
 * Example of usage: 
 * 
 * include 'ADempiere/ProductUpdate.php';
 * 
 * $pr = new ProductUpdate();
 * 
 * $pr->setIsDiscontinued(false);
 * $pr->setIsSelfService(true);
 * $pr->setName('Nytt namn');
 * $pr->setDescription("beskrivning");
 * $pr->setNameShort('Kort namn');
 * $pr->setPriceLimit(10);
 * $pr->setPriceList(20);
 * $pr->setPriceStd(30);
 * $pr->setPriceListId(ProductUpdate::PRICELIST_SWEDEN);
 * $pr->setProductId(1023335);
 * $pr->setUpdatedBy(1000001); // NK
 * $pr->setSalesRepId(1000001); // NK
 * $pr->setUpdateTime('2014-05-20 07:00:00');
 * 
 * $res = $pr->add();
 * if ($res !== true) {
 * 	echo "error: " . $res;
 * }
 * 
 * 
 * @author nils
 * @version 1.0 2014-05-19
 *
 */
class ProductUpdate {
	var $clientId = 1000000;
	var $orgId = 1000000;
	var $updatedBy = 0;
	var $productId;
	var $updateTime;
	var $pricelistId;
	var $pricelistVersionId;
	var $priceList;
	var $priceStd;
	var $priceLimit;
	var $adLanguage;
	var $isSelfService;
	var $isDiscontinued;
	var $name;
	var $nameShort;
	var $description;
	var $salesRepId;
	var $client;
	const PRICELIST_SWEDEN = 1000000;
	const PRICELIST_FINLAND = 1000018;
	const PRICELIST_NORWAY = 1000280;
	
	/**
	 * Language for Swedish
	 */
	const AD_LANG_SE = null; // null when main language
	/**
	 * Language for Finnish
	 */
	const AD_LANG_FI = 'fi_FI';
	/**
	 * Language for Norwegian
	 */
	const AD_LANG_NO = 'no_NO';
	/**
	 * Link to web service
	 */
	public static $wsdlLink = "http://erp-services.cyberphoto.se:8080/adempiere-adempiereWs/ProductUpdateWs?wsdl";
	function __construct() {
		$this->client = new SoapClient ( self::$wsdlLink, array (
				'cache_wsdl' => WSDL_CACHE_NONE 
		) );
	}
	/**
	 * Client id, use if different than default
	 *
	 * @param integer $clientId        	
	 */
	function setClientId($clientId) {
		$this->clientId = $clientId;
	}
	/**
	 * Organization id, use if different than default
	 *
	 * @param integer $orgId        	
	 */
	function setOrgId($orgId) {
		$this->orgId = $orgId;
	}
	/**
	 * Product id, mandatory
	 *
	 * @param integer $productId        	
	 */
	function setProductId($productId) {
		$this->productId = $productId;
	}
	/**
	 * Id of creator (not mandatory)
	 *
	 * @param integer $updatedBy        	
	 */
	function setUpdatedBy($updatedBy) {
		$this->updatedBy = $updatedBy;
	}
	/**
	 * Price list id, use constant for id
	 *
	 * @param integer $priceListId        	
	 */
	function setPriceListId($priceListId) {
		$this->pricelistId = $priceListId;
	}
	/**
	 * Price list version id.
	 * Normally not used
	 *
	 * @param integer $priceListVersionId        	
	 */
	function setPricelistVersionId($priceListVersionId) {
		$this->pricelistVersionId = $priceListVersionId;
	}
	/**
	 * Price standard
	 *
	 * @param double $priceStd        	
	 */
	function setPriceStd($priceStd) {
		$this->priceStd = $priceStd;
	}
	/**
	 * Price limit
	 *
	 * @param double $priceLimit        	
	 */
	function setPriceLimit($priceLimit) {
		$this->priceLimit = $priceLimit;
	}
	/**
	 * Price list
	 *
	 * @param double $priceList        	
	 */
	function setPriceList($priceList) {
		$this->priceList = $priceList;
	}
	/**
	 * Language for update.
	 * Use constant to set.
	 * If omitted, default langauge will be used
	 *
	 * @param String $adLanguage        	
	 */
	function setAdLanguage($adLanguage) {
		$this->adLanguage = $adLanguage;
	}
	/**
	 * Update isSelfService of product
	 *
	 * @param boolean $isSelfService        	
	 */
	function setIsSelfService($isSelfService) {
		$this->isSelfService = $isSelfService ? 'Y' : 'N';
	}
	/**
	 * Update discontinued of product
	 *
	 * @param boolean $isDiscontinued        	
	 */
	function setIsDiscontinued($isDiscontinued) {
		$this->isDiscontinued = $isDiscontinued ? 'Y' : 'N';
	}
	/**
	 * Update name of product for selected language
	 *
	 * @param String $name        	
	 */
	function setName($name) {
		$this->name = $name;
	}
	/**
	 * Update name short for product
	 *
	 * @param String $nameShort        	
	 */
	function setNameShort($nameShort) {
		$this->nameShort = $nameShort;
	}
	/**
	 * Update description (comment) for product
	 *
	 * @param String $description        	
	 */
	function setDescription($description) {
		$this->description = $description;
	}
	/**
	 * Set salesrep for update, i.e.
	 * the sales rep that will receive email
	 * when update is executed
	 *
	 * @param unknown $salesRepId        	
	 */
	function setSalesRepId($salesRepId) {
		$this->salesRepId = $salesRepId;
	}
	/**
	 * Date/time when update shall occur
	 * Format 'YYYYmmdd HH:mm:ss'
	 *
	 * @param String $updateTime        	
	 */
	function setUpdateTime($updateTime) {
		$this->updateTime = $updateTime;
	}
	/**
	 * Shows functions on web service
	 */
	function showFunctions() {
		return $this->client->__getFunctions ();
	}
	/**
	 * Connects to web service to add productUpdate to ADempiere
	 *
	 * @throws Exception
	 * @return boolean
	 */
	function add() {
		if ($this->clientId == "" || $this->orgId == "" || $this->productId == "")
			throw new Exception ( "Client/org or product not set" );
		$res = $this->client->productUpdateNew ( ( array ) $this );
		if ($res->return == 'success')
			return true;
		else
			return $res->return;
	}
}

?>
