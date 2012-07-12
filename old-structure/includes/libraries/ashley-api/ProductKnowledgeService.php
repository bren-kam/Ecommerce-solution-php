<?php

if (!class_exists("GetWarehouseClassDetails")) {
/**
 * GetWarehouseClassDetails
 */
class GetWarehouseClassDetails {
	/**
	 * @access public
	 * @var WarehouseClassDetailRequest
	 */
	public $request;
}}

if (!class_exists("RequestBase")) {
/**
 * RequestBase
 */
class RequestBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ClientTag;
	/**
	 * @access public
	 * @var sstring
	 */
	public $AccessToken;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Version;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ClientCulture;
	/**
	 * @access public
	 * @var ArrayOfString
	 */
	public $EnvironmentCodes;
	/**
	 * @access public
	 * @var sstring
	 */
	public $RequestId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $UserName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Password;
	/**
	 * @access public
	 * @var sstring
	 */
	public $MachineName;
	/**
	 * @access public
	 * @var tnsTransportDataType
	 */
	public $TransportDataType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LoadOptions;
	/**
	 * @access public
	 * @var tnsPersistType
	 */
	public $Action;
}}

if (!class_exists("TransportDataType")) {
/**
 * TransportDataType
 */
class TransportDataType {
}}

if (!class_exists("PersistType")) {
/**
 * PersistType
 */
class PersistType {
}}

if (!class_exists("CriteriaBase")) {
/**
 * CriteriaBase
 */
class CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SortExpression;
}}

if (!class_exists("WarehouseClassDetailExecuteOption")) {
/**
 * WarehouseClassDetailExecuteOption
 */
class WarehouseClassDetailExecuteOption {
}}

if (!class_exists("ServiceData")) {
/**
 * ServiceData
 */
class ServiceData {
	/**
	 * @access public
	 * @var tnsTransportDataType
	 */
	public $TransportDataType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $XmlData;
	/**
	 * @access public
	 * @var anyType
	 */
	public $XmlDocumentData;
	/**
	 * @access public
	 * @var sbase64Binary
	 */
	public $ByteArrayData;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PlistData;
}}

if (!class_exists("XmlDocumentData")) {
/**
 * XmlDocumentData
 */
class XmlDocumentData {
}}

if (!class_exists("GetWarehouseClassDetailsResponse")) {
/**
 * GetWarehouseClassDetailsResponse
 */
class GetWarehouseClassDetailsResponse {
	/**
	 * @access public
	 * @var WarehouseClassDetailResponse
	 */
	public $GetWarehouseClassDetailsResult;
}}

if (!class_exists("ResponseBase")) {
/**
 * ResponseBase
 */
class ResponseBase {
	/**
	 * @access public
	 * @var tnsAcknowledgeType
	 */
	public $Acknowledge;
	/**
	 * @access public
	 * @var sstring
	 */
	public $CorrelationId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Message;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ReservationId;
	/**
	 * @access public
	 * @var sdateTime
	 */
	public $ReservationExpires;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Version;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Build;
	/**
	 * @access public
	 * @var sint
	 */
	public $RowsAffected;
}}

if (!class_exists("AcknowledgeType")) {
/**
 * AcknowledgeType
 */
class AcknowledgeType {
}}

if (!class_exists("SetWarehouseClassDetails")) {
/**
 * SetWarehouseClassDetails
 */
class SetWarehouseClassDetails {
	/**
	 * @access public
	 * @var WarehouseClassDetailRequest
	 */
	public $request;
}}

if (!class_exists("SetWarehouseClassDetailsResponse")) {
/**
 * SetWarehouseClassDetailsResponse
 */
class SetWarehouseClassDetailsResponse {
	/**
	 * @access public
	 * @var WarehouseClassDetailResponse
	 */
	public $SetWarehouseClassDetailsResult;
}}

if (!class_exists("GetWarehouseClassHeaders")) {
/**
 * GetWarehouseClassHeaders
 */
class GetWarehouseClassHeaders {
	/**
	 * @access public
	 * @var WarehouseClassHeaderRequest
	 */
	public $request;
}}

if (!class_exists("WarehouseClassHeaderRequest")) {
/**
 * WarehouseClassHeaderRequest
 */
class WarehouseClassHeaderRequest extends RequestBase {
	/**
	 * @access public
	 * @var WarehouseClassHeaderCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfWarehouseClassHeaderExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassHeader;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassHeadersCollection;
}}

if (!class_exists("WarehouseClassHeaderCriteria")) {
/**
 * WarehouseClassHeaderCriteria
 */
class WarehouseClassHeaderCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $WhsDefaultType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
}}

if (!class_exists("WarehouseClassHeaderExecuteOption")) {
/**
 * WarehouseClassHeaderExecuteOption
 */
class WarehouseClassHeaderExecuteOption {
}}

if (!class_exists("GetWarehouseClassHeadersResponse")) {
/**
 * GetWarehouseClassHeadersResponse
 */
class GetWarehouseClassHeadersResponse {
	/**
	 * @access public
	 * @var WarehouseClassHeaderResponse
	 */
	public $GetWarehouseClassHeadersResult;
}}

if (!class_exists("WarehouseClassHeaderResponse")) {
/**
 * WarehouseClassHeaderResponse
 */
class WarehouseClassHeaderResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassHeader;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassHeadersCollection;
}}

if (!class_exists("SetWarehouseClassHeaders")) {
/**
 * SetWarehouseClassHeaders
 */
class SetWarehouseClassHeaders {
	/**
	 * @access public
	 * @var WarehouseClassHeaderRequest
	 */
	public $request;
}}

if (!class_exists("SetWarehouseClassHeadersResponse")) {
/**
 * SetWarehouseClassHeadersResponse
 */
class SetWarehouseClassHeadersResponse {
	/**
	 * @access public
	 * @var WarehouseClassHeaderResponse
	 */
	public $SetWarehouseClassHeadersResult;
}}

if (!class_exists("GetWarehouseClassSites")) {
/**
 * GetWarehouseClassSites
 */
class GetWarehouseClassSites {
	/**
	 * @access public
	 * @var WarehouseClassSiteRequest
	 */
	public $request;
}}

if (!class_exists("WarehouseClassSiteRequest")) {
/**
 * WarehouseClassSiteRequest
 */
class WarehouseClassSiteRequest extends RequestBase {
	/**
	 * @access public
	 * @var WarehouseClassSiteCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfWarehouseClassSiteExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassSite;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassSitesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentDropDownsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SiteDropDownsCollection;
}}

if (!class_exists("WarehouseClassSiteCriteria")) {
/**
 * WarehouseClassSiteCriteria
 */
class WarehouseClassSiteCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $RecordId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SiteType;
}}

if (!class_exists("WarehouseClassSiteExecuteOption")) {
/**
 * WarehouseClassSiteExecuteOption
 */
class WarehouseClassSiteExecuteOption {
}}

if (!class_exists("GetWarehouseClassSitesResponse")) {
/**
 * GetWarehouseClassSitesResponse
 */
class GetWarehouseClassSitesResponse {
	/**
	 * @access public
	 * @var WarehouseClassSiteResponse
	 */
	public $GetWarehouseClassSitesResult;
}}

if (!class_exists("WarehouseClassSiteResponse")) {
/**
 * WarehouseClassSiteResponse
 */
class WarehouseClassSiteResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassSite;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassSitesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseTypeLookupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentDropDownsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SiteDropDownsCollection;
}}

if (!class_exists("SetWarehouseClassSites")) {
/**
 * SetWarehouseClassSites
 */
class SetWarehouseClassSites {
	/**
	 * @access public
	 * @var WarehouseClassSiteRequest
	 */
	public $request;
}}

if (!class_exists("SetWarehouseClassSitesResponse")) {
/**
 * SetWarehouseClassSitesResponse
 */
class SetWarehouseClassSitesResponse {
	/**
	 * @access public
	 * @var WarehouseClassSiteResponse
	 */
	public $SetWarehouseClassSitesResult;
}}

if (!class_exists("GetWarehouseGroupCountries")) {
/**
 * GetWarehouseGroupCountries
 */
class GetWarehouseGroupCountries {
	/**
	 * @access public
	 * @var WarehouseGroupCountryRequest
	 */
	public $request;
}}

if (!class_exists("WarehouseGroupCountryRequest")) {
/**
 * WarehouseGroupCountryRequest
 */
class WarehouseGroupCountryRequest extends RequestBase {
	/**
	 * @access public
	 * @var WarehouseGroupCountryCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfWarehouseGroupCountryExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupCountry;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupCountriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CountriesCollection;
}}

if (!class_exists("WarehouseGroupCountryCriteria")) {
/**
 * WarehouseGroupCountryCriteria
 */
class WarehouseGroupCountryCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $WarehouseGroupId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Country;
}}

if (!class_exists("WarehouseGroupCountryExecuteOption")) {
/**
 * WarehouseGroupCountryExecuteOption
 */
class WarehouseGroupCountryExecuteOption {
}}

if (!class_exists("GetWarehouseGroupCountriesResponse")) {
/**
 * GetWarehouseGroupCountriesResponse
 */
class GetWarehouseGroupCountriesResponse {
	/**
	 * @access public
	 * @var WarehouseGroupCountryResponse
	 */
	public $GetWarehouseGroupCountriesResult;
}}

if (!class_exists("WarehouseGroupCountryResponse")) {
/**
 * WarehouseGroupCountryResponse
 */
class WarehouseGroupCountryResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupCountry;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupCountriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CountriesCollection;
}}

if (!class_exists("SetWarehouseGroupCountries")) {
/**
 * SetWarehouseGroupCountries
 */
class SetWarehouseGroupCountries {
	/**
	 * @access public
	 * @var WarehouseGroupCountryRequest
	 */
	public $request;
}}

if (!class_exists("SetWarehouseGroupCountriesResponse")) {
/**
 * SetWarehouseGroupCountriesResponse
 */
class SetWarehouseGroupCountriesResponse {
	/**
	 * @access public
	 * @var WarehouseGroupCountryResponse
	 */
	public $SetWarehouseGroupCountriesResult;
}}

if (!class_exists("GetWarehouseGroups")) {
/**
 * GetWarehouseGroups
 */
class GetWarehouseGroups {
	/**
	 * @access public
	 * @var WarehouseGroupRequest
	 */
	public $request;
}}

if (!class_exists("WarehouseGroupRequest")) {
/**
 * WarehouseGroupRequest
 */
class WarehouseGroupRequest extends RequestBase {
	/**
	 * @access public
	 * @var WarehouseGroupCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfWarehouseGroupExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroup;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupsCollection;
}}

if (!class_exists("WarehouseGroupCriteria")) {
/**
 * WarehouseGroupCriteria
 */
class WarehouseGroupCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $WarehouseGroupId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Warehouse;
}}

if (!class_exists("WarehouseGroupExecuteOption")) {
/**
 * WarehouseGroupExecuteOption
 */
class WarehouseGroupExecuteOption {
}}

if (!class_exists("GetWarehouseGroupsResponse")) {
/**
 * GetWarehouseGroupsResponse
 */
class GetWarehouseGroupsResponse {
	/**
	 * @access public
	 * @var WarehouseGroupResponse
	 */
	public $GetWarehouseGroupsResult;
}}

if (!class_exists("WarehouseGroupResponse")) {
/**
 * WarehouseGroupResponse
 */
class WarehouseGroupResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroup;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupsCollection;
}}

if (!class_exists("SetWarehouseGroups")) {
/**
 * SetWarehouseGroups
 */
class SetWarehouseGroups {
	/**
	 * @access public
	 * @var WarehouseGroupRequest
	 */
	public $request;
}}

if (!class_exists("SetWarehouseGroupsResponse")) {
/**
 * SetWarehouseGroupsResponse
 */
class SetWarehouseGroupsResponse {
	/**
	 * @access public
	 * @var WarehouseGroupResponse
	 */
	public $SetWarehouseGroupsResult;
}}

if (!class_exists("GetmlCleanCodes")) {
/**
 * GetmlCleanCodes
 */
class GetmlCleanCodes {
	/**
	 * @access public
	 * @var mlCleanCodeRequest
	 */
	public $request;
}}

if (!class_exists("mlCleanCodeRequest")) {
/**
 * mlCleanCodeRequest
 */
class mlCleanCodeRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlCleanCodeCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLCleanCodeExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCleanCode;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCleanCodesCollection;
}}

if (!class_exists("mlCleanCodeCriteria")) {
/**
 * mlCleanCodeCriteria
 */
class mlCleanCodeCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Id;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLCleanCodeExecuteOption")) {
/**
 * MLCleanCodeExecuteOption
 */
class MLCleanCodeExecuteOption {
}}

if (!class_exists("GetmlCleanCodesResponse")) {
/**
 * GetmlCleanCodesResponse
 */
class GetmlCleanCodesResponse {
	/**
	 * @access public
	 * @var mlCleanCodeResponse
	 */
	public $GetmlCleanCodesResult;
}}

if (!class_exists("mlCleanCodeResponse")) {
/**
 * mlCleanCodeResponse
 */
class mlCleanCodeResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCleanCode;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCleanCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlCleanCodes")) {
/**
 * SetmlCleanCodes
 */
class SetmlCleanCodes {
	/**
	 * @access public
	 * @var mlCleanCodeRequest
	 */
	public $request;
}}

if (!class_exists("SetmlCleanCodesResponse")) {
/**
 * SetmlCleanCodesResponse
 */
class SetmlCleanCodesResponse {
	/**
	 * @access public
	 * @var mlCleanCodeResponse
	 */
	public $SetmlCleanCodesResult;
}}

if (!class_exists("GetmlColors")) {
/**
 * GetmlColors
 */
class GetmlColors {
	/**
	 * @access public
	 * @var mlColorRequest
	 */
	public $request;
}}

if (!class_exists("mlColorRequest")) {
/**
 * mlColorRequest
 */
class mlColorRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlColorCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLColorExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlColor;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlColorsCollection;
}}

if (!class_exists("mlColorCriteria")) {
/**
 * mlColorCriteria
 */
class mlColorCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Id;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLColorExecuteOption")) {
/**
 * MLColorExecuteOption
 */
class MLColorExecuteOption {
}}

if (!class_exists("GetmlColorsResponse")) {
/**
 * GetmlColorsResponse
 */
class GetmlColorsResponse {
	/**
	 * @access public
	 * @var mlColorResponse
	 */
	public $GetmlColorsResult;
}}

if (!class_exists("mlColorResponse")) {
/**
 * mlColorResponse
 */
class mlColorResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlColor;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlColorsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlColors")) {
/**
 * SetmlColors
 */
class SetmlColors {
	/**
	 * @access public
	 * @var mlColorRequest
	 */
	public $request;
}}

if (!class_exists("SetmlColorsResponse")) {
/**
 * SetmlColorsResponse
 */
class SetmlColorsResponse {
	/**
	 * @access public
	 * @var mlColorResponse
	 */
	public $SetmlColorsResult;
}}

if (!class_exists("GetmlCoverContents")) {
/**
 * GetmlCoverContents
 */
class GetmlCoverContents {
	/**
	 * @access public
	 * @var mlCoverContentRequest
	 */
	public $request;
}}

if (!class_exists("mlCoverContentRequest")) {
/**
 * mlCoverContentRequest
 */
class mlCoverContentRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlCoverContentCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLCoverContentExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCoverContent;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCoverContentsCollection;
}}

if (!class_exists("mlCoverContentCriteria")) {
/**
 * mlCoverContentCriteria
 */
class mlCoverContentCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $CoverId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ContentName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLCoverContentExecuteOption")) {
/**
 * MLCoverContentExecuteOption
 */
class MLCoverContentExecuteOption {
}}

if (!class_exists("GetmlCoverContentsResponse")) {
/**
 * GetmlCoverContentsResponse
 */
class GetmlCoverContentsResponse {
	/**
	 * @access public
	 * @var mlCoverContentResponse
	 */
	public $GetmlCoverContentsResult;
}}

if (!class_exists("mlCoverContentResponse")) {
/**
 * mlCoverContentResponse
 */
class mlCoverContentResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCoverContent;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCoverContentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlCoverContents")) {
/**
 * SetmlCoverContents
 */
class SetmlCoverContents {
	/**
	 * @access public
	 * @var mlCoverContentRequest
	 */
	public $request;
}}

if (!class_exists("SetmlCoverContentsResponse")) {
/**
 * SetmlCoverContentsResponse
 */
class SetmlCoverContentsResponse {
	/**
	 * @access public
	 * @var mlCoverContentResponse
	 */
	public $SetmlCoverContentsResult;
}}

if (!class_exists("GetmlCovers")) {
/**
 * GetmlCovers
 */
class GetmlCovers {
	/**
	 * @access public
	 * @var mlCoverRequest
	 */
	public $request;
}}

if (!class_exists("mlCoverRequest")) {
/**
 * mlCoverRequest
 */
class mlCoverRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlCoverCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLCoverExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCover;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCoversCollection;
}}

if (!class_exists("mlCoverCriteria")) {
/**
 * mlCoverCriteria
 */
class mlCoverCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $CoverId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLCoverExecuteOption")) {
/**
 * MLCoverExecuteOption
 */
class MLCoverExecuteOption {
}}

if (!class_exists("GetmlCoversResponse")) {
/**
 * GetmlCoversResponse
 */
class GetmlCoversResponse {
	/**
	 * @access public
	 * @var mlCoverResponse
	 */
	public $GetmlCoversResult;
}}

if (!class_exists("mlCoverResponse")) {
/**
 * mlCoverResponse
 */
class mlCoverResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCover;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCoversCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlCovers")) {
/**
 * SetmlCovers
 */
class SetmlCovers {
	/**
	 * @access public
	 * @var mlCoverRequest
	 */
	public $request;
}}

if (!class_exists("SetmlCoversResponse")) {
/**
 * SetmlCoversResponse
 */
class SetmlCoversResponse {
	/**
	 * @access public
	 * @var mlCoverResponse
	 */
	public $SetmlCoversResult;
}}

if (!class_exists("GetmlDivisions")) {
/**
 * GetmlDivisions
 */
class GetmlDivisions {
	/**
	 * @access public
	 * @var mlDivisionRequest
	 */
	public $request;
}}

if (!class_exists("mlDivisionRequest")) {
/**
 * mlDivisionRequest
 */
class mlDivisionRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlDivisionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLDivisionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlDivision;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlDivisionsCollection;
}}

if (!class_exists("mlDivisionCriteria")) {
/**
 * mlDivisionCriteria
 */
class mlDivisionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DivCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLDivisionExecuteOption")) {
/**
 * MLDivisionExecuteOption
 */
class MLDivisionExecuteOption {
}}

if (!class_exists("GetmlDivisionsResponse")) {
/**
 * GetmlDivisionsResponse
 */
class GetmlDivisionsResponse {
	/**
	 * @access public
	 * @var mlDivisionResponse
	 */
	public $GetmlDivisionsResult;
}}

if (!class_exists("mlDivisionResponse")) {
/**
 * mlDivisionResponse
 */
class mlDivisionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlDivision;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlDivisionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlDivisions")) {
/**
 * SetmlDivisions
 */
class SetmlDivisions {
	/**
	 * @access public
	 * @var mlDivisionRequest
	 */
	public $request;
}}

if (!class_exists("SetmlDivisionsResponse")) {
/**
 * SetmlDivisionsResponse
 */
class SetmlDivisionsResponse {
	/**
	 * @access public
	 * @var mlDivisionResponse
	 */
	public $SetmlDivisionsResult;
}}

if (!class_exists("GetMLGeneralDescriptions")) {
/**
 * GetMLGeneralDescriptions
 */
class GetMLGeneralDescriptions {
	/**
	 * @access public
	 * @var MLGeneralDescriptionRequest
	 */
	public $request;
}}

if (!class_exists("MLGeneralDescriptionRequest")) {
/**
 * MLGeneralDescriptionRequest
 */
class MLGeneralDescriptionRequest extends RequestBase {
	/**
	 * @access public
	 * @var MLGeneralDescriptionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLGeneralDescriptionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MLGeneralDescription;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MLGeneralDescriptionsCollection;
}}

if (!class_exists("MLGeneralDescriptionCriteria")) {
/**
 * MLGeneralDescriptionCriteria
 */
class MLGeneralDescriptionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $Code;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLGeneralDescriptionExecuteOption")) {
/**
 * MLGeneralDescriptionExecuteOption
 */
class MLGeneralDescriptionExecuteOption {
}}

if (!class_exists("GetMLGeneralDescriptionsResponse")) {
/**
 * GetMLGeneralDescriptionsResponse
 */
class GetMLGeneralDescriptionsResponse {
	/**
	 * @access public
	 * @var MLGeneralDescriptionResponse
	 */
	public $GetMLGeneralDescriptionsResult;
}}

if (!class_exists("MLGeneralDescriptionResponse")) {
/**
 * MLGeneralDescriptionResponse
 */
class MLGeneralDescriptionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MLGeneralDescription;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MLGeneralDescriptionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetMLGeneralDescriptions")) {
/**
 * SetMLGeneralDescriptions
 */
class SetMLGeneralDescriptions {
	/**
	 * @access public
	 * @var MLGeneralDescriptionRequest
	 */
	public $request;
}}

if (!class_exists("SetMLGeneralDescriptionsResponse")) {
/**
 * SetMLGeneralDescriptionsResponse
 */
class SetMLGeneralDescriptionsResponse {
	/**
	 * @access public
	 * @var MLGeneralDescriptionResponse
	 */
	public $SetMLGeneralDescriptionsResult;
}}

if (!class_exists("GetmlGroupings")) {
/**
 * GetmlGroupings
 */
class GetmlGroupings {
	/**
	 * @access public
	 * @var mlGroupingRequest
	 */
	public $request;
}}

if (!class_exists("mlGroupingRequest")) {
/**
 * mlGroupingRequest
 */
class mlGroupingRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlGroupingCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLGroupingExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlGrouping;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlGroupingsCollection;
}}

if (!class_exists("mlGroupingCriteria")) {
/**
 * mlGroupingCriteria
 */
class mlGroupingCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $LookupId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LookupCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLGroupingExecuteOption")) {
/**
 * MLGroupingExecuteOption
 */
class MLGroupingExecuteOption {
}}

if (!class_exists("GetmlGroupingsResponse")) {
/**
 * GetmlGroupingsResponse
 */
class GetmlGroupingsResponse {
	/**
	 * @access public
	 * @var mlGroupingResponse
	 */
	public $GetmlGroupingsResult;
}}

if (!class_exists("mlGroupingResponse")) {
/**
 * mlGroupingResponse
 */
class mlGroupingResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlGrouping;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlGroupingsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlGroupings")) {
/**
 * SetmlGroupings
 */
class SetmlGroupings {
	/**
	 * @access public
	 * @var mlGroupingRequest
	 */
	public $request;
}}

if (!class_exists("SetmlGroupingsResponse")) {
/**
 * SetmlGroupingsResponse
 */
class SetmlGroupingsResponse {
	/**
	 * @access public
	 * @var mlGroupingResponse
	 */
	public $SetmlGroupingsResult;
}}

if (!class_exists("GetmlItemStatuses")) {
/**
 * GetmlItemStatuses
 */
class GetmlItemStatuses {
	/**
	 * @access public
	 * @var mlItemStatusRequest
	 */
	public $request;
}}

if (!class_exists("mlItemStatusRequest")) {
/**
 * mlItemStatusRequest
 */
class mlItemStatusRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlItemStatusCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLItemStatusExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlItemStatus;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlItemStatusesCollection;
}}

if (!class_exists("mlItemStatusCriteria")) {
/**
 * mlItemStatusCriteria
 */
class mlItemStatusCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Code;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLItemStatusExecuteOption")) {
/**
 * MLItemStatusExecuteOption
 */
class MLItemStatusExecuteOption {
}}

if (!class_exists("GetmlItemStatusesResponse")) {
/**
 * GetmlItemStatusesResponse
 */
class GetmlItemStatusesResponse {
	/**
	 * @access public
	 * @var mlItemStatusResponse
	 */
	public $GetmlItemStatusesResult;
}}

if (!class_exists("mlItemStatusResponse")) {
/**
 * mlItemStatusResponse
 */
class mlItemStatusResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlItemStatus;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlItemStatusesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlItemStatuses")) {
/**
 * SetmlItemStatuses
 */
class SetmlItemStatuses {
	/**
	 * @access public
	 * @var mlItemStatusRequest
	 */
	public $request;
}}

if (!class_exists("SetmlItemStatusesResponse")) {
/**
 * SetmlItemStatusesResponse
 */
class SetmlItemStatusesResponse {
	/**
	 * @access public
	 * @var mlItemStatusResponse
	 */
	public $SetmlItemStatusesResult;
}}

if (!class_exists("GetmlSerieses")) {
/**
 * GetmlSerieses
 */
class GetmlSerieses {
	/**
	 * @access public
	 * @var mlSeriesRequest
	 */
	public $request;
}}

if (!class_exists("mlSeriesRequest")) {
/**
 * mlSeriesRequest
 */
class mlSeriesRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlSeriesCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var LanguageCriteria
	 */
	public $LanguageCriteria;
	/**
	 * @access public
	 * @var ArrayOfMLSeriesExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlSeries;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlSeriesesCollection;
}}

if (!class_exists("mlSeriesCriteria")) {
/**
 * mlSeriesCriteria
 */
class mlSeriesCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Series_no;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("LanguageCriteria")) {
/**
 * LanguageCriteria
 */
class LanguageCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLSeriesExecuteOption")) {
/**
 * MLSeriesExecuteOption
 */
class MLSeriesExecuteOption {
}}

if (!class_exists("GetmlSeriesesResponse")) {
/**
 * GetmlSeriesesResponse
 */
class GetmlSeriesesResponse {
	/**
	 * @access public
	 * @var mlSeriesResponse
	 */
	public $GetmlSeriesesResult;
}}

if (!class_exists("mlSeriesResponse")) {
/**
 * mlSeriesResponse
 */
class mlSeriesResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlSeries;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlSeriesesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlSerieses")) {
/**
 * SetmlSerieses
 */
class SetmlSerieses {
	/**
	 * @access public
	 * @var mlSeriesRequest
	 */
	public $request;
}}

if (!class_exists("SetmlSeriesesResponse")) {
/**
 * SetmlSeriesesResponse
 */
class SetmlSeriesesResponse {
	/**
	 * @access public
	 * @var mlSeriesResponse
	 */
	public $SetmlSeriesesResult;
}}

if (!class_exists("GetmlStyles")) {
/**
 * GetmlStyles
 */
class GetmlStyles {
	/**
	 * @access public
	 * @var mlStyleRequest
	 */
	public $request;
}}

if (!class_exists("mlStyleRequest")) {
/**
 * mlStyleRequest
 */
class mlStyleRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlStyleCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLStyleExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlStyle;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlStylesCollection;
}}

if (!class_exists("mlStyleCriteria")) {
/**
 * mlStyleCriteria
 */
class mlStyleCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Code;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLStyleExecuteOption")) {
/**
 * MLStyleExecuteOption
 */
class MLStyleExecuteOption {
}}

if (!class_exists("GetmlStylesResponse")) {
/**
 * GetmlStylesResponse
 */
class GetmlStylesResponse {
	/**
	 * @access public
	 * @var mlStyleResponse
	 */
	public $GetmlStylesResult;
}}

if (!class_exists("mlStyleResponse")) {
/**
 * mlStyleResponse
 */
class mlStyleResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlStyle;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlStylesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlStyles")) {
/**
 * SetmlStyles
 */
class SetmlStyles {
	/**
	 * @access public
	 * @var mlStyleRequest
	 */
	public $request;
}}

if (!class_exists("SetmlStylesResponse")) {
/**
 * SetmlStylesResponse
 */
class SetmlStylesResponse {
	/**
	 * @access public
	 * @var mlStyleResponse
	 */
	public $SetmlStylesResult;
}}

if (!class_exists("Getml_items")) {
/**
 * Getml_items
 */
class Getml_items {
	/**
	 * @access public
	 * @var ml_itemRequest
	 */
	public $request;
}}

