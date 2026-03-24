<?php 
require_once ('nusoap/nusoap.php');

/**
	Usage: for simple available stock run static function: AxTool::getAvailableStock('0160')
	For information on estimated delivery date, use function getStockInfo (non-static). 
	Delivery date will be found in $this->estimatedDeliveryDate. 
	
*/
class AxTool {
	var $key = "5E7BDC32-FF3C-4862-88E0-AEE09F840584";
	var $userName = "70874";
	var $url = "https://axtool.2020mobile.se/b2b/b2bservice.asmx?WSDL";
	var $errorMessage = "";
	var $qtyInStock = 0;
	var $estimatedDeliveryDate = "";
	var $ean = "";
	var $request = "";
	var $response = "";
	var $client;
	
	
	public static function getAvailableStock($vendorProdIdentifier) {
		$a = new AxTool();
		$a->getStockInfo($vendorProdIdentifier);
		if ($a->qtyInStock == "") {
			return "temporary error";
		} elseif ($a->qtyInStock == 0) {
			return date("Y-m-d",strtotime($a->estimatedDeliveryDate));
		} else {
			return $a->qtyInStock;
		}
	}
	
	public function getStockInfo($vendorProdIdentifier) {
	
		$this->client = new nusoap_client($this->url, true);
		$client->soap_defencoding = 'UTF-8'; 
		$data = $this->createXML($vendorProdIdentifier);
		$result = $this->client->call('GetStockInfo', $data);
		$xml = simplexml_load_string($result['GetStockInfoResult']);
		$json_string = json_encode($xml);
		$res = json_decode($json_string, TRUE);

		$this->qtyInStock = $res['StockInfo']['QuantityAvailable'];
		$this->estimatedDeliveryDate = $res['StockInfo']['DateConfirmed'];
		$this->ean = $res['StockInfo']['EANCode'];
		if ($this->client->getError() != "") {
			$this->qtyInStock=-1;
			$this->errorMessage = $this->client->getError();
			$this->request = $this->client->request;
			$this->response = $this->client->response;
		} else {
			$this->client = null;
		}
		return $a;
	}
	
	

	public function createXML($vendorProdIdentifier) {

		$data = "    <GetStockInfo xmlns=\"http://2020mobile.se/\">\r\n";
		$data.= "      <SecurityToken>" . $this->key . "</SecurityToken>\r\n";
		$data.= "      <UserName>" . $this->userName . "</UserName>\r\n";
		$data.= "      <Items>\r\n";
		$data.= "      <xs:schema id=\"B2BResponseDataSet\" targetNamespace=\"http://2020mobile.se/B2BResponseDataSet.xsd\" xmlns:mstns=\"http://2020mobile.se/B2BResponseDataSet.xsd\" xmlns=\"http://2020mobile.se/B2BResponseDataSet.xsd\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:msdata=\"urn:schemas-microsoft-com:xml-msdata\" attributeFormDefault=\"qualified\" elementFormDefault=\"qualified\">\r\n";
		$data.= "      <xs:element name=\"B2BResponseDataSet\" msdata:IsDataSet=\"true\" msdata:UseCurrentLocale=\"true\">\r\n";
		$data.= "      <xs:complexType>\r\n";
		$data.= "      <xs:choice minOccurs=\"0\" maxOccurs=\"unbounded\">\r\n";
		$data.= "      <xs:element name=\"Response\">\r\n";
		$data.= "      <xs:complexType>\r\n";
		$data.= "      <xs:sequence>\r\n";
		$data.= "      <xs:element name=\"Info\" type=\"xs:string\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"Status\" type=\"xs:int\" minOccurs=\"0\" />\r\n";
		$data.= "      </xs:sequence></xs:complexType></xs:element>\r\n";
		$data.= "      <xs:element name=\"Errors\"><xs:complexType><xs:sequence>\r\n";
		$data.= "      <xs:element name=\"ErrorMessage\" type=\"xs:string\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"ErrorNo\" type=\"xs:int\" minOccurs=\"0\" />\r\n";
		$data.= "      </xs:sequence></xs:complexType></xs:element>\r\n";
		$data.= "      <xs:element name=\"StockInfo\">\r\n";
		$data.= "      <xs:complexType>\r\n";
		$data.= "      <xs:sequence>\r\n";
		$data.= "      <xs:element name=\"StockCode\" type=\"xs:string\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"EANCode\" type=\"xs:string\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"ItemDescription1\" type=\"xs:string\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"ItemDescription2\" type=\"xs:string\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"ErrorMessage\" type=\"xs:string\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"CustomerStockCode\" type=\"xs:string\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"QuantityRequested\" type=\"xs:int\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"QuantityAvailable\" type=\"xs:int\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"DateRequested\" type=\"xs:dateTime\" minOccurs=\"0\" />\r\n";
		$data.= "      <xs:element name=\"DateConfirmed\" type=\"xs:dateTime\" minOccurs=\"0\" />\r\n";
		$data.= "      </xs:sequence>\r\n";
		$data.= "      </xs:complexType>\r\n";
		$data.= "      </xs:element>\r\n";
		$data.= "      </xs:choice>\r\n";
		$data.= "      </xs:complexType>\r\n";
		$data.= "      </xs:element>\r\n";
		$data.= "      </xs:schema>\r\n";
		$data.= "      <diffgr:diffgram xmlns:msdata=\"urn:schemas-microsoft-com:xml-msdata\" xmlns:diffgr=\"urn:schemas-microsoft-com:xml-diffgram-v1\">\r\n";
		$data.= "      <B2BResponseDataSet xmlns=\"http://2020mobile.se/B2BResponseDataSet.xsd\">\r\n";
		$data.= "      <StockInfo diffgr:id=\"StockInfo1\" msdata:rowOrder=\"0\" diffgr:hasChanges=\"inserted\">\r\n";
		$data.= "      <StockCode>" . $vendorProdIdentifier . "</StockCode>\r\n";
		$data.= "      </StockInfo>\r\n";
		$data.= "      </B2BResponseDataSet>\r\n";
		$data.= "      </diffgr:diffgram>\r\n";
		$data.= "      </Items>\r\n";
		$data.= "    </GetStockInfo>\r\n";		
		
		return $data;
	}
}


?>