if (!class_exists("ml_itemRequest")) {
/**
 * ml_itemRequest
 */
class ml_itemRequest extends RequestBase {
	/**
	 * @access public
	 * @var ml_itemCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfML_itemExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ml_item;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ml_itemsCollection;
}}

if (!class_exists("ml_itemCriteria")) {
/**
 * ml_itemCriteria
 */
class ml_itemCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $item_sku;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("ML_itemExecuteOption")) {
/**
 * ML_itemExecuteOption
 */
class ML_itemExecuteOption {
}}

if (!class_exists("Getml_itemsResponse")) {
/**
 * Getml_itemsResponse
 */
class Getml_itemsResponse {
	/**
	 * @access public
	 * @var ml_itemResponse
	 */
	public $Getml_itemsResult;
}}

if (!class_exists("ml_itemResponse")) {
/**
 * ml_itemResponse
 */
class ml_itemResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ml_item;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ml_itemsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("Setml_items")) {
/**
 * Setml_items
 */
class Setml_items {
	/**
	 * @access public
	 * @var ml_itemRequest
	 */
	public $request;
}}

if (!class_exists("Setml_itemsResponse")) {
/**
 * Setml_itemsResponse
 */
class Setml_itemsResponse {
	/**
	 * @access public
	 * @var ml_itemResponse
	 */
	public $Setml_itemsResult;
}}

if (!class_exists("GetMarkets")) {
/**
 * GetMarkets
 */
class GetMarkets {
	/**
	 * @access public
	 * @var MarketRequest
	 */
	public $request;
}}

if (!class_exists("MarketRequest")) {
/**
 * MarketRequest
 */
class MarketRequest extends RequestBase {
	/**
	 * @access public
	 * @var MarketCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMarketExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Market;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketLookup;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketLookupsCollection;
}}

if (!class_exists("MarketCriteria")) {
/**
 * MarketCriteria
 */
class MarketCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $MarketCode;
	/**
	 * @access public
	 * @var sint
	 */
	public $Index;
	/**
	 * @access public
	 * @var sstring
	 */
	public $MarketId;
}}

if (!class_exists("MarketExecuteOption")) {
/**
 * MarketExecuteOption
 */
class MarketExecuteOption {
}}

if (!class_exists("GetMarketsResponse")) {
/**
 * GetMarketsResponse
 */
class GetMarketsResponse {
	/**
	 * @access public
	 * @var MarketResponse
	 */
	public $GetMarketsResult;
}}

if (!class_exists("MarketResponse")) {
/**
 * MarketResponse
 */
class MarketResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Market;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketLookup;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketLookupsCollection;
}}

if (!class_exists("SetMarkets")) {
/**
 * SetMarkets
 */
class SetMarkets {
	/**
	 * @access public
	 * @var MarketRequest
	 */
	public $request;
}}

if (!class_exists("SetMarketsResponse")) {
/**
 * SetMarketsResponse
 */
class SetMarketsResponse {
	/**
	 * @access public
	 * @var MarketResponse
	 */
	public $SetMarketsResult;
}}

if (!class_exists("GetMissingItemPhotos")) {
/**
 * GetMissingItemPhotos
 */
class GetMissingItemPhotos {
	/**
	 * @access public
	 * @var MissingItemPhotoRequest
	 */
	public $request;
}}

if (!class_exists("MissingItemPhotoRequest")) {
/**
 * MissingItemPhotoRequest
 */
class MissingItemPhotoRequest extends RequestBase {
	/**
	 * @access public
	 * @var MissingItemPhotoCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMissingItemPhotoExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MissingItemPhoto;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MissingItemPhotosCollection;
}}

if (!class_exists("MissingItemPhotoCriteria")) {
/**
 * MissingItemPhotoCriteria
 */
class MissingItemPhotoCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemNumber;
}}

if (!class_exists("MissingItemPhotoExecuteOption")) {
/**
 * MissingItemPhotoExecuteOption
 */
class MissingItemPhotoExecuteOption {
}}

if (!class_exists("GetMissingItemPhotosResponse")) {
/**
 * GetMissingItemPhotosResponse
 */
class GetMissingItemPhotosResponse {
	/**
	 * @access public
	 * @var MissingItemPhotoResponse
	 */
	public $GetMissingItemPhotosResult;
}}

if (!class_exists("MissingItemPhotoResponse")) {
/**
 * MissingItemPhotoResponse
 */
class MissingItemPhotoResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MissingItemPhoto;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MissingItemPhotosCollection;
}}

if (!class_exists("SetMissingItemPhotos")) {
/**
 * SetMissingItemPhotos
 */
class SetMissingItemPhotos {
	/**
	 * @access public
	 * @var MissingItemPhotoRequest
	 */
	public $request;
}}

if (!class_exists("SetMissingItemPhotosResponse")) {
/**
 * SetMissingItemPhotosResponse
 */
class SetMissingItemPhotosResponse {
	/**
	 * @access public
	 * @var MissingItemPhotoResponse
	 */
	public $SetMissingItemPhotosResult;
}}

if (!class_exists("GetMultiSeriesDescriptions")) {
/**
 * GetMultiSeriesDescriptions
 */
class GetMultiSeriesDescriptions {
	/**
	 * @access public
	 * @var MultiSeriesDescriptionRequest
	 */
	public $request;
}}

if (!class_exists("MultiSeriesDescriptionRequest")) {
/**
 * MultiSeriesDescriptionRequest
 */
class MultiSeriesDescriptionRequest extends RequestBase {
	/**
	 * @access public
	 * @var MultiSeriesDescriptionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMultiSeriesDescriptionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MultiSeriesDescription;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MultiSeriesDescriptionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MultiSeriesSellersCollection;
}}

if (!class_exists("MultiSeriesDescriptionCriteria")) {
/**
 * MultiSeriesDescriptionCriteria
 */
class MultiSeriesDescriptionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesNo;
}}

if (!class_exists("MultiSeriesDescriptionExecuteOption")) {
/**
 * MultiSeriesDescriptionExecuteOption
 */
class MultiSeriesDescriptionExecuteOption {
}}

if (!class_exists("GetMultiSeriesDescriptionsResponse")) {
/**
 * GetMultiSeriesDescriptionsResponse
 */
class GetMultiSeriesDescriptionsResponse {
	/**
	 * @access public
	 * @var MultiSeriesDescriptionResponse
	 */
	public $GetMultiSeriesDescriptionsResult;
}}

if (!class_exists("MultiSeriesDescriptionResponse")) {
/**
 * MultiSeriesDescriptionResponse
 */
class MultiSeriesDescriptionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MultiSeriesDescription;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MultiSeriesDescriptionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MultiSeriesSellersCollection;
}}

if (!class_exists("SetMultiSeriesDescriptions")) {
/**
 * SetMultiSeriesDescriptions
 */
class SetMultiSeriesDescriptions {
	/**
	 * @access public
	 * @var MultiSeriesDescriptionRequest
	 */
	public $request;
}}

if (!class_exists("SetMultiSeriesDescriptionsResponse")) {
/**
 * SetMultiSeriesDescriptionsResponse
 */
class SetMultiSeriesDescriptionsResponse {
	/**
	 * @access public
	 * @var MultiSeriesDescriptionResponse
	 */
	public $SetMultiSeriesDescriptionsResult;
}}

if (!class_exists("GetNewModelInspectionReports")) {
/**
 * GetNewModelInspectionReports
 */
class GetNewModelInspectionReports {
	/**
	 * @access public
	 * @var NewModelInspectionRequest
	 */
	public $request;
}}

if (!class_exists("NewModelInspectionRequest")) {
/**
 * NewModelInspectionRequest
 */
class NewModelInspectionRequest extends RequestBase {
	/**
	 * @access public
	 * @var NewModelInspectionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfNewModelInspectionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $NewModelInspection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $NewModelInspectionReportCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AccessoryNewModelInspection;
}}

if (!class_exists("NewModelInspectionCriteria")) {
/**
 * NewModelInspectionCriteria
 */
class NewModelInspectionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemNumber;
}}

if (!class_exists("NewModelInspectionExecuteOption")) {
/**
 * NewModelInspectionExecuteOption
 */
class NewModelInspectionExecuteOption {
}}

if (!class_exists("GetNewModelInspectionReportsResponse")) {
/**
 * GetNewModelInspectionReportsResponse
 */
class GetNewModelInspectionReportsResponse {
	/**
	 * @access public
	 * @var NewModelInspectionResponse
	 */
	public $GetNewModelInspectionReportsResult;
}}

if (!class_exists("NewModelInspectionResponse")) {
/**
 * NewModelInspectionResponse
 */
class NewModelInspectionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $NewModelInspection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $NewModelInspectionReportCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AccessoryNewModelInspection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AccessoryNewModelInspectionReportCollection;
}}

if (!class_exists("GetPackageApplicationCodes")) {
/**
 * GetPackageApplicationCodes
 */
class GetPackageApplicationCodes {
	/**
	 * @access public
	 * @var PackageApplicationCodeRequest
	 */
	public $request;
}}

if (!class_exists("PackageApplicationCodeRequest")) {
/**
 * PackageApplicationCodeRequest
 */
class PackageApplicationCodeRequest extends RequestBase {
	/**
	 * @access public
	 * @var PackageApplicationCodeCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPackageApplicationCodeExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageApplicationCode;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageApplicationCodesCollection;
}}

if (!class_exists("PackageApplicationCodeCriteria")) {
/**
 * PackageApplicationCodeCriteria
 */
class PackageApplicationCodeCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $AppCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $AppDescr;
}}

if (!class_exists("PackageApplicationCodeExecuteOption")) {
/**
 * PackageApplicationCodeExecuteOption
 */
class PackageApplicationCodeExecuteOption {
}}

if (!class_exists("GetPackageApplicationCodesResponse")) {
/**
 * GetPackageApplicationCodesResponse
 */
class GetPackageApplicationCodesResponse {
	/**
	 * @access public
	 * @var PackageApplicationCodeResponse
	 */
	public $GetPackageApplicationCodesResult;
}}

if (!class_exists("PackageApplicationCodeResponse")) {
/**
 * PackageApplicationCodeResponse
 */
class PackageApplicationCodeResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageApplicationCode;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageApplicationCodesCollection;
}}

if (!class_exists("SetPackageApplicationCodes")) {
/**
 * SetPackageApplicationCodes
 */
class SetPackageApplicationCodes {
	/**
	 * @access public
	 * @var PackageApplicationCodeRequest
	 */
	public $request;
}}

if (!class_exists("SetPackageApplicationCodesResponse")) {
/**
 * SetPackageApplicationCodesResponse
 */
class SetPackageApplicationCodesResponse {
	/**
	 * @access public
	 * @var PackageApplicationCodeResponse
	 */
	public $SetPackageApplicationCodesResult;
}}

if (!class_exists("GetPackageItemApplicationCodes")) {
/**
 * GetPackageItemApplicationCodes
 */
class GetPackageItemApplicationCodes {
	/**
	 * @access public
	 * @var PackageItemApplicationCodeRequest
	 */
	public $request;
}}

if (!class_exists("PackageItemApplicationCodeRequest")) {
/**
 * PackageItemApplicationCodeRequest
 */
class PackageItemApplicationCodeRequest extends RequestBase {
	/**
	 * @access public
	 * @var PackageItemApplicationCodeCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPackageItemApplicationCodeExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageItemApplicationCode;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageItemApplicationCodesCollection;
}}

if (!class_exists("PackageItemApplicationCodeCriteria")) {
/**
 * PackageItemApplicationCodeCriteria
 */
class PackageItemApplicationCodeCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $PackageId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $AppCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesNo;
}}

if (!class_exists("PackageItemApplicationCodeExecuteOption")) {
/**
 * PackageItemApplicationCodeExecuteOption
 */
class PackageItemApplicationCodeExecuteOption {
}}

if (!class_exists("GetPackageItemApplicationCodesResponse")) {
/**
 * GetPackageItemApplicationCodesResponse
 */
class GetPackageItemApplicationCodesResponse {
	/**
	 * @access public
	 * @var PackageItemApplicationCodeResponse
	 */
	public $GetPackageItemApplicationCodesResult;
}}

if (!class_exists("PackageItemApplicationCodeResponse")) {
/**
 * PackageItemApplicationCodeResponse
 */
class PackageItemApplicationCodeResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageItemApplicationCode;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageItemApplicationCodesCollection;
}}

if (!class_exists("SetPackageItemApplicationCodes")) {
/**
 * SetPackageItemApplicationCodes
 */
class SetPackageItemApplicationCodes {
	/**
	 * @access public
	 * @var PackageItemApplicationCodeRequest
	 */
	public $request;
}}

if (!class_exists("SetPackageItemApplicationCodesResponse")) {
/**
 * SetPackageItemApplicationCodesResponse
 */
class SetPackageItemApplicationCodesResponse {
	/**
	 * @access public
	 * @var PackageItemApplicationCodeResponse
	 */
	public $SetPackageItemApplicationCodesResult;
}}

if (!class_exists("GetPackageItems")) {
/**
 * GetPackageItems
 */
class GetPackageItems {
	/**
	 * @access public
	 * @var PackageItemRequest
	 */
	public $request;
}}

if (!class_exists("PackageItemRequest")) {
/**
 * PackageItemRequest
 */
class PackageItemRequest extends RequestBase {
	/**
	 * @access public
	 * @var PackageItemCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPackageItemExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageItem;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageItemsCollection;
}}

if (!class_exists("PackageItemCriteria")) {
/**
 * PackageItemCriteria
 */
class PackageItemCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $PackageId;
}}

if (!class_exists("PackageItemExecuteOption")) {
/**
 * PackageItemExecuteOption
 */
class PackageItemExecuteOption {
}}

if (!class_exists("GetPackageItemsResponse")) {
/**
 * GetPackageItemsResponse
 */
class GetPackageItemsResponse {
	/**
	 * @access public
	 * @var PackageItemResponse
	 */
	public $GetPackageItemsResult;
}}

if (!class_exists("PackageItemResponse")) {
/**
 * PackageItemResponse
 */
class PackageItemResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageItem;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageItemsCollection;
}}

if (!class_exists("SetPackageItems")) {
/**
 * SetPackageItems
 */
class SetPackageItems {
	/**
	 * @access public
	 * @var PackageItemRequest
	 */
	public $request;
}}

if (!class_exists("SetPackageItemsResponse")) {
/**
 * SetPackageItemsResponse
 */
class SetPackageItemsResponse {
	/**
	 * @access public
	 * @var PackageItemResponse
	 */
	public $SetPackageItemsResult;
}}

if (!class_exists("GetPackages")) {
/**
 * GetPackages
 */
class GetPackages {
	/**
	 * @access public
	 * @var PackageRequest
	 */
	public $request;
}}

if (!class_exists("PackageRequest")) {
/**
 * PackageRequest
 */
class PackageRequest extends RequestBase {
	/**
	 * @access public
	 * @var PackageCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPackageExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Package;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackagesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageTemplatesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageImagesCollection;
}}

if (!class_exists("PackageCriteria")) {
/**
 * PackageCriteria
 */
class PackageCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesNo;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PackageId;
}}

if (!class_exists("PackageExecuteOption")) {
/**
 * PackageExecuteOption
 */
class PackageExecuteOption {
}}

if (!class_exists("GetPackagesResponse")) {
/**
 * GetPackagesResponse
 */
class GetPackagesResponse {
	/**
	 * @access public
	 * @var PackageResponse
	 */
	public $GetPackagesResult;
}}

if (!class_exists("PackageResponse")) {
/**
 * PackageResponse
 */
class PackageResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Package;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackagesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageTemplatesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageImagesCollection;
}}

if (!class_exists("SetPackages")) {
/**
 * SetPackages
 */
class SetPackages {
	/**
	 * @access public
	 * @var PackageRequest
	 */
	public $request;
}}

if (!class_exists("SetPackagesResponse")) {
/**
 * SetPackagesResponse
 */
class SetPackagesResponse {
	/**
	 * @access public
	 * @var PackageResponse
	 */
	public $SetPackagesResult;
}}

if (!class_exists("GetPackageTemplates")) {
/**
 * GetPackageTemplates
 */
class GetPackageTemplates {
	/**
	 * @access public
	 * @var PackageTemplateRequest
	 */
	public $request;
}}

if (!class_exists("PackageTemplateRequest")) {
/**
 * PackageTemplateRequest
 */
class PackageTemplateRequest extends RequestBase {
	/**
	 * @access public
	 * @var PackageTemplateCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPackageTemplateExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageTemplate;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageTemplatesCollection;
}}

if (!class_exists("PackageTemplateCriteria")) {
/**
 * PackageTemplateCriteria
 */
class PackageTemplateCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TemplateId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesNo;
}}

if (!class_exists("PackageTemplateExecuteOption")) {
/**
 * PackageTemplateExecuteOption
 */
class PackageTemplateExecuteOption {
}}

if (!class_exists("GetPackageTemplatesResponse")) {
/**
 * GetPackageTemplatesResponse
 */
class GetPackageTemplatesResponse {
	/**
	 * @access public
	 * @var PackageTemplateResponse
	 */
	public $GetPackageTemplatesResult;
}}

if (!class_exists("PackageTemplateResponse")) {
/**
 * PackageTemplateResponse
 */
class PackageTemplateResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageTemplate;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageTemplatesCollection;
}}

if (!class_exists("SetPackageTemplates")) {
/**
 * SetPackageTemplates
 */
class SetPackageTemplates {
	/**
	 * @access public
	 * @var PackageTemplateRequest
	 */
	public $request;
}}

if (!class_exists("SetPackageTemplatesResponse")) {
/**
 * SetPackageTemplatesResponse
 */
class SetPackageTemplatesResponse {
	/**
	 * @access public
	 * @var PackageTemplateResponse
	 */
	public $SetPackageTemplatesResult;
}}

if (!class_exists("GetPriceCodes")) {
/**
 * GetPriceCodes
 */
class GetPriceCodes {
	/**
	 * @access public
	 * @var PriceCodeRequest
	 */
	public $request;
}}

if (!class_exists("PriceCodeRequest")) {
/**
 * PriceCodeRequest
 */
class PriceCodeRequest extends RequestBase {
	/**
	 * @access public
	 * @var PriceCodeCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPriceCodeExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceCodeCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentsCollection;
}}

if (!class_exists("PriceCodeCriteria")) {
/**
 * PriceCodeCriteria
 */
class PriceCodeCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
}}

if (!class_exists("PriceCodeExecuteOption")) {
/**
 * PriceCodeExecuteOption
 */
class PriceCodeExecuteOption {
}}

if (!class_exists("GetPriceCodesResponse")) {
/**
 * GetPriceCodesResponse
 */
class GetPriceCodesResponse {
	/**
	 * @access public
	 * @var PriceCodeResponse
	 */
	public $GetPriceCodesResult;
}}

if (!class_exists("PriceCodeResponse")) {
/**
 * PriceCodeResponse
 */
class PriceCodeResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceCodeCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentsCollection;
}}

if (!class_exists("GetPriceListQueues")) {
/**
 * GetPriceListQueues
 */
class GetPriceListQueues {
	/**
	 * @access public
	 * @var PriceListQueueRequest
	 */
	public $request;
}}

if (!class_exists("PriceListQueueRequest")) {
/**
 * PriceListQueueRequest
 */
class PriceListQueueRequest extends RequestBase {
	/**
	 * @access public
	 * @var PriceListQueueCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPriceListQueueExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceListQueue;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceListQueuesCollection;
}}

if (!class_exists("PriceListQueueCriteria")) {
/**
 * PriceListQueueCriteria
 */
class PriceListQueueCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $PriceListID;
	/**
	 * @access public
	 * @var sint
	 */
	public $SectionCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Groups;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
	/**
	 * @access public
	 * @var sstring
	 */
	public $URL;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DisplayURL;
	/**
	 * @access public
	 * @var sstring
	 */
	public $UserName;
}}

if (!class_exists("PriceListQueueExecuteOption")) {
/**
 * PriceListQueueExecuteOption
 */
class PriceListQueueExecuteOption {
}}

if (!class_exists("GetPriceListQueuesResponse")) {
/**
 * GetPriceListQueuesResponse
 */
class GetPriceListQueuesResponse {
	/**
	 * @access public
	 * @var PriceListQueueResponse
	 */
	public $GetPriceListQueuesResult;
}}

if (!class_exists("PriceListQueueResponse")) {
/**
 * PriceListQueueResponse
 */
class PriceListQueueResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceListQueue;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceListQueuesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $IntroPriceListQueueCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ValueAddedTaxCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductionPriceListQueueCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $NonCompletedPriceListsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CompletedPriceListsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $NextPriceListID;
}}

if (!class_exists("SetPriceListQueues")) {
/**
 * SetPriceListQueues
 */
class SetPriceListQueues {
	/**
	 * @access public
	 * @var PriceListQueueRequest
	 */
	public $request;
}}

if (!class_exists("SetPriceListQueuesResponse")) {
/**
 * SetPriceListQueuesResponse
 */
class SetPriceListQueuesResponse {
	/**
	 * @access public
	 * @var PriceListQueueResponse
	 */
	public $SetPriceListQueuesResult;
}}

if (!class_exists("GetPriceListSections")) {
/**
 * GetPriceListSections
 */
class GetPriceListSections {
	/**
	 * @access public
	 * @var PriceListSectionRequest
	 */
	public $request;
}}

if (!class_exists("PriceListSectionRequest")) {
/**
 * PriceListSectionRequest
 */
class PriceListSectionRequest extends RequestBase {
	/**
	 * @access public
	 * @var PriceListSectionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPriceListSectionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceListSection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceListSectionsCollection;
}}

if (!class_exists("PriceListSectionCriteria")) {
/**
 * PriceListSectionCriteria
 */
class PriceListSectionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Division;
}}

if (!class_exists("PriceListSectionExecuteOption")) {
/**
 * PriceListSectionExecuteOption
 */
class PriceListSectionExecuteOption {
}}

if (!class_exists("GetPriceListSectionsResponse")) {
/**
 * GetPriceListSectionsResponse
 */
class GetPriceListSectionsResponse {
	/**
	 * @access public
	 * @var PriceListSectionResponse
	 */
	public $GetPriceListSectionsResult;
}}

if (!class_exists("PriceListSectionResponse")) {
/**
 * PriceListSectionResponse
 */
class PriceListSectionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceListSection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PriceListSectionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SalesCategoryCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesGroupingCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SectionGroupCollection;
}}

if (!class_exists("SetPriceListSections")) {
/**
 * SetPriceListSections
 */
class SetPriceListSections {
	/**
	 * @access public
	 * @var PriceListSectionRequest
	 */
	public $request;
}}

if (!class_exists("SetPriceListSectionsResponse")) {
/**
 * SetPriceListSectionsResponse
 */
class SetPriceListSectionsResponse {
	/**
	 * @access public
	 * @var PriceListSectionResponse
	 */
	public $SetPriceListSectionsResult;
}}

if (!class_exists("GetPricelist")) {
/**
 * GetPricelist
 */
class GetPricelist {
	/**
	 * @access public
	 * @var PricelistRequest
	 */
	public $request;
}}

if (!class_exists("PricelistRequest")) {
/**
 * PricelistRequest
 */
class PricelistRequest extends RequestBase {
	/**
	 * @access public
	 * @var PricelistCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfPricelistExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistBooksCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistPriceCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistBuyGroupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistMarketsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistGroupsByBookCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionClassesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GroupCollection;
}}

if (!class_exists("PricelistCriteria")) {
/**
 * PricelistCriteria
 */
class PricelistCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
	/**
	 * @access public
	 * @var sint
	 */
	public $DivisionClassID;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Book;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Series;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Application;
	/**
	 * @access public
	 * @var sstring
	 */
	public $MessageApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $MessageCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PriceCode;
}}

if (!class_exists("PricelistExecuteOption")) {
/**
 * PricelistExecuteOption
 */
class PricelistExecuteOption {
}}

if (!class_exists("GetPricelistResponse")) {
/**
 * GetPricelistResponse
 */
class GetPricelistResponse {
	/**
	 * @access public
	 * @var PricelistResponse
	 */
	public $GetPricelistResult;
}}

if (!class_exists("PricelistResponse")) {
/**
 * PricelistResponse
 */
class PricelistResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistBooksCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistPriceCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistBuyGroupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistMarketsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PricelistGroupsByBookCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionClassesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GroupCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ValueAddedTaxCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackagesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ApplicationMessage;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguageCollection;
}}

if (!class_exists("GetProductAttributes")) {
/**
 * GetProductAttributes
 */
class GetProductAttributes {
	/**
	 * @access public
	 * @var ProductAttributeRequest
	 */
	public $request;
}}

if (!class_exists("ProductAttributeRequest")) {
/**
 * ProductAttributeRequest
 */
class ProductAttributeRequest extends RequestBase {
	/**
	 * @access public
	 * @var ProductAttributeCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfProductAttributeExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductAttribute;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductAttributesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemProductAttribute;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemProductAttributesCollection;
}}

if (!class_exists("ProductAttributeCriteria")) {
/**
 * ProductAttributeCriteria
 */
class ProductAttributeCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $ProductAttributeCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $AttributeDescription;
	/**
	 * @access public
	 * @var sint
	 */
	public $AttributeCode;
}}

if (!class_exists("ProductAttributeExecuteOption")) {
/**
 * ProductAttributeExecuteOption
 */
class ProductAttributeExecuteOption {
}}

if (!class_exists("GetProductAttributesResponse")) {
/**
 * GetProductAttributesResponse
 */
class GetProductAttributesResponse {
	/**
	 * @access public
	 * @var ProductAttributeResponse
	 */
	public $GetProductAttributesResult;
}}

if (!class_exists("ProductAttributeResponse")) {
/**
 * ProductAttributeResponse
 */
class ProductAttributeResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductAttribute;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductAttributesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemProductAttribute;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemProductAttributesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemDifferenceCodesCollection;
}}

if (!class_exists("SetProductAttributes")) {
/**
 * SetProductAttributes
 */
class SetProductAttributes {
	/**
	 * @access public
	 * @var ProductAttributeRequest
	 */
	public $request;
}}

if (!class_exists("SetProductAttributesResponse")) {
/**
 * SetProductAttributesResponse
 */
class SetProductAttributesResponse {
	/**
	 * @access public
	 * @var ProductAttributeResponse
	 */
	public $SetProductAttributesResult;
}}

if (!class_exists("GetProductDownloads")) {
/**
 * GetProductDownloads
 */
class GetProductDownloads {
	/**
	 * @access public
	 * @var ProductDownloadRequest
	 */
	public $request;
}}

if (!class_exists("ProductDownloadRequest")) {
/**
 * ProductDownloadRequest
 */
class ProductDownloadRequest extends RequestBase {
	/**
	 * @access public
	 * @var ProductDownloadCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfProductDownloadExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductDownloadsCollection;
}}

if (!class_exists("ProductDownloadCriteria")) {
/**
 * ProductDownloadCriteria
 */
class ProductDownloadCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sboolean
	 */
	public $AllShipTo;
	/**
	 * @access public
	 * @var sint
	 */
	public $Days;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $UseCountryCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ListOfCustomerNumbers;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ListOfShipToNumbers;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ListofAllShipTos;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ListofDays;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ListOfUseCountryCodes;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PreferredLanguage;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_CustomerNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_ShipToNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_OrderNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_SessionId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_PriceType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_ShipType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_Warehouse;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $PC_EcommMaster;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_OrderId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_PriceCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PC_BuyGroup;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $PC_IncludeVAT;
}}

if (!class_exists("ProductDownloadExecuteOption")) {
/**
 * ProductDownloadExecuteOption
 */
class ProductDownloadExecuteOption {
}}

if (!class_exists("GetProductDownloadsResponse")) {
/**
 * GetProductDownloadsResponse
 */
class GetProductDownloadsResponse {
	/**
	 * @access public
	 * @var ProductDownloadResponse
	 */
	public $GetProductDownloadsResult;
}}

if (!class_exists("ProductDownloadResponse")) {
/**
 * ProductDownloadResponse
 */
class ProductDownloadResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductDownload;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductDownloadsCollection;
}}

if (!class_exists("GetReleaseTemplates")) {
/**
 * GetReleaseTemplates
 */
class GetReleaseTemplates {
	/**
	 * @access public
	 * @var ReleaseTemplateRequest
	 */
	public $request;
}}

if (!class_exists("ReleaseTemplateRequest")) {
/**
 * ReleaseTemplateRequest
 */
class ReleaseTemplateRequest extends RequestBase {
	/**
	 * @access public
	 * @var ReleaseTemplateCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfReleaseTemplateExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ReleaseTemplate;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ReleaseTemplatesCollection;
}}

if (!class_exists("ReleaseTemplateCriteria")) {
/**
 * ReleaseTemplateCriteria
 */
class ReleaseTemplateCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $TemplateName;
}}

if (!class_exists("ReleaseTemplateExecuteOption")) {
/**
 * ReleaseTemplateExecuteOption
 */
class ReleaseTemplateExecuteOption {
}}

if (!class_exists("GetReleaseTemplatesResponse")) {
/**
 * GetReleaseTemplatesResponse
 */
class GetReleaseTemplatesResponse {
	/**
	 * @access public
	 * @var ReleaseTemplateResponse
	 */
	public $GetReleaseTemplatesResult;
}}

if (!class_exists("ReleaseTemplateResponse")) {
/**
 * ReleaseTemplateResponse
 */
class ReleaseTemplateResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ReleaseTemplate;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ReleaseTemplatesCollection;
}}

if (!class_exists("SetReleaseTemplates")) {
/**
 * SetReleaseTemplates
 */
class SetReleaseTemplates {
	/**
	 * @access public
	 * @var ReleaseTemplateRequest
	 */
	public $request;
}}

if (!class_exists("SetReleaseTemplatesResponse")) {
/**
 * SetReleaseTemplatesResponse
 */
class SetReleaseTemplatesResponse {
	/**
	 * @access public
	 * @var ReleaseTemplateResponse
	 */
	public $SetReleaseTemplatesResult;
}}

if (!class_exists("GetRemoveIntroPriceListFlags")) {
/**
 * GetRemoveIntroPriceListFlags
 */
class GetRemoveIntroPriceListFlags {
	/**
	 * @access public
	 * @var RemoveIntroPriceListFlagRequest
	 */
	public $request;
}}

if (!class_exists("RemoveIntroPriceListFlagRequest")) {
/**
 * RemoveIntroPriceListFlagRequest
 */
class RemoveIntroPriceListFlagRequest extends RequestBase {
	/**
	 * @access public
	 * @var RemoveIntroPriceListFlagCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfRemoveIntroPriceListFlagExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $RemoveIntroPriceListFlag;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $RemoveIntroPriceListFlagsCollection;
}}

if (!class_exists("RemoveIntroPriceListFlagCriteria")) {
/**
 * RemoveIntroPriceListFlagCriteria
 */
class RemoveIntroPriceListFlagCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Market;
}}

if (!class_exists("RemoveIntroPriceListFlagExecuteOption")) {
/**
 * RemoveIntroPriceListFlagExecuteOption
 */
class RemoveIntroPriceListFlagExecuteOption {
}}

if (!class_exists("GetRemoveIntroPriceListFlagsResponse")) {
/**
 * GetRemoveIntroPriceListFlagsResponse
 */
class GetRemoveIntroPriceListFlagsResponse {
	/**
	 * @access public
	 * @var RemoveIntroPriceListFlagResponse
	 */
	public $GetRemoveIntroPriceListFlagsResult;
}}

if (!class_exists("RemoveIntroPriceListFlagResponse")) {
/**
 * RemoveIntroPriceListFlagResponse
 */
class RemoveIntroPriceListFlagResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $RemoveIntroPriceListFlag;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $RemoveIntroPriceListFlagsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketLookupsCollection;
}}

if (!class_exists("SetRemoveIntroPriceListFlags")) {
/**
 * SetRemoveIntroPriceListFlags
 */
class SetRemoveIntroPriceListFlags {
	/**
	 * @access public
	 * @var RemoveIntroPriceListFlagRequest
	 */
	public $request;
}}

if (!class_exists("SetRemoveIntroPriceListFlagsResponse")) {
/**
 * SetRemoveIntroPriceListFlagsResponse
 */
class SetRemoveIntroPriceListFlagsResponse {
	/**
	 * @access public
	 * @var RemoveIntroPriceListFlagResponse
	 */
	public $SetRemoveIntroPriceListFlagsResult;
}}

if (!class_exists("GetSectionGroups")) {
/**
 * GetSectionGroups
 */
class GetSectionGroups {
	/**
	 * @access public
	 * @var SectionGroupRequest
	 */
	public $request;
}}

if (!class_exists("SectionGroupRequest")) {
/**
 * SectionGroupRequest
 */
class SectionGroupRequest extends RequestBase {
	/**
	 * @access public
	 * @var SectionGroupCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfSectionGroupExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SectionGroup;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SectionGroupsCollection;
}}

if (!class_exists("SectionGroupCriteria")) {
/**
 * SectionGroupCriteria
 */
class SectionGroupCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $Code;
}}

if (!class_exists("SectionGroupExecuteOption")) {
/**
 * SectionGroupExecuteOption
 */
class SectionGroupExecuteOption {
}}

if (!class_exists("GetSectionGroupsResponse")) {
/**
 * GetSectionGroupsResponse
 */
class GetSectionGroupsResponse {
	/**
	 * @access public
	 * @var SectionGroupResponse
	 */
	public $GetSectionGroupsResult;
}}

if (!class_exists("SectionGroupResponse")) {
/**
 * SectionGroupResponse
 */
class SectionGroupResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SectionGroup;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SectionGroupsCollection;
}}

if (!class_exists("SetSectionGroups")) {
/**
 * SetSectionGroups
 */
class SetSectionGroups {
	/**
	 * @access public
	 * @var SectionGroupRequest
	 */
	public $request;
}}

if (!class_exists("SetSectionGroupsResponse")) {
/**
 * SetSectionGroupsResponse
 */
class SetSectionGroupsResponse {
	/**
	 * @access public
	 * @var SectionGroupResponse
	 */
	public $SetSectionGroupsResult;
}}

if (!class_exists("GetSequences")) {
/**
 * GetSequences
 */
class GetSequences {
	/**
	 * @access public
	 * @var SequenceRequest
	 */
	public $request;
}}

if (!class_exists("SequenceRequest")) {
/**
 * SequenceRequest
 */
class SequenceRequest extends RequestBase {
	/**
	 * @access public
	 * @var SequenceCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfSequenceExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Sequence;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SequencesCollection;
}}

if (!class_exists("SequenceCriteria")) {
/**
 * SequenceCriteria
 */
class SequenceCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Label;
}}

if (!class_exists("SequenceExecuteOption")) {
/**
 * SequenceExecuteOption
 */
class SequenceExecuteOption {
}}

if (!class_exists("GetSequencesResponse")) {
/**
 * GetSequencesResponse
 */
class GetSequencesResponse {
	/**
	 * @access public
	 * @var SequenceResponse
	 */
	public $GetSequencesResult;
}}

if (!class_exists("SequenceResponse")) {
/**
 * SequenceResponse
 */
class SequenceResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Sequence;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SequencesCollection;
}}

if (!class_exists("SetSequences")) {
/**
 * SetSequences
 */
class SetSequences {
	/**
	 * @access public
	 * @var SequenceRequest
	 */
	public $request;
}}

if (!class_exists("SetSequencesResponse")) {
/**
 * SetSequencesResponse
 */
class SetSequencesResponse {
	/**
	 * @access public
	 * @var SequenceResponse
	 */
	public $SetSequencesResult;
}}

if (!class_exists("GetSeries")) {
/**
 * GetSeries
 */
class GetSeries {
	/**
	 * @access public
	 * @var SeriesRequest
	 */
	public $request;
}}

if (!class_exists("SeriesRequest")) {
/**
 * SeriesRequest
 */
class SeriesRequest extends RequestBase {
	/**
	 * @access public
	 * @var SeriesCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfSeriesExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Series;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentCollection;
}}

if (!class_exists("SeriesCriteria")) {
/**
 * SeriesCriteria
 */
class SeriesCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $GroupingId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $CustomerNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ShipToNumber;
	/**
	 * @access public
	 * @var sint
	 */
	public $AllShipTo;
	/**
	 * @access public
	 * @var sint
	 */
	public $Days;
	/**
	 * @access public
	 * @var sstring
	 */
	public $grouping;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesNo;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemNo;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Market;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesName;
}}

if (!class_exists("SeriesExecuteOption")) {
/**
 * SeriesExecuteOption
 */
class SeriesExecuteOption {
}}

if (!class_exists("GetSeriesResponse")) {
/**
 * GetSeriesResponse
 */
class GetSeriesResponse {
	/**
	 * @access public
	 * @var SeriesResponse
	 */
	public $GetSeriesResult;
}}

if (!class_exists("SeriesResponse")) {
/**
 * SeriesResponse
 */
class SeriesResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Series;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentCollection;
}}

if (!class_exists("Setseries")) {
/**
 * Setseries
 */
class Setseries {
	/**
	 * @access public
	 * @var SeriesRequest
	 */
	public $request;
}}

if (!class_exists("SetseriesResponse")) {
/**
 * SetseriesResponse
 */
class SetseriesResponse {
	/**
	 * @access public
	 * @var SeriesResponse
	 */
	public $SetseriesResult;
}}

if (!class_exists("GetStatusChangeLogics")) {
/**
 * GetStatusChangeLogics
 */
class GetStatusChangeLogics {
	/**
	 * @access public
	 * @var StatusChangeLogicRequest
	 */
	public $request;
}}

if (!class_exists("StatusChangeLogicRequest")) {
/**
 * StatusChangeLogicRequest
 */
class StatusChangeLogicRequest extends RequestBase {
	/**
	 * @access public
	 * @var StatusChangeLogicCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfStatusChangeLogicExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangeLogic;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangeLogicsCollection;
}}

if (!class_exists("StatusChangeLogicCriteria")) {
/**
 * StatusChangeLogicCriteria
 */
class StatusChangeLogicCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $CurrentStatus;
}}

if (!class_exists("StatusChangeLogicExecuteOption")) {
/**
 * StatusChangeLogicExecuteOption
 */
class StatusChangeLogicExecuteOption {
}}

if (!class_exists("GetStatusChangeLogicsResponse")) {
/**
 * GetStatusChangeLogicsResponse
 */
class GetStatusChangeLogicsResponse {
	/**
	 * @access public
	 * @var StatusChangeLogicResponse
	 */
	public $GetStatusChangeLogicsResult;
}}

if (!class_exists("StatusChangeLogicResponse")) {
/**
 * StatusChangeLogicResponse
 */
class StatusChangeLogicResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangeLogic;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangeLogicsCollection;
}}

if (!class_exists("SetStatusChangeLogics")) {
/**
 * SetStatusChangeLogics
 */
class SetStatusChangeLogics {
	/**
	 * @access public
	 * @var StatusChangeLogicRequest
	 */
	public $request;
}}

if (!class_exists("SetStatusChangeLogicsResponse")) {
/**
 * SetStatusChangeLogicsResponse
 */
class SetStatusChangeLogicsResponse {
	/**
	 * @access public
	 * @var StatusChangeLogicResponse
	 */
	public $SetStatusChangeLogicsResult;
}}

if (!class_exists("GetStatusChanges")) {
/**
 * GetStatusChanges
 */
class GetStatusChanges {
	/**
	 * @access public
	 * @var StatusChangeRequest
	 */
	public $request;
}}

if (!class_exists("StatusChangeRequest")) {
/**
 * StatusChangeRequest
 */
class StatusChangeRequest extends RequestBase {
	/**
	 * @access public
	 * @var StatusChangeCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfStatusChangeExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChange;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangesCollection;
}}

if (!class_exists("StatusChangeCriteria")) {
/**
 * StatusChangeCriteria
 */
class StatusChangeCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $item_sku;
	/**
	 * @access public
	 * @var sstring
	 */
	public $series_no;
	/**
	 * @access public
	 * @var sstring
	 */
	public $CurrentStatus;
	/**
	 * @access public
	 * @var ArrayOfString
	 */
	public $iSeriesEnvironment;
}}

if (!class_exists("StatusChangeExecuteOption")) {
/**
 * StatusChangeExecuteOption
 */
class StatusChangeExecuteOption {
}}

if (!class_exists("GetStatusChangesResponse")) {
/**
 * GetStatusChangesResponse
 */
class GetStatusChangesResponse {
	/**
	 * @access public
	 * @var StatusChangeResponse
	 */
	public $GetStatusChangesResult;
}}

if (!class_exists("StatusChangeResponse")) {
/**
 * StatusChangeResponse
 */
class StatusChangeResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChange;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangeLogic;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangeLogicsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangeIseriesCollection;
}}

if (!class_exists("SetStatusChanges")) {
/**
 * SetStatusChanges
 */
class SetStatusChanges {
	/**
	 * @access public
	 * @var StatusChangeRequest
	 */
	public $request;
}}

if (!class_exists("SetStatusChangesResponse")) {
/**
 * SetStatusChangesResponse
 */
class SetStatusChangesResponse {
	/**
	 * @access public
	 * @var StatusChangeResponse
	 */
	public $SetStatusChangesResult;
}}

if (!class_exists("GetStyles")) {
/**
 * GetStyles
 */
class GetStyles {
	/**
	 * @access public
	 * @var StyleRequest
	 */
	public $request;
}}

if (!class_exists("StyleRequest")) {
/**
 * StyleRequest
 */
class StyleRequest extends RequestBase {
	/**
	 * @access public
	 * @var StyleCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfStyleExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Style;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StylesCollection;
}}

if (!class_exists("StyleCriteria")) {
/**
 * StyleCriteria
 */
class StyleCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $StyleCode;
}}

if (!class_exists("StyleExecuteOption")) {
/**
 * StyleExecuteOption
 */
class StyleExecuteOption {
}}

if (!class_exists("GetStylesResponse")) {
/**
 * GetStylesResponse
 */
class GetStylesResponse {
	/**
	 * @access public
	 * @var StyleResponse
	 */
	public $GetStylesResult;
}}

if (!class_exists("StyleResponse")) {
/**
 * StyleResponse
 */
class StyleResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Style;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StylesCollection;
}}

if (!class_exists("GetSupportData")) {
/**
 * GetSupportData
 */
class GetSupportData {
	/**
	 * @access public
	 * @var SupportDataRequest
	 */
	public $request;
}}

if (!class_exists("SupportDataRequest")) {
/**
 * SupportDataRequest
 */
class SupportDataRequest extends RequestBase {
	/**
	 * @access public
	 * @var CategoryCriteria
	 */
	public $CategoryCriteria;
	/**
	 * @access public
	 * @var GroupingCriteria
	 */
	public $GroupingCriteria;
	/**
	 * @access public
	 * @var StyleCriteria
	 */
	public $StyleCriteria;
	/**
	 * @access public
	 * @var ArrayOfSupportDataExecuteOption
	 */
	public $ExecuteOptions;
}}

if (!class_exists("CategoryCriteria")) {
/**
 * CategoryCriteria
 */
class CategoryCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $CategoryId;
}}

if (!class_exists("GroupingCriteria")) {
/**
 * GroupingCriteria
 */
class GroupingCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $LookupId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LookupCode;
}}

if (!class_exists("SupportDataExecuteOption")) {
/**
 * SupportDataExecuteOption
 */
class SupportDataExecuteOption {
}}

if (!class_exists("GetSupportDataResponse")) {
/**
 * GetSupportDataResponse
 */
class GetSupportDataResponse {
	/**
	 * @access public
	 * @var SupportDataResponse
	 */
	public $GetSupportDataResult;
}}

if (!class_exists("SupportDataResponse")) {
/**
 * SupportDataResponse
 */
class SupportDataResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CategoriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GroupingsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StylesCollection;
}}

if (!class_exists("GetAPKSInitialLoads")) {
/**
 * GetAPKSInitialLoads
 */
class GetAPKSInitialLoads {
	/**
	 * @access public
	 * @var APKSInitialLoadRequest
	 */
	public $request;
}}

if (!class_exists("APKSInitialLoadRequest")) {
/**
 * APKSInitialLoadRequest
 */
class APKSInitialLoadRequest extends RequestBase {
	/**
	 * @access public
	 * @var ArrayOfAPKSInitialLoadExecuteOption
	 */
	public $ExecuteOptions;
}}

if (!class_exists("APKSInitialLoadExecuteOption")) {
/**
 * APKSInitialLoadExecuteOption
 */
class APKSInitialLoadExecuteOption {
}}

if (!class_exists("GetAPKSInitialLoadsResponse")) {
/**
 * GetAPKSInitialLoadsResponse
 */
class GetAPKSInitialLoadsResponse {
	/**
	 * @access public
	 * @var APKSInitialLoadResponse
	 */
	public $GetAPKSInitialLoadsResult;
}}

if (!class_exists("APKSInitialLoadResponse")) {
/**
 * APKSInitialLoadResponse
 */
class APKSInitialLoadResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesCommentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesDivisionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesGroupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesShowroomsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesStylesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCommentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemConsumerDescrCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemFriendlyDescrCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemGeneralDescrCodeCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemStatusCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ColorsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketLookupCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CategoriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GroupingsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PublishCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CoversCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CoverContentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $UnitOfMeasureCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DimensionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ContentTypesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CountryMastersCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $RetailSalesCategoryCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ProductAttributesCollection;
}}

if (!class_exists("GetAdvancedSearch")) {
/**
 * GetAdvancedSearch
 */
class GetAdvancedSearch {
	/**
	 * @access public
	 * @var AdvancedSearchRequest
	 */
	public $request;
}}

if (!class_exists("AdvancedSearchRequest")) {
/**
 * AdvancedSearchRequest
 */
class AdvancedSearchRequest extends RequestBase {
	/**
	 * @access public
	 * @var ArrayOfAdvancedSearchExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AdvancedSearchCriteria;
}}

if (!class_exists("AdvancedSearchExecuteOption")) {
/**
 * AdvancedSearchExecuteOption
 */
class AdvancedSearchExecuteOption {
}}

if (!class_exists("GetAdvancedSearchResponse")) {
/**
 * GetAdvancedSearchResponse
 */
class GetAdvancedSearchResponse {
	/**
	 * @access public
	 * @var AdvancedSearchResponse
	 */
	public $GetAdvancedSearchResult;
}}

if (!class_exists("AdvancedSearchResponse")) {
/**
 * AdvancedSearchResponse
 */
class AdvancedSearchResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesCommentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesDivisionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesGroupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesShowroomsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesStylesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCommentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemConsumerDescrCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemFriendlyDescrCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GeneralDescriptionCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemStatusCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ColorsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketLookupCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CategoriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GroupingsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PublishCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $UnitOfMeasureCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DimensionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesNamesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesPublishCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SeriesTypesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AS400DescriptionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemFlagsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AdvancedSearchResultsCollection;
}}

if (!class_exists("GetAttributeTypes")) {
/**
 * GetAttributeTypes
 */
class GetAttributeTypes {
	/**
	 * @access public
	 * @var AttributeTypeRequest
	 */
	public $request;
}}

if (!class_exists("AttributeTypeRequest")) {
/**
 * AttributeTypeRequest
 */
class AttributeTypeRequest extends RequestBase {
	/**
	 * @access public
	 * @var AttributeTypeCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfAttributeTypeExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AttributeType;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AttributeTypesCollection;
}}

if (!class_exists("AttributeTypeCriteria")) {
/**
 * AttributeTypeCriteria
 */
class AttributeTypeCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $AttributeType;
}}

if (!class_exists("AttributeTypeExecuteOption")) {
/**
 * AttributeTypeExecuteOption
 */
class AttributeTypeExecuteOption {
}}

if (!class_exists("GetAttributeTypesResponse")) {
/**
 * GetAttributeTypesResponse
 */
class GetAttributeTypesResponse {
	/**
	 * @access public
	 * @var AttributeTypeResponse
	 */
	public $GetAttributeTypesResult;
}}

if (!class_exists("AttributeTypeResponse")) {
/**
 * AttributeTypeResponse
 */
class AttributeTypeResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AttributeType;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AttributeTypesCollection;
}}

if (!class_exists("SetAttributeTypes")) {
/**
 * SetAttributeTypes
 */
class SetAttributeTypes {
	/**
	 * @access public
	 * @var AttributeTypeRequest
	 */
	public $request;
}}

if (!class_exists("SetAttributeTypesResponse")) {
/**
 * SetAttributeTypesResponse
 */
class SetAttributeTypesResponse {
	/**
	 * @access public
	 * @var AttributeTypeResponse
	 */
	public $SetAttributeTypesResult;
}}

if (!class_exists("GetBuyGroups")) {
/**
 * GetBuyGroups
 */
class GetBuyGroups {
	/**
	 * @access public
	 * @var BuyGroupRequest
	 */
	public $request;
}}

if (!class_exists("BuyGroupRequest")) {
/**
 * BuyGroupRequest
 */
class BuyGroupRequest extends RequestBase {
	/**
	 * @access public
	 * @var BuyGroupCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfBuyGroupExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $BuyGroupCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentsCollection;
}}

if (!class_exists("BuyGroupCriteria")) {
/**
 * BuyGroupCriteria
 */
class BuyGroupCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
}}

if (!class_exists("BuyGroupExecuteOption")) {
/**
 * BuyGroupExecuteOption
 */
class BuyGroupExecuteOption {
}}

if (!class_exists("GetBuyGroupsResponse")) {
/**
 * GetBuyGroupsResponse
 */
class GetBuyGroupsResponse {
	/**
	 * @access public
	 * @var BuyGroupResponse
	 */
	public $GetBuyGroupsResult;
}}

if (!class_exists("BuyGroupResponse")) {
/**
 * BuyGroupResponse
 */
class BuyGroupResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $BuyGroupCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentsCollection;
}}

if (!class_exists("GetCategories")) {
/**
 * GetCategories
 */
class GetCategories {
	/**
	 * @access public
	 * @var CategoryRequest
	 */
	public $request;
}}

if (!class_exists("CategoryRequest")) {
/**
 * CategoryRequest
 */
class CategoryRequest extends RequestBase {
	/**
	 * @access public
	 * @var CategoryCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfCategoryExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Category;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CategoriesCollection;
}}

if (!class_exists("CategoryExecuteOption")) {
/**
 * CategoryExecuteOption
 */
class CategoryExecuteOption {
}}

if (!class_exists("GetCategoriesResponse")) {
/**
 * GetCategoriesResponse
 */
class GetCategoriesResponse {
	/**
	 * @access public
	 * @var CategoryResponse
	 */
	public $GetCategoriesResult;
}}

if (!class_exists("CategoryResponse")) {
/**
 * CategoryResponse
 */
class CategoryResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Category;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CategoriesCollection;
}}

if (!class_exists("SetCategories")) {
/**
 * SetCategories
 */
class SetCategories {
	/**
	 * @access public
	 * @var CategoryRequest
	 */
	public $request;
}}

if (!class_exists("SetCategoriesResponse")) {
/**
 * SetCategoriesResponse
 */
class SetCategoriesResponse {
	/**
	 * @access public
	 * @var CategoryResponse
	 */
	public $SetCategoriesResult;
}}

if (!class_exists("GetConsumerCategory")) {
/**
 * GetConsumerCategory
 */
class GetConsumerCategory {
	/**
	 * @access public
	 * @var ConsumerCategoryRequest
	 */
	public $request;
}}

if (!class_exists("ConsumerCategoryRequest")) {
/**
 * ConsumerCategoryRequest
 */
class ConsumerCategoryRequest extends RequestBase {
	/**
	 * @access public
	 * @var ConsumerCategoryCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfConsumerCategoryExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ConsumerCategoryCollection;
}}

if (!class_exists("ConsumerCategoryCriteria")) {
/**
 * ConsumerCategoryCriteria
 */
class ConsumerCategoryCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
}}

if (!class_exists("ConsumerCategoryExecuteOption")) {
/**
 * ConsumerCategoryExecuteOption
 */
class ConsumerCategoryExecuteOption {
}}

if (!class_exists("GetConsumerCategoryResponse")) {
/**
 * GetConsumerCategoryResponse
 */
class GetConsumerCategoryResponse {
	/**
	 * @access public
	 * @var ConsumerCategoryResponse
	 */
	public $GetConsumerCategoryResult;
}}

if (!class_exists("ConsumerCategoryResponse")) {
/**
 * ConsumerCategoryResponse
 */
class ConsumerCategoryResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ConsumerCategoryCollection;
}}

if (!class_exists("GetConsumerGrouping")) {
/**
 * GetConsumerGrouping
 */
class GetConsumerGrouping {
	/**
	 * @access public
	 * @var ConsumerGroupingRequest
	 */
	public $request;
}}

if (!class_exists("ConsumerGroupingRequest")) {
/**
 * ConsumerGroupingRequest
 */
class ConsumerGroupingRequest extends RequestBase {
	/**
	 * @access public
	 * @var ConsumerGroupingCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfConsumerGroupingExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ConsumerGroupingCollection;
}}

if (!class_exists("ConsumerGroupingCriteria")) {
/**
 * ConsumerGroupingCriteria
 */
class ConsumerGroupingCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
}}

if (!class_exists("ConsumerGroupingExecuteOption")) {
/**
 * ConsumerGroupingExecuteOption
 */
class ConsumerGroupingExecuteOption {
}}

if (!class_exists("GetConsumerGroupingResponse")) {
/**
 * GetConsumerGroupingResponse
 */
class GetConsumerGroupingResponse {
	/**
	 * @access public
	 * @var ConsumerGroupingResponse
	 */
	public $GetConsumerGroupingResult;
}}

if (!class_exists("ConsumerGroupingResponse")) {
/**
 * ConsumerGroupingResponse
 */
class ConsumerGroupingResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ConsumerGroupingCollection;
}}

if (!class_exists("GetCountryGroups")) {
/**
 * GetCountryGroups
 */
class GetCountryGroups {
	/**
	 * @access public
	 * @var CountryGroupRequest
	 */
	public $request;
}}

if (!class_exists("CountryGroupRequest")) {
/**
 * CountryGroupRequest
 */
class CountryGroupRequest extends RequestBase {
	/**
	 * @access public
	 * @var CountryGroupCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfCountryGroupExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupCountry;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupCountriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CountriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MapicsWarehousesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseCountriesCollection;
}}

if (!class_exists("CountryGroupCriteria")) {
/**
 * CountryGroupCriteria
 */
class CountryGroupCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $WarehouseGroupId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Country;
}}

if (!class_exists("CountryGroupExecuteOption")) {
/**
 * CountryGroupExecuteOption
 */
class CountryGroupExecuteOption {
}}

if (!class_exists("GetCountryGroupsResponse")) {
/**
 * GetCountryGroupsResponse
 */
class GetCountryGroupsResponse {
	/**
	 * @access public
	 * @var CountryGroupResponse
	 */
	public $GetCountryGroupsResult;
}}

if (!class_exists("CountryGroupResponse")) {
/**
 * CountryGroupResponse
 */
class CountryGroupResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupCountry;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupCountriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseGroupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CountriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MapicsWarehousesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseCountriesCollection;
}}

if (!class_exists("SetCountryGroups")) {
/**
 * SetCountryGroups
 */
class SetCountryGroups {
	/**
	 * @access public
	 * @var CountryGroupRequest
	 */
	public $request;
}}

if (!class_exists("SetCountryGroupsResponse")) {
/**
 * SetCountryGroupsResponse
 */
class SetCountryGroupsResponse {
	/**
	 * @access public
	 * @var CountryGroupResponse
	 */
	public $SetCountryGroupsResult;
}}

if (!class_exists("GetCovers")) {
/**
 * GetCovers
 */
class GetCovers {
	/**
	 * @access public
	 * @var CoverRequest
	 */
	public $request;
}}

if (!class_exists("CoverRequest")) {
/**
 * CoverRequest
 */
class CoverRequest extends RequestBase {
	/**
	 * @access public
	 * @var CoverCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfCoverExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Cover;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CoversCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ColorsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CoverContent;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CoverContentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ContentTypesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CleaningCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCover;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCoversCollection;
}}

if (!class_exists("CoverCriteria")) {
/**
 * CoverCriteria
 */
class CoverCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $CoverId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $CoverName;
	/**
	 * @access public
	 * @var sint
	 */
	public $CurCoverId;
}}

if (!class_exists("CoverExecuteOption")) {
/**
 * CoverExecuteOption
 */
class CoverExecuteOption {
}}

if (!class_exists("GetCoversResponse")) {
/**
 * GetCoversResponse
 */
class GetCoversResponse {
	/**
	 * @access public
	 * @var CoverResponse
	 */
	public $GetCoversResult;
}}

if (!class_exists("CoverResponse")) {
/**
 * CoverResponse
 */
class CoverResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Cover;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CoversCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ColorsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CoverContent;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CoverContentsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ContentTypesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CleaningCodesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCover;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCoversCollection;
}}

if (!class_exists("SetCovers")) {
/**
 * SetCovers
 */
class SetCovers {
	/**
	 * @access public
	 * @var CoverRequest
	 */
	public $request;
}}

if (!class_exists("SetCoversResponse")) {
/**
 * SetCoversResponse
 */
class SetCoversResponse {
	/**
	 * @access public
	 * @var CoverResponse
	 */
	public $SetCoversResult;
}}

if (!class_exists("UpdateDuplicateCovers")) {
/**
 * UpdateDuplicateCovers
 */
class UpdateDuplicateCovers {
	/**
	 * @access public
	 * @var CoverRequest
	 */
	public $request;
}}

if (!class_exists("UpdateDuplicateCoversResponse")) {
/**
 * UpdateDuplicateCoversResponse
 */
class UpdateDuplicateCoversResponse {
	/**
	 * @access public
	 * @var CoverResponse
	 */
	public $UpdateDuplicateCoversResult;
}}

if (!class_exists("GetDimensions")) {
/**
 * GetDimensions
 */
class GetDimensions {
	/**
	 * @access public
	 * @var DimensionRequest
	 */
	public $request;
}}

if (!class_exists("DimensionRequest")) {
/**
 * DimensionRequest
 */
class DimensionRequest extends RequestBase {
	/**
	 * @access public
	 * @var DimensionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfDimensionExecuteOption
	 */
	public $ExecuteOptions;
}}

if (!class_exists("DimensionCriteria")) {
/**
 * DimensionCriteria
 */
class DimensionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ListOfItems;
}}

if (!class_exists("DimensionExecuteOption")) {
/**
 * DimensionExecuteOption
 */
class DimensionExecuteOption {
}}

if (!class_exists("GetDimensionsResponse")) {
/**
 * GetDimensionsResponse
 */
class GetDimensionsResponse {
	/**
	 * @access public
	 * @var DimensionResponse
	 */
	public $GetDimensionsResult;
}}

if (!class_exists("DimensionResponse")) {
/**
 * DimensionResponse
 */
class DimensionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DimensionsCollection;
}}

if (!class_exists("GetDivisionClasses")) {
/**
 * GetDivisionClasses
 */
class GetDivisionClasses {
	/**
	 * @access public
	 * @var DivisionClassRequest
	 */
	public $request;
}}

if (!class_exists("DivisionClassRequest")) {
/**
 * DivisionClassRequest
 */
class DivisionClassRequest extends RequestBase {
	/**
	 * @access public
	 * @var DivisionClassCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfDivisionClassExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionClass;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionClassesCollection;
}}

if (!class_exists("DivisionClassCriteria")) {
/**
 * DivisionClassCriteria
 */
class DivisionClassCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $DivisionClassID;
}}

if (!class_exists("DivisionClassExecuteOption")) {
/**
 * DivisionClassExecuteOption
 */
class DivisionClassExecuteOption {
}}

if (!class_exists("GetDivisionClassesResponse")) {
/**
 * GetDivisionClassesResponse
 */
class GetDivisionClassesResponse {
	/**
	 * @access public
	 * @var DivisionClassResponse
	 */
	public $GetDivisionClassesResult;
}}

if (!class_exists("DivisionClassResponse")) {
/**
 * DivisionClassResponse
 */
class DivisionClassResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionClass;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionClassesCollection;
}}

if (!class_exists("SetDivisionClasses")) {
/**
 * SetDivisionClasses
 */
class SetDivisionClasses {
	/**
	 * @access public
	 * @var DivisionClassRequest
	 */
	public $request;
}}

if (!class_exists("SetDivisionClassesResponse")) {
/**
 * SetDivisionClassesResponse
 */
class SetDivisionClassesResponse {
	/**
	 * @access public
	 * @var DivisionClassResponse
	 */
	public $SetDivisionClassesResult;
}}

if (!class_exists("GetDivisions")) {
/**
 * GetDivisions
 */
class GetDivisions {
	/**
	 * @access public
	 * @var DivisionRequest
	 */
	public $request;
}}

if (!class_exists("DivisionRequest")) {
/**
 * DivisionRequest
 */
class DivisionRequest extends RequestBase {
	/**
	 * @access public
	 * @var DivisionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfDivisionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Division;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionCollection;
}}

if (!class_exists("DivisionCriteria")) {
/**
 * DivisionCriteria
 */
class DivisionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $Division;
}}

if (!class_exists("DivisionExecuteOption")) {
/**
 * DivisionExecuteOption
 */
class DivisionExecuteOption {
}}

if (!class_exists("GetDivisionsResponse")) {
/**
 * GetDivisionsResponse
 */
class GetDivisionsResponse {
	/**
	 * @access public
	 * @var DivisionResponse
	 */
	public $GetDivisionsResult;
}}

if (!class_exists("DivisionResponse")) {
/**
 * DivisionResponse
 */
class DivisionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Division;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DivisionsCollection;
}}

if (!class_exists("GetEnvironmentSpecifics")) {
/**
 * GetEnvironmentSpecifics
 */
class GetEnvironmentSpecifics {
	/**
	 * @access public
	 * @var EnvironmentSpecificRequest
	 */
	public $request;
}}

if (!class_exists("EnvironmentSpecificRequest")) {
/**
 * EnvironmentSpecificRequest
 */
class EnvironmentSpecificRequest extends RequestBase {
	/**
	 * @access public
	 * @var EnvironmentSpecificCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfEnvironmentSpecificExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentSpecific;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentSpecificsCollection;
}}

if (!class_exists("EnvironmentSpecificCriteria")) {
/**
 * EnvironmentSpecificCriteria
 */
class EnvironmentSpecificCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
}}

if (!class_exists("EnvironmentSpecificExecuteOption")) {
/**
 * EnvironmentSpecificExecuteOption
 */
class EnvironmentSpecificExecuteOption {
}}

if (!class_exists("GetEnvironmentSpecificsResponse")) {
/**
 * GetEnvironmentSpecificsResponse
 */
class GetEnvironmentSpecificsResponse {
	/**
	 * @access public
	 * @var EnvironmentSpecificResponse
	 */
	public $GetEnvironmentSpecificsResult;
}}

if (!class_exists("EnvironmentSpecificResponse")) {
/**
 * EnvironmentSpecificResponse
 */
class EnvironmentSpecificResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentSpecific;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentSpecificsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentsCollection;
}}

if (!class_exists("SetEnvironmentSpecifics")) {
/**
 * SetEnvironmentSpecifics
 */
class SetEnvironmentSpecifics {
	/**
	 * @access public
	 * @var EnvironmentSpecificRequest
	 */
	public $request;
}}

if (!class_exists("SetEnvironmentSpecificsResponse")) {
/**
 * SetEnvironmentSpecificsResponse
 */
class SetEnvironmentSpecificsResponse {
	/**
	 * @access public
	 * @var EnvironmentSpecificResponse
	 */
	public $SetEnvironmentSpecificsResult;
}}

if (!class_exists("GetEnvironments")) {
/**
 * GetEnvironments
 */
class GetEnvironments {
	/**
	 * @access public
	 * @var EnvironmentsRequest
	 */
	public $request;
}}

if (!class_exists("EnvironmentsRequest")) {
/**
 * EnvironmentsRequest
 */
class EnvironmentsRequest extends RequestBase {
	/**
	 * @access public
	 * @var EnvironmentsCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfEnvironmentsExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Environments;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentsCollection;
}}

if (!class_exists("EnvironmentsCriteria")) {
/**
 * EnvironmentsCriteria
 */
class EnvironmentsCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
}}

if (!class_exists("EnvironmentsExecuteOption")) {
/**
 * EnvironmentsExecuteOption
 */
class EnvironmentsExecuteOption {
}}

if (!class_exists("GetEnvironmentsResponse")) {
/**
 * GetEnvironmentsResponse
 */
class GetEnvironmentsResponse {
	/**
	 * @access public
	 * @var EnvironmentsResponse
	 */
	public $GetEnvironmentsResult;
}}

if (!class_exists("EnvironmentsResponse")) {
/**
 * EnvironmentsResponse
 */
class EnvironmentsResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Environments;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentsCollection;
}}

if (!class_exists("SetEnvironments")) {
/**
 * SetEnvironments
 */
class SetEnvironments {
	/**
	 * @access public
	 * @var EnvironmentsRequest
	 */
	public $request;
}}

if (!class_exists("SetEnvironmentsResponse")) {
/**
 * SetEnvironmentsResponse
 */
class SetEnvironmentsResponse {
	/**
	 * @access public
	 * @var EnvironmentsResponse
	 */
	public $SetEnvironmentsResult;
}}

if (!class_exists("GetFieldExceptions")) {
/**
 * GetFieldExceptions
 */
class GetFieldExceptions {
	/**
	 * @access public
	 * @var FieldExceptionRequest
	 */
	public $request;
}}

if (!class_exists("FieldExceptionRequest")) {
/**
 * FieldExceptionRequest
 */
class FieldExceptionRequest extends RequestBase {
	/**
	 * @access public
	 * @var FieldExceptionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfFieldExceptionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $FieldException;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $FieldExceptionsCollection;
}}

if (!class_exists("FieldExceptionCriteria")) {
/**
 * FieldExceptionCriteria
 */
class FieldExceptionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ExceptionTableName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ExceptionField;
}}

if (!class_exists("FieldExceptionExecuteOption")) {
/**
 * FieldExceptionExecuteOption
 */
class FieldExceptionExecuteOption {
}}

if (!class_exists("GetFieldExceptionsResponse")) {
/**
 * GetFieldExceptionsResponse
 */
class GetFieldExceptionsResponse {
	/**
	 * @access public
	 * @var FieldExceptionResponse
	 */
	public $GetFieldExceptionsResult;
}}

if (!class_exists("FieldExceptionResponse")) {
/**
 * FieldExceptionResponse
 */
class FieldExceptionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $FieldException;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $FieldExceptionsCollection;
}}

if (!class_exists("SetFieldExceptions")) {
/**
 * SetFieldExceptions
 */
class SetFieldExceptions {
	/**
	 * @access public
	 * @var FieldExceptionRequest
	 */
	public $request;
}}

if (!class_exists("SetFieldExceptionsResponse")) {
/**
 * SetFieldExceptionsResponse
 */
class SetFieldExceptionsResponse {
	/**
	 * @access public
	 * @var FieldExceptionResponse
	 */
	public $SetFieldExceptionsResult;
}}

if (!class_exists("GetFriendlyDescriptions")) {
/**
 * GetFriendlyDescriptions
 */
class GetFriendlyDescriptions {
	/**
	 * @access public
	 * @var FriendlyDescriptionRequest
	 */
	public $request;
}}

if (!class_exists("FriendlyDescriptionRequest")) {
/**
 * FriendlyDescriptionRequest
 */
class FriendlyDescriptionRequest extends RequestBase {
	/**
	 * @access public
	 * @var FriendlyDescriptionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfFriendlyDescriptionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $FriendlyDescription;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $FriendlyDescriptionsCollection;
}}

if (!class_exists("FriendlyDescriptionCriteria")) {
/**
 * FriendlyDescriptionCriteria
 */
class FriendlyDescriptionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $FriendlyDescription;
}}

if (!class_exists("FriendlyDescriptionExecuteOption")) {
/**
 * FriendlyDescriptionExecuteOption
 */
class FriendlyDescriptionExecuteOption {
}}

if (!class_exists("GetFriendlyDescriptionsResponse")) {
/**
 * GetFriendlyDescriptionsResponse
 */
class GetFriendlyDescriptionsResponse {
	/**
	 * @access public
	 * @var FriendlyDescriptionResponse
	 */
	public $GetFriendlyDescriptionsResult;
}}

if (!class_exists("FriendlyDescriptionResponse")) {
/**
 * FriendlyDescriptionResponse
 */
class FriendlyDescriptionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $FriendlyDescription;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $FriendlyDescriptionsCollection;
}}

if (!class_exists("SetFriendlyDescriptions")) {
/**
 * SetFriendlyDescriptions
 */
class SetFriendlyDescriptions {
	/**
	 * @access public
	 * @var FriendlyDescriptionRequest
	 */
	public $request;
}}

if (!class_exists("SetFriendlyDescriptionsResponse")) {
/**
 * SetFriendlyDescriptionsResponse
 */
class SetFriendlyDescriptionsResponse {
	/**
	 * @access public
	 * @var FriendlyDescriptionResponse
	 */
	public $SetFriendlyDescriptionsResult;
}}

if (!class_exists("GetGeneralDescriptions")) {
/**
 * GetGeneralDescriptions
 */
class GetGeneralDescriptions {
	/**
	 * @access public
	 * @var GeneralDescriptionRequest
	 */
	public $request;
}}

if (!class_exists("GeneralDescriptionRequest")) {
/**
 * GeneralDescriptionRequest
 */
class GeneralDescriptionRequest extends RequestBase {
	/**
	 * @access public
	 * @var GeneralDescriptionCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfGeneralDescriptionExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GeneralDescription;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GeneralDescriptionsCollection;
}}

if (!class_exists("GeneralDescriptionCriteria")) {
/**
 * GeneralDescriptionCriteria
 */
class GeneralDescriptionCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $Code;
}}

if (!class_exists("GeneralDescriptionExecuteOption")) {
/**
 * GeneralDescriptionExecuteOption
 */
class GeneralDescriptionExecuteOption {
}}

if (!class_exists("GetGeneralDescriptionsResponse")) {
/**
 * GetGeneralDescriptionsResponse
 */
class GetGeneralDescriptionsResponse {
	/**
	 * @access public
	 * @var GeneralDescriptionResponse
	 */
	public $GetGeneralDescriptionsResult;
}}

if (!class_exists("GeneralDescriptionResponse")) {
/**
 * GeneralDescriptionResponse
 */
class GeneralDescriptionResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GeneralDescription;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GeneralDescriptionsCollection;
}}

if (!class_exists("SetGeneralDescriptions")) {
/**
 * SetGeneralDescriptions
 */
class SetGeneralDescriptions {
	/**
	 * @access public
	 * @var GeneralDescriptionRequest
	 */
	public $request;
}}

if (!class_exists("SetGeneralDescriptionsResponse")) {
/**
 * SetGeneralDescriptionsResponse
 */
class SetGeneralDescriptionsResponse {
	/**
	 * @access public
	 * @var GeneralDescriptionResponse
	 */
	public $SetGeneralDescriptionsResult;
}}

if (!class_exists("GetGroupings")) {
/**
 * GetGroupings
 */
class GetGroupings {
	/**
	 * @access public
	 * @var GroupingRequest
	 */
	public $request;
}}

if (!class_exists("GroupingRequest")) {
/**
 * GroupingRequest
 */
class GroupingRequest extends RequestBase {
	/**
	 * @access public
	 * @var GroupingCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfGroupingExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Grouping;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GroupingsCollection;
}}

if (!class_exists("GroupingExecuteOption")) {
/**
 * GroupingExecuteOption
 */
class GroupingExecuteOption {
}}

if (!class_exists("GetGroupingsResponse")) {
/**
 * GetGroupingsResponse
 */
class GetGroupingsResponse {
	/**
	 * @access public
	 * @var GroupingResponse
	 */
	public $GetGroupingsResult;
}}

if (!class_exists("GroupingResponse")) {
/**
 * GroupingResponse
 */
class GroupingResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Grouping;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $GroupingsCollection;
}}

if (!class_exists("SetGroupings")) {
/**
 * SetGroupings
 */
class SetGroupings {
	/**
	 * @access public
	 * @var GroupingRequest
	 */
	public $request;
}}

if (!class_exists("SetGroupingsResponse")) {
/**
 * SetGroupingsResponse
 */
class SetGroupingsResponse {
	/**
	 * @access public
	 * @var GroupingResponse
	 */
	public $SetGroupingsResult;
}}

if (!class_exists("GetHomeStoreHierarchyFamilyOfBusinessRooms")) {
/**
 * GetHomeStoreHierarchyFamilyOfBusinessRooms
 */
class GetHomeStoreHierarchyFamilyOfBusinessRooms {
	/**
	 * @access public
	 * @var HomeStoreHierarchyFamilyOfBusinessRoomRequest
	 */
	public $request;
}}

if (!class_exists("HomeStoreHierarchyFamilyOfBusinessRoomRequest")) {
/**
 * HomeStoreHierarchyFamilyOfBusinessRoomRequest
 */
class HomeStoreHierarchyFamilyOfBusinessRoomRequest extends RequestBase {
	/**
	 * @access public
	 * @var HomeStoreHierarchyFamilyOfBusinessRoomCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfHomeStoreHierarchyFamilyOfBusinessRoomExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyFamilyOfBusinessRoom;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyFamilyOfBusinessRoomsCollection;
}}

if (!class_exists("HomeStoreHierarchyFamilyOfBusinessRoomCriteria")) {
/**
 * HomeStoreHierarchyFamilyOfBusinessRoomCriteria
 */
class HomeStoreHierarchyFamilyOfBusinessRoomCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $RetailRoomID;
	/**
	 * @access public
	 * @var sint
	 */
	public $FamilyOfBusinessID;
}}

if (!class_exists("HomeStoreHierarchyFamilyOfBusinessRoomExecuteOption")) {
/**
 * HomeStoreHierarchyFamilyOfBusinessRoomExecuteOption
 */
class HomeStoreHierarchyFamilyOfBusinessRoomExecuteOption {
}}

if (!class_exists("GetHomeStoreHierarchyFamilyOfBusinessRoomsResponse")) {
/**
 * GetHomeStoreHierarchyFamilyOfBusinessRoomsResponse
 */
class GetHomeStoreHierarchyFamilyOfBusinessRoomsResponse {
	/**
	 * @access public
	 * @var HomeStoreHierarchyFamilyOfBusinessRoomResponse
	 */
	public $GetHomeStoreHierarchyFamilyOfBusinessRoomsResult;
}}

if (!class_exists("HomeStoreHierarchyFamilyOfBusinessRoomResponse")) {
/**
 * HomeStoreHierarchyFamilyOfBusinessRoomResponse
 */
class HomeStoreHierarchyFamilyOfBusinessRoomResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyFamilyOfBusinessRoom;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyFamilyOfBusinessRoomsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStorehierarchyFamilyOfBusinessesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailRoomsCollection;
}}

if (!class_exists("SetHomeStoreHierarchyFamilyOfBusinessRooms")) {
/**
 * SetHomeStoreHierarchyFamilyOfBusinessRooms
 */
class SetHomeStoreHierarchyFamilyOfBusinessRooms {
	/**
	 * @access public
	 * @var HomeStoreHierarchyFamilyOfBusinessRoomRequest
	 */
	public $request;
}}

if (!class_exists("SetHomeStoreHierarchyFamilyOfBusinessRoomsResponse")) {
/**
 * SetHomeStoreHierarchyFamilyOfBusinessRoomsResponse
 */
class SetHomeStoreHierarchyFamilyOfBusinessRoomsResponse {
	/**
	 * @access public
	 * @var HomeStoreHierarchyFamilyOfBusinessRoomResponse
	 */
	public $SetHomeStoreHierarchyFamilyOfBusinessRoomsResult;
}}

if (!class_exists("GetHomeStorehierarchyFamilyOfBusinesses")) {
/**
 * GetHomeStorehierarchyFamilyOfBusinesses
 */
class GetHomeStorehierarchyFamilyOfBusinesses {
	/**
	 * @access public
	 * @var HomeStorehierarchyFamilyOfBusinessRequest
	 */
	public $request;
}}

if (!class_exists("HomeStorehierarchyFamilyOfBusinessRequest")) {
/**
 * HomeStorehierarchyFamilyOfBusinessRequest
 */
class HomeStorehierarchyFamilyOfBusinessRequest extends RequestBase {
	/**
	 * @access public
	 * @var HomeStorehierarchyFamilyOfBusinessCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfHomeStorehierarchyFamilyOfBusinessExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStorehierarchyFamilyOfBusiness;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStorehierarchyFamilyOfBusinessesCollection;
}}

if (!class_exists("HomeStorehierarchyFamilyOfBusinessCriteria")) {
/**
 * HomeStorehierarchyFamilyOfBusinessCriteria
 */
class HomeStorehierarchyFamilyOfBusinessCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $FamilyOfBusinessID;
}}

if (!class_exists("HomeStorehierarchyFamilyOfBusinessExecuteOption")) {
/**
 * HomeStorehierarchyFamilyOfBusinessExecuteOption
 */
class HomeStorehierarchyFamilyOfBusinessExecuteOption {
}}

if (!class_exists("GetHomeStorehierarchyFamilyOfBusinessesResponse")) {
/**
 * GetHomeStorehierarchyFamilyOfBusinessesResponse
 */
class GetHomeStorehierarchyFamilyOfBusinessesResponse {
	/**
	 * @access public
	 * @var HomeStorehierarchyFamilyOfBusinessResponse
	 */
	public $GetHomeStorehierarchyFamilyOfBusinessesResult;
}}

if (!class_exists("HomeStorehierarchyFamilyOfBusinessResponse")) {
/**
 * HomeStorehierarchyFamilyOfBusinessResponse
 */
class HomeStorehierarchyFamilyOfBusinessResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStorehierarchyFamilyOfBusiness;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStorehierarchyFamilyOfBusinessesCollection;
}}

if (!class_exists("SetHomeStorehierarchyFamilyOfBusinesses")) {
/**
 * SetHomeStorehierarchyFamilyOfBusinesses
 */
class SetHomeStorehierarchyFamilyOfBusinesses {
	/**
	 * @access public
	 * @var HomeStorehierarchyFamilyOfBusinessRequest
	 */
	public $request;
}}

if (!class_exists("SetHomeStorehierarchyFamilyOfBusinessesResponse")) {
/**
 * SetHomeStorehierarchyFamilyOfBusinessesResponse
 */
class SetHomeStorehierarchyFamilyOfBusinessesResponse {
	/**
	 * @access public
	 * @var HomeStorehierarchyFamilyOfBusinessResponse
	 */
	public $SetHomeStorehierarchyFamilyOfBusinessesResult;
}}

if (!class_exists("GetHomeStoreHierarchyRetailRooms")) {
/**
 * GetHomeStoreHierarchyRetailRooms
 */
class GetHomeStoreHierarchyRetailRooms {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailRoomRequest
	 */
	public $request;
}}

if (!class_exists("HomeStoreHierarchyRetailRoomRequest")) {
/**
 * HomeStoreHierarchyRetailRoomRequest
 */
class HomeStoreHierarchyRetailRoomRequest extends RequestBase {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailRoomCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfHomeStoreHierarchyRetailRoomExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailRoom;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailRoomsCollection;
}}

if (!class_exists("HomeStoreHierarchyRetailRoomCriteria")) {
/**
 * HomeStoreHierarchyRetailRoomCriteria
 */
class HomeStoreHierarchyRetailRoomCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $RetailRoomID;
	/**
	 * @access public
	 * @var sint
	 */
	public $FamilyOfBusinessID;
}}

if (!class_exists("HomeStoreHierarchyRetailRoomExecuteOption")) {
/**
 * HomeStoreHierarchyRetailRoomExecuteOption
 */
class HomeStoreHierarchyRetailRoomExecuteOption {
}}

if (!class_exists("GetHomeStoreHierarchyRetailRoomsResponse")) {
/**
 * GetHomeStoreHierarchyRetailRoomsResponse
 */
class GetHomeStoreHierarchyRetailRoomsResponse {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailRoomResponse
	 */
	public $GetHomeStoreHierarchyRetailRoomsResult;
}}

if (!class_exists("HomeStoreHierarchyRetailRoomResponse")) {
/**
 * HomeStoreHierarchyRetailRoomResponse
 */
class HomeStoreHierarchyRetailRoomResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailRoom;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailRoomsCollection;
}}

if (!class_exists("SetHomeStoreHierarchyRetailRooms")) {
/**
 * SetHomeStoreHierarchyRetailRooms
 */
class SetHomeStoreHierarchyRetailRooms {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailRoomRequest
	 */
	public $request;
}}

if (!class_exists("SetHomeStoreHierarchyRetailRoomsResponse")) {
/**
 * SetHomeStoreHierarchyRetailRoomsResponse
 */
class SetHomeStoreHierarchyRetailRoomsResponse {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailRoomResponse
	 */
	public $SetHomeStoreHierarchyRetailRoomsResult;
}}

if (!class_exists("GetHomeStoreHierarchyRetailSalesCategories")) {
/**
 * GetHomeStoreHierarchyRetailSalesCategories
 */
class GetHomeStoreHierarchyRetailSalesCategories {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailSalesCategoryRequest
	 */
	public $request;
}}

if (!class_exists("HomeStoreHierarchyRetailSalesCategoryRequest")) {
/**
 * HomeStoreHierarchyRetailSalesCategoryRequest
 */
class HomeStoreHierarchyRetailSalesCategoryRequest extends RequestBase {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailSalesCategoryCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfHomeStoreHierarchyRetailSalesCategoryExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailSalesCategory;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailSalesCategoriesCollection;
}}

if (!class_exists("HomeStoreHierarchyRetailSalesCategoryCriteria")) {
/**
 * HomeStoreHierarchyRetailSalesCategoryCriteria
 */
class HomeStoreHierarchyRetailSalesCategoryCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $RetailSalesCategoryID;
}}

if (!class_exists("HomeStoreHierarchyRetailSalesCategoryExecuteOption")) {
/**
 * HomeStoreHierarchyRetailSalesCategoryExecuteOption
 */
class HomeStoreHierarchyRetailSalesCategoryExecuteOption {
}}

if (!class_exists("GetHomeStoreHierarchyRetailSalesCategoriesResponse")) {
/**
 * GetHomeStoreHierarchyRetailSalesCategoriesResponse
 */
class GetHomeStoreHierarchyRetailSalesCategoriesResponse {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailSalesCategoryResponse
	 */
	public $GetHomeStoreHierarchyRetailSalesCategoriesResult;
}}

if (!class_exists("HomeStoreHierarchyRetailSalesCategoryResponse")) {
/**
 * HomeStoreHierarchyRetailSalesCategoryResponse
 */
class HomeStoreHierarchyRetailSalesCategoryResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailSalesCategory;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailSalesCategoriesCollection;
}}

if (!class_exists("SetHomeStoreHierarchyRetailSalesCategories")) {
/**
 * SetHomeStoreHierarchyRetailSalesCategories
 */
class SetHomeStoreHierarchyRetailSalesCategories {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailSalesCategoryRequest
	 */
	public $request;
}}

if (!class_exists("SetHomeStoreHierarchyRetailSalesCategoriesResponse")) {
/**
 * SetHomeStoreHierarchyRetailSalesCategoriesResponse
 */
class SetHomeStoreHierarchyRetailSalesCategoriesResponse {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRetailSalesCategoryResponse
	 */
	public $SetHomeStoreHierarchyRetailSalesCategoriesResult;
}}

if (!class_exists("GetHomeStoreHierarchyRoomRetailSalesCategories")) {
/**
 * GetHomeStoreHierarchyRoomRetailSalesCategories
 */
class GetHomeStoreHierarchyRoomRetailSalesCategories {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRoomRetailSalesCategoryRequest
	 */
	public $request;
}}

if (!class_exists("HomeStoreHierarchyRoomRetailSalesCategoryRequest")) {
/**
 * HomeStoreHierarchyRoomRetailSalesCategoryRequest
 */
class HomeStoreHierarchyRoomRetailSalesCategoryRequest extends RequestBase {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRoomRetailSalesCategoryCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfHomeStoreHierarchyRoomRetailSalesCategoryExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRoomRetailSalesCategory;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRoomRetailSalesCategoriesCollection;
}}

if (!class_exists("HomeStoreHierarchyRoomRetailSalesCategoryCriteria")) {
/**
 * HomeStoreHierarchyRoomRetailSalesCategoryCriteria
 */
class HomeStoreHierarchyRoomRetailSalesCategoryCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $RetailRoomID;
	/**
	 * @access public
	 * @var sint
	 */
	public $RetailSalesCategoryID;
}}

if (!class_exists("HomeStoreHierarchyRoomRetailSalesCategoryExecuteOption")) {
/**
 * HomeStoreHierarchyRoomRetailSalesCategoryExecuteOption
 */
class HomeStoreHierarchyRoomRetailSalesCategoryExecuteOption {
}}

if (!class_exists("GetHomeStoreHierarchyRoomRetailSalesCategoriesResponse")) {
/**
 * GetHomeStoreHierarchyRoomRetailSalesCategoriesResponse
 */
class GetHomeStoreHierarchyRoomRetailSalesCategoriesResponse {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRoomRetailSalesCategoryResponse
	 */
	public $GetHomeStoreHierarchyRoomRetailSalesCategoriesResult;
}}

if (!class_exists("HomeStoreHierarchyRoomRetailSalesCategoryResponse")) {
/**
 * HomeStoreHierarchyRoomRetailSalesCategoryResponse
 */
class HomeStoreHierarchyRoomRetailSalesCategoryResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRoomRetailSalesCategory;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRoomRetailSalesCategoriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailRoomsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $HomeStoreHierarchyRetailSalesCategoriesCollection;
}}

if (!class_exists("SetHomeStoreHierarchyRoomRetailSalesCategories")) {
/**
 * SetHomeStoreHierarchyRoomRetailSalesCategories
 */
class SetHomeStoreHierarchyRoomRetailSalesCategories {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRoomRetailSalesCategoryRequest
	 */
	public $request;
}}

if (!class_exists("SetHomeStoreHierarchyRoomRetailSalesCategoriesResponse")) {
/**
 * SetHomeStoreHierarchyRoomRetailSalesCategoriesResponse
 */
class SetHomeStoreHierarchyRoomRetailSalesCategoriesResponse {
	/**
	 * @access public
	 * @var HomeStoreHierarchyRoomRetailSalesCategoryResponse
	 */
	public $SetHomeStoreHierarchyRoomRetailSalesCategoriesResult;
}}

if (!class_exists("GetImageLibraryAssociations")) {
/**
 * GetImageLibraryAssociations
 */
class GetImageLibraryAssociations {
	/**
	 * @access public
	 * @var ImageLibraryAssociationRequest
	 */
	public $request;
}}

if (!class_exists("ImageLibraryAssociationRequest")) {
/**
 * ImageLibraryAssociationRequest
 */
class ImageLibraryAssociationRequest extends RequestBase {
	/**
	 * @access public
	 * @var ImageLibraryAssociationCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfImageLibraryAssociationExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageLibraryAssociation;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageLibraryAssociationsCollection;
}}

if (!class_exists("ImageLibraryAssociationCriteria")) {
/**
 * ImageLibraryAssociationCriteria
 */
class ImageLibraryAssociationCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemSku;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ImageName;
}}

if (!class_exists("ImageLibraryAssociationExecuteOption")) {
/**
 * ImageLibraryAssociationExecuteOption
 */
class ImageLibraryAssociationExecuteOption {
}}

if (!class_exists("GetImageLibraryAssociationsResponse")) {
/**
 * GetImageLibraryAssociationsResponse
 */
class GetImageLibraryAssociationsResponse {
	/**
	 * @access public
	 * @var ImageLibraryAssociationResponse
	 */
	public $GetImageLibraryAssociationsResult;
}}

if (!class_exists("ImageLibraryAssociationResponse")) {
/**
 * ImageLibraryAssociationResponse
 */
class ImageLibraryAssociationResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageLibraryAssociation;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageLibraryAssociationsCollection;
}}

if (!class_exists("SetImageLibraryAssociations")) {
/**
 * SetImageLibraryAssociations
 */
class SetImageLibraryAssociations {
	/**
	 * @access public
	 * @var ImageLibraryAssociationRequest
	 */
	public $request;
}}

if (!class_exists("SetImageLibraryAssociationsResponse")) {
/**
 * SetImageLibraryAssociationsResponse
 */
class SetImageLibraryAssociationsResponse {
	/**
	 * @access public
	 * @var ImageLibraryAssociationResponse
	 */
	public $SetImageLibraryAssociationsResult;
}}

if (!class_exists("GetImageLibraries")) {
/**
 * GetImageLibraries
 */
class GetImageLibraries {
	/**
	 * @access public
	 * @var ImageLibraryRequest
	 */
	public $request;
}}

if (!class_exists("ImageLibraryRequest")) {
/**
 * ImageLibraryRequest
 */
class ImageLibraryRequest extends RequestBase {
	/**
	 * @access public
	 * @var ImageLibraryCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfImageLibraryExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageLibrary;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageLibrariesCollection;
}}

if (!class_exists("ImageLibraryCriteria")) {
/**
 * ImageLibraryCriteria
 */
class ImageLibraryCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ImageName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Opt;
}}

if (!class_exists("ImageLibraryExecuteOption")) {
/**
 * ImageLibraryExecuteOption
 */
class ImageLibraryExecuteOption {
}}

if (!class_exists("GetImageLibrariesResponse")) {
/**
 * GetImageLibrariesResponse
 */
class GetImageLibrariesResponse {
	/**
	 * @access public
	 * @var ImageLibraryResponse
	 */
	public $GetImageLibrariesResult;
}}

if (!class_exists("ImageLibraryResponse")) {
/**
 * ImageLibraryResponse
 */
class ImageLibraryResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageLibrary;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageLibrariesCollection;
}}

if (!class_exists("SetImageLibraries")) {
/**
 * SetImageLibraries
 */
class SetImageLibraries {
	/**
	 * @access public
	 * @var ImageLibraryRequest
	 */
	public $request;
}}

if (!class_exists("SetImageLibrariesResponse")) {
/**
 * SetImageLibrariesResponse
 */
class SetImageLibrariesResponse {
	/**
	 * @access public
	 * @var ImageLibraryResponse
	 */
	public $SetImageLibrariesResult;
}}

if (!class_exists("GetCatalogImages")) {
/**
 * GetCatalogImages
 */
class GetCatalogImages {
	/**
	 * @access public
	 * @var CatalogImageRequest
	 */
	public $request;
}}

if (!class_exists("CatalogImageRequest")) {
/**
 * CatalogImageRequest
 */
class CatalogImageRequest extends RequestBase {
	/**
	 * @access public
	 * @var CatalogImageCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfCatalogImageExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CatalogImage;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CatalogImagesCollection;
}}

if (!class_exists("CatalogImageCriteria")) {
/**
 * CatalogImageCriteria
 */
class CatalogImageCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ImageType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ImageName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Opt;
}}

if (!class_exists("CatalogImageExecuteOption")) {
/**
 * CatalogImageExecuteOption
 */
class CatalogImageExecuteOption {
}}

if (!class_exists("GetCatalogImagesResponse")) {
/**
 * GetCatalogImagesResponse
 */
class GetCatalogImagesResponse {
	/**
	 * @access public
	 * @var CatalogImageResponse
	 */
	public $GetCatalogImagesResult;
}}

if (!class_exists("CatalogImageResponse")) {
/**
 * CatalogImageResponse
 */
class CatalogImageResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CatalogImage;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $CatalogImagesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageItemsCollection;
}}

if (!class_exists("SetCatalogImages")) {
/**
 * SetCatalogImages
 */
class SetCatalogImages {
	/**
	 * @access public
	 * @var CatalogImageRequest
	 */
	public $request;
}}

if (!class_exists("SetCatalogImagesResponse")) {
/**
 * SetCatalogImagesResponse
 */
class SetCatalogImagesResponse {
	/**
	 * @access public
	 * @var CatalogImageResponse
	 */
	public $SetCatalogImagesResult;
}}

if (!class_exists("GetItemCategoryGroupings")) {
/**
 * GetItemCategoryGroupings
 */
class GetItemCategoryGroupings {
	/**
	 * @access public
	 * @var ItemCategoryGroupingRequest
	 */
	public $request;
}}

if (!class_exists("ItemCategoryGroupingRequest")) {
/**
 * ItemCategoryGroupingRequest
 */
class ItemCategoryGroupingRequest extends RequestBase {
	/**
	 * @access public
	 * @var ItemCategoryGroupingCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfItemCategoryGroupingExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCategoryGroupingCollection;
}}

if (!class_exists("ItemCategoryGroupingCriteria")) {
/**
 * ItemCategoryGroupingCriteria
 */
class ItemCategoryGroupingCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $CategoryID;
}}

if (!class_exists("ItemCategoryGroupingExecuteOption")) {
/**
 * ItemCategoryGroupingExecuteOption
 */
class ItemCategoryGroupingExecuteOption {
}}

if (!class_exists("GetItemCategoryGroupingsResponse")) {
/**
 * GetItemCategoryGroupingsResponse
 */
class GetItemCategoryGroupingsResponse {
	/**
	 * @access public
	 * @var ItemCategoryGroupingResponse
	 */
	public $GetItemCategoryGroupingsResult;
}}

if (!class_exists("ItemCategoryGroupingResponse")) {
/**
 * ItemCategoryGroupingResponse
 */
class ItemCategoryGroupingResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $AvailableItemCategoryGroupingsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LinkedItemCategoryGroupingsCollection;
}}

if (!class_exists("SetItemCategoryGroupings")) {
/**
 * SetItemCategoryGroupings
 */
class SetItemCategoryGroupings {
	/**
	 * @access public
	 * @var ItemCategoryGroupingRequest
	 */
	public $request;
}}

if (!class_exists("SetItemCategoryGroupingsResponse")) {
/**
 * SetItemCategoryGroupingsResponse
 */
class SetItemCategoryGroupingsResponse {
	/**
	 * @access public
	 * @var ItemCategoryGroupingResponse
	 */
	public $SetItemCategoryGroupingsResult;
}}

if (!class_exists("GetItemCountries")) {
/**
 * GetItemCountries
 */
class GetItemCountries {
	/**
	 * @access public
	 * @var ItemCountryRequest
	 */
	public $request;
}}

if (!class_exists("ItemCountryRequest")) {
/**
 * ItemCountryRequest
 */
class ItemCountryRequest extends RequestBase {
	/**
	 * @access public
	 * @var ItemCountryCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfItemCountryExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCountry;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCountriesCollection;
}}

if (!class_exists("ItemCountryCriteria")) {
/**
 * ItemCountryCriteria
 */
class ItemCountryCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Country;
}}

if (!class_exists("ItemCountryExecuteOption")) {
/**
 * ItemCountryExecuteOption
 */
class ItemCountryExecuteOption {
}}

if (!class_exists("GetItemCountriesResponse")) {
/**
 * GetItemCountriesResponse
 */
class GetItemCountriesResponse {
	/**
	 * @access public
	 * @var ItemCountryResponse
	 */
	public $GetItemCountriesResult;
}}

if (!class_exists("ItemCountryResponse")) {
/**
 * ItemCountryResponse
 */
class ItemCountryResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCountry;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCountriesCollection;
}}

if (!class_exists("SetItemCountries")) {
/**
 * SetItemCountries
 */
class SetItemCountries {
	/**
	 * @access public
	 * @var ItemCountryRequest
	 */
	public $request;
}}

if (!class_exists("SetItemCountriesResponse")) {
/**
 * SetItemCountriesResponse
 */
class SetItemCountriesResponse {
	/**
	 * @access public
	 * @var ItemCountryResponse
	 */
	public $SetItemCountriesResult;
}}

if (!class_exists("GetItemDifferenceCodes")) {
/**
 * GetItemDifferenceCodes
 */
class GetItemDifferenceCodes {
	/**
	 * @access public
	 * @var ItemDifferenceCodeRequest
	 */
	public $request;
}}

if (!class_exists("ItemDifferenceCodeRequest")) {
/**
 * ItemDifferenceCodeRequest
 */
class ItemDifferenceCodeRequest extends RequestBase {
	/**
	 * @access public
	 * @var ItemDifferenceCodeCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfItemDifferenceCodeExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemDifferenceCode;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemDifferenceCodesCollection;
}}

if (!class_exists("ItemDifferenceCodeCriteria")) {
/**
 * ItemDifferenceCodeCriteria
 */
class ItemDifferenceCodeCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $DifferenceCode;
}}

if (!class_exists("ItemDifferenceCodeExecuteOption")) {
/**
 * ItemDifferenceCodeExecuteOption
 */
class ItemDifferenceCodeExecuteOption {
}}

if (!class_exists("GetItemDifferenceCodesResponse")) {
/**
 * GetItemDifferenceCodesResponse
 */
class GetItemDifferenceCodesResponse {
	/**
	 * @access public
	 * @var ItemDifferenceCodeResponse
	 */
	public $GetItemDifferenceCodesResult;
}}

if (!class_exists("ItemDifferenceCodeResponse")) {
/**
 * ItemDifferenceCodeResponse
 */
class ItemDifferenceCodeResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemDifferenceCode;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemDifferenceCodesCollection;
}}

if (!class_exists("SetItemDifferenceCodes")) {
/**
 * SetItemDifferenceCodes
 */
class SetItemDifferenceCodes {
	/**
	 * @access public
	 * @var ItemDifferenceCodeRequest
	 */
	public $request;
}}

if (!class_exists("SetItemDifferenceCodesResponse")) {
/**
 * SetItemDifferenceCodesResponse
 */
class SetItemDifferenceCodesResponse {
	/**
	 * @access public
	 * @var ItemDifferenceCodeResponse
	 */
	public $SetItemDifferenceCodesResult;
}}

if (!class_exists("GetItemPricings")) {
/**
 * GetItemPricings
 */
class GetItemPricings {
	/**
	 * @access public
	 * @var ItemPricingRequest
	 */
	public $request;
}}

if (!class_exists("ItemPricingRequest")) {
/**
 * ItemPricingRequest
 */
class ItemPricingRequest extends RequestBase {
	/**
	 * @access public
	 * @var ItemPricingCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfItemPricingExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemPricing;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemPricingsCollection;
}}

if (!class_exists("ItemPricingCriteria")) {
/**
 * ItemPricingCriteria
 */
class ItemPricingCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemSku;
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
}}

if (!class_exists("ItemPricingExecuteOption")) {
/**
 * ItemPricingExecuteOption
 */
class ItemPricingExecuteOption {
}}

if (!class_exists("GetItemPricingsResponse")) {
/**
 * GetItemPricingsResponse
 */
class GetItemPricingsResponse {
	/**
	 * @access public
	 * @var ItemPricingResponse
	 */
	public $GetItemPricingsResult;
}}

if (!class_exists("ItemPricingResponse")) {
/**
 * ItemPricingResponse
 */
class ItemPricingResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemPricing;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemPricingsCollection;
}}

if (!class_exists("SetItemPricings")) {
/**
 * SetItemPricings
 */
class SetItemPricings {
	/**
	 * @access public
	 * @var ItemPricingRequest
	 */
	public $request;
}}

if (!class_exists("SetItemPricingsResponse")) {
/**
 * SetItemPricingsResponse
 */
class SetItemPricingsResponse {
	/**
	 * @access public
	 * @var ItemPricingResponse
	 */
	public $SetItemPricingsResult;
}}

if (!class_exists("GetItems")) {
/**
 * GetItems
 */
class GetItems {
	/**
	 * @access public
	 * @var ItemRequest
	 */
	public $request;
}}

if (!class_exists("ItemRequest")) {
/**
 * ItemRequest
 */
class ItemRequest extends RequestBase {
	/**
	 * @access public
	 * @var ItemCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfItemExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Item;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageItem;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Series;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemImagesKnockout;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemImagesImageSet;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SitesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehousesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemImagesKnockoutCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemImagesImageSetCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemNumbersCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemPricesCollection;
}}

if (!class_exists("ItemCriteria")) {
/**
 * ItemCriteria
 */
class ItemCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemNo;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesNo;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SeriesName;
	/**
	 * @access public
	 * @var sint
	 */
	public $GroupingId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $StyleCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $CategoryId;
	/**
	 * @access public
	 * @var sint
	 */
	public $Days;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ItemSelectionCriteria;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SiteType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PublishCodeId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $CustomerNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ShipToNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $OrderNumber;
	/**
	 * @access public
	 * @var sstring
	 */
	public $SessionId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PriceType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ShipType;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Warehouse;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $EcommMaster;
	/**
	 * @access public
	 * @var sstring
	 */
	public $OrderId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $PriceCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $BuyGroup;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $IncludeVAT;
	/**
	 * @access public
	 * @var sboolean
	 */
	public $SellablePackagesOnly;
	/**
	 * @access public
	 * @var sstring
	 */
	public $Application;
}}

if (!class_exists("ItemExecuteOption")) {
/**
 * ItemExecuteOption
 */
class ItemExecuteOption {
}}

if (!class_exists("GetItemsResponse")) {
/**
 * GetItemsResponse
 */
class GetItemsResponse {
	/**
	 * @access public
	 * @var ItemResponse
	 */
	public $GetItemsResult;
}}

if (!class_exists("ItemResponse")) {
/**
 * ItemResponse
 */
class ItemResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Item;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ImageItem;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemImagesKnockout;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemImagesImageSet;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $SitesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehousesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PhotoCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemImagesKnockoutCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemImagesImageSetCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $DimensionsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemPricesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackagesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemProductAttributesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemCategoriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemMaterialsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $StatusChangeIseriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $MarketableItemsCollection;
}}

if (!class_exists("SetItems")) {
/**
 * SetItems
 */
class SetItems {
	/**
	 * @access public
	 * @var ItemRequest
	 */
	public $request;
}}

if (!class_exists("SetItemsResponse")) {
/**
 * SetItemsResponse
 */
class SetItemsResponse {
	/**
	 * @access public
	 * @var ItemResponse
	 */
	public $SetItemsResult;
}}

if (!class_exists("GetItemStatuses")) {
/**
 * GetItemStatuses
 */
class GetItemStatuses {
	/**
	 * @access public
	 * @var ItemStatusRequest
	 */
	public $request;
}}

if (!class_exists("ItemStatusRequest")) {
/**
 * ItemStatusRequest
 */
class ItemStatusRequest extends RequestBase {
	/**
	 * @access public
	 * @var ItemStatusCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfItemStatusExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemStatus;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemStatusesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ReleaseTemplatesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentDropDownsCollection;
}}

if (!class_exists("ItemStatusCriteria")) {
/**
 * ItemStatusCriteria
 */
class ItemStatusCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $EnvironmentCode;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
}}

if (!class_exists("ItemStatusExecuteOption")) {
/**
 * ItemStatusExecuteOption
 */
class ItemStatusExecuteOption {
}}

if (!class_exists("GetItemStatusesResponse")) {
/**
 * GetItemStatusesResponse
 */
class GetItemStatusesResponse {
	/**
	 * @access public
	 * @var ItemStatusResponse
	 */
	public $GetItemStatusesResult;
}}

if (!class_exists("ItemStatusResponse")) {
/**
 * ItemStatusResponse
 */
class ItemStatusResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemStatus;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemStatusesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ReleaseTemplatesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentDropDownsCollection;
}}

if (!class_exists("SetItemStatuses")) {
/**
 * SetItemStatuses
 */
class SetItemStatuses {
	/**
	 * @access public
	 * @var ItemStatusRequest
	 */
	public $request;
}}

if (!class_exists("SetItemStatusesResponse")) {
/**
 * SetItemStatusesResponse
 */
class SetItemStatusesResponse {
	/**
	 * @access public
	 * @var ItemStatusResponse
	 */
	public $SetItemStatusesResult;
}}

if (!class_exists("GetItemValidation")) {
/**
 * GetItemValidation
 */
class GetItemValidation {
	/**
	 * @access public
	 * @var ItemValidationRequest
	 */
	public $request;
}}

if (!class_exists("ItemValidationRequest")) {
/**
 * ItemValidationRequest
 */
class ItemValidationRequest extends RequestBase {
	/**
	 * @access public
	 * @var ItemValidationCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfItemValidationExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemValidationCollection;
}}

if (!class_exists("ItemValidationCriteria")) {
/**
 * ItemValidationCriteria
 */
class ItemValidationCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sstring
	 */
	public $StatusOption1;
	/**
	 * @access public
	 * @var sstring
	 */
	public $StatusOption2;
	/**
	 * @access public
	 * @var sstring
	 */
	public $StartDate;
	/**
	 * @access public
	 * @var sstring
	 */
	public $EndDate;
}}

if (!class_exists("ItemValidationExecuteOption")) {
/**
 * ItemValidationExecuteOption
 */
class ItemValidationExecuteOption {
}}

if (!class_exists("GetItemValidationResponse")) {
/**
 * GetItemValidationResponse
 */
class GetItemValidationResponse {
	/**
	 * @access public
	 * @var ItemValidationResponse
	 */
	public $GetItemValidationResult;
}}

if (!class_exists("ItemValidationResponse")) {
/**
 * ItemValidationResponse
 */
class ItemValidationResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $ItemValidationCollection;
}}

if (!class_exists("GetLanguages")) {
/**
 * GetLanguages
 */
class GetLanguages {
	/**
	 * @access public
	 * @var LanguageRequest
	 */
	public $request;
}}

if (!class_exists("LanguageRequest")) {
/**
 * LanguageRequest
 */
class LanguageRequest extends RequestBase {
	/**
	 * @access public
	 * @var LanguageCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfLanguageExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Language;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("LanguageExecuteOption")) {
/**
 * LanguageExecuteOption
 */
class LanguageExecuteOption {
}}

if (!class_exists("GetLanguagesResponse")) {
/**
 * GetLanguagesResponse
 */
class GetLanguagesResponse {
	/**
	 * @access public
	 * @var LanguageResponse
	 */
	public $GetLanguagesResult;
}}

if (!class_exists("LanguageResponse")) {
/**
 * LanguageResponse
 */
class LanguageResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Language;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetLanguages")) {
/**
 * SetLanguages
 */
class SetLanguages {
	/**
	 * @access public
	 * @var LanguageRequest
	 */
	public $request;
}}

if (!class_exists("SetLanguagesResponse")) {
/**
 * SetLanguagesResponse
 */
class SetLanguagesResponse {
	/**
	 * @access public
	 * @var LanguageResponse
	 */
	public $SetLanguagesResult;
}}

if (!class_exists("GetmlCategories")) {
/**
 * GetmlCategories
 */
class GetmlCategories {
	/**
	 * @access public
	 * @var mlCategoryRequest
	 */
	public $request;
}}

if (!class_exists("mlCategoryRequest")) {
/**
 * mlCategoryRequest
 */
class mlCategoryRequest extends RequestBase {
	/**
	 * @access public
	 * @var mlCategoryCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfMLCategoryExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCategory;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCategoriesCollection;
}}

if (!class_exists("mlCategoryCriteria")) {
/**
 * mlCategoryCriteria
 */
class mlCategoryCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $CategoryId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $LanguageCode;
}}

if (!class_exists("MLCategoryExecuteOption")) {
/**
 * MLCategoryExecuteOption
 */
class MLCategoryExecuteOption {
}}

if (!class_exists("GetmlCategoriesResponse")) {
/**
 * GetmlCategoriesResponse
 */
class GetmlCategoriesResponse {
	/**
	 * @access public
	 * @var mlCategoryResponse
	 */
	public $GetmlCategoriesResult;
}}

if (!class_exists("mlCategoryResponse")) {
/**
 * mlCategoryResponse
 */
class mlCategoryResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCategory;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $mlCategoriesCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $LanguagesCollection;
}}

if (!class_exists("SetmlCategories")) {
/**
 * SetmlCategories
 */
class SetmlCategories {
	/**
	 * @access public
	 * @var mlCategoryRequest
	 */
	public $request;
}}

if (!class_exists("SetmlCategoriesResponse")) {
/**
 * SetmlCategoriesResponse
 */
class SetmlCategoriesResponse {
	/**
	 * @access public
	 * @var mlCategoryResponse
	 */
	public $SetmlCategoriesResult;
}}

if (!class_exists("WarehouseClassDetailRequest")) {
/**
 * WarehouseClassDetailRequest
 */
class WarehouseClassDetailRequest extends RequestBase {
	/**
	 * @access public
	 * @var WarehouseClassDetailCriteria
	 */
	public $Criteria;
	/**
	 * @access public
	 * @var ArrayOfWarehouseClassDetailExecuteOption
	 */
	public $ExecuteOptions;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassDetail;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassDetailsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentDropDownsCollection;
}}

if (!class_exists("WarehouseClassDetailCriteria")) {
/**
 * WarehouseClassDetailCriteria
 */
class WarehouseClassDetailCriteria extends CriteriaBase {
	/**
	 * @access public
	 * @var sint
	 */
	public $RecordId;
	/**
	 * @access public
	 * @var sstring
	 */
	public $ApplicationName;
	/**
	 * @access public
	 * @var sstring
	 */
	public $DatabaseSystem;
}}

if (!class_exists("WarehouseClassDetailResponse")) {
/**
 * WarehouseClassDetailResponse
 */
class WarehouseClassDetailResponse extends ResponseBase {
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassDetail;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseClassDetailsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $WarehouseTypeLookupsCollection;
	/**
	 * @access public
	 * @var ServiceData
	 */
	public $EnvironmentDropDownsCollection;
}}

if (!class_exists("ProductKnowledgeService")) {
/**
 * ProductKnowledgeService
 * @author WSDLInterpreter
 */
class ProductKnowledgeService extends SoapClient {
	/**
	 * Default class map for wsdl=>php
	 * @access private
	 * @var array
	 */
	private static $classmap = array(
		"GetWarehouseClassDetails" => "GetWarehouseClassDetails",
		"WarehouseClassDetailRequest" => "WarehouseClassDetailRequest",
		"RequestBase" => "RequestBase",
		"TransportDataType" => "TransportDataType",
		"PersistType" => "PersistType",
		"WarehouseClassDetailCriteria" => "WarehouseClassDetailCriteria",
		"CriteriaBase" => "CriteriaBase",
		"WarehouseClassDetailExecuteOption" => "WarehouseClassDetailExecuteOption",
		"ServiceData" => "ServiceData",
		"XmlDocumentData" => "XmlDocumentData",
		"GetWarehouseClassDetailsResponse" => "GetWarehouseClassDetailsResponse",
		"WarehouseClassDetailResponse" => "WarehouseClassDetailResponse",
		"ResponseBase" => "ResponseBase",
		"AcknowledgeType" => "AcknowledgeType",
		"SetWarehouseClassDetails" => "SetWarehouseClassDetails",
		"SetWarehouseClassDetailsResponse" => "SetWarehouseClassDetailsResponse",
		"GetWarehouseClassHeaders" => "GetWarehouseClassHeaders",
		"WarehouseClassHeaderRequest" => "WarehouseClassHeaderRequest",
		"WarehouseClassHeaderCriteria" => "WarehouseClassHeaderCriteria",
		"WarehouseClassHeaderExecuteOption" => "WarehouseClassHeaderExecuteOption",
		"GetWarehouseClassHeadersResponse" => "GetWarehouseClassHeadersResponse",
		"WarehouseClassHeaderResponse" => "WarehouseClassHeaderResponse",
		"SetWarehouseClassHeaders" => "SetWarehouseClassHeaders",
		"SetWarehouseClassHeadersResponse" => "SetWarehouseClassHeadersResponse",
		"GetWarehouseClassSites" => "GetWarehouseClassSites",
		"WarehouseClassSiteRequest" => "WarehouseClassSiteRequest",
		"WarehouseClassSiteCriteria" => "WarehouseClassSiteCriteria",
		"WarehouseClassSiteExecuteOption" => "WarehouseClassSiteExecuteOption",
		"GetWarehouseClassSitesResponse" => "GetWarehouseClassSitesResponse",
		"WarehouseClassSiteResponse" => "WarehouseClassSiteResponse",
		"SetWarehouseClassSites" => "SetWarehouseClassSites",
		"SetWarehouseClassSitesResponse" => "SetWarehouseClassSitesResponse",
		"GetWarehouseGroupCountries" => "GetWarehouseGroupCountries",
		"WarehouseGroupCountryRequest" => "WarehouseGroupCountryRequest",
		"WarehouseGroupCountryCriteria" => "WarehouseGroupCountryCriteria",
		"WarehouseGroupCountryExecuteOption" => "WarehouseGroupCountryExecuteOption",
		"GetWarehouseGroupCountriesResponse" => "GetWarehouseGroupCountriesResponse",
		"WarehouseGroupCountryResponse" => "WarehouseGroupCountryResponse",
		"SetWarehouseGroupCountries" => "SetWarehouseGroupCountries",
		"SetWarehouseGroupCountriesResponse" => "SetWarehouseGroupCountriesResponse",
		"GetWarehouseGroups" => "GetWarehouseGroups",
		"WarehouseGroupRequest" => "WarehouseGroupRequest",
		"WarehouseGroupCriteria" => "WarehouseGroupCriteria",
		"WarehouseGroupExecuteOption" => "WarehouseGroupExecuteOption",
		"GetWarehouseGroupsResponse" => "GetWarehouseGroupsResponse",
		"WarehouseGroupResponse" => "WarehouseGroupResponse",
		"SetWarehouseGroups" => "SetWarehouseGroups",
		"SetWarehouseGroupsResponse" => "SetWarehouseGroupsResponse",
		"GetmlCleanCodes" => "GetmlCleanCodes",
		"mlCleanCodeRequest" => "mlCleanCodeRequest",
		"mlCleanCodeCriteria" => "mlCleanCodeCriteria",
		"MLCleanCodeExecuteOption" => "MLCleanCodeExecuteOption",
		"GetmlCleanCodesResponse" => "GetmlCleanCodesResponse",
		"mlCleanCodeResponse" => "mlCleanCodeResponse",
		"SetmlCleanCodes" => "SetmlCleanCodes",
		"SetmlCleanCodesResponse" => "SetmlCleanCodesResponse",
		"GetmlColors" => "GetmlColors",
		"mlColorRequest" => "mlColorRequest",
		"mlColorCriteria" => "mlColorCriteria",
		"MLColorExecuteOption" => "MLColorExecuteOption",
		"GetmlColorsResponse" => "GetmlColorsResponse",
		"mlColorResponse" => "mlColorResponse",
		"SetmlColors" => "SetmlColors",
		"SetmlColorsResponse" => "SetmlColorsResponse",
		"GetmlCoverContents" => "GetmlCoverContents",
		"mlCoverContentRequest" => "mlCoverContentRequest",
		"mlCoverContentCriteria" => "mlCoverContentCriteria",
		"MLCoverContentExecuteOption" => "MLCoverContentExecuteOption",
		"GetmlCoverContentsResponse" => "GetmlCoverContentsResponse",
		"mlCoverContentResponse" => "mlCoverContentResponse",
		"SetmlCoverContents" => "SetmlCoverContents",
		"SetmlCoverContentsResponse" => "SetmlCoverContentsResponse",
		"GetmlCovers" => "GetmlCovers",
		"mlCoverRequest" => "mlCoverRequest",
		"mlCoverCriteria" => "mlCoverCriteria",
		"MLCoverExecuteOption" => "MLCoverExecuteOption",
		"GetmlCoversResponse" => "GetmlCoversResponse",
		"mlCoverResponse" => "mlCoverResponse",
		"SetmlCovers" => "SetmlCovers",
		"SetmlCoversResponse" => "SetmlCoversResponse",
		"GetmlDivisions" => "GetmlDivisions",
		"mlDivisionRequest" => "mlDivisionRequest",
		"mlDivisionCriteria" => "mlDivisionCriteria",
		"MLDivisionExecuteOption" => "MLDivisionExecuteOption",
		"GetmlDivisionsResponse" => "GetmlDivisionsResponse",
		"mlDivisionResponse" => "mlDivisionResponse",
		"SetmlDivisions" => "SetmlDivisions",
		"SetmlDivisionsResponse" => "SetmlDivisionsResponse",
		"GetMLGeneralDescriptions" => "GetMLGeneralDescriptions",
		"MLGeneralDescriptionRequest" => "MLGeneralDescriptionRequest",
		"MLGeneralDescriptionCriteria" => "MLGeneralDescriptionCriteria",
		"MLGeneralDescriptionExecuteOption" => "MLGeneralDescriptionExecuteOption",
		"GetMLGeneralDescriptionsResponse" => "GetMLGeneralDescriptionsResponse",
		"MLGeneralDescriptionResponse" => "MLGeneralDescriptionResponse",
		"SetMLGeneralDescriptions" => "SetMLGeneralDescriptions",
		"SetMLGeneralDescriptionsResponse" => "SetMLGeneralDescriptionsResponse",
		"GetmlGroupings" => "GetmlGroupings",
		"mlGroupingRequest" => "mlGroupingRequest",
		"mlGroupingCriteria" => "mlGroupingCriteria",
		"MLGroupingExecuteOption" => "MLGroupingExecuteOption",
		"GetmlGroupingsResponse" => "GetmlGroupingsResponse",
		"mlGroupingResponse" => "mlGroupingResponse",
		"SetmlGroupings" => "SetmlGroupings",
		"SetmlGroupingsResponse" => "SetmlGroupingsResponse",
		"GetmlItemStatuses" => "GetmlItemStatuses",
		"mlItemStatusRequest" => "mlItemStatusRequest",
		"mlItemStatusCriteria" => "mlItemStatusCriteria",
		"MLItemStatusExecuteOption" => "MLItemStatusExecuteOption",
		"GetmlItemStatusesResponse" => "GetmlItemStatusesResponse",
		"mlItemStatusResponse" => "mlItemStatusResponse",
		"SetmlItemStatuses" => "SetmlItemStatuses",
		"SetmlItemStatusesResponse" => "SetmlItemStatusesResponse",
		"GetmlSerieses" => "GetmlSerieses",
		"mlSeriesRequest" => "mlSeriesRequest",
		"mlSeriesCriteria" => "mlSeriesCriteria",
		"LanguageCriteria" => "LanguageCriteria",
		"MLSeriesExecuteOption" => "MLSeriesExecuteOption",
		"GetmlSeriesesResponse" => "GetmlSeriesesResponse",
		"mlSeriesResponse" => "mlSeriesResponse",
		"SetmlSerieses" => "SetmlSerieses",
		"SetmlSeriesesResponse" => "SetmlSeriesesResponse",
		"GetmlStyles" => "GetmlStyles",
		"mlStyleRequest" => "mlStyleRequest",
		"mlStyleCriteria" => "mlStyleCriteria",
		"MLStyleExecuteOption" => "MLStyleExecuteOption",
		"GetmlStylesResponse" => "GetmlStylesResponse",
		"mlStyleResponse" => "mlStyleResponse",
		"SetmlStyles" => "SetmlStyles",
		"SetmlStylesResponse" => "SetmlStylesResponse",
		"Getml_items" => "Getml_items",
		"ml_itemRequest" => "ml_itemRequest",
		"ml_itemCriteria" => "ml_itemCriteria",
		"ML_itemExecuteOption" => "ML_itemExecuteOption",
		"Getml_itemsResponse" => "Getml_itemsResponse",
		"ml_itemResponse" => "ml_itemResponse",
		"Setml_items" => "Setml_items",
		"Setml_itemsResponse" => "Setml_itemsResponse",
		"GetMarkets" => "GetMarkets",
		"MarketRequest" => "MarketRequest",
		"MarketCriteria" => "MarketCriteria",
		"MarketExecuteOption" => "MarketExecuteOption",
		"GetMarketsResponse" => "GetMarketsResponse",
		"MarketResponse" => "MarketResponse",
		"SetMarkets" => "SetMarkets",
		"SetMarketsResponse" => "SetMarketsResponse",
		"GetMissingItemPhotos" => "GetMissingItemPhotos",
		"MissingItemPhotoRequest" => "MissingItemPhotoRequest",
		"MissingItemPhotoCriteria" => "MissingItemPhotoCriteria",
		"MissingItemPhotoExecuteOption" => "MissingItemPhotoExecuteOption",
		"GetMissingItemPhotosResponse" => "GetMissingItemPhotosResponse",
		"MissingItemPhotoResponse" => "MissingItemPhotoResponse",
		"SetMissingItemPhotos" => "SetMissingItemPhotos",
		"SetMissingItemPhotosResponse" => "SetMissingItemPhotosResponse",
		"GetMultiSeriesDescriptions" => "GetMultiSeriesDescriptions",
		"MultiSeriesDescriptionRequest" => "MultiSeriesDescriptionRequest",
		"MultiSeriesDescriptionCriteria" => "MultiSeriesDescriptionCriteria",
		"MultiSeriesDescriptionExecuteOption" => "MultiSeriesDescriptionExecuteOption",
		"GetMultiSeriesDescriptionsResponse" => "GetMultiSeriesDescriptionsResponse",
		"MultiSeriesDescriptionResponse" => "MultiSeriesDescriptionResponse",
		"SetMultiSeriesDescriptions" => "SetMultiSeriesDescriptions",
		"SetMultiSeriesDescriptionsResponse" => "SetMultiSeriesDescriptionsResponse",
		"GetNewModelInspectionReports" => "GetNewModelInspectionReports",
		"NewModelInspectionRequest" => "NewModelInspectionRequest",
		"NewModelInspectionCriteria" => "NewModelInspectionCriteria",
		"NewModelInspectionExecuteOption" => "NewModelInspectionExecuteOption",
		"GetNewModelInspectionReportsResponse" => "GetNewModelInspectionReportsResponse",
		"NewModelInspectionResponse" => "NewModelInspectionResponse",
		"GetPackageApplicationCodes" => "GetPackageApplicationCodes",
		"PackageApplicationCodeRequest" => "PackageApplicationCodeRequest",
		"PackageApplicationCodeCriteria" => "PackageApplicationCodeCriteria",
		"PackageApplicationCodeExecuteOption" => "PackageApplicationCodeExecuteOption",
		"GetPackageApplicationCodesResponse" => "GetPackageApplicationCodesResponse",
		"PackageApplicationCodeResponse" => "PackageApplicationCodeResponse",
		"SetPackageApplicationCodes" => "SetPackageApplicationCodes",
		"SetPackageApplicationCodesResponse" => "SetPackageApplicationCodesResponse",
		"GetPackageItemApplicationCodes" => "GetPackageItemApplicationCodes",
		"PackageItemApplicationCodeRequest" => "PackageItemApplicationCodeRequest",
		"PackageItemApplicationCodeCriteria" => "PackageItemApplicationCodeCriteria",
		"PackageItemApplicationCodeExecuteOption" => "PackageItemApplicationCodeExecuteOption",
		"GetPackageItemApplicationCodesResponse" => "GetPackageItemApplicationCodesResponse",
		"PackageItemApplicationCodeResponse" => "PackageItemApplicationCodeResponse",
		"SetPackageItemApplicationCodes" => "SetPackageItemApplicationCodes",
		"SetPackageItemApplicationCodesResponse" => "SetPackageItemApplicationCodesResponse",
		"GetPackageItems" => "GetPackageItems",
		"PackageItemRequest" => "PackageItemRequest",
		"PackageItemCriteria" => "PackageItemCriteria",
		"PackageItemExecuteOption" => "PackageItemExecuteOption",
		"GetPackageItemsResponse" => "GetPackageItemsResponse",
		"PackageItemResponse" => "PackageItemResponse",
		"SetPackageItems" => "SetPackageItems",
		"SetPackageItemsResponse" => "SetPackageItemsResponse",
		"GetPackages" => "GetPackages",
		"PackageRequest" => "PackageRequest",
		"PackageCriteria" => "PackageCriteria",
		"PackageExecuteOption" => "PackageExecuteOption",
		"GetPackagesResponse" => "GetPackagesResponse",
		"PackageResponse" => "PackageResponse",
		"SetPackages" => "SetPackages",
		"SetPackagesResponse" => "SetPackagesResponse",
		"GetPackageTemplates" => "GetPackageTemplates",
		"PackageTemplateRequest" => "PackageTemplateRequest",
		"PackageTemplateCriteria" => "PackageTemplateCriteria",
		"PackageTemplateExecuteOption" => "PackageTemplateExecuteOption",
		"GetPackageTemplatesResponse" => "GetPackageTemplatesResponse",
		"PackageTemplateResponse" => "PackageTemplateResponse",
		"SetPackageTemplates" => "SetPackageTemplates",
		"SetPackageTemplatesResponse" => "SetPackageTemplatesResponse",
		"GetPriceCodes" => "GetPriceCodes",
		"PriceCodeRequest" => "PriceCodeRequest",
		"PriceCodeCriteria" => "PriceCodeCriteria",
		"PriceCodeExecuteOption" => "PriceCodeExecuteOption",
		"GetPriceCodesResponse" => "GetPriceCodesResponse",
		"PriceCodeResponse" => "PriceCodeResponse",
		"GetPriceListQueues" => "GetPriceListQueues",
		"PriceListQueueRequest" => "PriceListQueueRequest",
		"PriceListQueueCriteria" => "PriceListQueueCriteria",
		"PriceListQueueExecuteOption" => "PriceListQueueExecuteOption",
		"GetPriceListQueuesResponse" => "GetPriceListQueuesResponse",
		"PriceListQueueResponse" => "PriceListQueueResponse",
		"SetPriceListQueues" => "SetPriceListQueues",
		"SetPriceListQueuesResponse" => "SetPriceListQueuesResponse",
		"GetPriceListSections" => "GetPriceListSections",
		"PriceListSectionRequest" => "PriceListSectionRequest",
		"PriceListSectionCriteria" => "PriceListSectionCriteria",
		"PriceListSectionExecuteOption" => "PriceListSectionExecuteOption",
		"GetPriceListSectionsResponse" => "GetPriceListSectionsResponse",
		"PriceListSectionResponse" => "PriceListSectionResponse",
		"SetPriceListSections" => "SetPriceListSections",
		"SetPriceListSectionsResponse" => "SetPriceListSectionsResponse",
		"GetPricelist" => "GetPricelist",
		"PricelistRequest" => "PricelistRequest",
		"PricelistCriteria" => "PricelistCriteria",
		"PricelistExecuteOption" => "PricelistExecuteOption",
		"GetPricelistResponse" => "GetPricelistResponse",
		"PricelistResponse" => "PricelistResponse",
		"GetProductAttributes" => "GetProductAttributes",
		"ProductAttributeRequest" => "ProductAttributeRequest",
		"ProductAttributeCriteria" => "ProductAttributeCriteria",
		"ProductAttributeExecuteOption" => "ProductAttributeExecuteOption",
		"GetProductAttributesResponse" => "GetProductAttributesResponse",
		"ProductAttributeResponse" => "ProductAttributeResponse",
		"SetProductAttributes" => "SetProductAttributes",
		"SetProductAttributesResponse" => "SetProductAttributesResponse",
		"GetProductDownloads" => "GetProductDownloads",
		"ProductDownloadRequest" => "ProductDownloadRequest",
		"ProductDownloadCriteria" => "ProductDownloadCriteria",
		"ProductDownloadExecuteOption" => "ProductDownloadExecuteOption",
		"GetProductDownloadsResponse" => "GetProductDownloadsResponse",
		"ProductDownloadResponse" => "ProductDownloadResponse",
		"GetReleaseTemplates" => "GetReleaseTemplates",
		"ReleaseTemplateRequest" => "ReleaseTemplateRequest",
		"ReleaseTemplateCriteria" => "ReleaseTemplateCriteria",
		"ReleaseTemplateExecuteOption" => "ReleaseTemplateExecuteOption",
		"GetReleaseTemplatesResponse" => "GetReleaseTemplatesResponse",
		"ReleaseTemplateResponse" => "ReleaseTemplateResponse",
		"SetReleaseTemplates" => "SetReleaseTemplates",
		"SetReleaseTemplatesResponse" => "SetReleaseTemplatesResponse",
		"GetRemoveIntroPriceListFlags" => "GetRemoveIntroPriceListFlags",
		"RemoveIntroPriceListFlagRequest" => "RemoveIntroPriceListFlagRequest",
		"RemoveIntroPriceListFlagCriteria" => "RemoveIntroPriceListFlagCriteria",
		"RemoveIntroPriceListFlagExecuteOption" => "RemoveIntroPriceListFlagExecuteOption",
		"GetRemoveIntroPriceListFlagsResponse" => "GetRemoveIntroPriceListFlagsResponse",
		"RemoveIntroPriceListFlagResponse" => "RemoveIntroPriceListFlagResponse",
		"SetRemoveIntroPriceListFlags" => "SetRemoveIntroPriceListFlags",
		"SetRemoveIntroPriceListFlagsResponse" => "SetRemoveIntroPriceListFlagsResponse",
		"GetSectionGroups" => "GetSectionGroups",
		"SectionGroupRequest" => "SectionGroupRequest",
		"SectionGroupCriteria" => "SectionGroupCriteria",
		"SectionGroupExecuteOption" => "SectionGroupExecuteOption",
		"GetSectionGroupsResponse" => "GetSectionGroupsResponse",
		"SectionGroupResponse" => "SectionGroupResponse",
		"SetSectionGroups" => "SetSectionGroups",
		"SetSectionGroupsResponse" => "SetSectionGroupsResponse",
		"GetSequences" => "GetSequences",
		"SequenceRequest" => "SequenceRequest",
		"SequenceCriteria" => "SequenceCriteria",
		"SequenceExecuteOption" => "SequenceExecuteOption",
		"GetSequencesResponse" => "GetSequencesResponse",
		"SequenceResponse" => "SequenceResponse",
		"SetSequences" => "SetSequences",
		"SetSequencesResponse" => "SetSequencesResponse",
		"GetSeries" => "GetSeries",
		"SeriesRequest" => "SeriesRequest",
		"SeriesCriteria" => "SeriesCriteria",
		"SeriesExecuteOption" => "SeriesExecuteOption",
		"GetSeriesResponse" => "GetSeriesResponse",
		"SeriesResponse" => "SeriesResponse",
		"Setseries" => "Setseries",
		"SetseriesResponse" => "SetseriesResponse",
		"GetStatusChangeLogics" => "GetStatusChangeLogics",
		"StatusChangeLogicRequest" => "StatusChangeLogicRequest",
		"StatusChangeLogicCriteria" => "StatusChangeLogicCriteria",
		"StatusChangeLogicExecuteOption" => "StatusChangeLogicExecuteOption",
		"GetStatusChangeLogicsResponse" => "GetStatusChangeLogicsResponse",
		"StatusChangeLogicResponse" => "StatusChangeLogicResponse",
		"SetStatusChangeLogics" => "SetStatusChangeLogics",
		"SetStatusChangeLogicsResponse" => "SetStatusChangeLogicsResponse",
		"GetStatusChanges" => "GetStatusChanges",
		"StatusChangeRequest" => "StatusChangeRequest",
		"StatusChangeCriteria" => "StatusChangeCriteria",
		"StatusChangeExecuteOption" => "StatusChangeExecuteOption",
		"GetStatusChangesResponse" => "GetStatusChangesResponse",
		"StatusChangeResponse" => "StatusChangeResponse",
		"SetStatusChanges" => "SetStatusChanges",
		"SetStatusChangesResponse" => "SetStatusChangesResponse",
		"GetStyles" => "GetStyles",
		"StyleRequest" => "StyleRequest",
		"StyleCriteria" => "StyleCriteria",
		"StyleExecuteOption" => "StyleExecuteOption",
		"GetStylesResponse" => "GetStylesResponse",
		"StyleResponse" => "StyleResponse",
		"GetSupportData" => "GetSupportData",
		"SupportDataRequest" => "SupportDataRequest",
		"CategoryCriteria" => "CategoryCriteria",
		"GroupingCriteria" => "GroupingCriteria",
		"SupportDataExecuteOption" => "SupportDataExecuteOption",
		"GetSupportDataResponse" => "GetSupportDataResponse",
		"SupportDataResponse" => "SupportDataResponse",
		"GetAPKSInitialLoads" => "GetAPKSInitialLoads",
		"APKSInitialLoadRequest" => "APKSInitialLoadRequest",
		"APKSInitialLoadExecuteOption" => "APKSInitialLoadExecuteOption",
		"GetAPKSInitialLoadsResponse" => "GetAPKSInitialLoadsResponse",
		"APKSInitialLoadResponse" => "APKSInitialLoadResponse",
		"GetAdvancedSearch" => "GetAdvancedSearch",
		"AdvancedSearchRequest" => "AdvancedSearchRequest",
		"AdvancedSearchExecuteOption" => "AdvancedSearchExecuteOption",
		"GetAdvancedSearchResponse" => "GetAdvancedSearchResponse",
		"AdvancedSearchResponse" => "AdvancedSearchResponse",
		"GetAttributeTypes" => "GetAttributeTypes",
		"AttributeTypeRequest" => "AttributeTypeRequest",
		"AttributeTypeCriteria" => "AttributeTypeCriteria",
		"AttributeTypeExecuteOption" => "AttributeTypeExecuteOption",
		"GetAttributeTypesResponse" => "GetAttributeTypesResponse",
		"AttributeTypeResponse" => "AttributeTypeResponse",
		"SetAttributeTypes" => "SetAttributeTypes",
		"SetAttributeTypesResponse" => "SetAttributeTypesResponse",
		"GetBuyGroups" => "GetBuyGroups",
		"BuyGroupRequest" => "BuyGroupRequest",
		"BuyGroupCriteria" => "BuyGroupCriteria",
		"BuyGroupExecuteOption" => "BuyGroupExecuteOption",
		"GetBuyGroupsResponse" => "GetBuyGroupsResponse",
		"BuyGroupResponse" => "BuyGroupResponse",
		"GetCategories" => "GetCategories",
		"CategoryRequest" => "CategoryRequest",
		"CategoryExecuteOption" => "CategoryExecuteOption",
		"GetCategoriesResponse" => "GetCategoriesResponse",
		"CategoryResponse" => "CategoryResponse",
		"SetCategories" => "SetCategories",
		"SetCategoriesResponse" => "SetCategoriesResponse",
		"GetConsumerCategory" => "GetConsumerCategory",
		"ConsumerCategoryRequest" => "ConsumerCategoryRequest",
		"ConsumerCategoryCriteria" => "ConsumerCategoryCriteria",
		"ConsumerCategoryExecuteOption" => "ConsumerCategoryExecuteOption",
		"GetConsumerCategoryResponse" => "GetConsumerCategoryResponse",
		"ConsumerCategoryResponse" => "ConsumerCategoryResponse",
		"GetConsumerGrouping" => "GetConsumerGrouping",
		"ConsumerGroupingRequest" => "ConsumerGroupingRequest",
		"ConsumerGroupingCriteria" => "ConsumerGroupingCriteria",
		"ConsumerGroupingExecuteOption" => "ConsumerGroupingExecuteOption",
		"GetConsumerGroupingResponse" => "GetConsumerGroupingResponse",
		"ConsumerGroupingResponse" => "ConsumerGroupingResponse",
		"GetCountryGroups" => "GetCountryGroups",
		"CountryGroupRequest" => "CountryGroupRequest",
		"CountryGroupCriteria" => "CountryGroupCriteria",
		"CountryGroupExecuteOption" => "CountryGroupExecuteOption",
		"GetCountryGroupsResponse" => "GetCountryGroupsResponse",
		"CountryGroupResponse" => "CountryGroupResponse",
		"SetCountryGroups" => "SetCountryGroups",
		"SetCountryGroupsResponse" => "SetCountryGroupsResponse",
		"GetCovers" => "GetCovers",
		"CoverRequest" => "CoverRequest",
		"CoverCriteria" => "CoverCriteria",
		"CoverExecuteOption" => "CoverExecuteOption",
		"GetCoversResponse" => "GetCoversResponse",
		"CoverResponse" => "CoverResponse",
		"SetCovers" => "SetCovers",
		"SetCoversResponse" => "SetCoversResponse",
		"UpdateDuplicateCovers" => "UpdateDuplicateCovers",
		"UpdateDuplicateCoversResponse" => "UpdateDuplicateCoversResponse",
		"GetDimensions" => "GetDimensions",
		"DimensionRequest" => "DimensionRequest",
		"DimensionCriteria" => "DimensionCriteria",
		"DimensionExecuteOption" => "DimensionExecuteOption",
		"GetDimensionsResponse" => "GetDimensionsResponse",
		"DimensionResponse" => "DimensionResponse",
		"GetDivisionClasses" => "GetDivisionClasses",
		"DivisionClassRequest" => "DivisionClassRequest",
		"DivisionClassCriteria" => "DivisionClassCriteria",
		"DivisionClassExecuteOption" => "DivisionClassExecuteOption",
		"GetDivisionClassesResponse" => "GetDivisionClassesResponse",
		"DivisionClassResponse" => "DivisionClassResponse",
		"SetDivisionClasses" => "SetDivisionClasses",
		"SetDivisionClassesResponse" => "SetDivisionClassesResponse",
		"GetDivisions" => "GetDivisions",
		"DivisionRequest" => "DivisionRequest",
		"DivisionCriteria" => "DivisionCriteria",
		"DivisionExecuteOption" => "DivisionExecuteOption",
		"GetDivisionsResponse" => "GetDivisionsResponse",
		"DivisionResponse" => "DivisionResponse",
		"GetEnvironmentSpecifics" => "GetEnvironmentSpecifics",
		"EnvironmentSpecificRequest" => "EnvironmentSpecificRequest",
		"EnvironmentSpecificCriteria" => "EnvironmentSpecificCriteria",
		"EnvironmentSpecificExecuteOption" => "EnvironmentSpecificExecuteOption",
		"GetEnvironmentSpecificsResponse" => "GetEnvironmentSpecificsResponse",
		"EnvironmentSpecificResponse" => "EnvironmentSpecificResponse",
		"SetEnvironmentSpecifics" => "SetEnvironmentSpecifics",
		"SetEnvironmentSpecificsResponse" => "SetEnvironmentSpecificsResponse",
		"GetEnvironments" => "GetEnvironments",
		"EnvironmentsRequest" => "EnvironmentsRequest",
		"EnvironmentsCriteria" => "EnvironmentsCriteria",
		"EnvironmentsExecuteOption" => "EnvironmentsExecuteOption",
		"GetEnvironmentsResponse" => "GetEnvironmentsResponse",
		"EnvironmentsResponse" => "EnvironmentsResponse",
		"SetEnvironments" => "SetEnvironments",
		"SetEnvironmentsResponse" => "SetEnvironmentsResponse",
		"GetFieldExceptions" => "GetFieldExceptions",
		"FieldExceptionRequest" => "FieldExceptionRequest",
		"FieldExceptionCriteria" => "FieldExceptionCriteria",
		"FieldExceptionExecuteOption" => "FieldExceptionExecuteOption",
		"GetFieldExceptionsResponse" => "GetFieldExceptionsResponse",
		"FieldExceptionResponse" => "FieldExceptionResponse",
		"SetFieldExceptions" => "SetFieldExceptions",
		"SetFieldExceptionsResponse" => "SetFieldExceptionsResponse",
		"GetFriendlyDescriptions" => "GetFriendlyDescriptions",
		"FriendlyDescriptionRequest" => "FriendlyDescriptionRequest",
		"FriendlyDescriptionCriteria" => "FriendlyDescriptionCriteria",
		"FriendlyDescriptionExecuteOption" => "FriendlyDescriptionExecuteOption",
		"GetFriendlyDescriptionsResponse" => "GetFriendlyDescriptionsResponse",
		"FriendlyDescriptionResponse" => "FriendlyDescriptionResponse",
		"SetFriendlyDescriptions" => "SetFriendlyDescriptions",
		"SetFriendlyDescriptionsResponse" => "SetFriendlyDescriptionsResponse",
		"GetGeneralDescriptions" => "GetGeneralDescriptions",
		"GeneralDescriptionRequest" => "GeneralDescriptionRequest",
		"GeneralDescriptionCriteria" => "GeneralDescriptionCriteria",
		"GeneralDescriptionExecuteOption" => "GeneralDescriptionExecuteOption",
		"GetGeneralDescriptionsResponse" => "GetGeneralDescriptionsResponse",
		"GeneralDescriptionResponse" => "GeneralDescriptionResponse",
		"SetGeneralDescriptions" => "SetGeneralDescriptions",
		"SetGeneralDescriptionsResponse" => "SetGeneralDescriptionsResponse",
		"GetGroupings" => "GetGroupings",
		"GroupingRequest" => "GroupingRequest",
		"GroupingExecuteOption" => "GroupingExecuteOption",
		"GetGroupingsResponse" => "GetGroupingsResponse",
		"GroupingResponse" => "GroupingResponse",
		"SetGroupings" => "SetGroupings",
		"SetGroupingsResponse" => "SetGroupingsResponse",
		"GetHomeStoreHierarchyFamilyOfBusinessRooms" => "GetHomeStoreHierarchyFamilyOfBusinessRooms",
		"HomeStoreHierarchyFamilyOfBusinessRoomRequest" => "HomeStoreHierarchyFamilyOfBusinessRoomRequest",
		"HomeStoreHierarchyFamilyOfBusinessRoomCriteria" => "HomeStoreHierarchyFamilyOfBusinessRoomCriteria",
		"HomeStoreHierarchyFamilyOfBusinessRoomExecuteOption" => "HomeStoreHierarchyFamilyOfBusinessRoomExecuteOption",
		"GetHomeStoreHierarchyFamilyOfBusinessRoomsResponse" => "GetHomeStoreHierarchyFamilyOfBusinessRoomsResponse",
		"HomeStoreHierarchyFamilyOfBusinessRoomResponse" => "HomeStoreHierarchyFamilyOfBusinessRoomResponse",
		"SetHomeStoreHierarchyFamilyOfBusinessRooms" => "SetHomeStoreHierarchyFamilyOfBusinessRooms",
		"SetHomeStoreHierarchyFamilyOfBusinessRoomsResponse" => "SetHomeStoreHierarchyFamilyOfBusinessRoomsResponse",
		"GetHomeStorehierarchyFamilyOfBusinesses" => "GetHomeStorehierarchyFamilyOfBusinesses",
		"HomeStorehierarchyFamilyOfBusinessRequest" => "HomeStorehierarchyFamilyOfBusinessRequest",
		"HomeStorehierarchyFamilyOfBusinessCriteria" => "HomeStorehierarchyFamilyOfBusinessCriteria",
		"HomeStorehierarchyFamilyOfBusinessExecuteOption" => "HomeStorehierarchyFamilyOfBusinessExecuteOption",
		"GetHomeStorehierarchyFamilyOfBusinessesResponse" => "GetHomeStorehierarchyFamilyOfBusinessesResponse",
		"HomeStorehierarchyFamilyOfBusinessResponse" => "HomeStorehierarchyFamilyOfBusinessResponse",
		"SetHomeStorehierarchyFamilyOfBusinesses" => "SetHomeStorehierarchyFamilyOfBusinesses",
		"SetHomeStorehierarchyFamilyOfBusinessesResponse" => "SetHomeStorehierarchyFamilyOfBusinessesResponse",
		"GetHomeStoreHierarchyRetailRooms" => "GetHomeStoreHierarchyRetailRooms",
		"HomeStoreHierarchyRetailRoomRequest" => "HomeStoreHierarchyRetailRoomRequest",
		"HomeStoreHierarchyRetailRoomCriteria" => "HomeStoreHierarchyRetailRoomCriteria",
		"HomeStoreHierarchyRetailRoomExecuteOption" => "HomeStoreHierarchyRetailRoomExecuteOption",
		"GetHomeStoreHierarchyRetailRoomsResponse" => "GetHomeStoreHierarchyRetailRoomsResponse",
		"HomeStoreHierarchyRetailRoomResponse" => "HomeStoreHierarchyRetailRoomResponse",
		"SetHomeStoreHierarchyRetailRooms" => "SetHomeStoreHierarchyRetailRooms",
		"SetHomeStoreHierarchyRetailRoomsResponse" => "SetHomeStoreHierarchyRetailRoomsResponse",
		"GetHomeStoreHierarchyRetailSalesCategories" => "GetHomeStoreHierarchyRetailSalesCategories",
		"HomeStoreHierarchyRetailSalesCategoryRequest" => "HomeStoreHierarchyRetailSalesCategoryRequest",
		"HomeStoreHierarchyRetailSalesCategoryCriteria" => "HomeStoreHierarchyRetailSalesCategoryCriteria",
		"HomeStoreHierarchyRetailSalesCategoryExecuteOption" => "HomeStoreHierarchyRetailSalesCategoryExecuteOption",
		"GetHomeStoreHierarchyRetailSalesCategoriesResponse" => "GetHomeStoreHierarchyRetailSalesCategoriesResponse",
		"HomeStoreHierarchyRetailSalesCategoryResponse" => "HomeStoreHierarchyRetailSalesCategoryResponse",
		"SetHomeStoreHierarchyRetailSalesCategories" => "SetHomeStoreHierarchyRetailSalesCategories",
		"SetHomeStoreHierarchyRetailSalesCategoriesResponse" => "SetHomeStoreHierarchyRetailSalesCategoriesResponse",
		"GetHomeStoreHierarchyRoomRetailSalesCategories" => "GetHomeStoreHierarchyRoomRetailSalesCategories",
		"HomeStoreHierarchyRoomRetailSalesCategoryRequest" => "HomeStoreHierarchyRoomRetailSalesCategoryRequest",
		"HomeStoreHierarchyRoomRetailSalesCategoryCriteria" => "HomeStoreHierarchyRoomRetailSalesCategoryCriteria",
		"HomeStoreHierarchyRoomRetailSalesCategoryExecuteOption" => "HomeStoreHierarchyRoomRetailSalesCategoryExecuteOption",
		"GetHomeStoreHierarchyRoomRetailSalesCategoriesResponse" => "GetHomeStoreHierarchyRoomRetailSalesCategoriesResponse",
		"HomeStoreHierarchyRoomRetailSalesCategoryResponse" => "HomeStoreHierarchyRoomRetailSalesCategoryResponse",
		"SetHomeStoreHierarchyRoomRetailSalesCategories" => "SetHomeStoreHierarchyRoomRetailSalesCategories",
		"SetHomeStoreHierarchyRoomRetailSalesCategoriesResponse" => "SetHomeStoreHierarchyRoomRetailSalesCategoriesResponse",
		"GetImageLibraryAssociations" => "GetImageLibraryAssociations",
		"ImageLibraryAssociationRequest" => "ImageLibraryAssociationRequest",
		"ImageLibraryAssociationCriteria" => "ImageLibraryAssociationCriteria",
		"ImageLibraryAssociationExecuteOption" => "ImageLibraryAssociationExecuteOption",
		"GetImageLibraryAssociationsResponse" => "GetImageLibraryAssociationsResponse",
		"ImageLibraryAssociationResponse" => "ImageLibraryAssociationResponse",
		"SetImageLibraryAssociations" => "SetImageLibraryAssociations",
		"SetImageLibraryAssociationsResponse" => "SetImageLibraryAssociationsResponse",
		"GetImageLibraries" => "GetImageLibraries",
		"ImageLibraryRequest" => "ImageLibraryRequest",
		"ImageLibraryCriteria" => "ImageLibraryCriteria",
		"ImageLibraryExecuteOption" => "ImageLibraryExecuteOption",
		"GetImageLibrariesResponse" => "GetImageLibrariesResponse",
		"ImageLibraryResponse" => "ImageLibraryResponse",
		"SetImageLibraries" => "SetImageLibraries",
		"SetImageLibrariesResponse" => "SetImageLibrariesResponse",
		"GetCatalogImages" => "GetCatalogImages",
		"CatalogImageRequest" => "CatalogImageRequest",
		"CatalogImageCriteria" => "CatalogImageCriteria",
		"CatalogImageExecuteOption" => "CatalogImageExecuteOption",
		"GetCatalogImagesResponse" => "GetCatalogImagesResponse",
		"CatalogImageResponse" => "CatalogImageResponse",
		"SetCatalogImages" => "SetCatalogImages",
		"SetCatalogImagesResponse" => "SetCatalogImagesResponse",
		"GetItemCategoryGroupings" => "GetItemCategoryGroupings",
		"ItemCategoryGroupingRequest" => "ItemCategoryGroupingRequest",
		"ItemCategoryGroupingCriteria" => "ItemCategoryGroupingCriteria",
		"ItemCategoryGroupingExecuteOption" => "ItemCategoryGroupingExecuteOption",
		"GetItemCategoryGroupingsResponse" => "GetItemCategoryGroupingsResponse",
		"ItemCategoryGroupingResponse" => "ItemCategoryGroupingResponse",
		"SetItemCategoryGroupings" => "SetItemCategoryGroupings",
		"SetItemCategoryGroupingsResponse" => "SetItemCategoryGroupingsResponse",
		"GetItemCountries" => "GetItemCountries",
		"ItemCountryRequest" => "ItemCountryRequest",
		"ItemCountryCriteria" => "ItemCountryCriteria",
		"ItemCountryExecuteOption" => "ItemCountryExecuteOption",
		"GetItemCountriesResponse" => "GetItemCountriesResponse",
		"ItemCountryResponse" => "ItemCountryResponse",
		"SetItemCountries" => "SetItemCountries",
		"SetItemCountriesResponse" => "SetItemCountriesResponse",
		"GetItemDifferenceCodes" => "GetItemDifferenceCodes",
		"ItemDifferenceCodeRequest" => "ItemDifferenceCodeRequest",
		"ItemDifferenceCodeCriteria" => "ItemDifferenceCodeCriteria",
		"ItemDifferenceCodeExecuteOption" => "ItemDifferenceCodeExecuteOption",
		"GetItemDifferenceCodesResponse" => "GetItemDifferenceCodesResponse",
		"ItemDifferenceCodeResponse" => "ItemDifferenceCodeResponse",
		"SetItemDifferenceCodes" => "SetItemDifferenceCodes",
		"SetItemDifferenceCodesResponse" => "SetItemDifferenceCodesResponse",
		"GetItemPricings" => "GetItemPricings",
		"ItemPricingRequest" => "ItemPricingRequest",
		"ItemPricingCriteria" => "ItemPricingCriteria",
		"ItemPricingExecuteOption" => "ItemPricingExecuteOption",
		"GetItemPricingsResponse" => "GetItemPricingsResponse",
		"ItemPricingResponse" => "ItemPricingResponse",
		"SetItemPricings" => "SetItemPricings",
		"SetItemPricingsResponse" => "SetItemPricingsResponse",
		"GetItems" => "GetItems",
		"ItemRequest" => "ItemRequest",
		"ItemCriteria" => "ItemCriteria",
		"ItemExecuteOption" => "ItemExecuteOption",
		"GetItemsResponse" => "GetItemsResponse",
		"ItemResponse" => "ItemResponse",
		"SetItems" => "SetItems",
		"SetItemsResponse" => "SetItemsResponse",
		"GetItemStatuses" => "GetItemStatuses",
		"ItemStatusRequest" => "ItemStatusRequest",
		"ItemStatusCriteria" => "ItemStatusCriteria",
		"ItemStatusExecuteOption" => "ItemStatusExecuteOption",
		"GetItemStatusesResponse" => "GetItemStatusesResponse",
		"ItemStatusResponse" => "ItemStatusResponse",
		"SetItemStatuses" => "SetItemStatuses",
		"SetItemStatusesResponse" => "SetItemStatusesResponse",
		"GetItemValidation" => "GetItemValidation",
		"ItemValidationRequest" => "ItemValidationRequest",
		"ItemValidationCriteria" => "ItemValidationCriteria",
		"ItemValidationExecuteOption" => "ItemValidationExecuteOption",
		"GetItemValidationResponse" => "GetItemValidationResponse",
		"ItemValidationResponse" => "ItemValidationResponse",
		"GetLanguages" => "GetLanguages",
		"LanguageRequest" => "LanguageRequest",
		"LanguageExecuteOption" => "LanguageExecuteOption",
		"GetLanguagesResponse" => "GetLanguagesResponse",
		"LanguageResponse" => "LanguageResponse",
		"SetLanguages" => "SetLanguages",
		"SetLanguagesResponse" => "SetLanguagesResponse",
		"GetmlCategories" => "GetmlCategories",
		"mlCategoryRequest" => "mlCategoryRequest",
		"mlCategoryCriteria" => "mlCategoryCriteria",
		"MLCategoryExecuteOption" => "MLCategoryExecuteOption",
		"GetmlCategoriesResponse" => "GetmlCategoriesResponse",
		"mlCategoryResponse" => "mlCategoryResponse",
		"SetmlCategories" => "SetmlCategories",
		"SetmlCategoriesResponse" => "SetmlCategoriesResponse",
	);

	/**
	 * Constructor using wsdl location and options array
	 * @param string $wsdl WSDL location for this service
	 * @param array $options Options for the SoapClient
	 */
	public function __construct($wsdl="http://api.ashleyfurniture.com/Ashley.ProductKnowledge.Maintenance.NewService/Services/ProductKnowledgeService.asmx?WSDL", $options=array()) {
		foreach(self::$classmap as $wsdlClassName => $phpClassName) {
		    if(!isset($options['classmap'][$wsdlClassName])) {
		        $options['classmap'][$wsdlClassName] = $phpClassName;
		    }
		}
		parent::__construct($wsdl, $options);
	}

	/**
	 * Checks if an argument list matches against a valid argument type list
	 * @param array $arguments The argument list to check
	 * @param array $validParameters A list of valid argument types
	 * @return boolean true if arguments match against validParameters
	 * @throws Exception invalid function signature message
	 */
	public function _checkArguments($arguments, $validParameters) {
		$variables = "";
		foreach ($arguments as $arg) {
		    $type = gettype($arg);
		    if ($type == "object") {
		        $type = get_class($arg);
		    }
		    $variables .= "(".$type.")";
		}
		if (!in_array($variables, $validParameters)) {
		    throw new Exception("Invalid parameter types: ".str_replace(")(", ", ", $variables));
		}
		return true;
	}

	/**
	 * Service Call: GetWarehouseClassDetails
	 * Parameter options:
	 * (GetWarehouseClassDetails) parameters
	 * (GetWarehouseClassDetails) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetWarehouseClassDetailsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetWarehouseClassDetails($mixed = null) {
		$validParameters = array(
			"(GetWarehouseClassDetails)",
			"(GetWarehouseClassDetails)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetWarehouseClassDetails", $args);
	}


	/**
	 * Service Call: SetWarehouseClassDetails
	 * Parameter options:
	 * (SetWarehouseClassDetails) parameters
	 * (SetWarehouseClassDetails) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetWarehouseClassDetailsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetWarehouseClassDetails($mixed = null) {
		$validParameters = array(
			"(SetWarehouseClassDetails)",
			"(SetWarehouseClassDetails)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetWarehouseClassDetails", $args);
	}


	/**
	 * Service Call: GetWarehouseClassHeaders
	 * Parameter options:
	 * (GetWarehouseClassHeaders) parameters
	 * (GetWarehouseClassHeaders) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetWarehouseClassHeadersResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetWarehouseClassHeaders($mixed = null) {
		$validParameters = array(
			"(GetWarehouseClassHeaders)",
			"(GetWarehouseClassHeaders)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetWarehouseClassHeaders", $args);
	}


	/**
	 * Service Call: SetWarehouseClassHeaders
	 * Parameter options:
	 * (SetWarehouseClassHeaders) parameters
	 * (SetWarehouseClassHeaders) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetWarehouseClassHeadersResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetWarehouseClassHeaders($mixed = null) {
		$validParameters = array(
			"(SetWarehouseClassHeaders)",
			"(SetWarehouseClassHeaders)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetWarehouseClassHeaders", $args);
	}


	/**
	 * Service Call: GetWarehouseClassSites
	 * Parameter options:
	 * (GetWarehouseClassSites) parameters
	 * (GetWarehouseClassSites) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetWarehouseClassSitesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetWarehouseClassSites($mixed = null) {
		$validParameters = array(
			"(GetWarehouseClassSites)",
			"(GetWarehouseClassSites)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetWarehouseClassSites", $args);
	}


	/**
	 * Service Call: SetWarehouseClassSites
	 * Parameter options:
	 * (SetWarehouseClassSites) parameters
	 * (SetWarehouseClassSites) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetWarehouseClassSitesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetWarehouseClassSites($mixed = null) {
		$validParameters = array(
			"(SetWarehouseClassSites)",
			"(SetWarehouseClassSites)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetWarehouseClassSites", $args);
	}


	/**
	 * Service Call: GetWarehouseGroupCountries
	 * Parameter options:
	 * (GetWarehouseGroupCountries) parameters
	 * (GetWarehouseGroupCountries) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetWarehouseGroupCountriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetWarehouseGroupCountries($mixed = null) {
		$validParameters = array(
			"(GetWarehouseGroupCountries)",
			"(GetWarehouseGroupCountries)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetWarehouseGroupCountries", $args);
	}


	/**
	 * Service Call: SetWarehouseGroupCountries
	 * Parameter options:
	 * (SetWarehouseGroupCountries) parameters
	 * (SetWarehouseGroupCountries) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetWarehouseGroupCountriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetWarehouseGroupCountries($mixed = null) {
		$validParameters = array(
			"(SetWarehouseGroupCountries)",
			"(SetWarehouseGroupCountries)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetWarehouseGroupCountries", $args);
	}


	/**
	 * Service Call: GetWarehouseGroups
	 * Parameter options:
	 * (GetWarehouseGroups) parameters
	 * (GetWarehouseGroups) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetWarehouseGroupsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetWarehouseGroups($mixed = null) {
		$validParameters = array(
			"(GetWarehouseGroups)",
			"(GetWarehouseGroups)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetWarehouseGroups", $args);
	}


	/**
	 * Service Call: SetWarehouseGroups
	 * Parameter options:
	 * (SetWarehouseGroups) parameters
	 * (SetWarehouseGroups) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetWarehouseGroupsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetWarehouseGroups($mixed = null) {
		$validParameters = array(
			"(SetWarehouseGroups)",
			"(SetWarehouseGroups)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetWarehouseGroups", $args);
	}


	/**
	 * Service Call: GetmlCleanCodes
	 * Parameter options:
	 * (GetmlCleanCodes) parameters
	 * (GetmlCleanCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlCleanCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlCleanCodes($mixed = null) {
		$validParameters = array(
			"(GetmlCleanCodes)",
			"(GetmlCleanCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlCleanCodes", $args);
	}


	/**
	 * Service Call: SetmlCleanCodes
	 * Parameter options:
	 * (SetmlCleanCodes) parameters
	 * (SetmlCleanCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlCleanCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlCleanCodes($mixed = null) {
		$validParameters = array(
			"(SetmlCleanCodes)",
			"(SetmlCleanCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlCleanCodes", $args);
	}


	/**
	 * Service Call: GetmlColors
	 * Parameter options:
	 * (GetmlColors) parameters
	 * (GetmlColors) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlColorsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlColors($mixed = null) {
		$validParameters = array(
			"(GetmlColors)",
			"(GetmlColors)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlColors", $args);
	}


	/**
	 * Service Call: SetmlColors
	 * Parameter options:
	 * (SetmlColors) parameters
	 * (SetmlColors) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlColorsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlColors($mixed = null) {
		$validParameters = array(
			"(SetmlColors)",
			"(SetmlColors)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlColors", $args);
	}


	/**
	 * Service Call: GetmlCoverContents
	 * Parameter options:
	 * (GetmlCoverContents) parameters
	 * (GetmlCoverContents) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlCoverContentsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlCoverContents($mixed = null) {
		$validParameters = array(
			"(GetmlCoverContents)",
			"(GetmlCoverContents)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlCoverContents", $args);
	}


	/**
	 * Service Call: SetmlCoverContents
	 * Parameter options:
	 * (SetmlCoverContents) parameters
	 * (SetmlCoverContents) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlCoverContentsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlCoverContents($mixed = null) {
		$validParameters = array(
			"(SetmlCoverContents)",
			"(SetmlCoverContents)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlCoverContents", $args);
	}


	/**
	 * Service Call: GetmlCovers
	 * Parameter options:
	 * (GetmlCovers) parameters
	 * (GetmlCovers) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlCoversResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlCovers($mixed = null) {
		$validParameters = array(
			"(GetmlCovers)",
			"(GetmlCovers)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlCovers", $args);
	}


	/**
	 * Service Call: SetmlCovers
	 * Parameter options:
	 * (SetmlCovers) parameters
	 * (SetmlCovers) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlCoversResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlCovers($mixed = null) {
		$validParameters = array(
			"(SetmlCovers)",
			"(SetmlCovers)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlCovers", $args);
	}


	/**
	 * Service Call: GetmlDivisions
	 * Parameter options:
	 * (GetmlDivisions) parameters
	 * (GetmlDivisions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlDivisionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlDivisions($mixed = null) {
		$validParameters = array(
			"(GetmlDivisions)",
			"(GetmlDivisions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlDivisions", $args);
	}


	/**
	 * Service Call: SetmlDivisions
	 * Parameter options:
	 * (SetmlDivisions) parameters
	 * (SetmlDivisions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlDivisionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlDivisions($mixed = null) {
		$validParameters = array(
			"(SetmlDivisions)",
			"(SetmlDivisions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlDivisions", $args);
	}


	/**
	 * Service Call: GetMLGeneralDescriptions
	 * Parameter options:
	 * (GetMLGeneralDescriptions) parameters
	 * (GetMLGeneralDescriptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetMLGeneralDescriptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetMLGeneralDescriptions($mixed = null) {
		$validParameters = array(
			"(GetMLGeneralDescriptions)",
			"(GetMLGeneralDescriptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetMLGeneralDescriptions", $args);
	}


	/**
	 * Service Call: SetMLGeneralDescriptions
	 * Parameter options:
	 * (SetMLGeneralDescriptions) parameters
	 * (SetMLGeneralDescriptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetMLGeneralDescriptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetMLGeneralDescriptions($mixed = null) {
		$validParameters = array(
			"(SetMLGeneralDescriptions)",
			"(SetMLGeneralDescriptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetMLGeneralDescriptions", $args);
	}


	/**
	 * Service Call: GetmlGroupings
	 * Parameter options:
	 * (GetmlGroupings) parameters
	 * (GetmlGroupings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlGroupingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlGroupings($mixed = null) {
		$validParameters = array(
			"(GetmlGroupings)",
			"(GetmlGroupings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlGroupings", $args);
	}


	/**
	 * Service Call: SetmlGroupings
	 * Parameter options:
	 * (SetmlGroupings) parameters
	 * (SetmlGroupings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlGroupingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlGroupings($mixed = null) {
		$validParameters = array(
			"(SetmlGroupings)",
			"(SetmlGroupings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlGroupings", $args);
	}


	/**
	 * Service Call: GetmlItemStatuses
	 * Parameter options:
	 * (GetmlItemStatuses) parameters
	 * (GetmlItemStatuses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlItemStatusesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlItemStatuses($mixed = null) {
		$validParameters = array(
			"(GetmlItemStatuses)",
			"(GetmlItemStatuses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlItemStatuses", $args);
	}


	/**
	 * Service Call: SetmlItemStatuses
	 * Parameter options:
	 * (SetmlItemStatuses) parameters
	 * (SetmlItemStatuses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlItemStatusesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlItemStatuses($mixed = null) {
		$validParameters = array(
			"(SetmlItemStatuses)",
			"(SetmlItemStatuses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlItemStatuses", $args);
	}


	/**
	 * Service Call: GetmlSerieses
	 * Parameter options:
	 * (GetmlSerieses) parameters
	 * (GetmlSerieses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlSeriesesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlSerieses($mixed = null) {
		$validParameters = array(
			"(GetmlSerieses)",
			"(GetmlSerieses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlSerieses", $args);
	}


	/**
	 * Service Call: SetmlSerieses
	 * Parameter options:
	 * (SetmlSerieses) parameters
	 * (SetmlSerieses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlSeriesesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlSerieses($mixed = null) {
		$validParameters = array(
			"(SetmlSerieses)",
			"(SetmlSerieses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlSerieses", $args);
	}


	/**
	 * Service Call: GetmlStyles
	 * Parameter options:
	 * (GetmlStyles) parameters
	 * (GetmlStyles) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlStylesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlStyles($mixed = null) {
		$validParameters = array(
			"(GetmlStyles)",
			"(GetmlStyles)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlStyles", $args);
	}


	/**
	 * Service Call: SetmlStyles
	 * Parameter options:
	 * (SetmlStyles) parameters
	 * (SetmlStyles) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlStylesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlStyles($mixed = null) {
		$validParameters = array(
			"(SetmlStyles)",
			"(SetmlStyles)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlStyles", $args);
	}


	/**
	 * Service Call: Getml_items
	 * Parameter options:
	 * (Getml_items) parameters
	 * (Getml_items) parameters
	 * @param mixed,... See function description for parameter options
	 * @return Getml_itemsResponse
	 * @throws Exception invalid function signature message
	 */
	public function Getml_items($mixed = null) {
		$validParameters = array(
			"(Getml_items)",
			"(Getml_items)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("Getml_items", $args);
	}


	/**
	 * Service Call: Setml_items
	 * Parameter options:
	 * (Setml_items) parameters
	 * (Setml_items) parameters
	 * @param mixed,... See function description for parameter options
	 * @return Setml_itemsResponse
	 * @throws Exception invalid function signature message
	 */
	public function Setml_items($mixed = null) {
		$validParameters = array(
			"(Setml_items)",
			"(Setml_items)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("Setml_items", $args);
	}


	/**
	 * Service Call: GetMarkets
	 * Parameter options:
	 * (GetMarkets) parameters
	 * (GetMarkets) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetMarketsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetMarkets($mixed = null) {
		$validParameters = array(
			"(GetMarkets)",
			"(GetMarkets)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetMarkets", $args);
	}


	/**
	 * Service Call: SetMarkets
	 * Parameter options:
	 * (SetMarkets) parameters
	 * (SetMarkets) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetMarketsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetMarkets($mixed = null) {
		$validParameters = array(
			"(SetMarkets)",
			"(SetMarkets)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetMarkets", $args);
	}


	/**
	 * Service Call: GetMissingItemPhotos
	 * Parameter options:
	 * (GetMissingItemPhotos) parameters
	 * (GetMissingItemPhotos) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetMissingItemPhotosResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetMissingItemPhotos($mixed = null) {
		$validParameters = array(
			"(GetMissingItemPhotos)",
			"(GetMissingItemPhotos)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetMissingItemPhotos", $args);
	}


	/**
	 * Service Call: SetMissingItemPhotos
	 * Parameter options:
	 * (SetMissingItemPhotos) parameters
	 * (SetMissingItemPhotos) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetMissingItemPhotosResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetMissingItemPhotos($mixed = null) {
		$validParameters = array(
			"(SetMissingItemPhotos)",
			"(SetMissingItemPhotos)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetMissingItemPhotos", $args);
	}


	/**
	 * Service Call: GetMultiSeriesDescriptions
	 * Parameter options:
	 * (GetMultiSeriesDescriptions) parameters
	 * (GetMultiSeriesDescriptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetMultiSeriesDescriptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetMultiSeriesDescriptions($mixed = null) {
		$validParameters = array(
			"(GetMultiSeriesDescriptions)",
			"(GetMultiSeriesDescriptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetMultiSeriesDescriptions", $args);
	}


	/**
	 * Service Call: SetMultiSeriesDescriptions
	 * Parameter options:
	 * (SetMultiSeriesDescriptions) parameters
	 * (SetMultiSeriesDescriptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetMultiSeriesDescriptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetMultiSeriesDescriptions($mixed = null) {
		$validParameters = array(
			"(SetMultiSeriesDescriptions)",
			"(SetMultiSeriesDescriptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetMultiSeriesDescriptions", $args);
	}


	/**
	 * Service Call: GetNewModelInspectionReports
	 * Parameter options:
	 * (GetNewModelInspectionReports) parameters
	 * (GetNewModelInspectionReports) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetNewModelInspectionReportsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetNewModelInspectionReports($mixed = null) {
		$validParameters = array(
			"(GetNewModelInspectionReports)",
			"(GetNewModelInspectionReports)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetNewModelInspectionReports", $args);
	}


	/**
	 * Service Call: GetPackageApplicationCodes
	 * Parameter options:
	 * (GetPackageApplicationCodes) parameters
	 * (GetPackageApplicationCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPackageApplicationCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPackageApplicationCodes($mixed = null) {
		$validParameters = array(
			"(GetPackageApplicationCodes)",
			"(GetPackageApplicationCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPackageApplicationCodes", $args);
	}


	/**
	 * Service Call: SetPackageApplicationCodes
	 * Parameter options:
	 * (SetPackageApplicationCodes) parameters
	 * (SetPackageApplicationCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetPackageApplicationCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetPackageApplicationCodes($mixed = null) {
		$validParameters = array(
			"(SetPackageApplicationCodes)",
			"(SetPackageApplicationCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetPackageApplicationCodes", $args);
	}


	/**
	 * Service Call: GetPackageItemApplicationCodes
	 * Parameter options:
	 * (GetPackageItemApplicationCodes) parameters
	 * (GetPackageItemApplicationCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPackageItemApplicationCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPackageItemApplicationCodes($mixed = null) {
		$validParameters = array(
			"(GetPackageItemApplicationCodes)",
			"(GetPackageItemApplicationCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPackageItemApplicationCodes", $args);
	}


	/**
	 * Service Call: SetPackageItemApplicationCodes
	 * Parameter options:
	 * (SetPackageItemApplicationCodes) parameters
	 * (SetPackageItemApplicationCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetPackageItemApplicationCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetPackageItemApplicationCodes($mixed = null) {
		$validParameters = array(
			"(SetPackageItemApplicationCodes)",
			"(SetPackageItemApplicationCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetPackageItemApplicationCodes", $args);
	}


	/**
	 * Service Call: GetPackageItems
	 * Parameter options:
	 * (GetPackageItems) parameters
	 * (GetPackageItems) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPackageItemsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPackageItems($mixed = null) {
		$validParameters = array(
			"(GetPackageItems)",
			"(GetPackageItems)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPackageItems", $args);
	}


	/**
	 * Service Call: SetPackageItems
	 * Parameter options:
	 * (SetPackageItems) parameters
	 * (SetPackageItems) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetPackageItemsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetPackageItems($mixed = null) {
		$validParameters = array(
			"(SetPackageItems)",
			"(SetPackageItems)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetPackageItems", $args);
	}


	/**
	 * Service Call: GetPackages
	 * Parameter options:
	 * (GetPackages) parameters
	 * (GetPackages) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPackagesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPackages($mixed = null) {
		$validParameters = array(
			"(GetPackages)",
			"(GetPackages)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPackages", $args);
	}


	/**
	 * Service Call: SetPackages
	 * Parameter options:
	 * (SetPackages) parameters
	 * (SetPackages) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetPackagesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetPackages($mixed = null) {
		$validParameters = array(
			"(SetPackages)",
			"(SetPackages)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetPackages", $args);
	}


	/**
	 * Service Call: GetPackageTemplates
	 * Parameter options:
	 * (GetPackageTemplates) parameters
	 * (GetPackageTemplates) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPackageTemplatesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPackageTemplates($mixed = null) {
		$validParameters = array(
			"(GetPackageTemplates)",
			"(GetPackageTemplates)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPackageTemplates", $args);
	}


	/**
	 * Service Call: SetPackageTemplates
	 * Parameter options:
	 * (SetPackageTemplates) parameters
	 * (SetPackageTemplates) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetPackageTemplatesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetPackageTemplates($mixed = null) {
		$validParameters = array(
			"(SetPackageTemplates)",
			"(SetPackageTemplates)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetPackageTemplates", $args);
	}


	/**
	 * Service Call: GetPriceCodes
	 * Parameter options:
	 * (GetPriceCodes) parameters
	 * (GetPriceCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPriceCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPriceCodes($mixed = null) {
		$validParameters = array(
			"(GetPriceCodes)",
			"(GetPriceCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPriceCodes", $args);
	}


	/**
	 * Service Call: GetPriceListQueues
	 * Parameter options:
	 * (GetPriceListQueues) parameters
	 * (GetPriceListQueues) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPriceListQueuesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPriceListQueues($mixed = null) {
		$validParameters = array(
			"(GetPriceListQueues)",
			"(GetPriceListQueues)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPriceListQueues", $args);
	}


	/**
	 * Service Call: SetPriceListQueues
	 * Parameter options:
	 * (SetPriceListQueues) parameters
	 * (SetPriceListQueues) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetPriceListQueuesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetPriceListQueues($mixed = null) {
		$validParameters = array(
			"(SetPriceListQueues)",
			"(SetPriceListQueues)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetPriceListQueues", $args);
	}


	/**
	 * Service Call: GetPriceListSections
	 * Parameter options:
	 * (GetPriceListSections) parameters
	 * (GetPriceListSections) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPriceListSectionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPriceListSections($mixed = null) {
		$validParameters = array(
			"(GetPriceListSections)",
			"(GetPriceListSections)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPriceListSections", $args);
	}


	/**
	 * Service Call: SetPriceListSections
	 * Parameter options:
	 * (SetPriceListSections) parameters
	 * (SetPriceListSections) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetPriceListSectionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetPriceListSections($mixed = null) {
		$validParameters = array(
			"(SetPriceListSections)",
			"(SetPriceListSections)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetPriceListSections", $args);
	}


	/**
	 * Service Call: GetPricelist
	 * Parameter options:
	 * (GetPricelist) parameters
	 * (GetPricelist) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetPricelistResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetPricelist($mixed = null) {
		$validParameters = array(
			"(GetPricelist)",
			"(GetPricelist)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetPricelist", $args);
	}


	/**
	 * Service Call: GetProductAttributes
	 * Parameter options:
	 * (GetProductAttributes) parameters
	 * (GetProductAttributes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetProductAttributesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetProductAttributes($mixed = null) {
		$validParameters = array(
			"(GetProductAttributes)",
			"(GetProductAttributes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetProductAttributes", $args);
	}


	/**
	 * Service Call: SetProductAttributes
	 * Parameter options:
	 * (SetProductAttributes) parameters
	 * (SetProductAttributes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetProductAttributesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetProductAttributes($mixed = null) {
		$validParameters = array(
			"(SetProductAttributes)",
			"(SetProductAttributes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetProductAttributes", $args);
	}


	/**
	 * Service Call: GetProductDownloads
	 * Parameter options:
	 * (GetProductDownloads) parameters
	 * (GetProductDownloads) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetProductDownloadsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetProductDownloads($mixed = null) {
		$validParameters = array(
			"(GetProductDownloads)",
			"(GetProductDownloads)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetProductDownloads", $args);
	}


	/**
	 * Service Call: GetReleaseTemplates
	 * Parameter options:
	 * (GetReleaseTemplates) parameters
	 * (GetReleaseTemplates) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetReleaseTemplatesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetReleaseTemplates($mixed = null) {
		$validParameters = array(
			"(GetReleaseTemplates)",
			"(GetReleaseTemplates)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetReleaseTemplates", $args);
	}


	/**
	 * Service Call: SetReleaseTemplates
	 * Parameter options:
	 * (SetReleaseTemplates) parameters
	 * (SetReleaseTemplates) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetReleaseTemplatesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetReleaseTemplates($mixed = null) {
		$validParameters = array(
			"(SetReleaseTemplates)",
			"(SetReleaseTemplates)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetReleaseTemplates", $args);
	}


	/**
	 * Service Call: GetRemoveIntroPriceListFlags
	 * Parameter options:
	 * (GetRemoveIntroPriceListFlags) parameters
	 * (GetRemoveIntroPriceListFlags) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetRemoveIntroPriceListFlagsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetRemoveIntroPriceListFlags($mixed = null) {
		$validParameters = array(
			"(GetRemoveIntroPriceListFlags)",
			"(GetRemoveIntroPriceListFlags)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetRemoveIntroPriceListFlags", $args);
	}


	/**
	 * Service Call: SetRemoveIntroPriceListFlags
	 * Parameter options:
	 * (SetRemoveIntroPriceListFlags) parameters
	 * (SetRemoveIntroPriceListFlags) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetRemoveIntroPriceListFlagsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetRemoveIntroPriceListFlags($mixed = null) {
		$validParameters = array(
			"(SetRemoveIntroPriceListFlags)",
			"(SetRemoveIntroPriceListFlags)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetRemoveIntroPriceListFlags", $args);
	}


	/**
	 * Service Call: GetSectionGroups
	 * Parameter options:
	 * (GetSectionGroups) parameters
	 * (GetSectionGroups) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetSectionGroupsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetSectionGroups($mixed = null) {
		$validParameters = array(
			"(GetSectionGroups)",
			"(GetSectionGroups)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetSectionGroups", $args);
	}


	/**
	 * Service Call: SetSectionGroups
	 * Parameter options:
	 * (SetSectionGroups) parameters
	 * (SetSectionGroups) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetSectionGroupsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetSectionGroups($mixed = null) {
		$validParameters = array(
			"(SetSectionGroups)",
			"(SetSectionGroups)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetSectionGroups", $args);
	}


	/**
	 * Service Call: GetSequences
	 * Parameter options:
	 * (GetSequences) parameters
	 * (GetSequences) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetSequencesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetSequences($mixed = null) {
		$validParameters = array(
			"(GetSequences)",
			"(GetSequences)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetSequences", $args);
	}


	/**
	 * Service Call: SetSequences
	 * Parameter options:
	 * (SetSequences) parameters
	 * (SetSequences) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetSequencesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetSequences($mixed = null) {
		$validParameters = array(
			"(SetSequences)",
			"(SetSequences)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetSequences", $args);
	}


	/**
	 * Service Call: GetSeries
	 * Parameter options:
	 * (GetSeries) parameters
	 * (GetSeries) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetSeriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetSeries($mixed = null) {
		$validParameters = array(
			"(GetSeries)",
			"(GetSeries)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetSeries", $args);
	}


	/**
	 * Service Call: Setseries
	 * Parameter options:
	 * (Setseries) parameters
	 * (Setseries) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetseriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function Setseries($mixed = null) {
		$validParameters = array(
			"(Setseries)",
			"(Setseries)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("Setseries", $args);
	}


	/**
	 * Service Call: GetStatusChangeLogics
	 * Parameter options:
	 * (GetStatusChangeLogics) parameters
	 * (GetStatusChangeLogics) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetStatusChangeLogicsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetStatusChangeLogics($mixed = null) {
		$validParameters = array(
			"(GetStatusChangeLogics)",
			"(GetStatusChangeLogics)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetStatusChangeLogics", $args);
	}


	/**
	 * Service Call: SetStatusChangeLogics
	 * Parameter options:
	 * (SetStatusChangeLogics) parameters
	 * (SetStatusChangeLogics) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetStatusChangeLogicsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetStatusChangeLogics($mixed = null) {
		$validParameters = array(
			"(SetStatusChangeLogics)",
			"(SetStatusChangeLogics)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetStatusChangeLogics", $args);
	}


	/**
	 * Service Call: GetStatusChanges
	 * Parameter options:
	 * (GetStatusChanges) parameters
	 * (GetStatusChanges) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetStatusChangesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetStatusChanges($mixed = null) {
		$validParameters = array(
			"(GetStatusChanges)",
			"(GetStatusChanges)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetStatusChanges", $args);
	}


	/**
	 * Service Call: SetStatusChanges
	 * Parameter options:
	 * (SetStatusChanges) parameters
	 * (SetStatusChanges) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetStatusChangesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetStatusChanges($mixed = null) {
		$validParameters = array(
			"(SetStatusChanges)",
			"(SetStatusChanges)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetStatusChanges", $args);
	}


	/**
	 * Service Call: GetStyles
	 * Parameter options:
	 * (GetStyles) parameters
	 * (GetStyles) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetStylesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetStyles($mixed = null) {
		$validParameters = array(
			"(GetStyles)",
			"(GetStyles)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetStyles", $args);
	}


	/**
	 * Service Call: GetSupportData
	 * Parameter options:
	 * (GetSupportData) parameters
	 * (GetSupportData) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetSupportDataResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetSupportData($mixed = null) {
		$validParameters = array(
			"(GetSupportData)",
			"(GetSupportData)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetSupportData", $args);
	}


	/**
	 * Service Call: GetAPKSInitialLoads
	 * Parameter options:
	 * (GetAPKSInitialLoads) parameters
	 * (GetAPKSInitialLoads) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetAPKSInitialLoadsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetAPKSInitialLoads($mixed = null) {
		$validParameters = array(
			"(GetAPKSInitialLoads)",
			"(GetAPKSInitialLoads)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetAPKSInitialLoads", $args);
	}


	/**
	 * Service Call: GetAdvancedSearch
	 * Parameter options:
	 * (GetAdvancedSearch) parameters
	 * (GetAdvancedSearch) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetAdvancedSearchResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetAdvancedSearch($mixed = null) {
		$validParameters = array(
			"(GetAdvancedSearch)",
			"(GetAdvancedSearch)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetAdvancedSearch", $args);
	}


	/**
	 * Service Call: GetAttributeTypes
	 * Parameter options:
	 * (GetAttributeTypes) parameters
	 * (GetAttributeTypes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetAttributeTypesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetAttributeTypes($mixed = null) {
		$validParameters = array(
			"(GetAttributeTypes)",
			"(GetAttributeTypes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetAttributeTypes", $args);
	}


	/**
	 * Service Call: SetAttributeTypes
	 * Parameter options:
	 * (SetAttributeTypes) parameters
	 * (SetAttributeTypes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetAttributeTypesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetAttributeTypes($mixed = null) {
		$validParameters = array(
			"(SetAttributeTypes)",
			"(SetAttributeTypes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetAttributeTypes", $args);
	}


	/**
	 * Service Call: GetBuyGroups
	 * Parameter options:
	 * (GetBuyGroups) parameters
	 * (GetBuyGroups) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetBuyGroupsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetBuyGroups($mixed = null) {
		$validParameters = array(
			"(GetBuyGroups)",
			"(GetBuyGroups)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetBuyGroups", $args);
	}


	/**
	 * Service Call: GetCategories
	 * Parameter options:
	 * (GetCategories) parameters
	 * (GetCategories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetCategoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetCategories($mixed = null) {
		$validParameters = array(
			"(GetCategories)",
			"(GetCategories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetCategories", $args);
	}


	/**
	 * Service Call: SetCategories
	 * Parameter options:
	 * (SetCategories) parameters
	 * (SetCategories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetCategoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetCategories($mixed = null) {
		$validParameters = array(
			"(SetCategories)",
			"(SetCategories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetCategories", $args);
	}


	/**
	 * Service Call: GetConsumerCategory
	 * Parameter options:
	 * (GetConsumerCategory) parameters
	 * (GetConsumerCategory) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetConsumerCategoryResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetConsumerCategory($mixed = null) {
		$validParameters = array(
			"(GetConsumerCategory)",
			"(GetConsumerCategory)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetConsumerCategory", $args);
	}


	/**
	 * Service Call: GetConsumerGrouping
	 * Parameter options:
	 * (GetConsumerGrouping) parameters
	 * (GetConsumerGrouping) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetConsumerGroupingResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetConsumerGrouping($mixed = null) {
		$validParameters = array(
			"(GetConsumerGrouping)",
			"(GetConsumerGrouping)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetConsumerGrouping", $args);
	}


	/**
	 * Service Call: GetCountryGroups
	 * Parameter options:
	 * (GetCountryGroups) parameters
	 * (GetCountryGroups) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetCountryGroupsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetCountryGroups($mixed = null) {
		$validParameters = array(
			"(GetCountryGroups)",
			"(GetCountryGroups)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetCountryGroups", $args);
	}


	/**
	 * Service Call: SetCountryGroups
	 * Parameter options:
	 * (SetCountryGroups) parameters
	 * (SetCountryGroups) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetCountryGroupsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetCountryGroups($mixed = null) {
		$validParameters = array(
			"(SetCountryGroups)",
			"(SetCountryGroups)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetCountryGroups", $args);
	}


	/**
	 * Service Call: GetCovers
	 * Parameter options:
	 * (GetCovers) parameters
	 * (GetCovers) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetCoversResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetCovers($mixed = null) {
		$validParameters = array(
			"(GetCovers)",
			"(GetCovers)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetCovers", $args);
	}


	/**
	 * Service Call: SetCovers
	 * Parameter options:
	 * (SetCovers) parameters
	 * (SetCovers) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetCoversResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetCovers($mixed = null) {
		$validParameters = array(
			"(SetCovers)",
			"(SetCovers)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetCovers", $args);
	}


	/**
	 * Service Call: UpdateDuplicateCovers
	 * Parameter options:
	 * (UpdateDuplicateCovers) parameters
	 * (UpdateDuplicateCovers) parameters
	 * @param mixed,... See function description for parameter options
	 * @return UpdateDuplicateCoversResponse
	 * @throws Exception invalid function signature message
	 */
	public function UpdateDuplicateCovers($mixed = null) {
		$validParameters = array(
			"(UpdateDuplicateCovers)",
			"(UpdateDuplicateCovers)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("UpdateDuplicateCovers", $args);
	}


	/**
	 * Service Call: GetDimensions
	 * Parameter options:
	 * (GetDimensions) parameters
	 * (GetDimensions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetDimensionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetDimensions($mixed = null) {
		$validParameters = array(
			"(GetDimensions)",
			"(GetDimensions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetDimensions", $args);
	}


	/**
	 * Service Call: GetDivisionClasses
	 * Parameter options:
	 * (GetDivisionClasses) parameters
	 * (GetDivisionClasses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetDivisionClassesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetDivisionClasses($mixed = null) {
		$validParameters = array(
			"(GetDivisionClasses)",
			"(GetDivisionClasses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetDivisionClasses", $args);
	}


	/**
	 * Service Call: SetDivisionClasses
	 * Parameter options:
	 * (SetDivisionClasses) parameters
	 * (SetDivisionClasses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetDivisionClassesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetDivisionClasses($mixed = null) {
		$validParameters = array(
			"(SetDivisionClasses)",
			"(SetDivisionClasses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetDivisionClasses", $args);
	}


	/**
	 * Service Call: GetDivisions
	 * Parameter options:
	 * (GetDivisions) parameters
	 * (GetDivisions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetDivisionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetDivisions($mixed = null) {
		$validParameters = array(
			"(GetDivisions)",
			"(GetDivisions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetDivisions", $args);
	}


	/**
	 * Service Call: GetEnvironmentSpecifics
	 * Parameter options:
	 * (GetEnvironmentSpecifics) parameters
	 * (GetEnvironmentSpecifics) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetEnvironmentSpecificsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetEnvironmentSpecifics($mixed = null) {
		$validParameters = array(
			"(GetEnvironmentSpecifics)",
			"(GetEnvironmentSpecifics)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetEnvironmentSpecifics", $args);
	}


	/**
	 * Service Call: SetEnvironmentSpecifics
	 * Parameter options:
	 * (SetEnvironmentSpecifics) parameters
	 * (SetEnvironmentSpecifics) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetEnvironmentSpecificsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetEnvironmentSpecifics($mixed = null) {
		$validParameters = array(
			"(SetEnvironmentSpecifics)",
			"(SetEnvironmentSpecifics)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetEnvironmentSpecifics", $args);
	}


	/**
	 * Service Call: GetEnvironments
	 * Parameter options:
	 * (GetEnvironments) parameters
	 * (GetEnvironments) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetEnvironmentsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetEnvironments($mixed = null) {
		$validParameters = array(
			"(GetEnvironments)",
			"(GetEnvironments)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetEnvironments", $args);
	}


	/**
	 * Service Call: SetEnvironments
	 * Parameter options:
	 * (SetEnvironments) parameters
	 * (SetEnvironments) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetEnvironmentsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetEnvironments($mixed = null) {
		$validParameters = array(
			"(SetEnvironments)",
			"(SetEnvironments)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetEnvironments", $args);
	}


	/**
	 * Service Call: GetFieldExceptions
	 * Parameter options:
	 * (GetFieldExceptions) parameters
	 * (GetFieldExceptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetFieldExceptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetFieldExceptions($mixed = null) {
		$validParameters = array(
			"(GetFieldExceptions)",
			"(GetFieldExceptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetFieldExceptions", $args);
	}


	/**
	 * Service Call: SetFieldExceptions
	 * Parameter options:
	 * (SetFieldExceptions) parameters
	 * (SetFieldExceptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetFieldExceptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetFieldExceptions($mixed = null) {
		$validParameters = array(
			"(SetFieldExceptions)",
			"(SetFieldExceptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetFieldExceptions", $args);
	}


	/**
	 * Service Call: GetFriendlyDescriptions
	 * Parameter options:
	 * (GetFriendlyDescriptions) parameters
	 * (GetFriendlyDescriptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetFriendlyDescriptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetFriendlyDescriptions($mixed = null) {
		$validParameters = array(
			"(GetFriendlyDescriptions)",
			"(GetFriendlyDescriptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetFriendlyDescriptions", $args);
	}


	/**
	 * Service Call: SetFriendlyDescriptions
	 * Parameter options:
	 * (SetFriendlyDescriptions) parameters
	 * (SetFriendlyDescriptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetFriendlyDescriptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetFriendlyDescriptions($mixed = null) {
		$validParameters = array(
			"(SetFriendlyDescriptions)",
			"(SetFriendlyDescriptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetFriendlyDescriptions", $args);
	}


	/**
	 * Service Call: GetGeneralDescriptions
	 * Parameter options:
	 * (GetGeneralDescriptions) parameters
	 * (GetGeneralDescriptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetGeneralDescriptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetGeneralDescriptions($mixed = null) {
		$validParameters = array(
			"(GetGeneralDescriptions)",
			"(GetGeneralDescriptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetGeneralDescriptions", $args);
	}


	/**
	 * Service Call: SetGeneralDescriptions
	 * Parameter options:
	 * (SetGeneralDescriptions) parameters
	 * (SetGeneralDescriptions) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetGeneralDescriptionsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetGeneralDescriptions($mixed = null) {
		$validParameters = array(
			"(SetGeneralDescriptions)",
			"(SetGeneralDescriptions)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetGeneralDescriptions", $args);
	}


	/**
	 * Service Call: GetGroupings
	 * Parameter options:
	 * (GetGroupings) parameters
	 * (GetGroupings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetGroupingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetGroupings($mixed = null) {
		$validParameters = array(
			"(GetGroupings)",
			"(GetGroupings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetGroupings", $args);
	}


	/**
	 * Service Call: SetGroupings
	 * Parameter options:
	 * (SetGroupings) parameters
	 * (SetGroupings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetGroupingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetGroupings($mixed = null) {
		$validParameters = array(
			"(SetGroupings)",
			"(SetGroupings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetGroupings", $args);
	}


	/**
	 * Service Call: GetHomeStoreHierarchyFamilyOfBusinessRooms
	 * Parameter options:
	 * (GetHomeStoreHierarchyFamilyOfBusinessRooms) parameters
	 * (GetHomeStoreHierarchyFamilyOfBusinessRooms) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetHomeStoreHierarchyFamilyOfBusinessRoomsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetHomeStoreHierarchyFamilyOfBusinessRooms($mixed = null) {
		$validParameters = array(
			"(GetHomeStoreHierarchyFamilyOfBusinessRooms)",
			"(GetHomeStoreHierarchyFamilyOfBusinessRooms)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetHomeStoreHierarchyFamilyOfBusinessRooms", $args);
	}


	/**
	 * Service Call: SetHomeStoreHierarchyFamilyOfBusinessRooms
	 * Parameter options:
	 * (SetHomeStoreHierarchyFamilyOfBusinessRooms) parameters
	 * (SetHomeStoreHierarchyFamilyOfBusinessRooms) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetHomeStoreHierarchyFamilyOfBusinessRoomsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetHomeStoreHierarchyFamilyOfBusinessRooms($mixed = null) {
		$validParameters = array(
			"(SetHomeStoreHierarchyFamilyOfBusinessRooms)",
			"(SetHomeStoreHierarchyFamilyOfBusinessRooms)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetHomeStoreHierarchyFamilyOfBusinessRooms", $args);
	}


	/**
	 * Service Call: GetHomeStorehierarchyFamilyOfBusinesses
	 * Parameter options:
	 * (GetHomeStorehierarchyFamilyOfBusinesses) parameters
	 * (GetHomeStorehierarchyFamilyOfBusinesses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetHomeStorehierarchyFamilyOfBusinessesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetHomeStorehierarchyFamilyOfBusinesses($mixed = null) {
		$validParameters = array(
			"(GetHomeStorehierarchyFamilyOfBusinesses)",
			"(GetHomeStorehierarchyFamilyOfBusinesses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetHomeStorehierarchyFamilyOfBusinesses", $args);
	}


	/**
	 * Service Call: SetHomeStorehierarchyFamilyOfBusinesses
	 * Parameter options:
	 * (SetHomeStorehierarchyFamilyOfBusinesses) parameters
	 * (SetHomeStorehierarchyFamilyOfBusinesses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetHomeStorehierarchyFamilyOfBusinessesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetHomeStorehierarchyFamilyOfBusinesses($mixed = null) {
		$validParameters = array(
			"(SetHomeStorehierarchyFamilyOfBusinesses)",
			"(SetHomeStorehierarchyFamilyOfBusinesses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetHomeStorehierarchyFamilyOfBusinesses", $args);
	}


	/**
	 * Service Call: GetHomeStoreHierarchyRetailRooms
	 * Parameter options:
	 * (GetHomeStoreHierarchyRetailRooms) parameters
	 * (GetHomeStoreHierarchyRetailRooms) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetHomeStoreHierarchyRetailRoomsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetHomeStoreHierarchyRetailRooms($mixed = null) {
		$validParameters = array(
			"(GetHomeStoreHierarchyRetailRooms)",
			"(GetHomeStoreHierarchyRetailRooms)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetHomeStoreHierarchyRetailRooms", $args);
	}


	/**
	 * Service Call: SetHomeStoreHierarchyRetailRooms
	 * Parameter options:
	 * (SetHomeStoreHierarchyRetailRooms) parameters
	 * (SetHomeStoreHierarchyRetailRooms) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetHomeStoreHierarchyRetailRoomsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetHomeStoreHierarchyRetailRooms($mixed = null) {
		$validParameters = array(
			"(SetHomeStoreHierarchyRetailRooms)",
			"(SetHomeStoreHierarchyRetailRooms)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetHomeStoreHierarchyRetailRooms", $args);
	}


	/**
	 * Service Call: GetHomeStoreHierarchyRetailSalesCategories
	 * Parameter options:
	 * (GetHomeStoreHierarchyRetailSalesCategories) parameters
	 * (GetHomeStoreHierarchyRetailSalesCategories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetHomeStoreHierarchyRetailSalesCategoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetHomeStoreHierarchyRetailSalesCategories($mixed = null) {
		$validParameters = array(
			"(GetHomeStoreHierarchyRetailSalesCategories)",
			"(GetHomeStoreHierarchyRetailSalesCategories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetHomeStoreHierarchyRetailSalesCategories", $args);
	}


	/**
	 * Service Call: SetHomeStoreHierarchyRetailSalesCategories
	 * Parameter options:
	 * (SetHomeStoreHierarchyRetailSalesCategories) parameters
	 * (SetHomeStoreHierarchyRetailSalesCategories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetHomeStoreHierarchyRetailSalesCategoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetHomeStoreHierarchyRetailSalesCategories($mixed = null) {
		$validParameters = array(
			"(SetHomeStoreHierarchyRetailSalesCategories)",
			"(SetHomeStoreHierarchyRetailSalesCategories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetHomeStoreHierarchyRetailSalesCategories", $args);
	}


	/**
	 * Service Call: GetHomeStoreHierarchyRoomRetailSalesCategories
	 * Parameter options:
	 * (GetHomeStoreHierarchyRoomRetailSalesCategories) parameters
	 * (GetHomeStoreHierarchyRoomRetailSalesCategories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetHomeStoreHierarchyRoomRetailSalesCategoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetHomeStoreHierarchyRoomRetailSalesCategories($mixed = null) {
		$validParameters = array(
			"(GetHomeStoreHierarchyRoomRetailSalesCategories)",
			"(GetHomeStoreHierarchyRoomRetailSalesCategories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetHomeStoreHierarchyRoomRetailSalesCategories", $args);
	}


	/**
	 * Service Call: SetHomeStoreHierarchyRoomRetailSalesCategories
	 * Parameter options:
	 * (SetHomeStoreHierarchyRoomRetailSalesCategories) parameters
	 * (SetHomeStoreHierarchyRoomRetailSalesCategories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetHomeStoreHierarchyRoomRetailSalesCategoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetHomeStoreHierarchyRoomRetailSalesCategories($mixed = null) {
		$validParameters = array(
			"(SetHomeStoreHierarchyRoomRetailSalesCategories)",
			"(SetHomeStoreHierarchyRoomRetailSalesCategories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetHomeStoreHierarchyRoomRetailSalesCategories", $args);
	}


	/**
	 * Service Call: GetImageLibraryAssociations
	 * Parameter options:
	 * (GetImageLibraryAssociations) parameters
	 * (GetImageLibraryAssociations) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetImageLibraryAssociationsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetImageLibraryAssociations($mixed = null) {
		$validParameters = array(
			"(GetImageLibraryAssociations)",
			"(GetImageLibraryAssociations)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetImageLibraryAssociations", $args);
	}


	/**
	 * Service Call: SetImageLibraryAssociations
	 * Parameter options:
	 * (SetImageLibraryAssociations) parameters
	 * (SetImageLibraryAssociations) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetImageLibraryAssociationsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetImageLibraryAssociations($mixed = null) {
		$validParameters = array(
			"(SetImageLibraryAssociations)",
			"(SetImageLibraryAssociations)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetImageLibraryAssociations", $args);
	}


	/**
	 * Service Call: GetImageLibraries
	 * Parameter options:
	 * (GetImageLibraries) parameters
	 * (GetImageLibraries) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetImageLibrariesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetImageLibraries($mixed = null) {
		$validParameters = array(
			"(GetImageLibraries)",
			"(GetImageLibraries)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetImageLibraries", $args);
	}


	/**
	 * Service Call: SetImageLibraries
	 * Parameter options:
	 * (SetImageLibraries) parameters
	 * (SetImageLibraries) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetImageLibrariesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetImageLibraries($mixed = null) {
		$validParameters = array(
			"(SetImageLibraries)",
			"(SetImageLibraries)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetImageLibraries", $args);
	}


	/**
	 * Service Call: GetCatalogImages
	 * Parameter options:
	 * (GetCatalogImages) parameters
	 * (GetCatalogImages) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetCatalogImagesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetCatalogImages($mixed = null) {
		$validParameters = array(
			"(GetCatalogImages)",
			"(GetCatalogImages)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetCatalogImages", $args);
	}


	/**
	 * Service Call: SetCatalogImages
	 * Parameter options:
	 * (SetCatalogImages) parameters
	 * (SetCatalogImages) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetCatalogImagesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetCatalogImages($mixed = null) {
		$validParameters = array(
			"(SetCatalogImages)",
			"(SetCatalogImages)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetCatalogImages", $args);
	}


	/**
	 * Service Call: GetItemCategoryGroupings
	 * Parameter options:
	 * (GetItemCategoryGroupings) parameters
	 * (GetItemCategoryGroupings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetItemCategoryGroupingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetItemCategoryGroupings($mixed = null) {
		$validParameters = array(
			"(GetItemCategoryGroupings)",
			"(GetItemCategoryGroupings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetItemCategoryGroupings", $args);
	}


	/**
	 * Service Call: SetItemCategoryGroupings
	 * Parameter options:
	 * (SetItemCategoryGroupings) parameters
	 * (SetItemCategoryGroupings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetItemCategoryGroupingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetItemCategoryGroupings($mixed = null) {
		$validParameters = array(
			"(SetItemCategoryGroupings)",
			"(SetItemCategoryGroupings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetItemCategoryGroupings", $args);
	}


	/**
	 * Service Call: GetItemCountries
	 * Parameter options:
	 * (GetItemCountries) parameters
	 * (GetItemCountries) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetItemCountriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetItemCountries($mixed = null) {
		$validParameters = array(
			"(GetItemCountries)",
			"(GetItemCountries)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetItemCountries", $args);
	}


	/**
	 * Service Call: SetItemCountries
	 * Parameter options:
	 * (SetItemCountries) parameters
	 * (SetItemCountries) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetItemCountriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetItemCountries($mixed = null) {
		$validParameters = array(
			"(SetItemCountries)",
			"(SetItemCountries)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetItemCountries", $args);
	}


	/**
	 * Service Call: GetItemDifferenceCodes
	 * Parameter options:
	 * (GetItemDifferenceCodes) parameters
	 * (GetItemDifferenceCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetItemDifferenceCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetItemDifferenceCodes($mixed = null) {
		$validParameters = array(
			"(GetItemDifferenceCodes)",
			"(GetItemDifferenceCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetItemDifferenceCodes", $args);
	}


	/**
	 * Service Call: SetItemDifferenceCodes
	 * Parameter options:
	 * (SetItemDifferenceCodes) parameters
	 * (SetItemDifferenceCodes) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetItemDifferenceCodesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetItemDifferenceCodes($mixed = null) {
		$validParameters = array(
			"(SetItemDifferenceCodes)",
			"(SetItemDifferenceCodes)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetItemDifferenceCodes", $args);
	}


	/**
	 * Service Call: GetItemPricings
	 * Parameter options:
	 * (GetItemPricings) parameters
	 * (GetItemPricings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetItemPricingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetItemPricings($mixed = null) {
		$validParameters = array(
			"(GetItemPricings)",
			"(GetItemPricings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetItemPricings", $args);
	}


	/**
	 * Service Call: SetItemPricings
	 * Parameter options:
	 * (SetItemPricings) parameters
	 * (SetItemPricings) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetItemPricingsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetItemPricings($mixed = null) {
		$validParameters = array(
			"(SetItemPricings)",
			"(SetItemPricings)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetItemPricings", $args);
	}


	/**
	 * Service Call: GetItems
	 * Parameter options:
	 * (GetItems) parameters
	 * (GetItems) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetItemsResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetItems($mixed = null) {
		$validParameters = array(
			"(GetItems)",
			"(GetItems)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetItems", $args);
	}


	/**
	 * Service Call: SetItems
	 * Parameter options:
	 * (SetItems) parameters
	 * (SetItems) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetItemsResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetItems($mixed = null) {
		$validParameters = array(
			"(SetItems)",
			"(SetItems)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetItems", $args);
	}


	/**
	 * Service Call: GetItemStatuses
	 * Parameter options:
	 * (GetItemStatuses) parameters
	 * (GetItemStatuses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetItemStatusesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetItemStatuses($mixed = null) {
		$validParameters = array(
			"(GetItemStatuses)",
			"(GetItemStatuses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetItemStatuses", $args);
	}


	/**
	 * Service Call: SetItemStatuses
	 * Parameter options:
	 * (SetItemStatuses) parameters
	 * (SetItemStatuses) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetItemStatusesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetItemStatuses($mixed = null) {
		$validParameters = array(
			"(SetItemStatuses)",
			"(SetItemStatuses)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetItemStatuses", $args);
	}


	/**
	 * Service Call: GetItemValidation
	 * Parameter options:
	 * (GetItemValidation) parameters
	 * (GetItemValidation) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetItemValidationResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetItemValidation($mixed = null) {
		$validParameters = array(
			"(GetItemValidation)",
			"(GetItemValidation)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetItemValidation", $args);
	}


	/**
	 * Service Call: GetLanguages
	 * Parameter options:
	 * (GetLanguages) parameters
	 * (GetLanguages) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetLanguagesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetLanguages($mixed = null) {
		$validParameters = array(
			"(GetLanguages)",
			"(GetLanguages)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetLanguages", $args);
	}


	/**
	 * Service Call: SetLanguages
	 * Parameter options:
	 * (SetLanguages) parameters
	 * (SetLanguages) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetLanguagesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetLanguages($mixed = null) {
		$validParameters = array(
			"(SetLanguages)",
			"(SetLanguages)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetLanguages", $args);
	}


	/**
	 * Service Call: GetmlCategories
	 * Parameter options:
	 * (GetmlCategories) parameters
	 * (GetmlCategories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return GetmlCategoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function GetmlCategories($mixed = null) {
		$validParameters = array(
			"(GetmlCategories)",
			"(GetmlCategories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("GetmlCategories", $args);
	}


	/**
	 * Service Call: SetmlCategories
	 * Parameter options:
	 * (SetmlCategories) parameters
	 * (SetmlCategories) parameters
	 * @param mixed,... See function description for parameter options
	 * @return SetmlCategoriesResponse
	 * @throws Exception invalid function signature message
	 */
	public function SetmlCategories($mixed = null) {
		$validParameters = array(
			"(SetmlCategories)",
			"(SetmlCategories)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("SetmlCategories", $args);
	}


}}

?>